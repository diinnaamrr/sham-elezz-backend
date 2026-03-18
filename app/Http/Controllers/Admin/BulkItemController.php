<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BulkItemController extends Controller
{
    public function bulkCreate()
    {
        $categories = Category::all();
        $stores = Store::all();
          $module_type = 'grocery'; // Force grocery sidebar to be used

        return view('admin.bulk_items.create', compact('categories', 'stores'));
    }

    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.image' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'items.*.category_id' => 'required|integer|exists:categories,id',
            'items.*.store_id' => 'required|integer|exists:stores,id',
            // Commented out: module_id not required - only categories and items allowed
            // 'items.*.module_id' => 'required|integer',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.stock' => 'nullable|integer|min:0',
            'items.*.maximum_cart_quantity' => 'nullable|integer|min:1',
            'items.*.recommended' => 'required|boolean',
            'items.*.organic' => 'required|boolean',
            'items.*.is_approved' => 'required|boolean',
            'items.*.is_halal' => 'required|boolean',
            'items.*.sku' => 'nullable|string|unique:items,sku',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        foreach ($request->items as $itemData) {
            $item = new Item();
            $item->name = $itemData['name'];
            $item->description = $itemData['description'] ?? null;
            $item->category_id = $itemData['category_id'];
            $item->store_id = $itemData['store_id'];
            // Commented out: module_id not required - only categories and items allowed
            // $item->module_id = $itemData['module_id'] ?? null;
            $item->price = $itemData['price'];
            $item->stock = $itemData['stock'] ?? 0;
            $item->maximum_cart_quantity = $itemData['maximum_cart_quantity'] ?? null;
            $item->recommended = $itemData['recommended'] ?? 0;
            $item->organic = $itemData['organic'] ?? 0;
            $item->is_approved = $itemData['is_approved'] ?? 1;
            $item->is_halal = $itemData['is_halal'] ?? 0;
            $item->sku = $itemData['sku'] ?? null;

            if (isset($itemData['image'])) {
                $imagePath = $itemData['image']->storeAs('items', time() . '_' . $itemData['image']->getClientOriginalName(), 'public');
                $item->image = 'storage/items/' . time() . '_' . $itemData['image']->getClientOriginalName();
            }

            $item->save();
        }

        return response()->json(['success' => 'Items added successfully!'], 200);
    }
}
