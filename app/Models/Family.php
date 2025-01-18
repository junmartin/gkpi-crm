<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
 

class Family extends Model
{
    protected $table = 'family';

    protected $fillable = ['family_name'];

    function jemaat(){
        return $this->hasMany(Jemaat::class);
    }

    function familyDetails(){
        return $this->hasMany(FamilyDetail::class);
    }    

    function people(): BelongsToMany
    {
        return $this->belongsToMany(Jemaat::class,'family_detail')->withPivot('role')->orderByPivot('role','desc');
    }

    public function countPeople()
    {
        return $this->jemaat()->count();
    }

}
