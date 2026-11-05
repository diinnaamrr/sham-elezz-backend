<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;

class MenuBannerController extends Controller
{
    public function index()
    {
        $menu_banner = BusinessSetting::where('key', 'menu_banner')->first();
        return view('admin-views.business-settings.landing-page-settings.admin-menu-banner', compact('menu_banner'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'menu_banner' => 'nullable|image|max:10240',
        ]);

        if ($request->has('reset_banner')) {
            $old_banner = BusinessSetting::where('key', 'menu_banner')->first();
            if ($old_banner) {
                if (Storage::disk('public')->exists('banner/' . $old_banner->value)) {
                    Storage::disk('public')->delete('banner/' . $old_banner->value);
                }
                $old_banner->delete();
            }
            Toastr::success(translate('messages.banner_reset_successfully'));
            return back();
        }

        if ($request->hasFile('menu_banner')) {
            $old_banner = BusinessSetting::where('key', 'menu_banner')->first();
            $image_name = Helpers::update('banner/', $old_banner?->value, 'png', $request->file('menu_banner'));
            Helpers::businessUpdateOrInsert(['key' => 'menu_banner'], ['value' => $image_name]);
            Toastr::success(translate('messages.banner_updated_successfully'));
        }

        return back();
    }
}
