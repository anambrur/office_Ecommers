<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Common\Category;
use App\Model\Common\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    public function getCategory()
    {
        try {
            $categories = Category::where("parent_id", 0)
                ->orderBy("id", "desc")
                ->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'error' => 'No categories found.'
                ], 404);
            }

            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Fetch products by category
    public function categoryWiseProduct(Request $request)
    {
        try {
            $category_id = $request->category_id;

            $categoryWiseProduct = Category::findOrFail($category_id);

            $products = $categoryWiseProduct->products()->with('brand')->paginate(15);

            if ($products->isEmpty()) {
                return response()->json([
                    'error' => 'No products found for this category.'
                ], 404);
            }

            return response()->json($products, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Category not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
