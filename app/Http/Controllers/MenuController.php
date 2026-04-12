<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminMenuCategory;

class MenuController extends Controller
{
    public function index()
    {
        // تجيب كل الكاتيجوريز اللي مفعلة وكل أصنافها المفعلة
        $categories = AdminMenuCategory::with(['items' => function($q){
            $q->where('status', 1);
        }])->where('status', 1)->get();

        return view('menu.index', compact('categories'));
    }
}
