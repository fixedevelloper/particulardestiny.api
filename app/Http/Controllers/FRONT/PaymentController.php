<?php


namespace App\Http\Controllers\FRONT;


use App\Http\Controllers\Controller;
use App\Http\Services\TransactService;
use App\Models\Category;
use Illuminate\Http\Request;

class PaymentController extends Controller
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


    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'data' => $category
        ], 201);
    }
}
