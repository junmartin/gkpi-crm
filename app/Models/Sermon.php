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

    public function scopeWithAttendance($query, $limit)
    {
        return $query->selectRaw('sermons.id, sermons.sermon_date, COUNT(sermon_attendances.jemaat_id) as total_attendance')
            ->leftJoin('sermon_attendances', 'sermon_attendances.sermon_id', '=', 'sermons.id')
            ->groupBy('sermons.id', 'sermons.sermon_date')
            ->orderBy('sermons.sermon_date', 'desc')
            ->take($limit);
    }

}
