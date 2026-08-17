<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // 🔹 Vendor Search
    public function vendorSearch(Request $request)
    {
        $term = trim($request->get('term', ''));

        if (empty($term)) {
            return response()->json([]);
        }

        $vendors = Vendor::where('v_name', 'LIKE', "%{$term}%")
            ->where('company_id', auth()->user()->company_id)
            ->limit(10)
            ->get(['id', 'v_name', 'phone', 'email', 'address']);

        $data = $vendors->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'label' => $vendor->v_name, // Autocomplete list এ দেখা যাবে
                'value' => $vendor->v_name, // Input field এ বসবে
                'vendor_id' => $vendor->id,
                'phone' => $vendor->phone,
                'email' => $vendor->email ?? '',
                'address' => $vendor->address,
            ];
        });

        return response()->json($data);
    }

    public function customerSearch(Request $request)
    {
        $term = trim($request->get('term', ''));

        if (empty($term)) {
            return response()->json([]);
        }

        $customers = Customer::where('c_name', 'LIKE', "%{$term}%")
            ->where('company_id', auth()->user()->company_id)
            ->limit(10)
            ->get(['id', 'c_name', 'phone', 'email', 'address']);

        $data = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'label' => $customer->c_name, // Autocomplete list এ দেখা যাবে
                'value' => $customer->c_name, // Input field এ বসবে
                'customer_id' => $customer->id,
                'phone' => $customer->phone,
                'email' => $customer->email ?? '',
                'address' => $customer->address,
            ];
        });

        return response()->json($data);
    }

    // 🔹 Item Search
    public function itemSearch(Request $request)
    {
        $term = trim($request->get('term', ''));

        if (empty($term)) {
            return response()->json([]);
        }

        $items = Item::with('category:id,cat_name')
            ->where('item_name', 'LIKE', "%{$term}%")
            ->where('company_id', auth()->user()->company_id)
            ->limit(10)
            ->get([
                'id',
                'item_name',
                'item_code',
                'cat_id',
                'size',
                'stock_unit',
                'unit_price',
                'avg_purchase_price',
                'production_cost',
                'sales_price'
            ]);

        $data = $items->map(function ($item) {

            if ($item->production_cost !== null && $item->production_cost > 0) {
                $costPrice = $item->production_cost;
                $priceType = 'production';
            } elseif ($item->purchase_price !== null && $item->purchase_price > 0) {
                $costPrice = $item->purchase_price;
                $priceType = 'purchase';
            } else {
                $costPrice = $item->unit_price ?? 0;
                $priceType = 'unit';
            }

            return [
                'id' => $item->id,
                'label' => $item->item_name,
                'value' => $item->item_name,
                'item_id' => $item->id,
                'item_code' => $item->item_code,
                'cat_id' => $item->cat_id,
                'cat_name' => optional($item->category)->cat_name,
                'size' => $item->size ?? '',
                'stock_unit' => $item->stock_unit ?? '',
                // Existing prices
                'unit_price' => $item->unit_price ?? 0,
                'purchase_price' => $item->purchase_price ?? 0,
                'production_cost' => $item->production_cost ?? 0,
                'sales_price' => $item->sales_price ?? 0,
                // Production costing-এর জন্য
                'cost_price' => $costPrice,
                'price_type' => $priceType,
            ];
        });

        return response()->json($data);
    }

    public function categorySearch(Request $request)
    {
        $term = trim($request->get('term', ''));

        if (empty($term)) {
            return response()->json([]);
        }

        $categories = Category::where('cat_name', 'LIKE', "%{$term}%")
            ->where('company_id', auth()->user()->company_id)
            ->limit(10)
            ->get(['id', 'cat_name']);

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'label' => $category->cat_name,
                'value' => $category->cat_name,
            ];
        });

        return response()->json($data);
    }
    public function searchAccount(Request $request)
    {
        $term = trim($request->get('term', ''));

        if (empty($term)) {
            return response()->json([]);
        }
        $accounts = Account::where('account_name', 'LIKE', "%{$term}%")
            ->where('company_id', auth()->user()->company_id)
            ->select('id', 'account_name', 'ac_cat', 'ac_type')
            ->limit(10)->get();

        return response()->json($accounts);
    }
    public function searchUser(Request $request)
    {
        $term = trim($request->get('term', ''));

        if (empty($term)) {
            return response()->json([]);
        }
        $users = User::where('name', 'LIKE', "%{$term}%")
            ->where('company_id', auth()->user()->company_id)
            ->select('id', 'name')
            ->limit(10)->get();
        return response()->json($users);
    }


    public function getByStatus(string $status)
    {
        $status = strtolower($status); // PHPStorm runtime warning কম হবে

        switch ($status) {
            case 'paid':
                $actype = 'asset';
                break;
            case 'due':
                $actype = 'liability';
                break;
            case 'unpaid':
                $actype = 'liability';
                break;
            case 'partial':
                $actype = 'asset';
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        $accounts = Account::where('ac_type', $actype)
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('account_name', 'asc')
            ->get(['id', 'account_name']);

        return response()->json($accounts);
    }
}
