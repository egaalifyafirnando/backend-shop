<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // SET MIDDLEWARE
        $this->middleware('auth:api');
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET USER INVOICES
        $invoices = Invoice::where('customer_id', auth()->guard('api')->user()->id)->latest()->get();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'List Invoices: ' . auth()->guard()->user()->name,
            'data'      => $invoices
        ], 200);
    }

    public function show($snap_token)
    {
        // GET DETAIL INVOICE USER BY SNAP_TOKEN FROM PAYMENT GATEWAY
        $invoice = Invoice::where('customer_id', auth()->guard('api')->user()->id)->where('snap_token', $snap_token)->latest()->first();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'Detail Invoices: ' . auth()->guard('api')->user()->name,
            'data'      => $invoice
        ], 200);
    }
}
