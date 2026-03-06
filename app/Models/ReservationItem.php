<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    protected $fillable = [
        'reservation_id',
        'room_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'total_guests',
        'price_per_night',
        'nights',
        'total_price',
        'services'
    ];

    protected $casts = [
        'services' => 'array',
        'check_in' => 'date',
        'check_out' => 'date',
        'price_per_night' => 'float',
        'total_price' => 'float'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Boot : calcul automatique
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($item) {

            $item->total_guests = $item->adults + $item->children;

            $nights = \Carbon\Carbon::parse($item->check_in)
                ->diffInDays(\Carbon\Carbon::parse($item->check_out));

            $item->nights = $nights;

           // $item->total_price = $nights * $item->price_per_night;
        });
    }
}
