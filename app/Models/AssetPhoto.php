<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Asset;

class AssetPhoto extends Model
{    
    use HasFactory;
    
    protected $table = 'assets_picture';

    protected $fillable = [
        'asset_id',
        'asset_photo'
    ];

    function assert() {
        return $this->belongsTo(Asset::class,'asset_id');
    }
}
