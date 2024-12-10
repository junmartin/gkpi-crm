<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jemaat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
        'pass_photo'
    ];
}
