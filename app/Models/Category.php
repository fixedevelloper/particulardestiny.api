<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    // Champs autorisés en assignation de masse
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Définir automatiquement le slug à partir du nom
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relation : une catégorie a plusieurs chambres
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
