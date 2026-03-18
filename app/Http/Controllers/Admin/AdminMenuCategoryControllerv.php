<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminMenuCategory;

class AdminMenuCategoryControllerv extends Controller
{
    public function index()
    {
        $categories = AdminMenuCategory::all();
        return view('admin.menu.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.menu.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('menu_categories', 'public');
            $data['image'] = $path;
        }

        AdminMenuCategory::create($data);

        return redirect()->route('admin.menu.categories.index')->with('success', 'Category added successfully');
    }

    public function edit($id)
    {
        $category = AdminMenuCategory::findOrFail($id);
        return view('admin.menu.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = AdminMenuCategory::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('menu_categories', 'public');
            $data['image'] = $path;
        }

        $category->update($data);

        return redirect()->route('admin.menu.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = AdminMenuCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.menu.categories.index')->with('success', 'Category deleted successfully');
    }
}
