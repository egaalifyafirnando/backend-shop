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

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // VALIDATION DATA REQUEST
        $this->validate($request, [
            'airway_bill' => 'requires|max:20'
        ]);

        // UPDATE DATA AIRWAY_BILL 
        $invoice = Invoice::whereId($id);
        $invoice->update([
            'airway_bill' => $request->airway_bill
        ]);

        if ($invoice) {
            return redirect()->route('admin.order.index')->with(['success' => 'Data Berhasil Diperbarui!']);
        } else {
            return redirect()->route('admin.order.index')->with(['error' => 'Data Gagal Diperbarui!']);
        }
    }
}
