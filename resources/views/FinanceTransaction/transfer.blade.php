@extends('layouts.form')

@section('content')
@php
    $rupiah = fn($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
@endphp

<style>
    .frm-table { border-collapse: separate; border-spacing: 1px; background: #A8A8A8; }
    .frm-table th { font: bold 8pt Tahoma, Arial; background: #E5F0FC; padding: 3px 8px; text-align: left; white-space: nowrap; }
    .frm-table td { font: 8pt Tahoma, Arial; background: #FFFFFF; padding: 3px 8px; white-space: nowrap; }
    .frm-table td label { display: block; margin-bottom: 1px; }
    input[type=date], input[type=text], select, textarea { font: 8pt Tahoma, Arial; padding: 2px 4px; border: 1px solid #b0b8c4; }
    button[type=submit] { font: 8pt Tahoma, Arial; padding: 3px 12px; }
</style>

<h3 style="font: bold 14pt Tahoma, Arial; margin: 8px 0 12px;">Transfer Antar Rekening</h3>

@if (session('success'))
    <p style="color:green; font: 8pt Tahoma, Arial;">{{ session('success') }}</p>
@endif

@if ($errors->any())
    <ul style="color:red; font: 8pt Tahoma, Arial;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('finance.transfer.store') }}">
    @csrf
    <table class="frm-table">
        <thead>
            <tr>
                <th colspan="2">Formulir Transfer Antar Rekening</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><label>Tanggal</label></td>
                <td><input type="date" name="trx_date" value="{{ old('trx_date', now()->toDateString()) }}" required></td>
            </tr>
            <tr>
                <td><label>Dari Rekening</label></td>
                <td>
                    <select name="from_account" required>
                        <option value="">-- Pilih --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc }}" {{ old('from_account') === $acc ? 'selected' : '' }}>{{ $acc }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Ke Rekening</label></td>
                <td>
                    <select name="to_account" required>
                        <option value="">-- Pilih --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc }}" {{ old('to_account') === $acc ? 'selected' : '' }}>{{ $acc }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Jumlah (Rp)</label></td>
                <td><input type="text" name="amount" value="{{ old('amount') }}" placeholder="Contoh: 500000" required></td>
            </tr>
            <tr>
                <td><label>Keterangan</label></td>
                <td><input type="text" name="description" value="{{ old('description') }}" style="width:280px;" placeholder="Opsional"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;">
                    <button type="submit">Simpan Transfer</button>
                    &nbsp;<a href="{{ route('finance.index') }}">[Batal]</a>
                </td>
            </tr>
        </tbody>
    </table>
</form>
@endsection
