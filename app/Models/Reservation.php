<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'country_id',
        'name',
        'surname',
        'email',
        'phone',
        'message',
        'subtotal',
        'tax',
        'discount',
        'total_price',
        'status',
        'payment_status',
        'payment_reference',
        'payment_method',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
