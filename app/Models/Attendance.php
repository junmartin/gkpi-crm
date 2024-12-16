<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $fillable = ['sermon_date','ibadah_id','ibadah_name','jemaat_id','attendance'];

    function people()
    {
        return $this->belongsTo(Jemaat::class,'jemaat_id');
    }

    function ibadah()
    {
        return $this->belongsTo(Ibadah::class,'ibadah_id');
    }

    public function scopeAttendanceSummary($query)
    {
        return $query
            ->select('sermon_date', 'ibadah_id', 'ibadah_name')
            ->where('attendance', 1) // Only count rows with attendance = 1
            ->groupBy('sermon_date', 'ibadah_id', 'ibadah_name')
            ->selectRaw('COUNT(jemaat_id) as total_attendees');
    }

}
