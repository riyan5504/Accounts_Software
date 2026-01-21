<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Production;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function production()
    {
        return view('menufactur.modiul');
    }

    public function productAdd()
    {
        $items = Item::all();
        $today = now()->format('dmy');
        $lastBatch = Production::orderBy('id', 'desc')->first();


        if ($lastBatch) {
            $lastSerial = (int)substr($lastBatch->batch_no, -2);
            $newSerial = str_pad($lastSerial + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newSerial = '01';
        }

        $nextBatch = $today . $newSerial;

        return view('menufactur.add', compact('nextBatch', 'newSerial', 'items'));
    }
}
