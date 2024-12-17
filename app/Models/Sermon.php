<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\SermonAttendance;

class Sermon extends Model
{
    protected $fillable = ['sermon_date','ibadah_id','ibadah_name'];

    // function attendance() : HasMany {
    //     return $this->hasMany(SermonAttendance::class);
    // }

    function attendee() {
        return $this->hasMany(SermonAttendance::class);
    }

}
