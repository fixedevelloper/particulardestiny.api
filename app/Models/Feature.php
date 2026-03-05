<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_id',
    ];

    /**
     * Relation MANY TO MANY avec Room
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_feature');
    }
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
