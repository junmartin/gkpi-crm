<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Attendance;

class Ibadah extends Model
{

    protected $fillable = [
        'ibadah_name','remark'
    ];

    function attendance() : HasMany {
        return $this->hasMany(Attendance::class);
    }
}
