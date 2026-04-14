@extends('layouts.form')

@section('content')
@php
    $rupiah = fn($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
@endphp

<h3>Master Budget Item</h3>

@if (session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<a href="{{ route('finance_budget_item.create') }}">[+ Add Budget Item]</a>

<table border="1" width="100%" style="margin-top: 8px; border-collapse: collapse;">
    <thead>
        <tr style="background: #efefef;">
            <th style="text-align:left; padding:6px;">Budget Item</th>
            <th style="text-align:left; padding:6px;">Status</th>
            <th style="text-align:left; padding:6px;">Routine</th>
            <th style="text-align:left; padding:6px;">Routine Schedule</th>
            <th style="text-align:left; padding:6px;">Routine Amount</th>
            <th style="text-align:left; padding:6px;">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
            <tr>
                <td style="padding:6px;">{{ $item->name }}</td>
                <td style="padding:6px;">{{ $item->is_active ? 'Active' : 'Inactive' }}</td>
                <td style="padding:6px;">{{ $item->is_routine ? 'Yes' : 'No' }}</td>
                <td style="padding:6px;">{{ $item->routine_day_of_month ? 'Day ' . $item->routine_day_of_month : '-' }}</td>
                <td style="padding:6px;">{{ $item->routine_amount ? $rupiah($item->routine_amount) : '-' }}</td>
                <td style="padding:6px;">
                    <a href="{{ route('finance_budget_item.edit', $item->id) }}">[ Edit ]</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="padding:6px;">Belum ada budget item.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
