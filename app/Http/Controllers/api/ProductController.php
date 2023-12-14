<?php

namespace App\Http\Controllers\api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'data' => $products
        ], 200);
    }
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        $data['status'] = $request->status ?: 0;

        $image = $request->file('image');
        $image_name = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('public/product', $image_name);
        $data['image'] = $path;

        Product::create($data);

        return response()->json([
            'data' => $data,
            'message' => "Product created successfully"
        ], 201);
    }
    public function update(Product $product, ProductUpdateRequest $request)
    {
        if ($product) 
        {
            $data = $request->validated();

            if ($request->hasFile('image'))
             {
                Storage::delete($product->image);
                $file = $request->file('image');
                $img_name = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/product', $img_name);
                $data['image'] = $path;
            }

            $data['status'] = $request->status ?: $product->status;

            return response()->json([
                'data' => $data
            ], 200);
        }
    }

    public function show(Product $product)
    {
        return response()->json([
            'data' => $product,
        ], 200);
    }

    public function destroy(Product $product)
    {
        Storage::delete($product->image);
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
