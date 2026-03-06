<?php


namespace App\Http\Controllers\ADMIN;


use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationDetailResource;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Liste toutes les réservations
     */
    public function index()
    {
        $reservations = Reservation::with(['user','items'])->get();

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Détail d'une réservation
     */
    public function show($id)
    {
        // Charger les relations pour éviter N+1
        $reservation = Reservation::with(['user', 'country', 'items'])->findOrFail($id);

        return new ReservationDetailResource($reservation);
    }

    /**
     * Créer une nouvelle réservation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'price_per_night' => 'required|numeric|min:0',
            'nights' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'meta' => 'nullable|json',
        ]);

        $validated['total_guests'] = $validated['adults'] + ($validated['children'] ?? 0);

        $reservation = Reservation::create($validated);

        return response()->json([
            'success' => true,
            'data' => $reservation
        ], 201);
    }

    /**
     * Mettre à jour une réservation
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation introuvable'
            ], 404);
        }

        $validated = $request->validate([
            'check_in' => 'sometimes|date',
            'check_out' => 'sometimes|date|after_or_equal:check_in',
            'adults' => 'sometimes|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'price_per_night' => 'sometimes|numeric|min:0',
            'nights' => 'sometimes|integer|min:1',
            'subtotal' => 'sometimes|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_price' => 'sometimes|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'meta' => 'nullable|json',
        ]);

        if (isset($validated['adults']) || isset($validated['children'])) {
            $adults = $validated['adults'] ?? $reservation->adults;
            $children = $validated['children'] ?? $reservation->children;
            $validated['total_guests'] = $adults + $children;
        }

        $reservation->update($validated);

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Supprimer une réservation
     */
    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation introuvable'
            ], 404);
        }

        $reservation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réservation supprimée'
        ]);
    }
}
