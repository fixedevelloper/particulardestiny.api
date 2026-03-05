<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'capacity',
        'size',
        'image_id',
        'category_id',
        'room_type_id',
    ];

    /**
     * Auto-génération du slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (empty($room->slug)) {
                $room->slug = Str::slug($room->title);
            }
        });
    }

    /**
     * Relation avec le type de chambre
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec les réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    /**
     * Images de la chambre
     */
    public function featuredImage()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function images()
    {
        return $this->belongsToMany(
            Image::class,
            'room_images'
        );
    }
    /**
     * Features (WiFi, Clim, TV…)
     */
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'room_feature');
    }
    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class);
    }
}
