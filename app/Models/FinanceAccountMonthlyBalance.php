<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceAccountMonthlyBalance extends Model
{
    protected $fillable = [
        'account',
        'period_month',
        'opening_balance',
        'inflow_amount',
        'outflow_amount',
        'closing_balance',
        'create_by',
        'update_by',
    ];

    protected $casts = [
        'period_month' => 'date',
        'opening_balance' => 'integer',
        'inflow_amount' => 'integer',
        'outflow_amount' => 'integer',
        'closing_balance' => 'integer',
    ];
}
