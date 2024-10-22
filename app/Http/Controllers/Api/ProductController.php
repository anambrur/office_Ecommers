<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\SM\SM;
use App\Model\Common\Review;
use App\Model\Common\Slider;
use Illuminate\Http\Request;
use App\Model\Common\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{

    public function getPopularProducts(Request $request)
    {
        try {
            // Fetch popular products based on views
            $popularProducts = Product::where('status', 1)
                ->orderBy('views', 'desc')
                ->paginate(10);

            // Get product IDs for reviews aggregation
            $productIds = $popularProducts->pluck('id')->toArray();

            // Fetch reviews data for these products in bulk
            $reviewsData = Review::selectRaw('product_id, COUNT(*) as reviewsCount, SUM(rating) as totalRating')
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id')
                ->get()
                ->keyBy('product_id');

            // Append review data to each product
            foreach ($popularProducts as $product) {
                // Set review count and average rating
                $product->reviewsCount = $reviewsData[$product->id]->reviewsCount ?? 0;
                $product->reviewsRating = $product->reviewsCount > 0
                    ? $reviewsData[$product->id]->totalRating / $product->reviewsCount
                    : null;  // Avoid division by zero
            }

            // Return the popular products along with the reviews data
            return response()->json($popularProducts, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while fetching popular products.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    #make a function for get latest products

    public function getLatestProducts(Request $request)
    {
        try {
            // Fetch latest products with status = 1
            $latestProducts = Product::where('status', 1)
                ->latest()
                ->paginate(15);

            // Get product IDs for reviews aggregation
            $productIds = $latestProducts->pluck('id')->toArray();

            // Fetch reviews data for these products in bulk
            $reviewsData = Review::selectRaw('product_id, COUNT(*) as reviewsCount, SUM(rating) as totalRating')
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id')
                ->get()
                ->keyBy('product_id');

            // Append review data to each product
            foreach ($latestProducts as $product) {
                // Set review count and average rating
                $product->reviewsCount = $reviewsData[$product->id]->reviewsCount ?? 0;
                $product->reviewsRating = $product->reviewsCount > 0
                    ? $reviewsData[$product->id]->totalRating / $product->reviewsCount
                    : null;  // Avoid division by zero
            }

            // Return the latest products along with the reviews data
            return response()->json($latestProducts, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while fetching latest products.',
                'details' => $e->getMessage()
            ], 500);
        }
    }






    public function getBestSellingProducts(Request $request)
    {
        try {
            // Query for best-selling products
            $bestSellingProducts = DB::table('order_details')
                ->selectRaw('products.*, SUM(order_details.product_qty) as total_sold')
                ->join('products', 'products.id', '=', 'order_details.product_id')
                ->groupBy('products.id')
                ->orderBy('total_sold', 'desc')
                ->paginate(15);

            $productIds = $bestSellingProducts->pluck('id')->toArray();

            // Fetch reviews data in bulk
            $reviewsData = Review::selectRaw('product_id, COUNT(*) as reviewsCount, SUM(rating) as totalRating')
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id')
                ->get()
                ->keyBy('product_id');

            // Append reviewsCount and reviewsRating to each product
            foreach ($bestSellingProducts as $product) {
                $product->reviewsCount = $reviewsData[$product->id]->reviewsCount ?? 0;
                $product->reviewsRating = $product->reviewsCount > 0
                    ? $reviewsData[$product->id]->totalRating / $product->reviewsCount
                    : null;  // Avoid division by zero
            }


            return response()->json($bestSellingProducts, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve best selling products.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    #make a function for get slider products

    public function getSlider(Request $request)
    {
        try {
            $slider = Slider::Published()->get();

            return response()->json($slider, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while fetching slider.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #make a function for get slider products

    public function getSliderProducts(Request $request)
    {
        try {
            $sliderProducts = Product::where('status', 1)
                ->latest()
                ->paginate(15);

            return response()->json($sliderProducts, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while fetching products.',
                'details' => $e->getMessage()
            ], 500);
        }
    }



    // public function productDetails(Request $request)
    // {
    //     try {
    //         // Fetch the product with related categories, brand, and units
    //         $product = Product::where('id', $request->product_id)->with('categories', 'brand', 'units')->first();

    //         if ($product === null) {
    //             return response()->json([
    //                 'error' => 'Product not found.',
    //             ], 404);
    //         }

    //         // Append the 'star_rating' attribute
    //         $product->append('star_rating');

    //         // Fetch reviews for the product
    //         $reviewData = Review::selectRaw('COUNT(*) as reviewsCount, SUM(rating) as totalRating')
    //             ->where('product_id', $product->id)
    //             ->first();

    //         // Calculate the review count and average rating
    //         $reviewCount = $reviewData->reviewsCount ?? 0;
    //         $reviewsRating = $reviewCount > 0 ? $reviewData->totalRating / $reviewCount : null;

    //         // Update the product views
    //         if ($product->views === null) {
    //             $product->views = 0;
    //         }
    //         $product->views += 1;
    //         $product->save();

    //         // Fetch product attributes (colors, sizes)
    //         $colors = SM::productAttributeColor($product->id);
    //         $sizes = SM::productAttributeSize($product->id);

    //         // Return product details along with review count and rating
    //         return response()->json([
    //             'product' => $product,
    //             'reviewCount' => $reviewCount,
    //             'reviewsRating' => $reviewsRating,
    //             'colors' => $colors,
    //             'sizes' => $sizes,
    //         ], 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'error' => 'Product not found.',
    //         ], 404);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Something went wrong while fetching product details.',
    //             'details' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function productDetails(Request $request)
    {
        try {
            $product = Product::where('id', $request->product_id)
                ->with('categories', 'brand', 'units')
                ->first();

            if ($product === null) {
                return response()->json([
                    'error' => 'Product not found.',
                ], 404);
            }

            // Append the dynamic attributes
            $product->append('reviewCount', 'reviewsRating');

            // Update views
            if ($product->views === null) {
                $product->views = 0;
            }
            $product->views += 1;
            $product->save();

            // Fetch product attributes (colors, sizes)
            $colors = SM::productAttributeColor($product->id);
            $sizes = SM::productAttributeSize($product->id);

            return response()->json([
                'product' => $product,
                'colors' => $colors,
                'sizes' => $sizes,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Product not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while fetching product details.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }







    public function productFilter(Request $request)
    {
        try {
            $action = $request->action;
            $minimum_price = $request->minimum_price;
            $maximum_price = $request->maximum_price;
            $brand = $request->brand;
            $stock_status = $request->stock_status;
            $category = $request->category;
            $size = $request->size;
            $color = $request->color;
            $orderByPrice = $request->orderByPrice;
            $limitProduct = $request->limitProduct;
            $orderBy = $request->orderBy;
            $sortBy = $request->sortBy;


            $products = Product::where('status', 1)
                ->with('categories', 'brand', 'attributeProduct')
                ->when($action, function ($query) use ($action) {
                    if ($action == "latest") {
                        $query->orderBy('id', 'DESC');
                    } elseif ($action == "oldest") {
                        $query->orderBy('id', 'ASC');
                    } elseif ($action == "high_price") {
                        $query->orderBy('regular_price', 'DESC');
                    } elseif ($action == "low_price") {
                        $query->orderBy('regular_price', 'ASC');
                    }
                })
                ->when($minimum_price, function ($query) use ($minimum_price) {
                    $query->where('regular_price', '>=', $minimum_price);
                })
                ->when($maximum_price, function ($query) use ($maximum_price) {
                    $query->where('regular_price', '<=', $maximum_price);
                })
                ->when($brand, function ($query) use ($brand) {
                    $query->whereIn('brand_id', $brand);
                })
                ->when($stock_status, function ($query) use ($stock_status) {
                    $query->where('stock_status', $stock_status);
                })
                ->when($category, function ($query) use ($category) {
                    $query->whereHas('categories', function ($q) use ($category) {
                        $q->whereIn('categories.id', $category); // Category filter based on category's id
                    });
                })
                ->when($size, function ($query) use ($size) {
                    $query->whereHas('attributes', function ($q) use ($size) {
                        $q->where('title', 'Size')->whereIn('title', $size);
                    });
                })
                ->when($color, function ($query) use ($color) {
                    $query->whereHas('attributes', function ($q) use ($color) {
                        $q->where('title', 'Color')->whereIn('title', $color);
                    });
                })
                ->when($orderByPrice, function ($query) use ($orderByPrice) {
                    if ($orderByPrice == "asc") {
                        $query->orderBy('regular_price', 'ASC');
                    } elseif ($orderByPrice == "desc") {
                        $query->orderBy('regular_price', 'DESC');
                    }
                })
                ->when($orderBy, function ($query) use ($orderBy, $sortBy) {
                    $query->orderBy($orderBy, $sortBy ?? 'ASC');
                })
                ->limit($limitProduct)
                ->paginate(10);


            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Something went wrong while fetching product details',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    #make a function for products search
    public function productSearch(Request $request)
    {
        try {
            $search = $request->search;

            if ($search == null || $search == '') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Search field is empty'
                ], 400);
            }

            // Convert search term to lowercase and remove spaces/special characters
            $search = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $search));

            $products = Product::whereRaw('LOWER(REGEXP_REPLACE(title, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(REGEXP_REPLACE(long_description, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(REGEXP_REPLACE(regular_price, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(REGEXP_REPLACE(sale_price, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(REGEXP_REPLACE(sku, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(REGEXP_REPLACE(stock_status, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->paginate(15);

            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Something went wrong while searching products',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function productFilterAndSearch(Request $request)
    {
        try {
            // Extract search and filter parameters from the request
            $search = $request->search;
            $action = $request->action;
            $minimum_price = $request->minimum_price;
            $maximum_price = $request->maximum_price;
            $brand = $request->brand;
            $stock_status = $request->stock_status;
            $category = $request->category;
            $size = $request->size;
            $color = $request->color;
            $orderByPrice = $request->orderByPrice;
            $limitProduct = $request->limitProduct;
            $orderBy = $request->orderBy;
            $sortBy = $request->sortBy;

            // Initialize the base query
            $products = Product::where('status', 1)->with('categories', 'brand', 'attributeProduct');

            // Apply search filter if search is provided
            if ($search != null && $search != '') {
                $search = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $search));

                $products->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(REGEXP_REPLACE(title, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(REGEXP_REPLACE(long_description, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(REGEXP_REPLACE(regular_price, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(REGEXP_REPLACE(sale_price, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(REGEXP_REPLACE(sku, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(REGEXP_REPLACE(stock_status, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%']);
                });
            }

            // Apply filters
            $products->when($action, function ($query) use ($action) {
                if ($action == "latest") {
                    $query->orderBy('id', 'DESC');
                } elseif ($action == "oldest") {
                    $query->orderBy('id', 'ASC');
                } elseif ($action == "high_price") {
                    $query->orderBy('regular_price', 'DESC');
                } elseif ($action == "low_price") {
                    $query->orderBy('regular_price', 'ASC');
                }
            })
                ->when($minimum_price, function ($query) use ($minimum_price) {
                    $query->where('regular_price', '>=', $minimum_price);
                })
                ->when($maximum_price, function ($query) use ($maximum_price) {
                    $query->where('regular_price', '<=', $maximum_price);
                })
                ->when($brand, function ($query) use ($brand) {
                    $query->whereIn('brand_id', $brand);
                })
                ->when($stock_status, function ($query) use ($stock_status) {
                    $query->where('stock_status', $stock_status);
                })
                ->when($category, function ($query) use ($category) {
                    $query->whereHas('categories', function ($q) use ($category) {
                        $q->whereIn('categories.id', $category);
                    });
                })
                ->when($size, function ($query) use ($size) {
                    $query->whereHas('attributes', function ($q) use ($size) {
                        $q->where('title', 'Size')->whereIn('title', $size);
                    });
                })
                ->when($color, function ($query) use ($color) {
                    $query->whereHas('attributes', function ($q) use ($color) {
                        $q->where('title', 'Color')->whereIn('title', $color);
                    });
                })
                ->when($orderByPrice, function ($query) use ($orderByPrice) {
                    if ($orderByPrice == "asc") {
                        $query->orderBy('regular_price', 'ASC');
                    } elseif ($orderByPrice == "desc") {
                        $query->orderBy('regular_price', 'DESC');
                    }
                })
                ->when($orderBy, function ($query) use ($orderBy, $sortBy) {
                    $query->orderBy($orderBy, $sortBy ?? 'ASC');
                });

            // Limit the number of products
            if ($limitProduct) {
                $products->limit($limitProduct);
            }

            // Paginate the results
            $products = $products->paginate(10);

            // Return the response
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Something went wrong while fetching product details',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function searchSuggestions(Request $request)
    {
        try {
            $search = $request->search;

            if ($search == null || $search == '') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Search field is empty'
                ], 400);
            }

            // Convert search term to lowercase and remove spaces/special characters
            $search = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $search));

            // Limit the suggestions to 10 results for performance
            $suggestions = Product::whereRaw('LOWER(REGEXP_REPLACE(title, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(REGEXP_REPLACE(sku, "[^a-zA-Z0-9]", "")) like ?', ['%' . $search . '%'])
                ->select('id', 'title', 'sku', 'image')
                ->take(10)
                ->get();

            return response()->json($suggestions, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Something went wrong while fetching search suggestions',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    #make a function for products review
    public function addProductReview(Request $request)
    {
        try {
            $product_id = $request->product_id;
            $user_id = $request->user_id;
            $review_text = $request->review; // Avoid reusing the $review variable
            $rating = $request->rating;

            $review = new Review();
            $review->product_id = $product_id;
            $review->user_id = $user_id;
            $review->description = $review_text; // Corrected variable name
            $review->rating = $rating;
            $review->status = 1;
            $review->save();

            // Return the review without relationships to avoid recursion
            return response()->json([
                'status' => 'success',
                'message' => 'Review added successfully',
                'review' => $review
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Something went wrong while adding review',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #get product reviews
    public function getProductReviews(Request $request)
    {
        try {
            $product_id = $request->product_id;
            $reviews = Review::where('product_id', $product_id)->with('user')->get();
            $reviewCount = $reviews->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Reviews fetched successfully',
                'review_count' => $reviewCount,
                'reviews' => $reviews,

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Something went wrong while fetching reviews',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
