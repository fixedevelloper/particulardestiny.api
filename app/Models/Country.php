<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Country extends Model
{
    use HasFactory;

    // Champs autorisés en assignation de masse
    protected $fillable = [
        'name',
        'slug',
    ];

}
