<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Model\Common\Wishlist;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class wishlistController extends Controller
{
    public function add_to_wishlist($product_id)
    {
        if (empty($product_id)) {
            return response()->json(['message' => 'Product ID is required'], 400);
        }

        $check_wishlist = Wishlist::where('product_id', $product_id)->where('user_id', Auth::id())->first();
        if ($check_wishlist) {
            return response()->json(['message' => 'Already added in wishlist']);
        }

        try {
            Wishlist::insert([
                'user_id' => Auth::id(),
                'product_id' => $product_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return response()->json(['message' => 'Added in wishlist']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong while adding to wishlist'], 500);
        }
    }

    public function remove_to_wishlist($product_id)
    {
        if ($product_id) {
            $wishlist = Wishlist::where('product_id', $product_id)->where('user_id', Auth::id())->first();
            if ($wishlist) {
                $wishlist->delete();
                return response()->json(['message' => 'Removed from wishlist']);
            } else {
                return response()->json(['message' => 'Wishlist not found'], 404);
            }
        }
    }

    public function get_wishlist()
    {
        try {
            $wishlists = Wishlist::where('user_id', Auth::id())->with('product')->get();

            $wishlist_product = [];
            foreach ($wishlists as $wishlist) {
                if (isset($wishlist->product)) {
                    $wishlist_product[] = $wishlist->product;
                }
            }
            if (count($wishlist_product) > 0) {
                return response()->json($wishlist_product);
            } else {
                return response()->json(['message' => 'No records found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
