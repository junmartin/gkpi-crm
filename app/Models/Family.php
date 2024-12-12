<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $table = 'family';

    protected $fillable = ['family_name'];

    function jemaat(){
        return $this->hasMany(Jemaat::class);
    }

    
}
