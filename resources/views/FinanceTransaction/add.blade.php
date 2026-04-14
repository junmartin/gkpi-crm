@extends('layouts.form')

@section('content')
<h3>Input Keuangan (Pemasukan / Pengeluaran)</h3>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($budgetItems->isEmpty())
    <p style="color: #b94a48;">
        Belum ada master budget item. Silakan isi terlebih dahulu di
        <a href="{{ route('finance_budget_item.index') }}">Master Budget</a>.
    </p>
@endif

<form action="{{ route('finance.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <table border="1" width="100%" style="border-collapse: collapse;">
        <tr>
            <td style="width:220px; padding:6px;">Tanggal</td>
            <td style="padding:6px;"><input type="date" name="trx_date" value="{{ old('trx_date', now()->toDateString()) }}" required></td>
        </tr>
        <tr>
            <td style="padding:6px;">Jenis Transaksi</td>
            <td style="padding:6px;">
                <select name="transaction_type" required>
                    <option value="expense" {{ old('transaction_type', 'expense') === 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="income" {{ old('transaction_type') === 'income' ? 'selected' : '' }}>Income</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Account</td>
            <td style="padding:6px;">
                <select name="account" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc }}" {{ old('account', 'Kas Jun') === $acc ? 'selected' : '' }}>{{ $acc }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Budget Item (mandatory)</td>
            <td style="padding:6px;">
                <select name="budget_item_id" required>
                    <option value="">-- pilih budget item --</option>
                    @foreach($budgetItems as $item)
                        <option value="{{ $item->id }}" {{ (string) old('budget_item_id') === (string) $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Jumlah</td>
            <td style="padding:6px;">
                <input type="text" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Rp 0" required>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Description</td>
            <td style="padding:6px;"><textarea name="description" rows="3" style="width:95%;">{{ old('description') }}</textarea></td>
        </tr>
        <tr>
            <td style="padding:6px;">Project</td>
            <td style="padding:6px;"><input type="text" name="project" value="{{ old('project') }}" maxlength="255" style="width:95%;"></td>
        </tr>
        <tr>
            <td style="padding:6px;">Attachment (optional)</td>
            <td style="padding:6px;">
                <input type="file" name="attachment" accept="application/pdf,image/*" capture="environment">
                <small>Accepted: PDF, JPG, JPEG, PNG, WEBP (max 5MB)</small>
            </td>
        </tr>
    </table>

    <div style="text-align:right; margin-top:12px;">
        <input type="submit" value="Save">
    </div>
</form>
@endsection

@section('script')
<script>
    (function () {
        const amountInput = document.getElementById('amount');
        if (!amountInput) return;

        const formatRupiah = (value) => {
            const digits = String(value || '').replace(/\D/g, '');
            if (!digits) return '';
            return 'Rp ' + Number(digits).toLocaleString('id-ID');
        };

        amountInput.addEventListener('input', function () {
            amountInput.value = formatRupiah(amountInput.value);
        });

        amountInput.value = formatRupiah(amountInput.value);
    })();
</script>
@endsection
