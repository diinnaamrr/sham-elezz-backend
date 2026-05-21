<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Cart;
use App\Models\Item;
use App\Models\AddOn;
use App\Models\Coupon;
use App\Models\Store;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\CentralLogics\CouponLogic;
use App\Http\Controllers\Controller;
use App\Models\ItemCampaign;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Food items: validate variation, normalize payload, compute unit price on server.
     *
     * @return array{0: ?array, 1: array, 2: float, 3: int}|null  errors, normalized variation, unit price, units in line
     */
    protected function prepareFoodCartLine($item, Request $request): ?array
    {
        $productVariations = json_decode($item->food_variations ?? '[]', true);
        if (!is_array($productVariations) || count($productVariations) === 0) {
            return null;
        }

        $variationInput = $request->variation ?? [];
        $validationError = Helpers::validate_food_variations($productVariations, $variationInput);
        if ($validationError) {
            return [
                ['errors' => [['code' => $validationError['code'], 'message' => $validationError['message']]]],
                [],
                0.0,
                0,
            ];
        }

        $normalized = Helpers::normalize_food_variations_payload($variationInput);
        $unitPrice = Helpers::calculate_food_cart_unit_price($item, $normalized);
        $variationUnits = Helpers::food_variation_total_quantity($normalized);
        $lineUnits = max(1, $variationUnits) * (int)$request->quantity;

        return [null, $normalized, $unitPrice, $lineUnits];
    }

    protected function formatCartLine($data, ?int $contextStoreId = null)
    {
        $data->add_on_ids = json_decode($data->add_on_ids, true);
        $data->add_on_qtys = json_decode($data->add_on_qtys, true);
        $data->variation = json_decode($data->variation, true);
        if ($contextStoreId && $data->item && ($data->item->is_shared_menu ?? false) && !$data->item->getAttribute('context_store_id')) {
            $data->item->setAttribute('context_store_id', $contextStoreId);
        }
        $data->item = Helpers::cart_product_data_formatting(
            $data->item,
            $data->variation,
            $data->add_on_ids,
            $data->add_on_qtys,
            false,
            app()->getLocale()
        );
        $data->variation_selection_count = Helpers::food_variation_total_quantity(
            is_array($data->variation) ? $data->variation : []
        );

        return $data;
    }

    /**
     * Shared menu: resolve store_id param (id or slug) into numeric id.
     * Used to set context_store_id so formatted response keeps store_id non-null.
     */
    protected function resolveContextStoreId(Request $request): ?int
    {
        $storeId = $request->query('store_id', $request->input('store_id'));
        if ($storeId === null || $storeId === '') {
            return null;
        }
        if (is_numeric($storeId)) {
            return (int) $storeId;
        }
        return Store::where('slug', $storeId)->value('id');
    }

    public function get_carts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;
        $contextStoreId = $this->resolveContextStoreId($request);
        $carts = Cart::where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->get()
        ->map(fn ($data) => $this->formatCartLine($data, $contextStoreId));
        
        // حساب إجمالي المبلغ
        $total_amount = 0;
        foreach ($carts as $cart) {
            $item_price = $cart->price * $cart->quantity;
            
            // حساب أسعار الـ add-ons
            $add_on_price = 0;
            if (!empty($cart->add_on_ids) && is_array($cart->add_on_ids) && !empty($cart->add_on_qtys) && is_array($cart->add_on_qtys)) {
                $addons = AddOn::whereIn('id', $cart->add_on_ids)->get();
                if ($addons->count() > 0) {
                    $addon_data = Helpers::calculate_addon_price($addons, $cart->add_on_qtys);
                    if ($addon_data && isset($addon_data['total_add_on_price'])) {
                        $add_on_price = $addon_data['total_add_on_price'] * $cart->quantity;
                    }
                }
            }
            
            $total_amount += $item_price + $add_on_price;
        }
        
        // إرجاع الـ response مع إضافة total_amount
        $cartsArray = $carts->toArray();
        
        return response()->json([
            'carts' => $cartsArray,
            'total_amount' => round($total_amount, 2)
        ], 200);
    }

    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => $request->user ? 'nullable' : 'required',
            'item_id' => 'required|integer',
            'model' => 'required|string|in:Item,ItemCampaign',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'variation' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;
        $model = $request->model === 'Item' ? 'App\Models\Item' : 'App\Models\ItemCampaign';
        $item = $request->model === 'Item' ? Item::with('module')->find($request->item_id) : ItemCampaign::with('module')->find($request->item_id);

        if (!$item) {
            return response()->json([
                'errors' => [
                    ['code' => 'item_not_found', 'message' => translate('messages.product_not_found')],
                ],
            ], 404);
        }

        $foodLine = $this->prepareFoodCartLine($item, $request);
        if ($foodLine !== null) {
            if ($foodLine[0]) {
                return response()->json($foodLine[0], 403);
            }
            $variationStorage = Helpers::variation_payload_for_storage($foodLine[1]);
            $unitPrice = $foodLine[2];
            $lineUnits = $foodLine[3];
        } else {
            $variationStorage = Helpers::variation_payload_for_storage($request->variation ?? []);
            $unitPrice = (float)($request->price ?? $item->price);
            $lineUnits = (int)$request->quantity;
        }

        $cart = Cart::where('item_id',$request->item_id)->whereIn('item_type', [$model, $request->model])->where('variation',$variationStorage)->where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->first();

        if($cart){
            return response()->json([
                'errors' => [
                    ['code' => 'cart_item', 'message' => translate('messages.Item_already_exists')]
                ]
            ], 403);
        }

        if($item->maximum_cart_quantity && ($lineUnits > $item->maximum_cart_quantity)){
            return response()->json([
                'errors' => [
                    ['code' => 'cart_item_limit', 'message' => translate('messages.maximum_cart_quantity_exceeded')]
                ]
            ], 403);
        }

        $carts = Cart::where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->with('item')->get();

//        foreach($carts as $cart){
//                if($cart?->item?->store_id  && $cart?->item?->store_id != $item->store_id){
//                    return response()->json([
//                        'errors' => [
//                            ['code' => 'different_stores', 'message' => translate('messages.Please_select_items_from_the_same_store')]
//                        ]
//                    ], 403);
//                }
//            }


        $cart = new Cart();
        $cart->user_id = $user_id;
        $cart->module_id = $request->header('moduleId');
        $cart->item_id = $request->item_id;
        $cart->is_guest = $is_guest;
        $cart->add_on_ids = isset($request->add_on_ids)?json_encode($request->add_on_ids):json_encode([]);
        $cart->add_on_qtys = isset($request->add_on_qtys)?json_encode($request->add_on_qtys):json_encode([]);
        $cart->price = round($unitPrice, config('round_up_to_digit'));
        $cart->quantity = (int)$request->quantity;
        $cart->variation = $variationStorage;

        $item->carts()->save($cart);

        $carts = Cart::where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->get()
        ->map(fn ($data) => $this->formatCartLine($data));
        return response()->json($carts, 200);
    }

    public function update_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'guest_id' => $request->user ? 'nullable' : 'required',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'variation' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;
        $cart = Cart::find($request->cart_id);
        if (!$cart) {
            return response()->json([
                'errors' => [
                    ['code' => 'cart_not_found', 'message' => translate('messages.Cart_not_found')],
                ],
            ], 404);
        }

        $item = $cart->item ?? (in_array($cart->item_type, ['App\Models\Item', 'Item'], true)
            ? Item::with('module')->find($cart->item_id)
            : ItemCampaign::with('module')->find($cart->item_id));

        if (!$item) {
            return response()->json([
                'errors' => [
                    ['code' => 'item_not_found', 'message' => translate('messages.product_not_found')],
                ],
            ], 404);
        }

        $foodLine = $this->prepareFoodCartLine($item, $request);
        if ($foodLine !== null) {
            if ($foodLine[0]) {
                return response()->json($foodLine[0], 403);
            }
            $cart->variation = Helpers::variation_payload_for_storage($foodLine[1]);
            $cart->price = round($foodLine[2], config('round_up_to_digit'));
            $lineUnits = $foodLine[3];
        } else {
            if ($request->has('variation')) {
                $cart->variation = Helpers::variation_payload_for_storage($request->variation ?? []);
            }
            $cart->price = round((float)($request->price ?? $cart->price), config('round_up_to_digit'));
            $lineUnits = (int)$request->quantity;
        }

        if($item->maximum_cart_quantity && ($lineUnits > $item->maximum_cart_quantity)){
            return response()->json([
                'errors' => [
                    ['code' => 'cart_item_limit', 'message' => translate('messages.maximum_cart_quantity_exceeded')]
                ]
            ], 403);
        }

        $cart->user_id = $user_id;
        $cart->module_id = $request->header('moduleId');
        $cart->is_guest = $is_guest;
        $cart->add_on_ids = isset($request->add_on_ids)?json_encode($request->add_on_ids):$cart->add_on_ids;
        $cart->add_on_qtys = isset($request->add_on_qtys)?json_encode($request->add_on_qtys):$cart->add_on_qtys;
        $cart->quantity = (int)$request->quantity;
        $cart->save();

        $carts = Cart::where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->get()
        ->map(fn ($data) => $this->formatCartLine($data));
        return response()->json($carts, 200);
    }

    public function remove_cart_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;

        $cart = Cart::find($request->cart_id);
        $cart->delete();

        $carts = Cart::where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->get()
        ->map(fn ($data) => $this->formatCartLine($data));
        return response()->json($carts, 200);
    }

    public function remove_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;

        $carts = Cart::where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->get();

        foreach($carts as $cart){
            $cart->delete();
        }


        $carts = Cart::where('user_id', $user_id)->where('is_guest',$is_guest)->where('module_id',$request->header('moduleId'))->get()
        ->map(fn ($data) => $this->formatCartLine($data));
        return response()->json($carts, 200);
    }

    public function apply_coupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // التحقق من وجود المستخدم (يجب أن يكون مسجل دخول)
        if (!$request->user) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth', 'message' => translate('messages.please_login_first')]
                ]
            ], 401);
        }

        $user_id = $request->user->id;
        $is_guest = 0;

        // جلب الكارت الخاص باليوزر
        $carts = Cart::where('user_id', $user_id)
            ->where('is_guest', $is_guest)
            ->where('module_id', $request->header('moduleId'))
            ->with('item')
            ->get();

        // التحقق من أن الكارت غير فارغ
        if ($carts->isEmpty()) {
            return response()->json([
                'errors' => [
                    ['code' => 'cart', 'message' => translate('messages.cart_empty')]
                ]
            ], 404);
        }

        // التأكد من أن كل المنتجات من نفس المتجر
        $store_ids = [];
        foreach ($carts as $cart) {
            if ($cart->item) {
                $store_id = $cart->item->store_id ?? null;
                if ($store_id) {
                    $store_ids[] = $store_id;
                }
            }
        }

        $store_ids = array_unique($store_ids);
        if (count($store_ids) > 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'different_stores', 'message' => translate('messages.Please_select_items_from_the_same_store')]
                ]
            ], 403);
        }

        if (empty($store_ids)) {
            return response()->json([
                'errors' => [
                    ['code' => 'store', 'message' => translate('messages.store_not_found')]
                ]
            ], 404);
        }

        $store_id = $store_ids[0];
        $store = Store::with('discount')->find($store_id);

        if (!$store) {
            return response()->json([
                'errors' => [
                    ['code' => 'store', 'message' => translate('messages.store_not_found')]
                ]
            ], 404);
        }

        // حساب المبلغ الإجمالي للكارت
        $product_price = 0;
        $total_addon_price = 0;
        $store_discount_amount = 0;

        foreach ($carts as $cart) {
            $item = $cart->item;
            if (!$item) {
                continue;
            }

            // حساب سعر المنتج
            $item_price = $cart->price * $cart->quantity;
            $product_price += $item_price;

            // حساب أسعار الـ add-ons
            $add_on_price = 0;
            if (!empty($cart->add_on_ids) && is_array($cart->add_on_ids) && !empty($cart->add_on_qtys) && is_array($cart->add_on_qtys)) {
                $addons = AddOn::whereIn('id', $cart->add_on_ids)->get();
                if ($addons->count() > 0) {
                    $addon_data = Helpers::calculate_addon_price($addons, $cart->add_on_qtys);
                    if ($addon_data && isset($addon_data['total_add_on_price'])) {
                        $add_on_price = $addon_data['total_add_on_price'] * $cart->quantity;
                        $total_addon_price += $add_on_price;
                    }
                }
            }

            // حساب خصم المنتج (store discount على المنتج)
            $product_discount = Helpers::product_discount_calculate($item, $cart->price, $store);
            $store_discount_amount += ($product_discount['discount_amount'] ?? 0) * $cart->quantity;
        }

        // حساب خصم المتجر الإجمالي (store discount)
        $store_discount = Helpers::get_store_discount($store);
        if (isset($store_discount)) {
            if ($product_price + $total_addon_price < $store_discount['min_purchase']) {
                $store_discount_amount = 0;
            } else {
                if ($store_discount['max_discount'] != 0 && $store_discount_amount > $store_discount['max_discount']) {
                    $store_discount_amount = $store_discount['max_discount'];
                }
            }
        }

        // البحث عن الكوبون
        $coupon = Coupon::active()->where(['code' => $request['coupon_code']])->first();

        if (!$coupon) {
            return response()->json([
                'errors' => [
                    ['code' => 'coupon', 'message' => translate('messages.not_found')]
                ]
            ], 404);
        }

        // التحقق من أن الكوبون لم يُستخدم
        if ($coupon->is_used) {
            return response()->json([
                'errors' => [
                    ['code' => 'coupon', 'message' => translate('messages.coupon_already_used')]
                ]
            ], 409);
        }

        // التحقق من صحة الكوبون
        $coupon_status = CouponLogic::is_valide($coupon, $user_id, $store_id);

        switch ($coupon_status) {
            case 200:
                // الكوبون صالح - حساب الخصم
                break;
            case 406:
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.coupon_usage_limit_over')]
                    ]
                ], 406);
            case 407:
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.coupon_expire')]
                    ]
                ], 407);
            case 408:
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.You_are_not_eligible_for_this_coupon')]
                    ]
                ], 403);
            case 409:
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.coupon_already_used')]
                    ]
                ], 409);
            default:
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.not_found')]
                    ]
                ], 404);
        }

        // حساب المبلغ بعد خصم المتجر
        $amount_after_store_discount = $product_price + $total_addon_price - $store_discount_amount;

        // حساب خصم الكوبون
        $coupon_discount_amount = CouponLogic::get_discount($coupon, $amount_after_store_discount);

        // حساب المبلغ النهائي بعد تطبيق الكوبون
        $final_amount = $amount_after_store_discount - $coupon_discount_amount;
        $final_amount = max(0, $final_amount); // التأكد من أن المبلغ لا يكون سالب

        // إرجاع النتيجة
        return response()->json([
            'success' => true,
            'message' => translate('messages.coupon_applied_successfully'),
            'data' => [
                'coupon_code' => $coupon->code,
                'coupon_type' => $coupon->coupon_type,
                'discount_type' => $coupon->discount_type,
                'discount' => $coupon->discount,
                'max_discount' => $coupon->max_discount,
                'subtotal' => round($product_price + $total_addon_price, 2),
                'store_discount_amount' => round($store_discount_amount, 2),
                'amount_after_store_discount' => round($amount_after_store_discount, 2),
                'coupon_discount_amount' => round($coupon_discount_amount, 2),
                'final_amount' => round($final_amount, 2),
            ]
        ], 200);
    }
}
