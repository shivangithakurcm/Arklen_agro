<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(15);
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name'      => 'required|string|max:255',
            'product_price'     => 'required|numeric|min:0',
            'direct_commission' => 'required|numeric|min:0|max:100',
            'new_joinee'        => 'required|numeric|min:0|max:100',
            'level_1'           => 'required|numeric|min:0|max:100',
            'level_2'           => 'required|numeric|min:0|max:100',
            'product_image'     => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'product_name', 'product_price',
            'direct_commission', 'new_joinee', 'level_1', 'level_2'
        ]);

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name'      => 'required|string|max:255',
            'product_price'     => 'required|numeric|min:0',
            'direct_commission' => 'required|numeric|min:0|max:100',
            'new_joinee'        => 'required|numeric|min:0|max:100',
            'level_1'           => 'required|numeric|min:0|max:100',
            'level_2'           => 'required|numeric|min:0|max:100',
            'product_image'     => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'product_name', 'product_price',
            'direct_commission', 'new_joinee', 'level_1', 'level_2'
        ]);

        if ($request->hasFile('product_image')) {
            if ($product->product_image) {
                Storage::disk('public')->delete($product->product_image);
            }
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->product_image) {
            Storage::disk('public')->delete($product->product_image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }
}