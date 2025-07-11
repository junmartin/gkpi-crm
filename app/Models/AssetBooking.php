<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetBooking extends Model
{
    protected $fillable = [
        'jemaat_id',
        'asset_id',
        'booking_date',
        'status',
        'created_by',
        'updated_by',
    ];

    public function jemaat()
    {
        return $this->belongsTo(Jemaat::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
