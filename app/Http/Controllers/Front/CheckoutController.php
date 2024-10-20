<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Admin\Common\ShippingMethods;
use App\Mail\InvoiceMail;
use App\Mail\NormalMail;
use App\Model\Common\Category;
use App\Model\Common\Coupon;
use App\Model\Common\Order;
use App\Model\Common\Order_detail;
use App\Model\Common\Shipping;
use App\Model\Common\Payment_method;
use App\Model\Common\Product;
use App\Model\Common\ShippingMethod;
use App\Model\Common\Slider;
use App\Model\Common\Page as Page_model;
use App\Model\Common\Tax;
use App\SM\SM;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Model\Common\AttributeProduct;
use App\Library\SslCommerz\SslCommerzNotification;

class CheckoutController extends Controller
{

    /**
     * Home page methods and return view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewcart()
    {
        $result['activeMenu'] = 'dashboard';
        $result['cart'] = Cart::instance('cart')->content();

        return view('frontend.checkout.viewcart', $result);
    }

    public function save_billing_sipping(Request $data)
    {
        $this->validate($data, [
            'firstname' => 'required',
            'lastname' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        $user = Auth::user();
        $user->firstname = isset($data['firstname']) ? $data['firstname'] : null;
        $user->lastname = isset($data['lastname']) ? $data['lastname'] : null;
        $user->mobile = isset($data['mobile']) ? $data['mobile'] : null;
        $user->street = isset($data['street']) ? $data['street'] : null;
        $user->city = isset($data['city']) ? $data['city'] : null;
        $user->zip = isset($data['zip']) ? $data['zip'] : null;
        $user->state = isset($data['state']) ? $data['state'] : null;
        $user->country = isset($data['country']) ? $data['country'] : null;


        $user->update();

        $value = isset($data['skype']) ? $data['skype'] : null;
        SM::update_front_user_meta(\Auth::user()->id, 'skype', $value);

        $user_id = Auth::id();
        //        $user_data = array(
        //            'firstname' => $data->firstname,
        //            'lastname' => $data->lastname,
        //            'mobile' => $data->mobile,
        //            'skype' => $data->skype,
        //            'street' => $data->street,
        //            'city' => $data->city,
        //            'zip' => $data->zip,
        ////            'company' => $data->company,
        //            'country' => $data->country,
        //            'state' => $data->state,
        //        );
        //        User::where('id', $user_id)->update($user_data);
        $data = array(
            'user_id' => $user_id,
            'firstname' => $data->s_firstname,
            'lastname' => $data->s_lastname,
            //                'company' => $data->s_company,
            'mobile' => $data->s_mobile,
            'street' => $data->s_street,
            'city' => $data->s_city,
            'zip' => $data->s_zip,
            'country' => $data->s_country,
            'state' => $data->s_state,
        );
        //Check if Shipping Address exists
        $shippingCount = Shipping::where('user_id', $user_id)->count();
        if ($shippingCount > 0) {
            Shipping::where('user_id', $user_id)->update($data);
        }
    }

    public function checkout()
    {
        $data["cart"] = Cart::instance('cart')->content();
        if (count($data["cart"]) > 0) {
            if (empty(session('step'))) {
                session(['step' => '0']);
            }
            $data['shipping_methods'] = ShippingMethod::Published()->get();
            $data['payment_methods'] = Payment_method::Published()->get();
            $data["sub_total"] = Cart::instance('cart')->subTotal();
            $noraml_discount = Coupon::Published()->where('discount_type', 1)->where('validity', '>=', Carbon::now()->toDateString())->first();
            if ($noraml_discount != null) {
                if ($noraml_discount->type == 1) {
                    $data["noraml_discount_amount"] = $noraml_discount->coupon_amount;
                    $data["discount_amount"] = 0;
                } elseif ($noraml_discount->type == 2) {
                    $data["noraml_discount_amount"] = $data["sub_total"] * $noraml_discount->coupon_amount / 100;
                    $data["discount_amount"] = $noraml_discount->coupon_amount;
                } else {
                    $data["noraml_discount_amount"] = 0;
                }
            } else {
                $data["noraml_discount_amount"] = 0;
            }

            //        -----------tax-------------
            $data['is_tax_enable'] = SM::get_setting_value("is_tax_enable", 1);
            $data['default_tax'] = SM::get_setting_value("default_tax", 1);
            $data['default_tax_type'] = SM::get_setting_value("default_tax_type", 1);
            if ($data['is_tax_enable'] == 1 && Auth::check() && Session::get('shipping.country') != '') {
                $taxInfo = Tax::where("country", Session::get('shipping.country'))->first();
                if (!empty($taxInfo)) {
                    //                if (count($taxInfo) > 0) {
                    if ($taxInfo->type == 1) {
                        $tax = $taxInfo->tax;
                    } else {
                        $tax = $data["sub_total"] * $taxInfo->tax / 100;
                    }
                } else {
                    if ($data['default_tax_type'] == 1) {
                        $tax = (float) $data['default_tax'];
                    } else {
                        $tax = (float) $data['default_tax'] * $taxInfo->tax / 100;
                    }
                }
                $data['tax'] = $tax;
            } else {
                $data['tax'] = 0;
            }
            return view('frontend.checkout.checkout', $data);
        } else {
            return redirect('/shop')->with('s_message', "Please Order First...!");
        }
    }

    public function shippingMethod()
    {
        $data["userInfo"] = Auth::user();
        $data["shippingInfo"] = Auth::user()->shipping;
        $data['shipping_methods'] = ShippingMethod::Published()->get();
    }

    //checkout


    public function checkout_shipping_address(Request $request)
    {
        if (session('step') == '0') {
            session(['step' => '1']);
        }

        $shipping["firstname"] = $request->firstname;
        $shipping["lastname"] = $request->lastname;
        $shipping["mobile"] = $request->mobile;
        $shipping["company"] = $request->company;
        $shipping["address"] = $request->address;
        $shipping["country"] = $request->country;
        $shipping["state"] = $request->state;
        $shipping["city"] = $request->city;
        $shipping["zip"] = $request->zip;
        Session::put("shipping", $shipping);
        return redirect()->back();
    }

    //checkout_billing_address
    public function checkout_billing_address(Request $request)
    {
        if (session('step') == '1') {
            session(['step' => '2']);
        }

        $billing["billing_firstname"] = $request->billing_firstname;
        $billing["billing_lastname"] = $request->billing_lastname;
        $billing["billing_mobile"] = $request->billing_mobile;
        $billing["billing_company"] = $request->billing_company;
        $billing["billing_address"] = $request->billing_address;
        $billing["billing_country"] = $request->billing_country;
        $billing["billing_state"] = $request->billing_state;
        $billing["billing_city"] = $request->billing_city;
        $billing["billing_zip"] = $request->billing_zip;
        $billing["billing_same_address"] = $request->billing_same_address;
        Session::put("billing", $billing);
        return redirect()->back();
    }

    public function saveShippingMethod(Request $request)
    {
        $this->validate($request, [
            'shipping_method' => 'required',
        ]);
    }

    //checkout_shipping_method
    public function checkout_shipping_method(Request $request)
    {
        if (session('step') == '2') {
            session(['step' => '3']);
        }
        $shipping_data = ShippingMethod::find($request->shipping_method);
        $shipping_method["method_name"] = $shipping_data->title;
        $shipping_method["method_charge"] = $shipping_data->charge;
        Session::put("shipping_method", $shipping_method);
        return redirect()->back();
    }

    public function couponCheck(Request $request)
    {
        $this->validate($request, ['coupon_code' => 'required']);
        $sub_total_price = $request->sub_total_price;

        $coupon = Coupon::where("coupon_code", $request->coupon_code)->first();
        //        if (count($coupon) > 0) {
        if (!empty($coupon)) {
            if (!empty(Session::get('coupon.coupon_code'))) {
                $response['check_coupon'] = 0;
                $response['title'] = 'Coupon Already exits!';
                $response['message'] = 'Description';
                return response()->json($response);
            } else {
                $validity = $coupon->validity;
                $balance_qty = $coupon->balance_qty;
                $response["couponCode"] = $request->couponCode;
                if ($balance_qty > 0) {
                    if ($validity >= Carbon::now()->toDateString()) {
                        //                $response["check_coupon"] = 1;
                        $response["id"] = $coupon->id;
                        $response["coupon_code"] = $coupon->coupon_code;
                        $response["coupon_amount"] = $coupon->coupon_amount;
                        $response["type"] = $coupon->type;
                        Session::put("coupon", $response);
                        Session::save();
                        unset($response["id"]);
                        $update_qty = $balance_qty - 1;

                        Coupon::where("coupon_code", $request->coupon_code)
                            ->update(['balance_qty' => $update_qty]);
                        //                    $coupon->update('balance_qty', $update_qty);
                    } else {
                        $response['check_coupon'] = 0;
                        $response['title'] = 'Coupon Validity Expired!';
                        $response['message'] = 'Description';
                        return response()->json($response);
                    }
                } else {
                    $response['check_coupon'] = 0;
                    $response['title'] = 'Coupon Qty limit over';
                    $response['message'] = 'Description';
                    return response()->json($response);
                }

                $response['check_coupon'] = 1;
                $response['title'] = 'Coupon Successfully Applied!';
                $response['message'] = 'Description';
                $response['coupon_amount'] = Session::get('coupon.coupon_amount');
                $response['grand_total'] = $sub_total_price - Session::get('coupon.coupon_amount');

                return response()->json($response);
            }
        } else {
            $response['check_coupon'] = 0;
            $response['title'] = 'Coupon Not Found!';
            $response['message'] = 'Description';
            return response()->json($response);
        }
    }

    public function orderDetail()
    {
        $data["sub_total"] = Cart::instance('cart')->subTotal();

        //        $data["amount"] = Cart::instance('cart')->subTotal();

        $data['is_tax_enable'] = SM::get_setting_value("is_tax_enable", 1);
        $data['default_tax'] = SM::get_setting_value("default_tax", 1);
        $data['default_tax_type'] = SM::get_setting_value("default_tax_type", 1);
        $data['packageInfo'] = array();

        if ($data['is_tax_enable'] == 1 && Auth::check() && Auth::user()->country != '') {
            $taxInfo = Tax::where("country", Auth::user()->country)->first();

            if (!empty($taxInfo)) {
                //                if (count($taxInfo) > 0) {
                if ($taxInfo->type == 1) {
                    $tax = $taxInfo->tax;
                } else {
                    $tax = $data["sub_total"] * $taxInfo->tax / 100;
                }
            } else {
                if ($data['default_tax_type'] == 1) {
                    $tax = (float) $data['default_tax'];
                } else {
                    $tax = (float) $data['default_tax'] * $taxInfo->tax / 100;
                }
            }
            $data['tax'] = $tax;
        } else {
            $data['tax'] = 0;
        }

        $data['activeMenu'] = 'dashboard';
        $data['payment_methods'] = Payment_method::Published()->get();
        $data["cart"] = Cart::instance('cart')->content();

        return view('frontend.checkout.order_detail', $data);
    }

    public function placeOrder(Request $request)
    {
        // dd($request->all());

        if ($request->isMethod('post')) {

            $coupon_amount = !empty($request->coupon_amount) ? $request->coupon_amount : 0;

            $shipping = Session::get("shipping");
            $billing = Session::get("billing");
            $user = Auth::user();
            // dd($shipping);
            $name = $shipping["firstname"] . ' ' . $shipping["lastname"];

            // Update user details
            $user->firstname = $shipping["firstname"];
            $user->lastname = $shipping["lastname"];
            $user->mobile = $shipping["mobile"];
            $user->company = $shipping["company"];
            $user->address = $shipping["address"];
            $user->country = $shipping["country"];
            $user->state = $shipping["state"];
            $user->city = $shipping["city"];
            $user->zip = $shipping["zip"];
            $user->billing_firstname = $billing["billing_firstname"];
            $user->billing_lastname = $billing["billing_lastname"];
            $user->billing_mobile = $billing["billing_mobile"];
            $user->billing_company = $billing["billing_company"];
            $user->billing_address = $billing["billing_address"];
            $user->billing_country = $billing["billing_country"];
            $user->billing_state = $billing["billing_state"];
            $user->billing_city = $billing["billing_city"];
            $user->billing_zip = $billing["billing_zip"];
            $user->update();

            $cartProducts = Cart::instance('cart')->content();
            $user_id = Auth::id();
            $user_email = $user->email;
            // dd($user_email);

            $order = new Order;

            // SSLCommerz Payment Logic
            if ($request->payment_method_id == 6) {
                $tran_id = uniqid('sslcommerz-', true); // Generate a unique transaction ID
                Session::put('grand_total', $request->grand_total);
                Session::put('tax', $request->tax);
                Session::put('coupon_code', $request->coupon_code);
                Session::put('coupon_amount', $coupon_amount);
                Session::put('payment_method_id', $request->payment_method_id);
                Session::put('order_note', $request->order_note);
                Session::put('tran_id', $tran_id);

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
                $order->payment_status = 2; // Payment status pending

                // dd($post_data);
                $order->save();
                // Initiate SSLCommerz payment
                $sslc = new SslCommerzNotification();
                $payment_options = $sslc->makePayment($post_data, 'hosted');

                // dd($payment_options);

                if (!is_array($payment_options)) {

                    return redirect('/fail')->with('error', 'Payment initialization failed!');
                }

                // Redirect to SSLCommerz payment gateway
                return redirect($payment_options['GatewayPageURL']);
            }

            
            // If not SSLCommerz, proceed with normal order creation
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

            foreach ($cartProducts as $pro) {
                $cartPro = new Order_detail;
                $cartPro->order_id = $order->id;
                $cartPro->product_id = $pro->id;
                $cartPro->product_color = $pro->options->colorname;
                $cartPro->product_size = $pro->options->sizename;
                $cartPro->product_image = $pro->options->image;
                $cartPro->product_price = $pro->price;
                $cartPro->product_qty = $pro->qty;
                $cartPro->sub_total = $pro->price * $pro->qty;
                $cartPro->save();

                // Decrement product stock
                $product = Product::find($pro->id);
                $product->decrement('product_qty', $pro->qty);
                $product->update();

                // Decrement stock for product attributes (size/color)
                $attributeProduct = AttributeProduct::where('product_id', $pro->id)
                    ->where('attribute_id', $pro->options->size)
                    ->where('color_id', $pro->options->color)
                    ->first();
                    
                if (!empty($attributeProduct)) {
                    $attributeProduct->decrement('attribute_qty', $pro->qty);
                    $attributeProduct->update();
                }
            }

            Session::forget('step');
            Session::forget('shipping');
            Session::forget('billing');
            Session::forget('shipping_method');
            Session::forget('coupon');
            Cart::instance('cart')->destroy();
            Session::put('order_id', $order->id);
            Session::put('grand_total', $request->grand_total);

            // dd("test");

            // // Send confirmation email
            // $extra = new \stdClass();
            // $contact_email = $order->contact_email;
            // $contact_email2 = SM::get_setting_value('email');

            // if (filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            //     $extra->subject = "Order Invoice id # " . SM::orderNumberFormat($order) . " Mail";
            //     $extra->message = $request->message;
            //     \Mail::to($contact_email)->queue(new NormalMail($extra));
            //     \Mail::to($contact_email2)->queue(new NormalMail($extra));
            // }


            return redirect('/order-success')->with('s_message', "Order Successfully!");
        }

        return redirect('/order-success')->with('s_message', "Order Successfully!");
    }





    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        #Check order status in order tabel against the transaction id or order id.
        $order_details = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'order_status', 'grand_total')->first();


        $order = Order::where('transaction_id', $tran_id)->first();
        // dd($order);
        if ($order_details->order_status == 3) {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);
            // dd($validation);
            if ($validation) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as Processing or Complete.
                Here you can also sent sms or email for successfull transaction to customer
                */
                $update_product = DB::table('orders')
                    ->where('transaction_id', $tran_id)
                    ->update(['payment_status' => 1, 'order_status' => 2]);


                $cartProducts = Cart::instance('cart')->content();
                // $user = Auth::user();
                // $user_id = Auth::id();
                // $user_email = $user->email;

                // dd($cartProducts);

                foreach ($cartProducts as $pro) {
                    $cartPro = new Order_detail;
                    $cartPro->order_id = $order->id;
                    $cartPro->product_id = $pro->id;
                    $cartPro->product_color = $pro->options->colorname;
                    $cartPro->product_size = $pro->options->sizename;
                    $cartPro->product_image = $pro->options->image;
                    $cartPro->product_price = $pro->price;
                    $cartPro->product_qty = $pro->qty;
                    $cartPro->sub_total = $pro->price * $pro->qty;
                    $cartPro->save();

                    // Decrement product stock
                    $product = Product::find($pro->id);
                    $product->decrement('product_qty', $pro->qty);
                    $product->update();

                    // Decrement stock for product attributes (size/color)
                    $attributeProduct = AttributeProduct::where('product_id', $pro->id)
                        ->where('attribute_id', $pro->options->size)
                        ->where('color_id', $pro->options->color)
                        ->first();
                    if (!empty($attributeProduct)) {
                        $attributeProduct->decrement('attribute_qty', $pro->qty);
                        $attributeProduct->update();
                    }
                }

                // Session::forget('step');
                Session::forget('shipping');
                Session::forget('billing');
                Session::forget('shipping_method');
                Session::forget('coupon');
                Cart::instance('cart')->destroy();
                Session::put('order_id', $order->id);
                Session::put('grand_total', $request->grand_total);

                // Send confirmation email
                // $extra = new \stdClass();
                // $contact_email = $order->contact_email;
                // $contact_email2 = SM::get_setting_value('email');

                // if (filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                //     $extra->subject = "Order Invoice id # " . SM::orderNumberFormat($order) . " Mail";
                //     $extra->message = $request->message;
                //     \Mail::to($contact_email)->queue(new NormalMail($extra));
                //     \Mail::to($contact_email2)->queue(new NormalMail($extra));
                // }

                // echo "<br >Transaction is successfully Completed Check your email for details";
                return redirect('/')->with('s_message', "Order Successfully!");
            }
        } else if ($order_details->order_status == 2 || $order_details->order_status == 1) {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that transaction is completed. No need to udate database.
             */
            return redirect('/')->with('s_message', "Order Successfully!");
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
            return redirect('/')->with('s_message', "Transaction is Invalid!");
        }
    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_details = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'order_status', 'grand_total')->first();

        if ($order_details->order_status == 3) {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['order_status' => 3, 'payment_status' => 3]);
            return redirect('/')->with('s_message', "Transaction is Falied!");
        } else if ($order_details->order_status == 1 || $order_details->order_status == 2) {
            return redirect('/')->with('s_message', "Transaction is already Successful!");
        } else {
            return redirect('/')->with('s_message', "Transaction is Invalid!");
        }
    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_details = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'order_status', 'grand_total')->first();

        if ($order_details->order_status == 3) {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['order_status' => 4, 'payment_status' => 3]);
            return redirect('/')->with('s_message', "Transaction is Cancel!");
        } else if ($order_details->order_status == 1 || $order_details->status == 2) {
            return redirect('/')->with('s_message', "Transaction is already Successful!");
        } else {
            return redirect('/')->with('s_message', "Transaction is Invalid!");
        }
    }

    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->select('transaction_id', 'order_status', 'grand_total')->first();

            if ($order_details->order_status == 3) {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $order_details->paid);
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['order_status' => 2]);

                    return redirect('/')->with('s_message', "Transaction is successfully Completed!");
                }
            } else if ($order_details->status == 1 || $order_details->status == 2) {

                #That means Order status already updated. No need to udate database.
                return redirect('/')->with('s_message', "Transaction is already Successful!");
            } else {
                #That means something wrong happened. You can redirect customer to your product page.
                return redirect('/')->with('s_message', "Transaction is Invalid!");
            }
        } else {
            return redirect('/')->with('s_message', "Invalid Data!");
        }
    }













































    public function easypaywaySuccess(Request $request)
    {

        //        var_dump($request->all());
        //        exit;
        $shipping = Session::get("shipping");
        $billing = Session::get("billing");
        $user = Auth::user();
        $name = $shipping["firstname"] . ' ' . $shipping["lastname"];
        $user->firstname = $shipping["firstname"];
        $user->lastname = $shipping["lastname"];
        $user->mobile = $shipping["mobile"];
        $user->company = $shipping["company"];
        $user->address = $shipping["address"];
        $user->country = $shipping["country"];
        $user->state = $shipping["state"];
        $user->city = $shipping["city"];
        $user->zip = $shipping["zip"];
        $user->billing_firstname = $billing["billing_firstname"];
        $user->billing_lastname = $billing["billing_lastname"];
        $user->billing_mobile = $billing["billing_mobile"];
        $user->billing_company = $billing["billing_company"];
        $user->billing_address = $billing["billing_address"];
        $user->billing_country = $billing["billing_country"];
        $user->billing_state = $billing["billing_state"];
        $user->billing_city = $billing["billing_city"];
        $user->billing_zip = $billing["billing_zip"];
        $user->update();
        $cartProducts = Cart::instance('cart')->content();
        $user_id = Auth::id();
        $user_email = Auth::user()->email;
        //        $tran_id = Session::get('tran_id');
        //        $url = 'https://securepay.easypayway.com/api/v1/trxcheck/request.php?request_id="' . $tran_id . '"&store_id=buckelup&signature_key=6e1c769e1a768ef65d610bea66897c35&type=json';
        //
        //        $ch = curl_init();
        //        curl_setopt($ch, CURLOPT_URL, $url);
        //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        //        $headers = array();
        //        $headers[] = "Key: Value";
        //        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //
        //        $result = curl_exec($ch);
        //        $data = json_decode($result, true);
        //
        //        if (curl_errno($ch)) {
        //            echo 'Error:' . curl_error($ch);
        //        }
        //        curl_close($ch);

        //
        //        $result = '{
        //             "currency_merchant": "BDT",
        //    "convertion_rate": "",
        //    "ip_address": "220.158.206.79",
        //    "other_currency": "1.00",
        //    "success_url": "https://buckleup-bd.com/easypaywaySuccess",
        //    "fail_url": "https://buckleup-bd.com/order-fail",
        //    "epw_service_charge_bdt": "0.03",
        //    "epw_service_charge_usd": "Not-Available",
        //    "pay_status": "Successful",
        //    "epw_txnid": "BUC1557221360672152",
        //    "mer_txnid": "buckelup-19847",
        //    "store_id": "buckelup",
        //    "currency": "BDT",
        //    "store_amount": "0.97",
        //    "pay_time": "2019-05-07 09:29:59",
        //    "bank_txn": "6E76DBTJFO",
        //    "card_number": "01627809666",
        //    "card_type": "bKash-bKash",
        //    "reason": null,
        //    "epw_card_bank_name": null,
        //    "epw_card_bank_country": null,
        //    "epw_card_risklevel": null,
        //    "epw_error_code_details": null,
        //    "opt_a": "Optional Value A",
        //    "opt_b": "Optional Value B",
        //    "opt_c": "Optional Value C",
        //    "opt_d": "Optional Value D"
        //}';
        //        $data = json_decode($result, true);
        //        var_dump($data);
        //        exit;

        //        var_dump($request->all());
        //        exit;
        if ($request->pay_status == 'Successful') {
            $order = new Order;
            $order->user_id = $user_id;
            $order->contact_email = $user_email;
            $order->cart_json = json_encode($cartProducts);
            $order->coupon_code = Session::get('coupon_code');
            $order->sub_total = Session::get('sub_total');
            $order->tax = Session::get('tax');
            $order->coupon_amount = Session::get('coupon_amount');
            $order->grand_total = Session::get('grand_total');
            $order->payment_method_id = Session::get('payment_method_id');
            $order->order_note = Session::get('order_note');
            $order->order_status = 3;
            $order->payment_status = 1;
            $order->payment_details = json_encode($request->all());
            //            $order->created_by = SM::current_user_id();
            if ($order->save()) {
                $order_id = $order->id;
                foreach ($cartProducts as $pro) {
                    $cartPro = new Order_detail;
                    $cartPro->order_id = $order_id;
                    $cartPro->product_id = $pro->id;
                    $cartPro->product_color = $pro->options->colorname;
                    $cartPro->product_size = $pro->options->sizename;
                    $cartPro->product_price = $pro->price;
                    $cartPro->product_qty = $pro->qty;
                    $cartPro->sub_total = $pro->price * $pro->qty;
                    $cartPro->save();
                    $product = Product::find($pro->id);
                    $product->decrement('product_qty', $pro->qty);
                    $product->update();
                    $attributeProduct_id = AttributeProduct::where('product_id', $pro->id)
                        ->where('attribute_id', $pro->options->size)
                        ->where('color_id', $pro->options->color)->first();

                    if (!empty($attributeProduct_id)) {
                        $attributeProduct = AttributeProduct::find($attributeProduct_id->id);
                        $attributeProduct->decrement('attribute_qty', $pro->qty);
                        $attributeProduct->update();
                    }
                }
            }
            Session::forget('step');
            Session::forget('shipping');
            Session::forget('billing');
            Session::forget('shipping_method');
            Session::forget('coupon');
            Cart::instance('cart')->destroy();
            Session::forget('coupon_code');
            Session::forget('sub_total');
            Session::forget('tax');
            Session::forget('coupon_amount');
            Session::forget('grand_total');
            Session::forget('payment_method_id');
            Session::forget('order_note');
            Session::forget('tran_id');

            //mail
            $extra = new \stdClass();
            $contact_email = $order->contact_email;
            $contact_email2 = SM::get_setting_value('email');

            if (filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                $extra->subject = "Order Invoice id # " . SM::orderNumberFormat($order) . " Mail";
                $extra->message = $request->message;
                \Mail::to($contact_email)->queue(new NormalMail($extra));
                \Mail::to($contact_email2)->queue(new NormalMail($extra));
                $info['message'] = 'Mail Successfully Send';
            }
            return redirect('/order-success')->with('s_message', "Order Successfully!");
        } else {
            return redirect('/order-success')->with('w_message', "Order Payment Failed!");
        }
    }

    public function orderSuccess()
    {
        return view('frontend.checkout.order_success');
    }

    public function orderFail()
    {
        return redirect('/order_fail')->with('w_message', "Order Failed!");
        // return view('frontend.checkout.order_fail');
    }
}
