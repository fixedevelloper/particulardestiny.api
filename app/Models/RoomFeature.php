<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomFeature extends Model
{
    protected $table = 'room_feature';

    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'feature_id',
    ];
}
