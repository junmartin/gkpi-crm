<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Asset;

class AssetMaint extends Model
{
    protected $table = 'assets_maintenance';

    protected $fillable = [
        'asset_id',
        'maint_type',
        'maint_date',
        'next_maint_date',
        'maint_title',
        'desc',
        'maint_fee',
        'remark',
    ];

    public function asset() {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
