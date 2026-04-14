<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceBudgetItem extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'is_routine',
        'routine_day_of_month',
        'routine_amount',
        'notes',
        'create_by',
        'update_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_routine' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'budget_item_id');
    }
}
