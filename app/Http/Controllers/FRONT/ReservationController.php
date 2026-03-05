<?php


namespace App\Http\Controllers\FRONT;


use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Liste des réservations pour l'utilisateur connecté
     * GET /api/front/reservations
     */
    public function index()
    {
        $user = Auth::user();

        $reservations = Reservation::with(['room', 'room.roomType', 'room.category'])
            ->where('user_id', $user->id)
            ->orderBy('check_in', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Créer une nouvelle réservation
     * POST /api/front/reservations
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::findOrFail($request->room_id);

        $nights = now()->diffInDays(now()->parse($request->check_in), now()->parse($request->check_out));

        $subtotal = $room->price * $nights;

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'total_guests' => $request->adults + ($request->children ?? 0),
            'price_per_night' => $room->price,
            'nights' => $nights,
            'subtotal' => $subtotal,
            'tax' => 0,
            'discount' => 0,
            'total_price' => $subtotal,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Voir une réservation spécifique
     * GET /api/front/reservations/{id}
     */
    public function show($id)
    {
        $user = Auth::user();
        $reservation = Reservation::with(['room', 'room.roomType', 'room.category'])
            ->where('user_id', $user->id)
            ->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Annuler une réservation
     * POST /api/front/reservations/{id}/cancel
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $reservation = Reservation::where('user_id', $user->id)->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled',
            'data' => $reservation
        ]);
    }
}
