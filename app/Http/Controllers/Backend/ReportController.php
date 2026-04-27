<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\InventoryLedger;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function stockReport()
    {
        $stocks = InventoryLedger::select(
            'item_id',
            DB::raw("SUM(qty_in) as total_in"),
            DB::raw("SUM(qty_out) as total_out"),
            DB::raw("SUM(qty_in - qty_out) as stock")
        )
            ->groupBy('item_id')
            ->with('item')
            ->get();
        // final stock calculate
        $stocks->map(function ($row) {
            $row->stock = $row->total_in - $row->total_out;
            return $row;
        });


        return view('report.stock-report', compact('stocks'));
    }
}
