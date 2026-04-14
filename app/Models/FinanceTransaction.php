<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    public const ENTRY_TYPE_REGULAR = 'regular';
    public const ENTRY_TYPE_INTERNAL_TRANSFER = 'internal_transfer';
    public const ENTRY_TYPE_OPENING_BALANCE = 'opening_balance';

    protected $fillable = [
        'trx_date',
        'transaction_type',
        'account',
        'budget_item_id',
        'amount',
        'entry_type',
        'transfer_group_key',
        'import_fingerprint',
        'description',
        'project',
        'attachment_path',
        'create_by',
        'update_by',
    ];

    protected $casts = [
        'trx_date' => 'date',
        'amount' => 'integer',
    ];

    public function budgetItem()
    {
        return $this->belongsTo(FinanceBudgetItem::class, 'budget_item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }

    public function isInternalTransfer(): bool
    {
        if ($this->entry_type === self::ENTRY_TYPE_INTERNAL_TRANSFER) {
            return true;
        }

        return strtolower(trim((string) optional($this->budgetItem)->name)) === '<< internal transfer >>';
    }

    public function isOpeningBalance(): bool
    {
        if ($this->entry_type === self::ENTRY_TYPE_OPENING_BALANCE) {
            return true;
        }

        return strtolower(trim((string) optional($this->budgetItem)->name)) === '<< opening balance >>';
    }
}
