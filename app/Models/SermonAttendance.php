<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sermon;

class SermonAttendance extends Model
{
    protected $fillable = ['sermon_id','jemaat_id','attendance'];

    function sermon(): BelongsTo {
        return $this->belongsTo(Sermon::class);
    }

}
