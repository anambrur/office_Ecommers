<?php

namespace App\Http\Controllers\Api;

use App\Model\Common\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BrandController extends Controller
{
    // Fetch all brands
    public function getBrands()
    {
        try {
            $brands = Brand::orderBy('id', 'desc')->get();

            if ($brands->isEmpty()) {
                return response()->json([
                    'error' => 'No brands found.'
                ], 404);
            }

            return response()->json($brands, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Fetch products by brand
    public function brandWiseProduct(Request $request)
    {
        try {

            $brand_id = $request->brand_id;

            $brandWiseProduct = Brand::findOrFail($brand_id);
            $products = $brandWiseProduct->products()->paginate(15);

            if ($products->isEmpty()) {
                return response()->json([
                    'error' => 'No products found for this brand.'
                ], 404);
            }
 
            return response()->json($products, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Brand not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
