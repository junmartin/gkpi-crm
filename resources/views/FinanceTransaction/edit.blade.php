@extends('layouts.form')

@section('content')
@php
    $displayAmount = 'Rp ' . number_format((int) $transaction->amount, 0, ',', '.');
@endphp

<h3>Edit Transaksi Keuangan</h3>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('finance.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <table border="1" width="100%" style="border-collapse: collapse;">
        <tr>
            <td style="width:220px; padding:6px;">Tanggal</td>
            <td style="padding:6px;"><input type="date" name="trx_date" value="{{ old('trx_date', $transaction->trx_date->toDateString()) }}" required></td>
        </tr>
        <tr>
            <td style="padding:6px;">Jenis Transaksi</td>
            <td style="padding:6px;">
                <select name="transaction_type" required>
                    <option value="expense" {{ old('transaction_type', $transaction->transaction_type) === 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="income" {{ old('transaction_type', $transaction->transaction_type) === 'income' ? 'selected' : '' }}>Income</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Account</td>
            <td style="padding:6px;">
                <select name="account" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc }}" {{ old('account', $transaction->account) === $acc ? 'selected' : '' }}>{{ $acc }}</option>
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
                        <option value="{{ $item->id }}" {{ (string) old('budget_item_id', $transaction->budget_item_id) === (string) $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Jumlah</td>
            <td style="padding:6px;">
                <input type="text" id="amount" name="amount" value="{{ old('amount', $displayAmount) }}" placeholder="Rp 0" required>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Description</td>
            <td style="padding:6px;"><textarea name="description" rows="3" style="width:95%;">{{ old('description', $transaction->description) }}</textarea></td>
        </tr>
        <tr>
            <td style="padding:6px;">Project</td>
            <td style="padding:6px;"><input type="text" name="project" value="{{ old('project', $transaction->project) }}" maxlength="255" style="width:95%;"></td>
        </tr>
        <tr>
            <td style="padding:6px;">Attachment (optional)</td>
            <td style="padding:6px;">
                @php
                    $attachments = [];
                    if ($transaction->attachment_path) {
                        if (str_starts_with($transaction->attachment_path, '[')) {
                            $attachments = json_decode($transaction->attachment_path, true) ?? [];
                        } else {
                            $attachments = [$transaction->attachment_path];
                        }
                    }
                @endphp
                @if($transaction->attachment_path)
                    <a href="{{ route('finance.attachment.view', ['finance' => $transaction->id, 'i' => 0]) }}" target="_blank">[ View Existing Attachment ]</a><br>
                @endif

                @if(!empty($attachments))
                    <div style="display:flex; flex-wrap:wrap; gap:8px; margin:8px 0;">
                        @foreach($attachments as $idx => $path)
                            @php
                                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
                            @endphp
                            <div style="border:1px solid #ccc; padding:6px; width:140px; background:#fafafa;">
                                <div style="height:90px; display:flex; align-items:center; justify-content:center; background:#fff; border:1px solid #ddd; margin-bottom:6px; overflow:hidden;">
                                    @if($isImage)
                                        <img src="{{ asset('storage/' . $path) }}" alt="Attachment {{ $idx + 1 }}" style="max-width:100%; max-height:100%; object-fit:cover;">
                                    @else
                                        <span style="font: bold 8pt Tahoma, Arial; color:#666;">{{ strtoupper($ext ?: 'FILE') }}</span>
                                    @endif
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:4px;">
                                    <a href="{{ route('finance.attachment.view', ['finance' => $transaction->id, 'i' => $idx]) }}" target="_blank">[View]</a>
                                    <button
                                        type="submit"
                                        form="delete-attachment-{{ $transaction->id }}-{{ $idx }}"
                                        onclick="return confirm('Delete this attachment?');"
                                        style="font:8pt Tahoma, Arial;"
                                    >Delete</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <input type="file" name="attachment[]" accept="application/pdf,image/*" capture="environment" multiple>
                <small>Accepted: PDF, JPG, JPEG, PNG, WEBP (max 5MB each)</small>
            </td>
        </tr>
    </table>

    <div style="text-align:right; margin-top:12px;">
        <input type="submit" value="Update">
    </div>
</form>

@if(!empty($attachments))
    @foreach($attachments as $idx => $path)
        <form
            id="delete-attachment-{{ $transaction->id }}-{{ $idx }}"
            method="POST"
            action="{{ route('finance.attachment.delete', ['finance' => $transaction->id, 'index' => $idx]) }}"
            style="display:none;"
        >
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif
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
