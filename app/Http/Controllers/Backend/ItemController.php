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

    public function categoryAdd()
    {
        $categories = Category::latest()->get();
        return view('item.category-create', compact('categories'));
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'cat_name' => 'required|string|max:255|unique:categories,cat_name'
        ], [
            'cat_name.unique' => 'This category already exists!'
        ]);
        $category = new Category();

        $category->cat_name = $request->cat_name;
        $category->save();

        return redirect()->back()->with('success', 'Category Added Successfully');
    }

    public function categoryEdit($id)
    {
        $categories = Category::latest()->get();
        $category = Category::findOrFail($id);
        return view('item.category-edit', compact('categories', 'category'));
    }

    public function categoryUpdate(Request $request, $id)
    {
        $request->validate([
            'cat_name' => 'required|string|max:255'
        ]);
        $category = Category::findOrFail($id);

        $category->cat_name = $request->cat_name;
        $category->save();

        return redirect('item/category/add')->with('success', 'Category Update Successfully');
    }

    public function categoryDelete($id)
    {

        $category = Category::findOrFail($id);

        $category->delete();

        return redirect()->back()->with('success', 'Category Delete Successfully');
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

        $items = Item::with('category')->latest()->get();
        return view('item.item-add', compact('lastSerial', 'items'));
    }

    public function itemStore(Request $request)
    {
        $request->validate([
            'item_code'     => 'required|string|max:50|unique:items,item_code',
            'item_name'     => 'required|string|max:255',
            'cat_id'        => 'required|exists:categories,id',
            'size'          => 'nullable|string|max:50',
            'unit_price'    => 'required|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
        ], [
            'item_code.required' => 'Item code is required',
            'item_code.unique'   => 'This item code already exists',
            'cat_id.required'    => 'Category is required',
            'cat_id.exists'      => 'Invalid category selected',
            'unit_price.numeric' => 'Price must be a number',
            'opening_stock.numeric' => 'Stock must be a number',
        ]);

        $item = new Item();

        $item->item_code = $request->item_code;
        $item->item_name = $request->item_name;
        $item->cat_id = $request->cat_id;
        $item->size = $request->size;
        $item->unit_price = $request->unit_price;
        $item->opening_stock = $request->opening_stock ?? 0;

        $item->save();

        return redirect()->back()->with('success', 'Item saved successfully!');
    }
    public function itemEdit($id)
    {
        // সর্বশেষ item এর code বের করুন
        $lastItem = Item::latest('id')->first();

        // যদি কিছু না থাকে, তাহলে 0 থেকে শুরু হবে
        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            $lastSerial = intval($matches[0]);
        } else {
            $lastSerial = 0;
        }

        $items = Item::with('category')->latest()->get();
        $item = Item::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('item.edit', compact('lastSerial', 'item', 'categories', 'items'));
    }

    public function itemUpdate(Request $request, $id)
    {
        $request->validate([
            'item_code'     => 'required|string|max:50',
            'item_name'     => 'required|string|max:255',
            'cat_id'        => 'required|exists:categories,id',
            'size'          => 'nullable|string|max:50',
            'unit_price'    => 'required|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
        ]);

        $item = Item::findOrFail($id);
        $item->item_name = $request->item_name;
        $item->item_code = $request->item_code;
        $item->cat_id = $request->cat_id;
        $item->size = $request->size;
        $item->unit_price = $request->unit_price;
        $item->opening_stock = $request->opening_stock ?? 0;
        $item->save();

        return redirect('/item/add')->with('success', 'Item Updated successfully!');
    }

    public function itemDelete($id)
    {
        $item = Item::findOrFail($id);

        $item->delete();
        return redirect()->back()->with('success', 'Item Delete successfully!');
    }
}
