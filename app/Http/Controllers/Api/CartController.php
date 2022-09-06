<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // GET MIDDLEWARE
        $this->middleware('auth:api');
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET CART USER
        $carts = Cart::with('product')
            ->where('customer_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'List Data Cart',
            'cart'      => $carts
        ]);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // GET CART ITEM CUSTOMER
        $item = Cart::where('product_id', $request->product_id)->where('customer_id', $request->customer_id);

        // IF ITEM ALREADY IN CART BEFORE(?)
        if ($item->count()) {
            // INCREMENT QUANTITY
            $item->increment('quantity');
            $item = $item->first();

            // SUM PRICE * QUANTITY
            $price = $request->price * $item->quantity;

            // SUM WEIGHT * QUANTITY
            $weight = $request->weight * $item->quantity;

            // UPDATE CART ITEM
            $item->update([
                'price'     => $price,
                'weight'    => $weight
            ]);
        } else {
            // CREATE NEW CART
            $item = Cart::Create([
                'product_id'    => $request->product_id,
                'customer_id'   => $request->customer_id,
                'quantity'      => $request->quantity,
                'price'         => $request->price,
                'weight'        => $request->weight
            ]);
        }

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'Success Add To Cart',
            'quantity'  => $item->quantity,
            'product'   => $item->product
        ]);
    }

    /**
     * getCartTotal
     *
     * @return void
     */
    public function getCartTotal()
    {
        // GET TOTAL PRICE OF CART CUSTOMER
        $carts = Cart::with('product')
            ->where('customer_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->sum('price');

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'Total Cart Price ',
            'total'     => $carts
        ]);
    }

    /**
     * getCartTotalWeight
     *
     * @return void
     */
    function getCartTotalWeight()
    {
        // GET TOTAL WEIGHT OF CART CUSTOMER
        $carts = Cart::with('product')
            ->where('customer_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->sum('weight');

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'Total Cart Weight ',
            'total'     => $carts
        ]);
    }

    /**
     * removeCart
     *
     * @param  mixed $request
     * @return void
     */
    public function removeCart(Request $request)
    {
        // DELETE CART BY CART_ID
        Cart::with('product')
            ->whereId($request->cart_id)
            ->delete();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'Remove Item Cart'
        ]);
    }

    /**
     * removeAllCart
     *
     * @param  mixed $request
     * @return void
     */
    public function removeAllCart(Request $request)
    {
        // DELETE ALL CART BY CUSTOMER_ID
        Cart::with('product')
            ->where('customer_id', auth()->guard('api')->user()->id)
            ->delete();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'Remove All Item in Cart'
        ]);
    }
}
