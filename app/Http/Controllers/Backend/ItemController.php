<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function itemModule()
    {
        return view('item.item-module');
    }
    public function itemAdd()
    {
        // সর্বশেষ item এর code বের করুন
        $lastItem = Item::latest('id')->first();

        // যদি কিছু না থাকে, তাহলে 0 থেকে শুরু হবে
        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            $lastSerial = intval($matches[0]);
        } else {
            $lastSerial = 0;
        }

        return view('item.item-add', compact('lastSerial'));
    }

    public function itemStore(Request $request)
    {
                        // 🔹 Category: একবারই থাকবে
                $category = Category::updateOrCreate(
                    [
                        'cat_name' => $request->cat_name,
                    ]
                );

                // 🔹 Item: একবারই থাকবে (item_name + cat_id unique)
                $item = Item::updateOrCreate(
                    [
                        'item_name' => $request->item_name,
                        'cat_id' => $category->id,
                    ],
                    [
                        'item_code' => $request->item_code ?? null,
                        'size' => $request->size ?? null,
                        'unit_price' => $request->unit_price ?? null,
                    ]
                );
            return redirect('/item/list')->with('success', 'Item saved successfully!');
           
    }

    public function itemList()
    {
        $items = Item::with('category')->get();
        return view('item.list', compact('items'));
    }
    public function itemEdit($id)
    {
        $item = Item::with('category')->find($id);
        $categories = Category::all();
        return view('item.edit', compact('item', 'categories'));
    }

    public function itemUpdate(Request $request, $id)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'cat_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric',
        ]);

        $item = Item::find($id);
        $item->item_name = $request->item_name;
        $item->item_code = $request->item_code;
        $item->cat_id = $request->cat_id;
        $item->size = $request->size;
        $item->unit_price = $request->unit_price;
        $item->save();

        return redirect('/item/list');
    }

    public function itemDelete($id)
    {
        $item = Item::find($id);

        $item->delete();
        return redirect()->back();
    }
}
