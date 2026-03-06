<?php


namespace App\Http\Controllers\FRONT;


use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Category;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Liste des chambres avec filtres et pagination
     * GET /api/front/rooms
     */
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'category', 'features', 'images','featuredImage']);

        // Filtrer par catégorie
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request){
                $q->where('slug', $request->category);
            });
        }

        // Filtrer par type
        if ($request->has('room_type')) {
            $query->whereHas('roomType', function($q) use ($request){
                $q->where('slug', $request->room_type);
            });
        }

        // Recherche par titre
        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Pagination
        $rooms = $query->paginate(10);

        return Helpers::success(RoomResource::collection($rooms));
    }
    public function roomSearch(Request $request)
    {
        $location = $request->input('location');
        $arrivalDate = $request->input('arrivalDate');
        $departureDate = $request->input('departureDate');
        $guests = $request->input('guests');

        logger($request->all());
        // Validation
        $request->validate([
            'location' => 'required|string',
            'arrivalDate' => 'required|date',
            'departureDate' => 'required|date|after_or_equal:arrivalDate',
            'guests' => 'nullable|integer',
        ]);

        // Générer la liste des dates demandées
        $dates = [];
        $start = strtotime($arrivalDate);
        $end = strtotime($departureDate);
        for ($date = $start; $date <= $end; $date = strtotime('+1 day', $date)) {
            $dates[] = date('Y-m-d', $date);
        }

        // Récupérer les chambres disponibles
        $rooms = Room::where('capacity', '>=', $guests ?? 1)
            ->whereHas('availabilities', function ($query) use ($dates) {
                $query->whereIn('date', $dates)
                    ->where('is_available', true);
            }, '=', count($dates)) // s'assurer que toutes les dates sont disponibles
            ->get();

        $rws=Room::with(['roomType', 'category', 'features', 'images','featuredImage']);
        return Helpers::success(RoomResource::collection($rws->get()));
    }
    /**
     * Détails d’une chambre
     * GET /api/front/rooms/{slug}
     */
    public function show($slug)
    {
        $room = Room::with(['roomType', 'category', 'features', 'images','featuredImage'])
            ->where('slug', $slug)
            ->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        return Helpers::success(new RoomResource($room));
    }

    /**
     * Chambres populaires ou récentes
     * GET /api/front/rooms/recent
     */
    public function recent()
    {
        $rooms = Room::with(['roomType', 'category'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }
}
