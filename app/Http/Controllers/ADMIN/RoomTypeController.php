<?php


namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    /**
     * Liste tous les types de chambre
     */
    public function index()
    {
        $types = RoomType::all();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Détail d'un type de chambre
     */
    public function show($id)
    {
        $type = RoomType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type de chambre introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $type
        ]);
    }

    /**
     * Créer un nouveau type de chambre
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:room_types',
        ]);

        $type = RoomType::create($validated);

        return response()->json([
            'success' => true,
            'data' => $type
        ], 201);
    }

    /**
     * Mettre à jour un type de chambre
     */
    public function update(Request $request, $id)
    {
        $type = RoomType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type de chambre introuvable'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:room_types,slug,' . $id,
        ]);

        $type->update($validated);

        return response()->json([
            'success' => true,
            'data' => $type
        ]);
    }

    /**
     * Supprimer un type de chambre
     */
    public function destroy($id)
    {
        $type = RoomType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Type de chambre introuvable'
            ], 404);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type de chambre supprimé'
        ]);
    }
}
