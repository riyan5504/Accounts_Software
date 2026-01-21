<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
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
            ->limit(10)
            ->get(['id', 'v_name', 'phone', 'email', 'address']);

        $data = $vendors->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'label' => $vendor->v_name, // Autocomplete list এ দেখা যাবে
                'value' => $vendor->v_name, // Input field এ বসবে
                'phone' => $vendor->phone,
                'email' => $vendor->email ?? '',
                'address' => $vendor->address,
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
            ->limit(10)
            ->get(['id', 'item_name', 'item_code', 'cat_id', 'size', 'unit_price']);

        $data = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'label' => $item->item_name,
                'value' => $item->item_name,
                'item_code' => $item->item_code,
                'cat_id' => $item->cat_id,
                'cat_name' => optional($item->category)->cat_name, // নিরাপদ access
                'size' => $item->size ?? '',
                'unit_price' => $item->unit_price ?? 0,
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
            ->limit(10)
            ->get(['id', 'cat_name']);

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'label' => $category->cat_name, // Autocomplete list এ দেখা যাবে
                'value' => $category->cat_name, // Input field এ বসবে
            ];
        });

        return response()->json($data);
    }
    public function searchAccount(Request $request)
    {
        $term = $request->get('term');
        $accounts = Account::where('account_name', 'LIKE', "%{$term}%")
            ->select('id', 'account_name', 'ac_cat', 'ac_type')
            ->limit(10)->get();

        return response()->json($accounts);
    }
    public function searchUser(Request $request)
    {
        $term = $request->get('term');
        $users = User::where('name', 'LIKE', "%{$term}%")
            ->select('id', 'name')
            ->limit(10)->get();
        return response()->json($users);
    }

    
    public function getByStatus(string $type)
{
    $type = strtolower($type); // PHPStorm runtime warning কম হবে

    switch ($type) {
        case 'paid':
            $actype = 'Asset';
            break;
        case 'unpaid':
        case 'partial':
            $actype = 'Liability';
            break;
        default:
            return response()->json(['error' => 'Invalid type'], 400);
    }

    $accounts = Account::where('ac_type', $actype)
        ->orderBy('account_name', 'asc')
        ->get(['id', 'account_name']);

    return response()->json($accounts);
}

}
