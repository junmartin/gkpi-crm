<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AssetMaintenance;

class AssetMaintenancePhoto extends Model
{
    use HasFactory;
    
    protected $table = 'assets_maintenance_picture';

    protected $fillable = [
        'asset_maint_id',
        'asset_photo'
    ];

    function assert() {
        return $this->belongsTo(AssetMaintenance::class,'asset_id');
    }
}
