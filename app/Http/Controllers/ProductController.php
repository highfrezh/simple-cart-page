<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{   
    //Get all product
    public function getProducts()
    {
        $products = Product::all();

        return response()->json([
            'products' => $products
        ],200);
    }

    //Add product to cart
    public function addToCart($id)
    {
        $getProduct = Product::select('id','product_price')->where('id',$id)->first();

        $checkCart = Cart::where('product_id',$id)->first();
        if (!empty($checkCart)) {
            Cart::where('product_id',$id)->update([
                'qty'=> $checkCart->qty + 1, 
                'amount'=> $checkCart->amount + $getProduct->product_price
            ]);
        }else{
            $cart = new Cart;
            $cart->product_id = $id;
            $cart->amount = $getProduct->product_price;
            $cart->qty = 1;
            $cart->save();
        }
        return response()->json([
            'message' => 'Product add to cart successfull!'
        ], 200);
    }

    //Get Cart Items
    public function getCartProducts()
    {
        $cartItems = Cart::with('product')->get();

        //Get the total amount in the cart;
        $totalAmount = 0;
        foreach ($cartItems as $item){
            $amount = $item->qty * $item->product->product_price;
            $totalAmount += $amount;
        }
        return response()->json([
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount
        ],200);
    }

    //Update Cart Items
    public function updateCart(Request $request)
    {        
        if(!empty($request->id)){

            $getProduct = Product::select('id','product_price')->where('id',$request->product_id)->first(); 

            if ($request->type == "plus") {
                $qty = $request->quantity + 1;
            }else {
                $qty = $request->quantity - 1;
            }

            $amount = $getProduct->product_price * $qty;         

            $cartItem = Cart::where('id',$request->id)->first();
            $cartItem->qty = $qty;
            $cartItem->amount = $amount;
            $cartItem->save();
        }
        return response()->json([
            "message" => "Cart updated successfully!"
        ],200);
    }

    //get total item in the cart
    public function totalCartItems()
    {
        $totalCartItems = Cart::sum('qty');
        return response()->json([ 
            "items" => $totalCartItems
        ], 200);
        
    }

    //Apply Coupon Code
    public function applyCoupon($couponCode)
    {
        $couponDetails = Coupon::where('coupon_code',$couponCode)->first();
        // echo "<pre>"; print_r($couponDetails);die;
        
        if (!empty($couponDetails)) {
            $couponOption = $couponDetails->coupon_option;
            $amountType = $couponDetails->amount_type;
            $requiredItem = $couponDetails->required_item;
            $amountRequired = $couponDetails->required_amount;
            $couponAmount = $couponDetails->amount;

            $totalPrice = Cart::sum('amount');
            $totalCartItems = Cart::sum('qty');   
            
            if ($couponOption == "fixed" && $amountType == "fixed") {
                if($totalPrice > $amountRequired && $totalCartItems >= $requiredItem ){
                    $couponDiscount = $couponAmount;
                    return response()->json([
                        'discount' => $couponDiscount,
                        'message' => "Coupon applied successfull!"
                    ], 200);
                }else{
                    return response()->json([
                        'message' => 'Total price is < #'.$amountRequired.' OR Cart item is < '.$requiredItem
                    ],200);
                }               
            }elseif ($couponOption == "percent" && $amountType == "percent") {
                if($totalPrice > $amountRequired && $totalCartItems >= $requiredItem ){
                    $couponDiscount = ($totalPrice * $couponAmount)/ 100;
                    return response()->json([
                        'discount' => $couponDiscount,
                        'message' => "Coupon applied successfull!"
                    ], 200);
                }else{
                    return response()->json([
                        'message' => 'Total price is < #'.$amountRequired.' OR Cart item is < '.$requiredItem
                    ],200);
                }
            }elseif ($couponOption == "mixed" && $amountType == "fixed") {
                if($totalPrice > $amountRequired && $totalCartItems >= $requiredItem ){
                    $couponDiscount = $couponAmount;
                    return response()->json([
                        'discount' => $couponDiscount,
                        'message' => "Coupon applied successfull!"
                    ], 200);
                }else{
                    return response()->json([
                        'message' => 'Total price is < #'.$amountRequired.' OR Cart item is < '.$requiredItem
                    ],200);
                }
            }elseif ($couponOption == "rejected" && $amountType == "percent") {
                if($totalPrice > $amountRequired && $totalCartItems >= $requiredItem ){
                    $couponDiscount = ($totalPrice * $couponAmount)/ 100;
                    return response()->json([
                        'discount' => $couponDiscount,
                        'message' => "Coupon applied successfull!"
                    ], 200);
                }else{
                    return response()->json([
                        'message' => 'Total price is < #'.$amountRequired.' OR Cart item is < '.$requiredItem
                    ],200);
                }
            }
        }else{
            return response()->json([
                'message' => "Coupon code is not valid please try valid coupon"
            ], 200);
        }
    }
}
