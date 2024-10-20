<?php

namespace App\Http\Controllers\Api;

use Cart;
use Illuminate\Http\Request;
use App\Model\Common\Product;
use App\Http\Controllers\Controller;
use App\Model\Common\AttributeProduct;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // dd(request()->all());
        try {
            // Retrieve product details from request
            $id = $request->product_id;
            $qty = $request->qty ?? 1; // Default quantity is 1 if not provided
            $product_attribute_size = $request->product_attribute_size;
            $product_attribute_color = $request->product_attribute_color;
            $sizename = $request->sizename;
            $colorname = $request->colorname;


            // Fetch product information
            $product_info = Product::findOrFail($id);
            // dd($product_info);

            if ($product_info->product_type == 2) {
                // For variable products with size and color attributes
                $attribute_product = AttributeProduct::where('product_id', $id)
                    ->where('attribute_id', $product_attribute_size)
                    ->where('color_id', $product_attribute_color)
                    ->firstOrFail();

                $attribute_image = $attribute_product->attribute_image ?: $product_info->image;

                // Add to cart with attributes
                Cart::instance('cart')->add([
                    'id' => $id,
                    'name' => $product_info->title,
                    'price' => $attribute_product->attribute_price,
                    'qty' => $qty,
                    'options' => [
                        'image' => $attribute_image,
                        'slug' => $product_info->slug,
                        'sku' => $product_info->sku,
                        'size' => $product_attribute_size,
                        'color' => $product_attribute_color,
                        'sizename' => $sizename,
                        'colorname' => $colorname,
                    ]
                ]);
            } else {
                // For simple products
                $product_price = $product_info->sale_price > 0 ? $product_info->sale_price : $product_info->regular_price;


                // Add to cart without attributes
                Cart::instance('cart')->add([
                    'id' => $id,
                    'name' => $product_info->title,
                    'price' => $product_price,
                    'qty' => $qty,
                    'options' => [
                        'image' => $product_info->image,
                        'slug' => $product_info->slug,
                        'sku' => $product_info->sku,
                    ]
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Product added to cart successfully',
                'cart_count' => Cart::instance('cart')->count(),
                'cart_content' => Cart::instance('cart')->content(),
                'session_id' => session()->getId(),

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while adding product to cart: ' . $e->getMessage()
            ], 500);
        }
    }





    public function getCartContents(Request $request)
    {

        try {
            $sessionId = $request->header('Session-Id'); // Get session ID from the request header
            session()->setId($sessionId); // Set the session ID manually
            session()->start(); // Start the session

            $cartItems = Cart::content();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Your cart is empty',
                    'cart_items' => [],
                ], 200);
            }

            $formattedCart = [];
            foreach ($cartItems as $item) {
                $formattedCart[] = [
                    'row_id' => $item->rowId,
                    'product_id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'qty' => $item->qty,
                    'subtotal' => $item->subtotal,
                    'options' => $item->options,
                ];
            }

            return response()->json([
                'status' => 'success',
                'cart_items' => $formattedCart,
                'total' => Cart::total(),
                'count' => Cart::count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while fetching cart contents: ' . $e->getMessage(),
            ], 500);
        }
    }
}
