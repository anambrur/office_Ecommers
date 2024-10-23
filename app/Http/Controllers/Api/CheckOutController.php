<?php

namespace App\Http\Controllers\Api;

use App\Cart;
use App\Model\Common\Order;
use Illuminate\Http\Request;
use App\Model\Common\Product;
use App\Model\Common\Order_detail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\Common\AttributeProduct;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CheckOutController extends Controller
{
    public function checkOutApi(Request $request)
    {

        try {
            // Validate the request input
            $request->validate([
                'payment_method_id' => 'required|integer',
                'grand_total' => 'required|numeric',
                'tax' => 'nullable|numeric',
                'coupon_code' => 'nullable|string',
                'coupon_amount' => 'nullable|numeric',
                'sub_total' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'cart_items' => 'required|array',
                'shipping' => 'required|array',
                'order_note' => 'nullable|string',
            ]);

            $coupon_amount = !empty($request->coupon_amount) ? $request->coupon_amount : 0;
            $shipping = $request->shipping;
            $user = Auth::user();
            $name = $shipping['firstname'] . ' ' . $shipping['lastname'];
            // Update user details
            $user->update([
                'billing_firstname' => $shipping['firstname'],
                'billing_lastname' => $shipping['lastname'],
                'billing_mobile' => $shipping['mobile'],
                'billing_address' => $shipping['address'],
                'billing_country' => $shipping['country'],
                'billing_state' => $shipping['state'],
                'billing_city' => $shipping['city'],
                'billing_zip' => $shipping['zip'],
            ]);
            $cartProducts = $request->cart_items;
            $user_id = Auth::id();
            $user_email = $user->email;
            $order = new Order();
            // Handle SSLCommerz Payment Logic
            if ($request->payment_method_id == 6) {
                $tran_id = uniqid('sslcommerz-', true);

                // Prepare data for SSLCommerz
                $post_data = array();
                $post_data['total_amount'] = $request->grand_total;
                $post_data['currency'] = "BDT";
                $post_data['tran_id'] = $tran_id;

                // Customer Information
                $post_data['cus_name'] = $name;
                $post_data['cus_email'] = $user_email;
                $post_data['cus_add1'] = $shipping["address"];
                $post_data['cus_city'] = $shipping["city"];
                $post_data['cus_state'] = $shipping["state"];
                $post_data['cus_postcode'] = $shipping["zip"];
                $post_data['cus_country'] = $shipping["country"];
                $post_data['cus_phone'] = $shipping["mobile"];

                // Shipment Information
                $post_data['ship_name'] = $name;
                $post_data['ship_add1'] = $shipping["address"];
                $post_data['ship_city'] = $shipping["city"];
                $post_data['ship_state'] = $shipping["state"];
                $post_data['ship_postcode'] = $shipping["zip"];
                $post_data['ship_country'] = $shipping["country"];

                // Other Information
                $post_data['shipping_method'] = "NO";
                $post_data['product_name'] = "Products";
                $post_data['product_category'] = "Goods";
                $post_data['product_profile'] = "physical-goods";

                // Optional Parameters
                $post_data['value_a'] = "Additional Value A";
                $post_data['value_b'] = "Additional Value B";

                // Add custom redirect URLs
                $post_data['success_url'] = url('/success');
                $post_data['fail_url'] = url('/fail');
                $post_data['cancel_url'] = url('/cancel');

                // Save order with pending status before payment
                $order->user_id = $user_id;
                $order->contact_email = $user_email;
                $order->cart_json = json_encode($cartProducts);
                $order->coupon_code = $request->coupon_code;
                $order->sub_total = $request->sub_total;
                $order->discount = $request->discount;
                $order->tax = $request->tax;
                $order->coupon_amount = $coupon_amount;
                $order->grand_total = $request->grand_total;
                $order->payment_method_id = $request->payment_method_id;
                $order->transaction_id = $tran_id;
                $order->order_note = $request->order_note;
                $order->order_status = 3; // Order is pending until payment is confirmed
                $order->payment_status = 2;


                $order->save();
                // SSLCommerz Payment Initialization
                $sslc = new SslCommerzNotification();
                // dd($post_data);
                $payment_options = $sslc->makePaymentByApi($post_data, 'hosted');

                $responseContent = $payment_options->getContent();  // Get the raw JSON content
                $data = json_decode($responseContent, true);

                $message = $data['message'];
                $paymentUrl = $data['payment_url'];

                // Now you can use the $message and $paymentUrl as needed
                // dd($message, $paymentUrl);
            }

            $order->user_id = $user_id;
            $order->contact_email = $user_email;
            $order->cart_json = json_encode($cartProducts);
            $order->coupon_code = $request->coupon_code;
            $order->sub_total = $request->sub_total;
            $order->discount = $request->discount;
            $order->tax = $request->tax;
            $order->coupon_amount = $coupon_amount;
            $order->grand_total = $request->grand_total;
            $order->payment_method_id = $request->payment_method_id;
            $order->transaction_id = null;
            $order->order_note = $request->order_note;
            $order->order_status = 3; // Order is pending until payment is confirmed
            $order->payment_status = 2;


            $order->save();

            // Save order details and update stock
            foreach ($cartProducts as $pro) {
                $cartPro = new Order_detail();
                $cartPro->order_id = $order->id;
                $cartPro->product_id = $pro['id'];
                $cartPro->product_color = $pro['options']['colorname'];
                $cartPro->product_size = $pro['options']['sizename'];
                $cartPro->product_image = $pro['options']['image'];
                $cartPro->product_price = $pro['price'];
                $cartPro->product_qty = $pro['qty'];
                $cartPro->sub_total = $pro['price'] * $pro['qty'];
                $cartPro->save();

                // Decrement product stock
                if ($pro['id'] instanceof Product) {
                    $product = Product::findOrFail($pro['id']);
                    $product->decrement('product_qty', $pro['qty']);
                } else {
                    $product = Product::findOrFail($pro['id']);
                    $product->decrement('product_qty', $pro['qty']);
                }

                // Decrement stock for product attributes (size/color)
                $attributeProduct = AttributeProduct::where('product_id', $pro['id'])
                    ->first();
                if ($attributeProduct instanceof AttributeProduct) {
                    $attributeProduct->decrement('attribute_qty', $pro['qty']);
                }
            }

            if ($request->payment_method_id == 6) {
                return response()->json([
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                    'grand_total' => $request->grand_total,
                    'apiPaymentMessage' => $message,
                    'paymentUrl' => $paymentUrl
                ]);
            } else {
                return response()->json([
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                    'grand_total' => $request->grand_total,
                ]);
            }
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            return response()->json([
                'error' => 'An error occurred while placing the order.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function runningOrders()
    {
        try {
            $user_id = Auth::id();

            if (empty($user_id)) {
                return response()->json(['error' => 'User ID is required.'], 400);
            }

            $runningOrders = Order::where('user_id', $user_id)
                ->where(function ($query) {
                    $query->where('order_status', 2)
                        ->orWhere('order_status', 3)
                        ->orWhere('order_status', 4);
                })
                ->with('user')
                ->with('detail')
                ->paginate(10);


            foreach ($runningOrders as $order) {
                foreach ($order->detail as $detail) {
                    $product = Product::find($detail->product_id);
                    $detail->product_title = $product ? $product->title : null;
                }
            }

            if ($runningOrders->isEmpty()) {
                return response()->json(['error' => 'Order list is empty.'], 404);
            }

            return response()->json($runningOrders, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the order list.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function orderHistory()
    {
        try {
            $user_id = Auth::id();

            if (empty($user_id)) {
                return response()->json(['error' => 'User ID is required.'], 400);
            }
            $orderHistory = Order::where('user_id', $user_id)
                ->where('order_status', 1)
                ->with('user')
                ->with('detail')
                ->paginate(10);

            if ($orderHistory->isEmpty()) {
                return response()->json(['error' => 'Order history is empty.'], 404);
            };

            foreach ($orderHistory as $order) {
                foreach ($order->detail as $detail) {
                    $product = Product::find($detail->product_id);
                    $detail->product_title = $product ? $product->title : null;
                }
            }

            return response()->json($orderHistory, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the order history.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    // #make a function for order details
    public function orderDetails(Request $request)
    {
        try {
            $orderId = $request->input('order_id');

            if (empty($orderId)) {
                return response()->json(['error' => 'Order ID is required.'], 400);
            }

            $order = Order::find($orderId);

            if (!$order instanceof Order) {
                return response()->json(['error' => 'Order not found.'], 404);
            }

            $order->load('detail');

            if ($order->detail === null) {
                return response()->json(['error' => 'Order detail not found.'], 404);
            }

            $products = $order->detail;

            foreach ($products as $product) {
                if ($product->product === null) {
                    return response()->json(['error' => 'Product not found for order detail.'], 404);
                }

                $productModel = Product::find($product->product_id);
                if (!$productModel instanceof Product) {
                    return response()->json(['error' => 'Product not found in database.'], 404);
                }
            }

            return response()->json(['order' => $order, 'products' => $productModel->title], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving order details.', 'message' => $e->getMessage()], 500);
        }
    }


    public function orderCancel(Request $request)
    {

        try {
            $orderId = $request->input('order_id');

            if (empty($orderId)) {
                return response()->json(['error' => 'Order ID is required.'], 400);
            }

            $order = Order::findOrFail($orderId);

            if (!$order instanceof Order) {
                return response()->json(['error' => 'Order not found.'], 404);
            }

            $order->order_status = 4;
            $order->save();

            return response()->json(['success' => 'Order has been cancelled.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while cancelling the order.', 'message' => $e->getMessage()], 500);
        }
    }
}
