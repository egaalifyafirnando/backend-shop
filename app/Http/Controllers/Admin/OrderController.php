<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET INVOICES ORDER BY LATEST
        $invoices = Invoice::latest()->when(request()->q, function ($invoices) {
            $invoices = $invoices->where('invoice', 'like', '%' . request()->q . '%');
        })->paginate(10);

        // RETURN VIEW WITH PASSING DATA INVOICES
        return view('admin.order.index', compact('invoices'));
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // GET DATA INVOICE BY ID
        $invoice = Invoice::findOrFail($id);

        // RETURN VIEW
        return view('admin.order.show', compact('invoice'));
    }
}
