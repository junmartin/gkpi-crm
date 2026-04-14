<?php

namespace App\Http\Controllers;

use App\Models\FinanceBudgetItem;
use Illuminate\Http\Request;

class FinanceBudgetItemController extends Controller
{
    public function index()
    {
        $items = FinanceBudgetItem::orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('FinanceBudgetItem.index', compact('items'));
    }

    public function create()
    {
        return view('FinanceBudgetItem.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:finance_budget_items,name',
            'is_active' => 'nullable|boolean',
            'is_routine' => 'nullable|boolean',
            'routine_day_of_month' => 'nullable|integer|min:1|max:31',
            'routine_amount' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $isRoutine = (bool) ($validated['is_routine'] ?? false);

        FinanceBudgetItem::create([
            'name' => $validated['name'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'is_routine' => $isRoutine,
            'routine_day_of_month' => $isRoutine ? ($validated['routine_day_of_month'] ?? null) : null,
            'routine_amount' => $isRoutine ? $this->parseAmount($validated['routine_amount'] ?? null) : null,
            'notes' => $validated['notes'] ?? null,
            'create_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        return redirect()->route('finance_budget_item.index')->with('success', 'Budget item berhasil ditambahkan.');
    }

    public function edit(FinanceBudgetItem $finance_budget_item)
    {
        return view('FinanceBudgetItem.edit', ['item' => $finance_budget_item]);
    }

    public function update(Request $request, FinanceBudgetItem $finance_budget_item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:finance_budget_items,name,' . $finance_budget_item->id,
            'is_active' => 'nullable|boolean',
            'is_routine' => 'nullable|boolean',
            'routine_day_of_month' => 'nullable|integer|min:1|max:31',
            'routine_amount' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $isRoutine = (bool) ($validated['is_routine'] ?? false);

        $finance_budget_item->update([
            'name' => $validated['name'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'is_routine' => $isRoutine,
            'routine_day_of_month' => $isRoutine ? ($validated['routine_day_of_month'] ?? null) : null,
            'routine_amount' => $isRoutine ? $this->parseAmount($validated['routine_amount'] ?? null) : null,
            'notes' => $validated['notes'] ?? null,
            'update_by' => auth()->id(),
        ]);

        return redirect()->route('finance_budget_item.index')->with('success', 'Budget item berhasil diubah.');
    }

    private function parseAmount(?string $amount): ?int
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $cleaned = preg_replace('/\D/', '', $amount);
        return $cleaned === '' ? null : (int) $cleaned;
    }
}
