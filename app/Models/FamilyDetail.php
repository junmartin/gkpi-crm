<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyDetail extends Model
{
    protected $table = 'family_detail';

    protected $fillable = [
        'jemaat_id',
        'family_id',
        'role'
    ];
}
