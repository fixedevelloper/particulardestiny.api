<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    use HasFactory;

    public $timestamps = false; // inutile ici

    protected $fillable = [
        'room_id',
        'date',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
    ];

    /**
     * Relation avec Room
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope disponible
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
