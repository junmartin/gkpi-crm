@extends('layouts.form')

@section('content')
@php
    $rupiah = fn($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
@endphp

@php
    $fmtExp = fn($v) => '<span style="color:#cc0000">-Rp ' . number_format((int) $v, 0, ',', '.') . '</span>';
    $fmtInc = fn($v) => '<span style="color:#0000cc">Rp '  . number_format((int) $v, 0, ',', '.') . '</span>';
    $fmtNeutral = fn($v) => '<span style="color:#444444">Rp ' . number_format((int) $v, 0, ',', '.') . '</span>';
@endphp

<style>
    .idx-wrap { font: 8pt Tahoma, Arial; }
    table.idx { border-collapse: separate; border-spacing: 1px; background: #A8A8A8; width: 100%; margin-bottom: 8px; }
    table.idx th { font: bold 8pt Tahoma, Arial; background: #E5F0FC; padding: 3px 6px; text-align: left; white-space: nowrap; }
    table.idx td { font: 8pt Tahoma, Arial; background: #FFFFFF; padding: 3px 6px; white-space: nowrap; vertical-align: top; }
    table.idx tbody tr:nth-child(even) td { background: #F7F7F7; }
    table.idx .num { text-align: right; }
</style>

<div class="idx-wrap">
<h3 style="font: bold 14pt Tahoma, Arial; margin: 8px 0 12px;">Keuangan</h3>

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<form method="GET" action="{{ route('finance.index') }}">
    <table class="idx" style="width:auto;">
        <thead>
            <tr><th colspan="6">Filter</th></tr>
        </thead>
        <tbody>
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
                    <button type="submit">Apply</button>
                    &nbsp;<a href="{{ route('finance.index') }}">[Reset]</a>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<p style="font:8pt Tahoma,Arial; margin:4px 0 6px;">
    <a href="{{ route('finance.create') }}">[+ Add New Transaction]</a> |
    <a href="{{ route('finance.transfer.create') }}">[Transfer Antar Rekening]</a> |
    <a href="{{ route('finance_budget_item.index') }}">[Master Budget]</a> |
    <a href="{{ route('finance.report', request()->query()) }}">[Open Report]</a>
</p>

<table class="idx">
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
                    @if($trx->isInternalTransfer() || $trx->isOpeningBalance())
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
                        -
                    @endif
                </td>
                <td><a href="{{ route('finance.edit', $trx->id) }}">[Edit]</a></td>
            </tr>
        @empty
            <tr><td colspan="8">No transactions found.</td></tr>
        @endforelse
    </tbody>
</table>

@if ($transactions->hasPages())
    <div style="margin-top:6px; font:8pt Tahoma,Arial; text-align:right;">
        @if ($transactions->onFirstPage())
            <span style="color:#888;">[Prev]</span>
        @else
            <a href="{{ $transactions->previousPageUrl() }}">[Prev]</a>
        @endif
        <span style="margin:0 8px;">Page {{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}</span>
        @if ($transactions->hasMorePages())
            <a href="{{ $transactions->nextPageUrl() }}">[Next]</a>
        @else
            <span style="color:#888;">[Next]</span>
        @endif
    </div>
@endif
</div>
@endsection
