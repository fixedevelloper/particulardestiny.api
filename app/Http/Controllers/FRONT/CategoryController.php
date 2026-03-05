<?php


namespace App\Http\Controllers\FRONT;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Retourne la liste des catégories de chambres
     * GET /api/front/categories
     */
    public function index()
    {
        // On peut aussi charger les rooms count si nécessaire
        $categories = Category::withCount('rooms')->get();

        return Helpers::success($categories);
    }

    /**
     * Détails d'une catégorie (optionnel)
     * GET /api/front/categories/{id}
     */
    public function show($id)
    {
        $category = Category::with('rooms')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }
}
