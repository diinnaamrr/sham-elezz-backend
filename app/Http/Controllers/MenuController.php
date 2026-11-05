<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminMenuCategory;
use App\Models\BusinessSetting;
use App\CentralLogics\Helpers;

class MenuController extends Controller
{
    public function index()
    {
        // تجيب كل الكاتيجوريز اللي مفعلة وكل أصنافها المفعلة
        $categories = AdminMenuCategory::with(['items' => function($q){
            $q->where('status', 1);
        }])->where('status', 1)->get();

        $menu_banner_setting = BusinessSetting::where('key', 'menu_banner')->first();
        $menu_banner = null;
        if ($menu_banner_setting && $menu_banner_setting->value) {
            $menu_banner = Helpers::get_full_url('banner', $menu_banner_setting->value, $menu_banner_setting->storage[0]->value ?? 'public');
        }

        return view('menu.index', compact('categories', 'menu_banner'));
    }
}
