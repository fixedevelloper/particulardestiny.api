<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'total_guests',
        'price_per_night',
        'nights',
        'subtotal',
        'tax',
        'discount',
        'total_price',
        'status',
        'payment_status',
        'meta',
        'confirmed_at',
        'cancelled_at',
    ];

    /**
     * Casts (TRÈS IMPORTANT 🔥)
     */
    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'meta' => 'array',
        'price_per_night' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * RELATIONS
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * ACCESSORS
     */

    // Durée formatée
    public function getDurationAttribute()
    {
        return $this->nights . ' nuit(s)';
    }

    // Statut lisible
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
        'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'checked_in' => 'En cours',
            'checked_out' => 'Terminée',
            'cancelled' => 'Annulée',
            default => 'Inconnu',
        };
    }

    /**
     * SCOPES (ULTRA UTILES 🔥)
     */

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in', Carbon::today());
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('check_in', [$start, $end])
                ->orWhereBetween('check_out', [$start, $end]);
        });
    }

    /**
     * HELPERS MÉTIER 🔥
     */

    public function calculateTotals()
    {
        $this->nights = Carbon::parse($this->check_in)
            ->diffInDays(Carbon::parse($this->check_out));

        $this->total_guests = $this->adults + $this->children;

        $this->subtotal = $this->price_per_night * $this->nights;

        $this->total_price = $this->subtotal + $this->tax - $this->discount;

        return $this;
    }

    public function confirm()
    {
        $this->status = 'confirmed';
        $this->confirmed_at = now();
        $this->save();
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->save();
    }

    /**
     * Vérifier si la réservation chevauche une autre (IMPORTANT 🔥)
     */
    public static function isRoomAvailable($roomId, $checkIn, $checkOut)
    {
        return !self::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();
    }
}
