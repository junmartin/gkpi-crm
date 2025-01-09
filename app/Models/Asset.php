<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AssetType;


class Asset extends Model
{
    protected $fillable = [
        'type_id',
        'name',
        'merk',
        'model',
        'serial_number',
        'tipe',
        'spec',
        'acquired_date'
    ];

    public function asset_type() {
        return $this->belongsTo(AssetType::class, 'type_id');
    }
}
