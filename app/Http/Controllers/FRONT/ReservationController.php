<?php


namespace App\Http\Controllers\FRONT;


use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers;
use App\Http\Services\TransactService;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    protected $tranzakService;

    /**
     * PaymentController constructor.
     * @param $tranzakService
     */
    public function __construct(TransactService $tranzakService)
    {
        $this->tranzakService = $tranzakService;
    }

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
    public function store2(Request $request)
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'country' => 'required',
            'items' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {

            // 1️⃣ créer ou récupérer l'utilisateur
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name . ' ' . $request->surname,
                    'phone' => $request->phone,
                    'password' => bcrypt('password'),
                    'role' => 'customer'
                ]
            );

            // 2️⃣ créer la réservation
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'name' => $request->name ,
                'surname' =>  $request->surname,
                'phone' => $request->phone,
                'email' => $request->email,
                'country_id' => $request->country,
                'message' => $request->message,
                'subtotal' => 0,
                'total_price' => 0
            ]);

            $subtotal = 0;

            // 3️⃣ enregistrer les chambres
            foreach ($request->items as $item) {

                $checkIn = Carbon::parse($item['arrivalDate']);
                $checkOut = Carbon::parse($item['departureDate']);

                $nights = $checkIn->diffInDays($checkOut);

                $features = $item['features'] ?? [];

                $featuresTotal = collect($features)->sum(function ($f) {
                    return $f['price'] ?? 0;
                });

                $roomPrice = $item['pricePerNight'];

                $itemTotal = ($roomPrice + $featuresTotal) * $nights;

                $subtotal += $itemTotal;

                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'room_id' => $item['roomId'],
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'adults' => $item['adults'],
                    'children' => $item['children'],
                    'total_guests' => $item['adults'] + $item['children'],
                    'price_per_night' => $roomPrice,
                    'nights' => $nights,
                    'services' => $features
                ]);
            }

            // 4️⃣ calcul final
            $reservation->update([
                'subtotal' => $subtotal,
                'total_price' => $subtotal
            ]);

            // 5️⃣ référence paiement
            $reference = 'RES-' . Str::upper(Str::random(10));

            // 6️⃣ appel paiement
            $response = $this->tranzakService->makeColletion([
                'amount' => $reservation->total_price,
                'reference' => $reference,
                'success_url' => url('/payment/success'),
                'cancel_url' => url('/payment/cancel'),
                'callback_url' => url('/tranzak/webhook'),
                'description' => 'Paiement réservation suites'
            ]);

            // 7️⃣ créer paiement
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $reservation->total_price,
                'transaction_id' => $reference,
                'status' => 'pending',
                'provider_id'=>$response['data']['requestId'],
                'method'=>$response['data']['requestId']
            ]);

            DB::commit();

            return Helpers::success([
                'success' => true,
                'url' => $response['data']['links']['paymentAuthUrl']
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
