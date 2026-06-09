<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->search) {
            $query->where('product_name', 'like', '%'.$request->search.'%');
        }
        return response()->json(['success' => true, 'data' => $query->latest()->paginate(15)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name'      => 'required|string|max:255',
            'product_price'     => 'required|numeric|min:0',
            'business_value'    => 'required|numeric|min:0',
            'direct_commission' => 'required|numeric|min:0|max:100',
            'new_joinee'        => 'required|numeric|min:0|max:100',
            'level_1'           => 'required|numeric|min:0|max:100',
            'level_2'           => 'required|numeric|min:0|max:100',
        ]);

        $product = Product::create($validated);
        return response()->json(['success' => true, 'message' => 'Product created.', 'data' => $product], 201);
    }

    public function show(Product $product)
    {
        return response()->json(['success' => true, 'data' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name'      => 'required|string|max:255',
            'product_price'     => 'required|numeric|min:0',
            'business_value'    => 'required|numeric|min:0',
            'direct_commission' => 'required|numeric|min:0|max:100',
            'new_joinee'        => 'required|numeric|min:0|max:100',
            'level_1'           => 'required|numeric|min:0|max:100',
            'level_2'           => 'required|numeric|min:0|max:100',
        ]);

        $product->update($validated);
        return response()->json(['success' => true, 'message' => 'Product updated.', 'data' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted.']);
    }
}