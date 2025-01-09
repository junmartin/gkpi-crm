<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Attendance;
use App\Models\Family;

class Jemaat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nick_name',
        'jenis_kelamin',
        'address',
        'birth_place',
        'birth_date',
        'mobile_no',
        'email',
        'marital_status',
        'marriage_date',
        'spouse_name',
        'member_type',
        'baptise_status',
        'previous_church',
        'remark',
        'pass_photo',
        'family_id',
        'role'
    ];

    function attendance() : HasMany {  
        return $this->hasMany(Attendance::class);
        
    }

    public function family() {
        return $this->belongsTo(Family::class, 'family_id');
    }
}
