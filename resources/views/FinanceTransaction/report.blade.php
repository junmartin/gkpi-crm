@extends('layouts.form')

@section('content')
@php
    $rupiah  = fn($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
    $fmtExp  = fn($v) => '<span style="color:#cc0000">-Rp ' . number_format((int) $v, 0, ',', '.') . '</span>';
    $fmtInc  = fn($v) => '<span style="color:#0000cc">Rp '  . number_format((int) $v, 0, ',', '.') . '</span>';
    $fmtNeutral = fn($v) => '<span style="color:#444444">Rp ' . number_format((int) $v, 0, ',', '.') . '</span>';
@endphp

<style>
    /* === Report style: Tahoma/Arial compact, matching Trade History reference === */
    .rpt-wrap { font: 8pt Tahoma, Arial; }
    h3.rpt-title  { font: bold 14pt Tahoma, Arial; margin: 8px 0 12px; }
    h4.rpt-section { font: bold 9pt Tahoma, Arial; margin: 10px 0 3px; }

    table.rpt {
        border-collapse: separate;
        border-spacing: 1px;
        background: #A8A8A8;
        width: 100%;
        margin-bottom: 10px;
    }
    table.rpt th {
        font: bold 8pt Tahoma, Arial;
        background: #E5F0FC;
        padding: 3px 6px;
        white-space: nowrap;
        text-align: left;
        vertical-align: middle;
    }
    table.rpt td {
        font: 8pt Tahoma, Arial;
        background: #FFFFFF;
        padding: 3px 6px;
        white-space: nowrap;
        vertical-align: top;
    }
    table.rpt tbody tr:nth-child(even) td { background: #F7F7F7; }
    table.rpt tfoot th { background: #ccd9e5; }
    table.rpt .num { text-align: right; }

    /* KPI summary table */
    table.kpi { border-collapse: separate; border-spacing: 1px; background: #A8A8A8; width: 100%; margin-bottom: 10px; }
    table.kpi th { font: bold 8pt Tahoma, Arial; background: #E5F0FC; padding: 3px 8px; text-align: center; }
    table.kpi td { font: 8pt Tahoma, Arial; background: #FFFFFF; padding: 5px 8px; text-align: center; }
    table.kpi .kval { font: bold 12pt Tahoma, Arial; }

    /* Split grid */
    .rpt-split { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 2px; }
    @media (max-width: 900px) { .rpt-split { grid-template-columns: 1fr; } }

    /* Progress bar */
    .prog-track { height: 10px; background: #d8e4ef; border-radius: 2px; overflow: hidden; display: inline-block; vertical-align: middle; }
    .prog-fill   { height: 100%; background: linear-gradient(90deg, #5b9bd5, #2d72b8); }

    /* Status pill */
    .pill       { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7.5pt; }
    .pill-ok    { background: #c6efce; color: #276221; }
    .pill-warn  { background: #ffeb9c; color: #9c5700; }
    .pill-danger{ background: #ffc7ce; color: #9c0006; }

    /* Upload box */
    .upload-wrap { border: 1px dashed #7a9ec0; border-radius: 4px; padding: 5px; background: #f5f9fc; margin-top: 3px; }
    .upload-wrap.dragover { background: #dde8f5; border-color: #2d72b8; }

    /* Popup modal */
    #rpt-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; overflow:auto; }
    #rpt-modal-box { background:#fff; margin:40px auto; max-width:960px; min-width:400px; border:1px solid #9ab; font:8pt Tahoma,Arial; }
    #rpt-modal-head { background:#E5F0FC; padding:5px 8px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #b0c4d8; }
    #rpt-modal-head strong { font: bold 10pt Tahoma, Arial; }
    #rpt-modal-close { cursor:pointer; font:bold 11pt Tahoma,Arial; color:#333; border:none; background:none; padding:0 4px; }
    #rpt-modal-body { padding:6px 8px; max-height:65vh; overflow-y:auto; }

    /* Drill-down link */
    a.dl { color:#0055cc; text-decoration:underline; cursor:pointer; }
    a.dl:hover { color:#003399; }
</style>

<div class="rpt-wrap">
<h3 class="rpt-title">Financial Report</h3>

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<!-- Filter form -->
@php
    $today = now();
    $monthlyReportPresets = collect([        
        [
            'label' => $today->copy()->startOfMonth()->subMonths(2)->format("M'y"),
            'start_date' => $today->copy()->startOfMonth()->subMonths(2)->startOfMonth()->toDateString(),
            'end_date' => $today->copy()->startOfMonth()->subMonths(2)->endOfMonth()->toDateString(),
        ],
        [
            'label' => $today->copy()->startOfMonth()->subMonth()->format("M'y"),
            'start_date' => $today->copy()->startOfMonth()->subMonth()->startOfMonth()->toDateString(),
            'end_date' => $today->copy()->startOfMonth()->subMonth()->endOfMonth()->toDateString(),
        ],
        [
            'label' => 'Current month-to-date',
            'start_date' => $today->copy()->startOfMonth()->toDateString(),
            'end_date' => $today->copy()->toDateString(),
        ],
    ])->map(function ($preset) {
        $preset['active'] = request('start_date') === $preset['start_date']
            && request('end_date') === $preset['end_date']
            && blank(request('account'))
            && blank(request('transaction_type'))
            && blank(request('budget_item_id'))
            && blank(request('project'));

        return $preset;
    });
@endphp
<form method="GET" action="{{ route('finance.report') }}">
    <table class="rpt" style="width:auto;">
        <thead>
            <tr><th colspan="6">Report Filter (default: Month To Date)</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Monthly Report</td>
                <td colspan="5">
                    @foreach($monthlyReportPresets as $preset)
                        <a
                            href="{{ route('finance.report', ['start_date' => $preset['start_date'], 'end_date' => $preset['end_date']]) }}"
                            style="display:inline-block; margin-right:6px; padding:2px 6px; border:1px solid {{ $preset['active'] ? '#2d72b8' : '#9ab' }}; background:{{ $preset['active'] ? '#dde8f5' : '#f5f9fc' }}; color:#003366; text-decoration:none; border-radius:3px;"
                        >{{ $preset['label'] }}</a>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>Start Date</td>
                <td><input type="date" name="start_date" value="{{ request('start_date', $startDate) }}"></td>
                <td>End Date</td>
                <td><input type="date" name="end_date" value="{{ request('end_date', $endDate) }}"></td>
                <td>Account</td>
                <td>
                    <select name="account">
                        <option value="">All</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc }}" {{ request('account') === $acc ? 'selected' : '' }}>{{ $acc }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>Type</td>
                <td>
                    <select name="transaction_type">
                        <option value="">All</option>
                        <option value="expense" {{ request('transaction_type') === 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="income"  {{ request('transaction_type') === 'income'  ? 'selected' : '' }}>Income</option>
                    </select>
                </td>
                <td>Budget Item</td>
                <td>
                    <select name="budget_item_id">
                        <option value="">All</option>
                        @foreach($budgetItems as $item)
                            <option value="{{ $item->id }}" {{ (string) request('budget_item_id') === (string) $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>Project</td>
                <td><input type="text" name="project" value="{{ request('project') }}"></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:right;">
                    <button type="submit">Generate</button>
                    &nbsp;<a href="{{ route('finance.report') }}">[Reset]</a>
                    &nbsp;<a href="{{ route('finance.create') }}">[+ Add Transaction]</a>
                    &nbsp;<a href="{{ route('finance.transfer.create') }}">[Transfer Antar Rekening]</a>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<!-- KPI Summary -->
<table class="kpi">
    <thead>
        <tr>
            <th>Total Income</th>
            <th>Total Expense</th>
            <th>Net</th>
            <th>Records</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><div class="kval">
                @if($incomeAllIds)
                    <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $incomeAllIds }}','All Income Transactions')">{!! $fmtInc($totalIncome) !!}</a>
                @else {!! $fmtInc($totalIncome) !!} @endif
            </div></td>
            <td><div class="kval">
                @if($expenseAllIds)
                    <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $expenseAllIds }}','All Expense Transactions')">{!! $fmtExp($totalExpense) !!}</a>
                @else {!! $fmtExp($totalExpense) !!} @endif
            </div></td>
            <td><div class="kval" style="color:{{ $netAmount >= 0 ? '#276221' : '#9c0006' }}">{{ $rupiah(abs($netAmount)) }}</div></td>
            <td><div class="kval">
                @if($allIds)
                    <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $allIds }}','All Transactions')">{{ $transactions->count() }}</a>
                @else {{ $transactions->count() }} @endif
            </div></td>
        </tr>
    </tbody>
</table>

<h4 class="rpt-section">Matrix: Account Balances (Opening from {{ $balanceSnapshotMonthLabel }} Closing Snapshot)</h4>
<table class="rpt">
    <thead>
        <tr>
            <th>Account</th>
            <th class="num">Beginning Balance</th>
            <th class="num">Money In</th>
            <th class="num">Money Out</th>
            <th class="num">Ending Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($accountBalances as $row)
            <tr>
                <td>{{ strtoupper($row['account']) }}</td>
                <td class="num">{{ $rupiah($row['opening_balance']) }}</td>
                <td class="num">
                    @if($row['inflow_txn_ids'])
                        <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['inflow_txn_ids'] }}','{{ addslashes(strtoupper($row['account'])) }} — Money In')">{{ $rupiah($row['inflow_amount']) }}</a>
                    @else
                        {{ $rupiah($row['inflow_amount']) }}
                    @endif
                </td>
                <td class="num">
                    @if($row['outflow_txn_ids'])
                        <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['outflow_txn_ids'] }}','{{ addslashes(strtoupper($row['account'])) }} — Money Out')">{{ $rupiah($row['outflow_amount']) }}</a>
                    @else
                        {{ $rupiah($row['outflow_amount']) }}
                    @endif
                </td>
                <td class="num" style="color:{{ $row['closing_balance'] >= 0 ? '#276221' : '#9c0006' }}">{{ $rupiah(abs($row['closing_balance'])) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;">No account balances to display for the selected period.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th class="num">{{ $rupiah($accountBalanceTotals['opening_balance']) }}</th>
            <th class="num">{{ $rupiah($accountBalanceTotals['inflow_amount']) }}</th>
            <th class="num">{{ $rupiah($accountBalanceTotals['outflow_amount']) }}</th>
            <th class="num" style="color:{{ $accountBalanceTotals['closing_balance'] >= 0 ? '#276221' : '#9c0006' }}">{{ $rupiah(abs($accountBalanceTotals['closing_balance'])) }}</th>
        </tr>
    </tfoot>
</table>

<div class="rpt-split">
    <!-- Incoming Breakdown -->
    <div>
        <h4 class="rpt-section">Matrix: Incoming Breakdown (Persembahan &amp; Sumbangan)</h4>
        <table class="rpt">
            <thead>
                <tr>
                    <th>Budget Item</th>
                    <th class="num">Total Incoming</th>
                    <th class="num">#Txn</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomingMatrix as $row)
                    <tr>
                        <td>{{ $row['budget_item'] }}</td>
                        <td class="num">
                            <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['txn_ids'] }}','{{ addslashes($row['budget_item']) }}')">{!! $fmtInc($row['amount']) !!}</a>
                        </td>
                        <td class="num">{{ $row['count'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No incoming data.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="num">{!! $fmtInc($incomingMatrixTotal) !!}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Budget Item Breakdown -->
    <div>
        <h4 class="rpt-section">Matrix: Budget Item Breakdown</h4>
        <table class="rpt">
            <thead>
                <tr>
                    <th>Budget Item</th>
                    <th class="num">Expense</th>
                    <th class="num">#Txn</th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgetMatrix as $row)
                    <tr>
                        <td>{{ $row['budget_item'] }}</td>
                        <td class="num">
                            <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['txn_ids'] }}','{{ addslashes($row['budget_item']) }}')">{!! $fmtExp($row['expense']) !!}</a>
                        </td>
                        <td class="num">{{ $row['count'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No data.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="num">{!! $fmtExp($budgetMatrixTotalExpense) !!}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<div class="rpt-split">    
    <!-- Account Performance -->
    <div>
        <h4 class="rpt-section">Matrix: Account Performance</h4>
        <table class="rpt">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="num">Income</th>
                    <th class="num">Expense</th>
                    <th class="num">Net</th>
                    <th class="num">#Txn</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accountMatrix as $account => $row)
                    <tr>
                        <td>{{ strtoupper($account) }}</td>
                        <td class="num">
                            @if($row['income_txn_ids'])
                                <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['income_txn_ids'] }}','{{ addslashes(strtoupper($account)) }} — Income')">{!! $fmtInc($row['income']) !!}</a>
                            @else - @endif
                        </td>
                        <td class="num">
                            @if($row['expense_txn_ids'])
                                <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['expense_txn_ids'] }}','{{ addslashes(strtoupper($account)) }} — Expense')">{!! $fmtExp($row['expense']) !!}</a>
                            @else - @endif
                        </td>
                        <td class="num" style="color:{{ $row['net'] >= 0 ? '#276221' : '#9c0006' }}">{{ $rupiah(abs($row['net'])) }}</td>
                        <td class="num">{{ $row['count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4 class="rpt-section">Matrix: Attachment Compliance</h4>
        <table class="rpt" style="width:auto;">
            <tbody>
                <tr>
                    <th>With Attachment</th>
                    <td>{{ $attachmentStats['with'] }} ({{ $attachmentStats['with_pct'] }}%)</td>
                </tr>
                <tr>
                    <th>Without Attachment</th>
                    <td>{{ $attachmentStats['without'] }} ({{ $attachmentStats['without_pct'] }}%)</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Top 5 Expenses (Aggregated by Category) -->
        <h4 class="rpt-section">Top 5 Expenses</h4>
        <table class="rpt" style="width:auto;">
            <thead>
                <tr>
                    <th>Budget Item</th>
                    <th class="num">Total Expense</th>
                    <th class="num">#Txn</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topExpenses as $row)
                    <tr>
                        <td>{{ $row['budget_item'] }}</td>
                        <td class="num">
                            <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['txn_ids'] }}','Top Expense: {{ addslashes($row['budget_item']) }}')">{!! $fmtExp($row['amount']) !!}</a>
                        </td>
                        <td class="num">{{ $row['count'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No expense data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Unpaid Routine -->
    <div>
        <h4 class="rpt-section">Insight: Unpaid Expense This Month (Routine)</h4>
        <table class="rpt">
            <thead>
                <tr>
                    <th>Budget Item</th>
                    <th class="num">Expected</th>
                    <th class="num">Paid</th>
                    <th class="num">Remaining</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($routinePending as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td class="num">{{ $row['expected_amount'] ? $rupiah($row['expected_amount']) : '-' }}</td>
                        <td class="num">
                            @if($row['paid_amount'] > 0)
                                @if($row['paid_txn_ids'])
                                    <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $row['paid_txn_ids'] }}','{{ addslashes($row['name']) }} — Paid This Month')">{!! $fmtExp($row['paid_amount']) !!}</a>
                                @else
                                    {!! $fmtExp($row['paid_amount']) !!}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="num" style="color:#9c0006;">{{ $row['remaining_amount'] ? $rupiah($row['remaining_amount']) : '-' }}</td>
                        <td>
                            <div style="width:{{ $row['size_pct'] }}%; min-width:50px;">
                                <div class="prog-track" style="width:100%;">
                                    <div class="prog-fill" style="width:{{ $row['paid_pct'] }}%;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No consistent routine unpaid expense.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="num">{{ $rupiah($routinePendingTotals['expected_amount']) }}</th>
                    <th class="num">
                        @if($routinePendingTotals['paid_txn_ids'])
                            <a href="javascript:void(0)" class="dl" onclick="showPopup('{{ $routinePendingTotals['paid_txn_ids'] }}','Routine Expense — Paid This Month')">{!! $fmtExp($routinePendingTotals['paid_amount']) !!}</a>
                        @else
                            {!! $fmtExp($routinePendingTotals['paid_amount']) !!}
                        @endif
                    </th>
                    <th class="num">{{ $rupiah($routinePendingTotals['remaining_amount']) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    
</div>



<!-- Transaction History -->
<h4 class="rpt-section">Transaction History</h4>
<table class="rpt">
    <thead>
        <tr>
            <th>Date</th>
            <th>Account</th>
            <th>Budget Item</th>
            <th class="num">Amount</th>
            <th>Description</th>
            <th>Project</th>
            <th>Attachment</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $trx)
            <tr>
                <td>{{ $trx->trx_date->format('d M Y') }}</td>
                <td>{{ strtoupper($trx->account) }}</td>
                <td>{{ optional($trx->budgetItem)->name ?? '-' }}</td>
                <td class="num">
                    @if($trx->isOpeningBalance())
                        {!! $fmtNeutral($trx->amount) !!}
                    @elseif($trx->transaction_type === 'expense')
                        {!! $fmtExp($trx->amount) !!}
                    @else
                        {!! $fmtInc($trx->amount) !!}
                    @endif
                </td>
                <td>{{ $trx->description }}</td>
                <td>{{ $trx->project }}</td>
                <td>
                    @if($trx->attachment_path)
                        <a href="{{ asset('storage/' . $trx->attachment_path) }}" target="_blank">[View]</a>
                    @else
                        <a href="javascript:void(0)" class="attach-toggle" data-target="attach-box-{{ $trx->id }}">[+ Attach]</a>
                        <div id="attach-box-{{ $trx->id }}" style="display:none; margin-top:3px;">
                            <form method="POST" action="{{ route('finance.attachment.store', $trx->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="upload-wrap" data-drop="drop-{{ $trx->id }}">
                                    Drop file here or click to choose.
                                    <input id="drop-{{ $trx->id }}" type="file" name="attachment" accept="application/pdf,image/*" capture="environment" required>
                                </div>
                                <button type="submit" style="margin-top:3px;">Upload</button>
                            </form>
                        </div>
                    @endif
                </td>
                <td><a href="{{ route('finance.edit', $trx->id) }}">[Edit]</a></td>
            </tr>
        @empty
            <tr><td colspan="8">No transactions.</td></tr>
        @endforelse
    </tbody>
</table>
</div>

<!-- Popup Modal -->
<div id="rpt-modal">
    <div id="rpt-modal-box">
        <div id="rpt-modal-head">
            <strong id="rpt-modal-title">Detail Transactions</strong>
            <button id="rpt-modal-close" title="Close (Esc)">&#x2715;</button>
        </div>
        <div id="rpt-modal-body">
            <table class="rpt" id="rpt-modal-table" style="width:100%;"></table>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
    (function () {
        /* ---- Transaction data ---- */
        const TXN = @json($txnJson);

        /* ---- Rupiah formatter ---- */
        function rupiah(v) { return 'Rp\u00a0' + parseInt(v).toLocaleString('id-ID'); }
        function fmt(type, amount, isTransfer, isOpeningBalance) {
            if (isOpeningBalance) return '<span style="color:#444444">' + rupiah(amount) + '</span>';
            if (type === 'expense') return '<span style="color:#cc0000">-' + rupiah(amount) + '</span>';
            return '<span style="color:#0000cc">' + rupiah(amount) + '</span>';
        }

        /* ---- Popup drill-down ---- */
        window.showPopup = function (idsStr, title) {
            if (!idsStr) return;
            const ids = new Set(idsStr.split(',').map(Number).filter(Boolean));
            const rows = TXN.filter(t => ids.has(t.id));
            const tbody = rows.map(t =>
                '<tr>' +
                '<td>' + t.date + '</td>' +
                '<td>' + t.account.toUpperCase() + '</td>' +
                '<td>' + (t.item || '-') + '</td>' +
                '<td style="text-align:right;">' + fmt(t.type, t.amount, t.is_transfer, t.is_opening_balance) + '</td>' +
                '<td>' + (t.desc || '') + '</td>' +
                '<td>' + (t.project || '') + '</td>' +
                '</tr>'
            ).join('');

            document.getElementById('rpt-modal-title').textContent = title + ' (' + rows.length + ' records)';
            document.getElementById('rpt-modal-table').innerHTML =
                '<thead><tr>' +
                '<th>Date</th><th>Account</th><th>Budget Item</th>' +
                '<th style="text-align:right;">Amount</th><th>Description</th><th>Project</th>' +
                '</tr></thead><tbody>' + tbody + '</tbody>';
            document.getElementById('rpt-modal').style.display = 'block';
        };

        function closeModal() { document.getElementById('rpt-modal').style.display = 'none'; }
        document.getElementById('rpt-modal-close').addEventListener('click', closeModal);
        document.getElementById('rpt-modal').addEventListener('click', function (e) { if (e.target === this) closeModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

        /* ---- Attachment toggle ---- */
        document.querySelectorAll('.attach-toggle').forEach(function (el) {
            el.addEventListener('click', function () {
                const target = document.getElementById(el.dataset.target);
                if (!target) return;
                target.style.display = target.style.display === 'none' ? 'block' : 'none';
            });
        });

        /* ---- Drag-and-drop upload ---- */
        document.querySelectorAll('[data-drop]').forEach(function (wrap) {
            const inputId = wrap.getAttribute('data-drop');
            const input = document.getElementById(inputId);
            if (!input) return;
            wrap.addEventListener('click', function () { input.click(); });
            wrap.addEventListener('dragover', function (e) { e.preventDefault(); wrap.classList.add('dragover'); });
            wrap.addEventListener('dragleave', function () { wrap.classList.remove('dragover'); });
            wrap.addEventListener('drop', function (e) {
                e.preventDefault();
                wrap.classList.remove('dragover');
                if (e.dataTransfer.files && e.dataTransfer.files.length) input.files = e.dataTransfer.files;
            });
        });
    })();
    </script>
    @endsection
