<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // GET CUSTOMERS ORDER BY LATEST
        $customers = Customer::latest()->when(request()->q, function ($customers) {
            $customers = $customers->where('name', 'like', '%' . request()->q . '%');
        })->paginate(10);

        // RETURN VIEW WITH PASSING DATA CUSTOMERS
        return view('admin.customer.index', compact('customers'));
    }
}
