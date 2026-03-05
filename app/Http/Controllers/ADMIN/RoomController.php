<?php


namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    /**
     * Liste toutes les chambres
     */
    public function index()
    {
        $rooms = Room::with([
            'category',
            'roomType',
            'featuredImage',
            'images',
            'features',
            'availabilities'
        ])
            ->withCount('reservations')
            ->paginate(10);

        return RoomResource::collection($rooms);
    }

    /**
     * Détail d'une chambre
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $room = Room::with(['category', 'roomType', 'features', 'images'])->find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Chambre introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new RoomResource($room)
        ]);
    }

    /**
     * Créer une nouvelle chambre
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        logger($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'capacity' => 'required|integer',
            'size' => 'nullable|integer',
            'category_id' => 'required|exists:categories,id',
            'room_type_id' => 'required|exists:room_types,id',
            'image_id' => 'required|exists:images,id',
        ]);
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        $room = Room::create($validated);
        if ($request->filled('gallery')) {
            $room->images()->sync($request->gallery ?? []);
        }
        if ($request->has('features')) {
            $room->features()->sync($request->features);
        }
        return response()->json([
            'success' => true,
            'data' => $room
        ], 201);
    }

    /**
     * Mettre à jour une chambre
     */
    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Chambre introuvable'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:rooms,slug,' . $id,
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'capacity' => 'sometimes|integer',
            'size' => 'nullable|integer',
            'category_id' => 'sometimes|exists:categories,id',
            'room_type_id' => 'sometimes|exists:room_types,id',
            'image_id' => 'required|exists:images,id',
        ]);

        $room->update($validated);
        if ($request->filled('gallery')) {
            $room->images()->sync($request->gallery ?? []);
        }
        if ($request->has('features')) {
            $room->features()->sync($request->features);
        }
        return response()->json([
            'success' => true,
            'data' => $room
        ]);
    }

    /**
     * Supprimer une chambre
     */
    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Chambre introuvable'
            ], 404);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chambre supprimée'
        ]);
    }
}
