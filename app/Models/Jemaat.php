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

    public function scopeGroupByGender($query)
    {
        return $query->selectRaw('jenis_kelamin, COUNT(*) as total')
            ->groupBy('jenis_kelamin');
    }

    public function scopeGroupByAgeCategory($query)
    {
        return $query->selectRaw("
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 14 THEN 'Sekolah Minggu'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 15 AND 30 THEN 'Pemuda'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 60 THEN 'Dewasa'
                    ELSE 'Lansia'
                END as age_category,
                COUNT(*) as total
            ")
            ->groupBy('age_category');
    }
}
