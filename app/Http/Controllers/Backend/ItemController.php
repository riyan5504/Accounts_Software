<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    private $inventoryService; // ✅ এখানে property declare

    public function __construct(InventoryService $inventoryService)
    {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
    }

    public function categoryAdd()
    {
        $categories = Category::latest()->get();
        return view('item.category-create', compact('categories'));
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'cat_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')
                    ->where('company_id', auth()->user()->company_id)
                    ->whereNull('deleted_at')
            ]
        ]);

        $category = new Category();

        $category->cat_name = $request->cat_name;
        $category->save();

        return redirect()->back()->with('success', 'Category Added Successfully');
    }

    public function categoryEdit($id)
    {
        $categories = Category::latest()->get();
        $category = Category::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);
        return view('item.category-edit', compact('categories', 'category'));
    }

    public function categoryUpdate(Request $request, $id)
    {
        $request->validate([
            'cat_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')
                    ->where('company_id', auth()->user()->company_id)
                    ->whereNull('deleted_at')
                    ->ignore($id)
            ]
        ]);
        $category = Category::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);
        $category->cat_name = $request->cat_name;
        $category->save();

        return redirect('item/category/add')->with('success', 'Category Update Successfully');
    }

    public function categoryDelete($id)
    {

        Category::where('company_id', auth()->user()->company_id)
            ->findOrFail($id)
            ->delete();

        return redirect()->back()->with('success', 'Category Delete Successfully');
    }

    public function itemModule()
    {
        return view('item.item-module');
    }

    private function getLastSerial()
    {
        $lastItem = Item::latest('id')->first();

        if ($lastItem && preg_match('/\d+$/', $lastItem->item_code, $matches)) {
            return intval($matches[0]);
        }

        return 0;
    }
    public function itemAdd()
    {
        $lastSerial = $this->getLastSerial();

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
            'sales_price'   => 'nullable|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
            'stock_unit'    => 'nullable|string|max:50',
        ]);

        $item = Item::create([
            'company_id'    => auth()->user()->company_id, // 🔥 CompanyScope support
            'item_code'     => $request->item_code,
            'item_name'     => $request->item_name,
            'cat_id'        => $request->cat_id,
            'size'          => $request->size,
            'stock_unit'    => $request->stock_unit,
            'unit_price'    => $request->unit_price,
            'sales_price'    => $request->sales_price,
            'opening_stock' => $request->opening_stock ?? 0,
        ]);

        // 🔥 inventory entry
        $this->inventoryService->openingStockEntry($item);

        return back()->with('success', 'Item saved successfully!');
    }

    public function itemEdit($id)
    {
        $lastSerial = $this->getLastSerial();

        $items = Item::with('category')->latest()->get();
        $item = Item::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('item.edit', compact('lastSerial', 'item', 'categories', 'items'));
    }

    public function itemUpdate(Request $request, $id)
    {
        $request->validate([
            'item_code'     => 'required|string|max:50|unique:items,item_code,' . $id,
            'item_name'     => 'required|string|max:255',
            'cat_id'        => 'required|exists:categories,id',
            'size'          => 'nullable|string|max:50',
            'unit_price'    => 'required|numeric|min:0',
            'sales_price'   => 'nullable|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
            'stock_unit'    => 'nullable|string|max:50',
        ]);

        $item = Item::findOrFail($id);

        $item->update([
            'item_code'     => $request->item_code,
            'item_name'     => $request->item_name,
            'cat_id'        => $request->cat_id,
            'size'          => $request->size,
            'unit_price'    => $request->unit_price,
            'sales_price'    => $request->sales_price,
            'opening_stock' => $request->opening_stock ?? 0,
            'stock_unit'    => $request->stock_unit,
        ]);

        // 🔥 re-generate opening stock
        $this->inventoryService->openingStockEntry($item);

        return redirect()->route('item.add')->with('success', 'Item updated successfully!');
    }

    public function itemDelete($id)
    {
        $item = Item::findOrFail($id);

        $item->delete();

        return back()->with('success', 'Item deleted successfully!');
    }

    public function categoryTrashList()
    {
        $categories = Category::onlyTrashed()->latest()->get();

        return view('item.category-trash', compact('categories'));
    }

    public function restoreCategory($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        $category->restore();

        return redirect()->route('item.category.add')->with('success', 'Category restored successfully!');
    }

    public function forceCategoryDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        $category->forceDelete(); // 🔥 permanently delete

        return back()->with('success', 'Category permanently deleted!');
    }

    public function itemTrashList()
    {
        $items = Item::onlyTrashed()->with('category')->latest()->get();

        return view('item.trash', compact('items'));
    }

    public function restoreItem($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);

        $item->restore();

        return redirect()->route('item.add')->with('success', 'Item restored successfully!');
    }

    public function forceIitemDelete($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);

        $item->forceDelete(); // 🔥 permanently delete

        return back()->with('success', 'Item permanently deleted!');
    }
}
