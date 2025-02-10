<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Sermon;

class SermonAttendance extends Model
{
    protected $fillable = ['sermon_id','jemaat_id','attendance'];

    function sermon(): BelongsTo {
        return $this->belongsTo(Sermon::class);
    }
    
    function jemaat(): BelongsTo {
        return $this->belongsTo(Jemaat::class);
    }

}
