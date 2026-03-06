<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'country' => $this->country ? [
                'id' => $this->country->id,
                'name' => $this->country->name,
            ] : null,
            'details' => [
                'name' => $this->name,
                'surname' => $this->surname,
                'email' => $this->email,
                'phone' => $this->phone,
                'message' => $this->message,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'discount' => $this->discount,
                'total_price' => $this->total_price,
                'status' => $this->status,
                'payment_status' => $this->payment_status,
                'payment_method' => $this->payment_method,
                'payment_reference' => $this->payment_reference,
                'confirmed_at' => optional($this->confirmed_at)?->format('d/m/Y H:i'),
                'cancelled_at' => optional($this->cancelled_at)?->format('d/m/Y H:i'),
            ],
            'meta' => $this->meta,
            'items' => $this->items->map(fn($item) => [
        'id' => $item->id,
        'room_name' => $item->room_name ?? null,
        'check_in' => $item->check_in?->format('d/m/Y'),
                'check_out' => $item->check_out?->format('d/m/Y'),
                'price' => $item->total_price,
                'quantity' => $item->total_guests,
        'adults'=>$item->adults,
        'children'=>$item->children,
            ]),
            'timestamps' => [
        'created_at' => $this->created_at->format('d/m/Y H:i'),
        'updated_at' => $this->updated_at->format('d/m/Y H:i'),
    ],
        ];
    }
}
