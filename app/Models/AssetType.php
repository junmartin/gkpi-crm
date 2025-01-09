<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    protected $table = 'assets_type';

    protected $fillable = [
        'name',
    ];
}
