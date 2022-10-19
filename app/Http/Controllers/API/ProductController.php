<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if ($id)
        {
            $product = Product::with(['category', 'galleries'])->find($id);

            if ($product)
            {
                return ResponseFormatter::success(
                    $product,
                    'Data successfully display'
                );
            }

            else 
            {
                return ResponseFormatter::error(
                    $product,
                    'Data failed to be display',
                    404
                );
            }
        }

        $product = Product::with(['category', 'galleries']);

        if ($name)
        {
            $product->where('name', 'LIKE', '%' . $name . '%');
        }

        if ($description)
        {
            $product->where('description', 'LIKE', '%' . $description . '%');
        }

        if ($tags)
        {
            $product->where('tags', '>=' . $tags . '%');
        }

        if ($price_from)
        {
            $product->where('price', '>=' , 'price_from');
        }

        if ($price_to)
        {
            $product->where('price', '<=' , 'price_to');
        }

        if ($categories)
        {
            $product->where('categories', 'categories');
        }

        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data successfully display'
        );

        

    }

    public function create(Request $request)
    {
        try {
            
            $request->validate([
                'name' => ['required', 'max:255', 'string'],
                'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'description' => ['nullable', 'string'],
                'tags' => ['nullable', 'string'],
                'categories_id' => ['required'],
            ]);

            Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'tags' => $request->tags,
                'categories_id' => $request->categories_id,
            ]);

            $product = Product::where('name', $request->name)->first();

            return ResponseFormatter::success([
                'product' => $product,  
            ], 'Product successfully Added');

        } catch (Exception $error) {
            return ResponseFormatter::error(
                $product,
                'Data failed to be added',
                404
            );
        }
    }
}
