<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AssetType;
use App\Models\AssetPhoto;



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

    public function maintenance() {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function asset_photo(){
        return $this->hasMany(AssetPhoto::class);
    }
}
