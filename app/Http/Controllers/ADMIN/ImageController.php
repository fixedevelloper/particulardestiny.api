<?php


namespace App\Http\Controllers\ADMIN;


use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 20);

        $images = Image::latest()->paginate($perPage);

        return ImageResource::collection($images);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120',
            'name' => 'required|string|max:255',
            'alt'  => 'nullable|string|max:255',
        ]);

        $image = Image::create([
            'name' => $request->name,
            'alt'  => $request->alt,
            'src'  => '', // sera rempli par media
        ]);

        $image->addMediaFromRequest('file')
            ->toMediaCollection('default');

        return response()->json([
            'data' => new ImageResource($image)
        ], 201);
    }

    public function show($id)
    {
        $image = Image::findOrFail($id);

        return new ImageResource($image);
    }

    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);

        $request->validate([
            'file' => 'nullable|image|max:5120',
            'name' => 'required|string|max:255',
            'alt'  => 'nullable|string|max:255',
        ]);

        $image->update([
            'name' => $request->name,
            'alt'  => $request->alt,
        ]);

        if ($request->hasFile('file')) {

            // supprimer ancienne image
            $image->clearMediaCollection('default');

            // ajouter nouvelle
            $image->addMediaFromRequest('file')
                ->toMediaCollection('default');
        }

        return response()->json([
            'data' => new ImageResource($image)
        ]);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        $image->clearMediaCollection('default');

        $image->delete();

        return response()->json([
            'message' => 'Image supprimée'
        ]);
    }
}
