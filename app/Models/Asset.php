<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AssetType;
use App\Models\AssetPhoto;
use App\Models\AssetMaint;



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
        'acquired_date',
        'status',
        'location',
        'pic',
        'ownership',
        'create_by',
        'update_by'
    ];

    public function asset_type() {
        return $this->belongsTo(AssetType::class, 'type_id');
    }

    public function maintenance() {
        return $this->hasMany(AssetMaint::class);
    }

    public function asset_photo(){
        return $this->hasMany(AssetPhoto::class);
    }
}
