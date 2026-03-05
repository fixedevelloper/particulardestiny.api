<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relation avec les chambres
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Relation avec les réservations (optionnel via rooms)
     */
    public function reservations()
    {
        return $this->hasManyThrough(
            Reservation::class,
            Room::class
        );
    }
}
