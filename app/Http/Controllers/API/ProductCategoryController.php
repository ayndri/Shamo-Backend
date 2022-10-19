<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function all (Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $show_product = $request->input('show_product');

        if ($id)
        {
            $category = ProductCategory::with(['products'])->find($id);

            if ($category)
            {
                return ResponseFormatter::success(
                    $category,
                    'Data successfully display'
                );
            }

            else 
            {
                return ResponseFormatter::error(
                    $category,
                    'Data failed to be display',
                    404
                );
            }
        }

        $category = ProductCategory::query();

        if ($name)
        {
            $category->where('name', 'LIKE', '%' . $name . '%');
        }

        if ($show_product)
        {
            $category->with('products');
        }

        return ResponseFormatter::success(
            $category->paginate($limit),
            'Data successfully display'
        );

    }

    public function create (Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255']
            ]);

            ProductCategory::create([
                'name' => $request->name
            ]);

            $productCategory = ProductCategory::where('name', $request->name)->first();

            return ResponseFormatter::success([
                'product category' => $productCategory,  
            ], 'Product successfully Added');

        } catch (Exception $error) {
            return ResponseFormatter::error(
                $productCategory,
                'Data failed to be added',
                404
            );
        }
    }
}
