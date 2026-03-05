<?php


namespace App\Http\Controllers\FRONT;


use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Retourne la liste de toutes les features
     * GET /api/front/features
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
     * Détails d'une feature spécifique (optionnel)
     * GET /api/front/features/{id}
     */
    public function show($id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feature
        ]);
    }
}
