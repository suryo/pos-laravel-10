<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalSales = $transactions->sum('total_amount');

        return view('reports.index', compact('transactions', 'totalSales', 'startDate', 'endDate'));
    }
}
