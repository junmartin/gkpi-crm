<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceAccountMonthlyBalance;
use App\Models\FinanceBudgetItem;
use App\Models\FinanceTransaction;
use App\Support\FinanceProductionData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FinanceTransactionController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $query = FinanceTransaction::with('budgetItem')
            ->whereBetween('trx_date', [$startDate, $endDate]);

        if ($request->filled('account')) {
            $query->where('account', $request->account);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('budget_item_id')) {
            $query->where('budget_item_id', $request->budget_item_id);
        }

        if ($request->filled('project')) {
            $query->where('project', 'like', '%' . $request->project . '%');
        }

        $transactions = $query->orderByDesc('trx_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $budgetItems = FinanceBudgetItem::where('is_active', true)->orderBy('name')->get();
        $accounts = self::accountList();

        return view('FinanceTransaction.index', compact('transactions', 'budgetItems', 'startDate', 'endDate', 'accounts'));
    }

    public function create()
    {
        $budgetItems = FinanceBudgetItem::where('is_active', true)->orderBy('name')->get();
        $accounts = self::accountList();
        return view('FinanceTransaction.add', compact('budgetItems', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransactionForCreate($request);
        $amount = $this->parseAmount($validated['amount']);

        if ($amount <= 0) {
            return back()->withErrors(['amount' => 'Jumlah harus lebih besar dari 0.'])->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPaths = [];
            foreach ($request->file('attachment') as $file) {
                $attachmentPaths[] = $file->store('finance/attachments', 'public');
            }
            $attachmentPath = !empty($attachmentPaths) ? json_encode($attachmentPaths) : null;
        }

        FinanceTransaction::create([
            'trx_date' => $validated['trx_date'],
            'transaction_type' => $validated['transaction_type'],
            'account' => $validated['account'],
            'budget_item_id' => $validated['budget_item_id'],
            'amount' => $amount,
            'entry_type' => $this->entryTypeForBudgetItemId((int) $validated['budget_item_id']),
            'description' => $validated['description'] ?? null,
            'project' => $validated['project'] ?? null,
            'attachment_path' => $attachmentPath,
            'create_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        return redirect()->route('finance.index')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function edit(FinanceTransaction $finance)
    {
        $budgetItems = FinanceBudgetItem::where('is_active', true)
            ->orWhere('id', $finance->budget_item_id)
            ->orderBy('name')
            ->get();

        $accounts = self::accountList();
        return view('FinanceTransaction.edit', ['transaction' => $finance, 'budgetItems' => $budgetItems, 'accounts' => $accounts]);
    }

    public function update(Request $request, FinanceTransaction $finance)
    {
        $validated = $this->validateTransaction($request);
        $amount = $this->parseAmount($validated['amount']);

        if ($amount <= 0) {
            return back()->withErrors(['amount' => 'Jumlah harus lebih besar dari 0.'])->withInput();
        }

        $attachmentPath = $finance->attachment_path;
        if ($request->hasFile('attachment')) {
            $existingAttachments = [];
            if ($attachmentPath) {
                if (str_starts_with($attachmentPath, '[')) {
                    $existingAttachments = json_decode($attachmentPath, true) ?? [];
                } else {
                    $existingAttachments = [$attachmentPath];
                }
            }

            $newAttachments = [];
            foreach ($request->file('attachment') as $file) {
                $newAttachments[] = $file->store('finance/attachments', 'public');
            }

            $allAttachments = array_merge($existingAttachments, $newAttachments);
            $attachmentPath = !empty($allAttachments) ? json_encode($allAttachments) : null;
        }

        $finance->update([
            'trx_date' => $validated['trx_date'],
            'transaction_type' => $validated['transaction_type'],
            'account' => $validated['account'],
            'budget_item_id' => $validated['budget_item_id'],
            'amount' => $amount,
            'entry_type' => $this->entryTypeForBudgetItemId((int) $validated['budget_item_id'], $finance->entry_type),
            'description' => $validated['description'] ?? null,
            'project' => $validated['project'] ?? null,
            'attachment_path' => $attachmentPath,
            'update_by' => auth()->id(),
        ]);

        return redirect()->route('finance.index')->with('success', 'Transaksi berhasil diubah.');
    }

    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $query = FinanceTransaction::with('budgetItem')
            ->whereBetween('trx_date', [$startDate, $endDate]);

        if ($request->filled('account')) {
            $query->where('account', $request->account);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('budget_item_id')) {
            $query->where('budget_item_id', $request->budget_item_id);
        }

        if ($request->filled('project')) {
            $query->where('project', 'like', '%' . $request->project . '%');
        }

        $transactions = $query->orderByDesc('trx_date')->orderByDesc('id')->get();

        $balanceQuery = FinanceTransaction::with('budgetItem')
            ->whereBetween('trx_date', [$startDate, $endDate]);

        if ($request->filled('account')) {
            $balanceQuery->where('account', $request->account);
        }

        $balanceTransactions = $balanceQuery->orderByDesc('trx_date')->orderByDesc('id')->get();

        $periodStart = Carbon::parse($startDate)->startOfMonth();
        $operationalTransactions = $transactions->reject(fn ($row) => $this->isExcludedFromOperations($row))->values();
        $accountPerformanceTransactions = $transactions->reject(fn ($row) => $this->isOpeningBalance($row))->values();

        $totalIncome = (int) $operationalTransactions->where('transaction_type', 'income')->sum('amount');
        $totalExpense = (int) $operationalTransactions->where('transaction_type', 'expense')->sum('amount');
        $netAmount = $totalIncome - $totalExpense;

        [$accountBalances, $accountBalanceTotals, $balanceSnapshotMonthLabel] = $this->buildAccountBalances(
            $balanceTransactions,
            $periodStart,
            $request->input('account')
        );

        $accountMatrix = $accountPerformanceTransactions
            ->groupBy('account')
            ->map(function ($rows, $account) {
                $incomeRows  = $rows->where('transaction_type', 'income');
                $expenseRows = $rows->where('transaction_type', 'expense');
                $income  = (int) $incomeRows->sum('amount');
                $expense = (int) $expenseRows->sum('amount');
                return [
                    'income'  => $income,
                    'expense' => $expense,
                    'net'     => $income - $expense,
                    'count'   => $rows->count(),
                    'income_txn_ids'  => $incomeRows->pluck('id')->join(','),
                    'expense_txn_ids' => $expenseRows->pluck('id')->join(','),
                ];
            })
            ->sortByDesc('expense');

        $budgetMatrix = $operationalTransactions
            ->where('transaction_type', 'expense')
            ->groupBy('budget_item_id')
            ->map(function ($rows, $itemId) {
                $expense = (int) $rows->sum('amount');
                $latest = $rows->sortByDesc('trx_date')->first();

                return [
                    'budget_item_id' => $itemId,
                    'budget_item' => optional($latest->budgetItem)->name ?? '-',
                    'expense' => $expense,
                    'count' => $rows->count(),
                    'txn_ids' => $rows->pluck('id')->join(','),
                ];
            })
            ->filter(function ($row) {
                $name = strtolower((string) $row['budget_item']);
                return !str_starts_with($name, 'persembahan')
                    && $name !== 'sumbangan jemaat'
                    && $name !== '<< internal transfer >>';
            })
            ->sortByDesc('expense')
            ->values();

        $budgetMatrixTotalExpense = (int) $budgetMatrix->sum('expense');

        $incomingMatrix = $operationalTransactions
            ->where('transaction_type', 'income')
            ->groupBy(fn ($row) => strtolower(trim((string) optional($row->budgetItem)->name)))
            ->map(function ($rows, $key) {
                $sampleName = (string) optional($rows->first()->budgetItem)->name;
                $isIncomingGroup = (str_starts_with($key, 'persembahan') || str_starts_with($key, 'sumbangan'))
                    && $key !== '<< internal transfer >>';

                return [
                    'budget_item' => $sampleName,
                    'amount' => (int) $rows->sum('amount'),
                    'count' => $rows->count(),
                    'include' => $isIncomingGroup,
                    'txn_ids' => $rows->pluck('id')->join(','),
                ];
            })
            ->filter(fn ($row) => $row['include'])
            ->sortByDesc('amount')
            ->values();

        $incomingMatrixTotal = (int) $incomingMatrix->sum('amount');
        if ($incomingMatrixTotal !== $totalIncome) {
            $lainnyaRows = $operationalTransactions
                ->where('transaction_type', 'income')
                ->filter(function ($row) {
                    $name = strtolower(trim((string) optional($row->budgetItem)->name));
                    return !str_starts_with($name, 'persembahan') && !str_starts_with($name, 'sumbangan');
                });
            $incomingMatrix->push([
                'budget_item' => 'Income Lainnya',
                'amount' => $totalIncome - $incomingMatrixTotal,
                'count' => $lainnyaRows->count(),
                'txn_ids' => $lainnyaRows->pluck('id')->join(','),
            ]);
            $incomingMatrixTotal = (int) $incomingMatrix->sum('amount');
        }

        $withAttachment = $transactions->whereNotNull('attachment_path')->count();
        $withoutAttachment = $transactions->whereNull('attachment_path')->count();
        $totalRows = max($transactions->count(), 1);

        $attachmentStats = [
            'with' => $withAttachment,
            'without' => $withoutAttachment,
            'with_pct' => round(($withAttachment / $totalRows) * 100, 1),
            'without_pct' => round(($withoutAttachment / $totalRows) * 100, 1),
        ];

        $topExpenses = $operationalTransactions
            ->where('transaction_type', 'expense')
            ->groupBy('budget_item_id')
            ->map(function ($rows) {
                $latest = $rows->sortByDesc('trx_date')->first();
                return [
                    'budget_item' => (string) (optional($latest->budgetItem)->name ?? '-'),
                    'amount' => (int) $rows->sum('amount'),
                    'count' => $rows->count(),
                    'txn_ids' => $rows->pluck('id')->join(','),
                ];
            })
            ->filter(function ($row) {
                return strtolower($row['budget_item']) !== '<< internal transfer >>';
            })
            ->sortByDesc('amount')
            ->take(5)
            ->values();

        $periodEnd = Carbon::parse($startDate)->endOfMonth();
        $historyStart = (clone $periodStart)->subMonths(3);
        $historyEnd = (clone $periodStart)->subMonth()->endOfMonth();

        $historyRows = FinanceTransaction::query()
            ->where('transaction_type', 'expense')
            ->whereBetween('trx_date', [$historyStart->toDateString(), $historyEnd->toDateString()])
            ->get(['budget_item_id', 'amount', 'trx_date']);

        $historyMonthly = $historyRows
            ->groupBy(fn ($row) => $row->budget_item_id . '|' . Carbon::parse($row->trx_date)->format('Y-m'))
            ->map(fn ($rows) => (int) $rows->sum('amount'));

        $thisMonthPaid = FinanceTransaction::query()
            ->where('transaction_type', 'expense')
            ->whereBetween('trx_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->groupBy('budget_item_id')
            ->selectRaw('budget_item_id, SUM(amount) as total_amount')
            ->pluck('total_amount', 'budget_item_id');

        $thisMonthPaidTxnIds = FinanceTransaction::query()
            ->where('transaction_type', 'expense')
            ->whereBetween('trx_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->get(['id', 'budget_item_id'])
            ->groupBy('budget_item_id')
            ->map(fn ($rows) => $rows->pluck('id')->join(','));

        $routinePending = FinanceBudgetItem::query()
            ->where('is_active', true)
            ->where('is_routine', true)
            ->orderBy('routine_day_of_month')
            ->get()
            ->map(function ($item) use ($periodStart, $historyMonthly, $thisMonthPaid, $thisMonthPaidTxnIds) {
                $today = now()->day;
                $day = (int) ($item->routine_day_of_month ?? 0);

                $monthKeys = [
                    (clone $periodStart)->subMonths(3)->format('Y-m'),
                    (clone $periodStart)->subMonths(2)->format('Y-m'),
                    (clone $periodStart)->subMonth()->format('Y-m'),
                ];

                $monthlyAmounts = collect($monthKeys)
                    ->map(function ($monthKey) use ($item, $historyMonthly) {
                        return (int) ($historyMonthly[$item->id . '|' . $monthKey] ?? 0);
                    });

                $isConsistentLast3Months = $monthlyAmounts->every(fn ($v) => $v > 0);
                $historyAvg = (int) round($monthlyAmounts->avg());

                $fallbackRoutine = (int) ($item->routine_amount ?? 0);
                $expected = $historyAvg > 0 ? $historyAvg : $fallbackRoutine;
                $paid = (int) ($thisMonthPaid[$item->id] ?? 0);
                $remaining = max($expected - $paid, 0);
                $paidPct = $expected > 0 ? min(100, round(($paid / $expected) * 100, 1)) : 0;

                if ($day === 0) {
                    $status = 'Belum dijadwalkan';
                } elseif ($day >= $today) {
                    $status = 'Upcoming this month';
                } else {
                    $status = 'Lewat jadwal, belum tercatat';
                }

                if ($remaining === 0) {
                    $status = 'Sudah terpenuhi';
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'is_consistent_last_3_months' => $isConsistentLast3Months,
                    'expected_amount' => $expected,
                    'paid_amount' => $paid,
                    'remaining_amount' => $remaining,
                    'paid_pct' => $paidPct,
                    'status' => $status,
                    'paid_txn_ids' => (string) ($thisMonthPaidTxnIds[$item->id] ?? ''),
                ];
            })
            ->filter(fn ($row) => $row['remaining_amount'] > 0 && $row['is_consistent_last_3_months'])
            ->values();

        $maxExpectedRoutine = (int) $routinePending->max('expected_amount');
        $routinePending = $routinePending
            ->map(function ($row) use ($maxExpectedRoutine) {
                $row['size_pct'] = $maxExpectedRoutine > 0
                    ? max(8, round(($row['expected_amount'] / $maxExpectedRoutine) * 100, 1))
                    : 8;
                return $row;
            })
            ->values();

        $routinePendingTotals = [
            'expected_amount' => (int) $routinePending->sum('expected_amount'),
            'paid_amount' => (int) $routinePending->sum('paid_amount'),
            'remaining_amount' => (int) $routinePending->sum('remaining_amount'),
            'paid_txn_ids' => $routinePending
                ->pluck('paid_txn_ids')
                ->filter()
                ->implode(','),
        ];

        $budgetItems = FinanceBudgetItem::where('is_active', true)->orderBy('name')->get();
        $accounts = self::accountList();

        $incomeAllIds   = $operationalTransactions->where('transaction_type', 'income')->pluck('id')->join(',');
        $expenseAllIds  = $operationalTransactions->where('transaction_type', 'expense')->pluck('id')->join(',');
        $allIds         = $transactions->pluck('id')->join(',');

        $txnJson = $transactions->map(function ($t) {
            $attachments = [];
            if ($t->attachment_path) {
                // Handle JSON array format
                if (str_starts_with($t->attachment_path, '[')) {
                    $attachments = json_decode($t->attachment_path, true) ?? [];
                } else {
                    // Handle single path format (backward compatibility)
                    $attachments = [$t->attachment_path];
                }
            }
            return [
                'id'      => $t->id,
                'date'    => $t->trx_date->format('d M Y'),
                'type'    => $t->transaction_type,
                'account' => $t->account,
                'item'    => (string) optional($t->budgetItem)->name,
                'amount'  => (int) $t->amount,
                'desc'    => (string) $t->description,
                'project' => (string) $t->project,
                'is_transfer' => $this->isInternalTransfer($t),
                'is_opening_balance' => $this->isOpeningBalance($t),
                'attachments' => $attachments,
            ];
        })->values();

        return view('FinanceTransaction.report', compact(
            'transactions',
            'budgetItems',
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpense',
            'netAmount',
            'accountBalances',
            'accountBalanceTotals',
            'balanceSnapshotMonthLabel',
            'accountMatrix',
            'budgetMatrix',
            'budgetMatrixTotalExpense',
            'incomingMatrix',
            'incomingMatrixTotal',
            'attachmentStats',
            'topExpenses',
            'accounts',
            'routinePending',
            'routinePendingTotals',
            'incomeAllIds',
            'expenseAllIds',
            'allIds',
            'txnJson'
        ));
    }

    public function createTransfer()
    {
        $accounts = self::accountList();
        return view('FinanceTransaction.transfer', compact('accounts'));
    }

    public function storeTransfer(Request $request)
    {
        $validated = $request->validate([
            'trx_date'     => 'required|date',
            'from_account' => 'required|string|max:100',
            'to_account'   => 'required|string|max:100|different:from_account',
            'amount'       => 'required|string|max:30',
            'description'  => 'nullable|string|max:255',
        ]);

        $amount = $this->parseAmount($validated['amount']);
        if ($amount <= 0) {
            return back()->withErrors(['amount' => 'Jumlah harus lebih besar dari 0.'])->withInput();
        }

        $transferItem = FinanceBudgetItem::firstOrCreate(
            ['name' => '<< INTERNAL TRANSFER >>'],
            ['is_active' => true, 'is_routine' => false]
        );

        $uid  = auth()->id();
        $date = $validated['trx_date'];
        $desc = $validated['description'] ?? null;
        $transferGroupKey = (string) Str::uuid();

        FinanceTransaction::create([
            'trx_date'         => $date,
            'transaction_type' => 'expense',
            'account'          => $validated['from_account'],
            'budget_item_id'   => $transferItem->id,
            'amount'           => $amount,
            'entry_type'       => FinanceTransaction::ENTRY_TYPE_INTERNAL_TRANSFER,
            'transfer_group_key' => $transferGroupKey,
            'description'      => $desc,
            'project'          => null,
            'attachment_path'  => null,
            'create_by'        => $uid,
            'update_by'        => $uid,
        ]);

        FinanceTransaction::create([
            'trx_date'         => $date,
            'transaction_type' => 'income',
            'account'          => $validated['to_account'],
            'budget_item_id'   => $transferItem->id,
            'amount'           => $amount,
            'entry_type'       => FinanceTransaction::ENTRY_TYPE_INTERNAL_TRANSFER,
            'transfer_group_key' => $transferGroupKey,
            'description'      => $desc,
            'project'          => null,
            'attachment_path'  => null,
            'create_by'        => $uid,
            'update_by'        => $uid,
        ]);

        return redirect()->route('finance.index')->with('success', 'Transfer berhasil dicatat.');
    }

    public function storeAttachment(Request $request, FinanceTransaction $finance)
    {
        $validated = $request->validate([
            'attachment' => 'required|array',
            'attachment.*' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        // Get existing attachments
        $existingAttachments = [];
        if ($finance->attachment_path) {
            if (str_starts_with($finance->attachment_path, '[')) {
                $existingAttachments = json_decode($finance->attachment_path, true) ?? [];
            } else {
                $existingAttachments = [$finance->attachment_path];
            }
        }

        // Process new files
        $newAttachments = [];
        foreach ($request->file('attachment') as $file) {
            $path = $file->store('finance/attachments', 'public');
            $newAttachments[] = $path;
        }

        // Combine existing and new attachments
        $allAttachments = array_merge($existingAttachments, $newAttachments);

        $finance->update([
            'attachment_path' => json_encode($allAttachments),
            'update_by' => auth()->id(),
        ]);

        return back()->with('success', 'Attachment berhasil diunggah.');
    }

    public function deleteAttachment(FinanceTransaction $finance, int $index)
    {
        $attachments = [];
        if ($finance->attachment_path) {
            if (str_starts_with($finance->attachment_path, '[')) {
                $attachments = json_decode($finance->attachment_path, true) ?? [];
            } else {
                $attachments = [$finance->attachment_path];
            }
        }

        if (!array_key_exists($index, $attachments)) {
            return back()->withErrors(['attachment' => 'Lampiran tidak ditemukan.']);
        }

        $pathToDelete = $attachments[$index];
        if ($pathToDelete && Storage::disk('public')->exists($pathToDelete)) {
            Storage::disk('public')->delete($pathToDelete);
        }

        unset($attachments[$index]);
        $attachments = array_values($attachments);

        $finance->update([
            'attachment_path' => !empty($attachments) ? json_encode($attachments) : null,
            'update_by' => auth()->id(),
        ]);

        return back()->with('success', 'Attachment berhasil dihapus.');
    }

    public function viewAttachment(Request $request, FinanceTransaction $finance)
    {
        $attachments = [];
        if ($finance->attachment_path) {
            if (str_starts_with($finance->attachment_path, '[')) {
                $attachments = json_decode($finance->attachment_path, true) ?? [];
            } else {
                $attachments = [$finance->attachment_path];
            }
        }

        if (empty($attachments)) {
            abort(404);
        }

        $total = count($attachments);
        $requestedIndex = (int) $request->query('i', 0);
        $index = (($requestedIndex % $total) + $total) % $total;

        $prevIndex = ($index - 1 + $total) % $total;
        $nextIndex = ($index + 1) % $total;
        $currentPath = $attachments[$index];
        $extension = strtolower(pathinfo($currentPath, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);

        return view('FinanceTransaction.attachment_viewer', [
            'finance' => $finance,
            'attachments' => $attachments,
            'index' => $index,
            'prevIndex' => $prevIndex,
            'nextIndex' => $nextIndex,
            'total' => $total,
            'currentPath' => $currentPath,
            'isImage' => $isImage,
        ]);
    }

    private function validateTransaction(Request $request): array
    {
        return $request->validate([
            'trx_date' => 'required|date',
            'transaction_type' => 'required|in:income,expense',
            'account' => 'required|string|max:100',
            'budget_item_id' => 'required|exists:finance_budget_items,id',
            'amount' => 'required|string|max:30',
            'description' => 'nullable|string',
            'project' => 'nullable|string|max:255',
            'attachment' => 'nullable|array',
            'attachment.*' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);
    }

    private function validateTransactionForCreate(Request $request): array
    {
        return $request->validate([
            'trx_date' => 'required|date',
            'transaction_type' => 'required|in:income,expense',
            'account' => 'required|string|max:100',
            'budget_item_id' => 'required|exists:finance_budget_items,id',
            'amount' => 'required|string|max:30',
            'description' => 'nullable|string',
            'project' => 'nullable|string|max:255',
            'attachment' => 'nullable|array',
            'attachment.*' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);
    }

    private static function accountList(): array
    {
        if (Schema::hasTable('finance_accounts')) {
            $accounts = FinanceAccount::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name')
                ->all();

            if (!empty($accounts)) {
                return $accounts;
            }
        }

        return FinanceProductionData::defaultAccountNames();
    }

    private function parseAmount(?string $amount): int
    {
        $cleaned = preg_replace('/\D/', '', (string) $amount);
        return $cleaned === '' ? 0 : (int) $cleaned;
    }

    private function isInternalTransfer(FinanceTransaction $transaction): bool
    {
        return $transaction->isInternalTransfer();
    }

    private function isOpeningBalance(FinanceTransaction $transaction): bool
    {
        return $transaction->isOpeningBalance();
    }

    private function isExcludedFromOperations(FinanceTransaction $transaction): bool
    {
        return $this->isInternalTransfer($transaction) || $this->isOpeningBalance($transaction);
    }

    private function entryTypeForBudgetItemId(int $budgetItemId, ?string $fallback = FinanceTransaction::ENTRY_TYPE_REGULAR): string
    {
        $name = strtolower(trim((string) FinanceBudgetItem::query()->whereKey($budgetItemId)->value('name')));

        return match ($name) {
            '<< internal transfer >>' => FinanceTransaction::ENTRY_TYPE_INTERNAL_TRANSFER,
            '<< opening balance >>' => FinanceTransaction::ENTRY_TYPE_OPENING_BALANCE,
            default => $fallback === FinanceTransaction::ENTRY_TYPE_OPENING_BALANCE
                ? FinanceTransaction::ENTRY_TYPE_OPENING_BALANCE
                : FinanceTransaction::ENTRY_TYPE_REGULAR,
        };
    }

    private function buildAccountBalances(Collection $periodTransactions, Carbon $periodStart, ?string $accountFilter = null): array
    {
        $snapshotMonth = (clone $periodStart)->subMonth()->startOfMonth();
        $this->syncMonthlyBalanceSnapshots($snapshotMonth, $accountFilter);

        $accounts = collect(self::accountList());
        if ($accountFilter) {
            $accounts = $accounts->filter(fn ($account) => $account === $accountFilter)->values();
        }

        $snapshotRows = FinanceAccountMonthlyBalance::query()
            ->whereDate('period_month', $snapshotMonth->toDateString())
            ->when($accountFilter, fn ($query) => $query->where('account', $accountFilter))
            ->get()
            ->keyBy('account');

        $historyBeforePeriod = FinanceTransaction::query()
            ->where('trx_date', '<', $periodStart->toDateString())
            ->when($accountFilter, fn ($query) => $query->where('account', $accountFilter))
            ->get(['account', 'transaction_type', 'amount'])
            ->groupBy('account');

        $periodByAccount = $periodTransactions->groupBy('account');

        $rows = $accounts->map(function ($account) use ($snapshotRows, $historyBeforePeriod, $periodByAccount) {
            $opening = (int) optional($snapshotRows->get($account))->closing_balance;

            if (!$snapshotRows->has($account)) {
                $historyRows = $historyBeforePeriod->get($account, collect());
                $opening = (int) $historyRows->where('transaction_type', 'income')->sum('amount')
                    - (int) $historyRows->where('transaction_type', 'expense')->sum('amount');
            }

            $periodRows = $periodByAccount->get($account, collect());
            $inflowRows = $periodRows->where('transaction_type', 'income');
            $outflowRows = $periodRows->where('transaction_type', 'expense');
            $inflow = (int) $inflowRows->sum('amount');
            $outflow = (int) $outflowRows->sum('amount');

            return [
                'account' => $account,
                'opening_balance' => $opening,
                'inflow_amount' => $inflow,
                'outflow_amount' => $outflow,
                'closing_balance' => $opening + $inflow - $outflow,
                'inflow_txn_ids' => $inflowRows->pluck('id')->join(','),
                'outflow_txn_ids' => $outflowRows->pluck('id')->join(','),
            ];
        })->filter(function ($row) {
            return $row['opening_balance'] !== 0
                || $row['inflow_amount'] !== 0
                || $row['outflow_amount'] !== 0;
        })->values();

        $totals = [
            'opening_balance' => (int) $rows->sum('opening_balance'),
            'inflow_amount' => (int) $rows->sum('inflow_amount'),
            'outflow_amount' => (int) $rows->sum('outflow_amount'),
            'closing_balance' => (int) $rows->sum('closing_balance'),
        ];

        return [$rows, $totals, $snapshotMonth->format('M Y')];
    }

    private function syncMonthlyBalanceSnapshots(Carbon $targetMonth, ?string $accountFilter = null): void
    {
        $accounts = collect(self::accountList());
        if ($accountFilter) {
            $accounts = $accounts->filter(fn ($account) => $account === $accountFilter)->values();
        }

        if ($accounts->isEmpty()) {
            return;
        }

        $targetMonth = (clone $targetMonth)->startOfMonth();
        $targetMonthEnd = (clone $targetMonth)->endOfMonth();

        $allRows = FinanceTransaction::query()
            ->whereIn('account', $accounts->all())
            ->where('trx_date', '<=', $targetMonthEnd->toDateString())
            ->orderBy('trx_date')
            ->orderBy('id')
            ->get(['account', 'transaction_type', 'amount', 'trx_date']);

        $rowsByAccountMonth = $allRows
            ->groupBy(function ($row) {
                return $row->account . '|' . Carbon::parse($row->trx_date)->format('Y-m');
            });

        $firstMonthByAccount = $allRows
            ->groupBy('account')
            ->map(function ($rows) {
                return Carbon::parse($rows->min('trx_date'))->startOfMonth();
            });

        $uid = auth()->id();

        foreach ($accounts as $account) {
            $firstMonth = $firstMonthByAccount->get($account, (clone $targetMonth));
            $runningBalance = 0;
            $cursor = (clone $firstMonth)->startOfMonth();

            while ($cursor->lessThanOrEqualTo($targetMonth)) {
                $monthKey = $account . '|' . $cursor->format('Y-m');
                $monthRows = $rowsByAccountMonth->get($monthKey, collect());

                $inflow = (int) $monthRows->where('transaction_type', 'income')->sum('amount');
                $outflow = (int) $monthRows->where('transaction_type', 'expense')->sum('amount');
                $opening = $runningBalance;
                $closing = $opening + $inflow - $outflow;

                FinanceAccountMonthlyBalance::updateOrCreate(
                    [
                        'account' => $account,
                        'period_month' => $cursor->toDateString(),
                    ],
                    [
                        'opening_balance' => $opening,
                        'inflow_amount' => $inflow,
                        'outflow_amount' => $outflow,
                        'closing_balance' => $closing,
                        'update_by' => $uid,
                        'create_by' => $uid,
                    ]
                );

                $runningBalance = $closing;
                $cursor->addMonth();
            }
        }
    }
}
