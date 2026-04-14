@extends('layouts.form')

@section('content')
<h3>Add Budget Item</h3>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('finance_budget_item.store') }}" method="POST">
    @csrf

    <table border="1" width="100%" style="border-collapse: collapse;">
        <tr>
            <td style="width: 220px; padding:6px;">Budget Item</td>
            <td style="padding:6px;"><input type="text" name="name" value="{{ old('name') }}" required maxlength="150" style="width: 95%;"></td>
        </tr>
        <tr>
            <td style="padding:6px;">Active</td>
            <td style="padding:6px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Routine Monthly Expense</td>
            <td style="padding:6px;">
                <input type="hidden" name="is_routine" value="0">
                <input type="checkbox" name="is_routine" id="is_routine" value="1" {{ old('is_routine') ? 'checked' : '' }}>
                <small>Enable if this item usually appears every month.</small>
            </td>
        </tr>
        <tr>
            <td style="padding:6px;">Routine Day (1-31)</td>
            <td style="padding:6px;"><input type="number" name="routine_day_of_month" min="1" max="31" value="{{ old('routine_day_of_month') }}"></td>
        </tr>
        <tr>
            <td style="padding:6px;">Routine Amount</td>
            <td style="padding:6px;"><input type="text" id="routine_amount" name="routine_amount" value="{{ old('routine_amount') }}" placeholder="Rp 0"></td>
        </tr>
        <tr>
            <td style="padding:6px;">Notes</td>
            <td style="padding:6px;"><textarea name="notes" rows="3" style="width:95%;">{{ old('notes') }}</textarea></td>
        </tr>
    </table>

    <div style="margin-top: 12px; text-align: right;">
        <input type="submit" value="Save">
    </div>
</form>
@endsection

@section('script')
<script>
    (function () {
        const input = document.getElementById('routine_amount');
        if (!input) return;

        const format = (raw) => {
            const digits = String(raw || '').replace(/\D/g, '');
            if (!digits) return '';
            return 'Rp ' + Number(digits).toLocaleString('id-ID');
        };

        input.addEventListener('input', function () {
            input.value = format(input.value);
        });

        input.value = format(input.value);
    })();
</script>
@endsection
