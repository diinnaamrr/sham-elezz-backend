<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * عرض كل الخصومات في مكان واحد: خصم المحل + الكوبونات (واختيارياً عروض الفلاش).
 */
class DiscountController extends Controller
{
    /**
     * إرجاع كل الخصومات المتاحة لليوزر حسب الزون في مكان واحد.
     * GET /api/v1/discount/all
     * Headers: zoneId (مطلوب), latitude, longitude (اختياري), customer_id (اختياري للضيوف)
     */
    public function all(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            return response()->json([
                'errors' => [
                    ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]
                ]
            ], 403);
        }

        $zone_id = $request->header('zoneId');
        $zone_ids = json_decode($zone_id, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($zone_ids) || empty($zone_ids)) {
            return response()->json([
                'errors' => [
                    ['code' => 'zoneId', 'message' => translate('messages.invalid_zone_id_format')]
                ]
            ], 403);
        }

        $customer_id = Auth::user()?->id ?? $request->customer_id ?? null;

        return response()->json([
            'store_discounts' => $this->getStoreDiscounts($zone_ids),
            'coupons' => $this->getCouponsForZone($zone_id, $customer_id),
        ], 200);
    }

    /**
     * خصومات المحلات النشطة ضمن الزون (خصم على كل الطلب).
     */
    protected function getStoreDiscounts(array $zone_ids): array
    {
        $stores = Store::when(config('module.current_module_data'), function ($query) {
                $query->whereHas('zone.modules', function ($q) {
                    $q->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
            })
            ->whereIn('zone_id', $zone_ids)
            ->active()
            ->whereHas('discount', function ($query) {
                $query->validate();
            })
            ->with(['discount', 'translations' => function ($q) {
                $q->where('locale', app()->getLocale());
            }])
            ->get(['id', 'name', 'zone_id', 'logo', 'cover_photo']);

        $list = [];
        foreach ($stores as $store) {
            $d = $store->discount;
            $list[] = [
                'type' => 'store_discount',
                'store_id' => (int) $store->id,
                'store_name' => $store->name,
                'store_logo' => $store->logo_full_url ?? null,
                'discount' => (float) $d->discount,
                'discount_type' => $d->discount_type ?? 'percent',
                'min_purchase' => (float) $d->min_purchase,
                'max_discount' => (float) ($d->max_discount ?? 0),
                'start_date' => $d->start_date,
                'end_date' => $d->end_date,
                'start_time' => $d->start_time ? date('H:i', strtotime($d->start_time)) : null,
                'end_time' => $d->end_time ? date('H:i', strtotime($d->end_time)) : null,
            ];
        }

        return $list;
    }

    /**
     * الكوبونات المتاحة للزون (نفس منطق CouponController@list).
     */
    protected function getCouponsForZone(string $zone_id, $customer_id): array
    {
        $coupons = Coupon::with('store:id,name')->active()
            ->when(config('module.current_module_data'), function ($query) {
                $query->module(config('module.current_module_data')['id']);
            })
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->get();

        $data = [];
        foreach ($coupons as $coupon) {
            if ($coupon->coupon_type == 'store_wise') {
                $temp = Store::active()
                    ->when(config('module.current_module_data'), function ($query) use ($zone_id) {
                        if (!config('module.current_module_data')['all_zone_service']) {
                            $query->whereIn('zone_id', json_decode($zone_id, true));
                        }
                    })
                    ->whereIn('id', json_decode($coupon->data, true))->first();
                if ($temp && (in_array('all', json_decode($coupon->customer_id, true)) || in_array($customer_id, json_decode($coupon->customer_id, true)))) {
                    $coupon->data = $temp->name;
                    $coupon['store_id'] = (int) $temp->id;
                    $data[] = $coupon;
                }
            } elseif ($coupon->coupon_type == 'zone_wise') {
                if (count(array_intersect(json_decode($zone_id, true), json_decode($coupon->data, true)))) {
                    $data[] = $coupon;
                }
            } elseif (isset($coupon->store_id)) {
                $temp = Store::active()
                    ->when(config('module.current_module_data'), function ($query) use ($zone_id) {
                        if (!config('module.current_module_data')['all_zone_service']) {
                            $query->whereIn('zone_id', json_decode($zone_id, true));
                        }
                    })
                    ->where('id', $coupon->store_id)
                    ->exists();
                if ($temp) {
                    $data[] = $coupon;
                }
            } else {
                if (in_array('all', json_decode($coupon->customer_id, true)) || in_array($customer_id, json_decode($coupon->customer_id, true))) {
                    $data[] = $coupon;
                }
            }
        }

        return $data;
    }
}
