<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Common\ShippingMethod;

class ShippingController extends Controller
{

    public function shippingMethods()
    {

       
        try {
            $shippingMethods = ShippingMethod::all();
            if ($shippingMethods === null) {
                return response()->json(['error' => 'No shipping methods found'], 404);
            }
        } catch (\Exception $e) {
            // Return the actual error message to debug
            return response()->json(['error' => 'An error occurred while retrieving shipping methods' . $e->getMessage()], 500);
        }

        return response()->json([
            'status' => 'success',
            'shippingMethods' => $shippingMethods,
        ], 200);
    }
}
