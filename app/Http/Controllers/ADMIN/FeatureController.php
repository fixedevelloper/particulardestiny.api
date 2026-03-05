<?php


namespace App\Http\Controllers\ADMIN;


use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Liste toutes les features
     */
    public function index()
    {
        $features = Feature::all();

        return response()->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Détail d'une feature
     */
    public function show($id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feature
        ]);
    }

    /**
     * Créer une nouvelle feature
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $feature = Feature::create($validated);

        return response()->json([
            'success' => true,
            'data' => $feature
        ], 201);
    }

    /**
     * Mettre à jour une feature
     */
    public function update(Request $request, $id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature introuvable'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $feature->update($validated);

        return response()->json([
            'success' => true,
            'data' => $feature
        ]);
    }

    /**
     * Supprimer une feature
     */
    public function destroy($id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature introuvable'
            ], 404);
        }

        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature supprimée'
        ]);
    }
}
