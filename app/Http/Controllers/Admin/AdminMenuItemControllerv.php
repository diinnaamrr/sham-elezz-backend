<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminMenuItem;
use App\Models\AdminMenuCategory;

class AdminMenuItemControllerv extends Controller
{
    public function index()
    {
        $items = AdminMenuItem::with('category')->get();
        return view('admin.menu.items.index', compact('items'));
    }

    public function create()
    {
        $categories = AdminMenuCategory::where('status', 1)->get();
        return view('admin.menu.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:admin_menu_categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('menu_items', 'public');
            $data['image'] = $path;
        }

        AdminMenuItem::create($data);

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item added successfully');
    }

    public function edit($id)
    {
        $item = AdminMenuItem::findOrFail($id);
        $categories = AdminMenuCategory::where('status', 1)->get();
        return view('admin.menu.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $item = AdminMenuItem::findOrFail($id);

        $data = $request->validate([
            'category_id' => 'required|exists:admin_menu_categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('menu_items', 'public');
            $data['image'] = $path;
        }

        $item->update($data);

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item updated successfully');
    }

    public function destroy($id)
    {
        $item = AdminMenuItem::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item deleted successfully');
    }
}
