<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // COUNT INVOICE FILTER BY STATUS
        $pending = Invoice::where('status', 'pending')->count();
        $success = Invoice::where('status', 'success')->count();
        $expired = Invoice::where('status', 'expired')->count();
        $failed = Invoice::where('status', 'failed')->count();

        // YEAR AND MONTH
        $year = date('Y');
        $month = date('m');

        // STATISTIC REVENUE
        $revenueMonth = Invoice::where('status', 'success')->whereMonth('created_at', '=', $month)->whereYear('created_at', $year)->sum('grand_total');
        $revenueYear  = Invoice::where('status', 'success')->whereYear('created_at', $year)->sum('grand_total');
        $revenueAll   = Invoice::where('status', 'success')->sum('grand_total');

        // RETURN VIEW AND PASSING ALL DATA
        return view('admin.dashboard.index', compact('pending', 'success', 'expired', 'failed', 'revenueMonth', 'revenueYear', 'revenueAll'));
    }
}
