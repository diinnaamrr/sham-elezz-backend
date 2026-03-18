<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CategoryLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Item;
use App\Models\PriorityList;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function get_categories(Request $request,$search=null)
    {
        try {
            $category_list_default_status = BusinessSetting::where('key', 'category_list_default_status')->first()?->value ?? 1;
            $category_list_sort_by_general = PriorityList::where('name', 'category_list_sort_by_general')->where('type','general')->first()?->value ?? '';
            $zone_id=  $request->header('zoneId') ? json_decode($request->header('zoneId'), true) : [];
            $key = explode(' ', $search);
            $featured = $request->query('featured');
            // Commented out: Sub-category support disabled - only main categories allowed
            // $categories = Category::withCount(['products','childes'=> function($query){
            //     $query->where('status',1);
            // } ])->with(['childes' => function($query)  {
            //     $query->where('status',1)->withCount(['products','childes'=> function($query){
            //         $query->where('status',1);
            //     }]);
            // }])
            $categories = Category::withCount(['products'])
            ->where(['position'=>0,'status'=>1])
            ->when(config('module.current_module_data'), function($query){
                $query->module(config('module.current_module_data')['id']);
            })
            ->when($featured, function($query){
                $query->featured();
            })
            ->when($search, function($query)use($key){
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%". $value."%");
                    }
                });
            })
            ->when($category_list_default_status  == 1 , function ($query) {
                $query->orderBy('priority','desc');
            })


            ->when($category_list_default_status  != 1 &&  $category_list_sort_by_general == 'latest', function ($query) {
                $query->latest();
            })
            ->when($category_list_default_status  != 1 &&  $category_list_sort_by_general == 'oldest', function ($query) {
                $query->oldest();
            })
            ->when($category_list_default_status  != 1 &&  $category_list_sort_by_general == 'a_to_z', function ($query) {
                $query->orderby('name');
            })
            ->when($category_list_default_status  != 1 &&  $category_list_sort_by_general == 'z_to_a', function ($query) {
                $query->orderby('name','desc');
            })
            ->get();

            if(count($zone_id) > 0){
                foreach ($categories as $category) {
                    $productCountQuery = Item::active()
                        ->whereHas('store', function ($query) use ($zone_id) {
                            $query->whereIn('zone_id', $zone_id);
                        })
                        // Commented out: Sub-category support disabled - only main categories
                        ->whereHas('category',function($q)use($category){
                            return $q->whereId($category->id); // Removed: ->orWhere('parent_id', $category->id)
                        })
                        ->withCount('orders');

                    $productCount = $productCountQuery->count();
                    $orderCount = $productCountQuery->sum('order_count');

                    $category['products_count'] = $productCount;
                    $category['order_count'] = $orderCount;
                }
                if($category_list_default_status  != 1 &&  $category_list_sort_by_general == 'order_count'){

                    $categories = $categories->sortByDesc('order_count')->values()->all();
                }
            }
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_childes($id)
    {
        try {
            $categories = Category::with('parent')->where(['parent_id' => $id,'status'=>1])->orderBy('priority','desc')->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

   public function get_products($id, Request $request)
{
    if (!$request->hasHeader('zoneId')) {
        return response()->json([
            'errors' => [['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]]
        ], 403);
    }

    $validator = Validator::make($request->all(), [
        'limit' => 'required|integer|min:1',
        'offset' => 'required|integer|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => Helpers::error_processor($validator)], 403);
    }

    $zone_id = json_decode($request->header('zoneId'), true);
    $limit = intval($request->input('limit', 10));
    $offset = max(1, intval($request->input('offset', 1))) - 1;

    try {
        // Commented out: Sub-category support disabled - only main categories allowed
        // Step 1: Get category ID only (no subcategories)
        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 200);
        }
        // $categoryIds = $category->getAllSubcategoryIds(); // Fetch all subcategories recursively
        $categoryIds = [$category->id]; // Only use main category ID

        // Step 2: Get all unique product SKUs first (before pagination)
        $allUniqueSkus = Item::whereIn('category_id', $categoryIds)
            ->whereHas('store', function ($query) use ($zone_id) {
                $query->whereIn('zone_id', $zone_id);
            })
            ->where('status', 1)
            ->select('sku') // Fetch only SKU for uniqueness
            ->distinct() // Ensure unique products
            ->orderBy('sku', 'asc') // Sorting for consistency
            ->pluck('sku');

        // Step 3: Apply proper pagination on SKUs
        $paginatedSkus = $allUniqueSkus->slice($offset * $limit, $limit); // Correct offset logic

        // Step 4: Fetch the actual product IDs based on these paginated SKUs
        $uniqueProductIds = Item::whereIn('sku', $paginatedSkus)
            ->whereIn('category_id', $categoryIds) // Include subcategories
            ->whereHas('store', function ($query) use ($zone_id) {
                $query->whereIn('zone_id', $zone_id);
            })
            ->where('status', 1)
            ->selectRaw('MIN(id) as id') // Ensures only one product per SKU
            ->groupBy('sku') // Groups by SKU to ensure uniqueness
            ->pluck('id');

        // Step 5: Fetch the actual product details using those IDs
        $products = Item::whereIn('id', $uniqueProductIds)
            ->orderBy('sku', 'asc') // Ensures same order consistency
            ->get();

        // Step 6: Get total count of unique products across the entire category + subcategories
        $total = $allUniqueSkus->count(); // Get total unique products count

        return response()->json([
            'total_size' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'products' => Helpers::product_data_formatting($products, true, false, app()->getLocale())
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'errors' => [['code' => 'server_error', 'message' => $e->getMessage()]]
        ], 500);
    }
}



    public function get_category_products(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'category_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zone_id= $request->header('zoneId');

        $type = $request->query('type', 'all');
        $category_ids = $request['category_ids']?json_decode($request['category_ids']):'';

        $data = CategoryLogic::category_products($category_ids, $zone_id, $request['limit'], $request['offset'], $type);
        $data['products'] = Helpers::product_data_formatting($data['products'] , true, false, app()->getLocale());
        return response()->json($data, 200);
    }


    public function get_stores($id, Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $type = $request->query('type', 'all');

        $data = CategoryLogic::stores($id, $zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $data['stores'] = Helpers::store_data_formatting($data['stores'] , true);
        return response()->json($data, 200);
    }

    public function get_category_stores(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'category_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $type = $request->query('type', 'all');
        $category_ids = $request['category_ids']?json_decode($request['category_ids']):'';

        $data = CategoryLogic::category_stores($category_ids, $zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $data['stores'] = Helpers::store_data_formatting($data['stores'] , true);
        return response()->json($data, 200);
    }



    public function get_all_products($id,Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $zone_id= $request->header('zoneId');

        try {
            return response()->json(Helpers::product_data_formatting(CategoryLogic::all_products($id, $zone_id), true, false, app()->getLocale()), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_featured_category_products(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zone_id= $request->header('zoneId');

        $type = $request->query('type', 'all');

        $data = CategoryLogic::featured_category_products($zone_id, $request['limit'], $request['offset'], $type);
        $data['products'] = Helpers::product_data_formatting($data['products'] , true, false, app()->getLocale());
        return response()->json($data, 200);
    }

    public function get_popular_category_list(){

        $avg_items=Item::where('order_count','>=', 1 )->avg('order_count') ?? 0;

        $items= Item::where('order_count','>', $avg_items )->pluck('category_ids');
        $get_popular_category_ids = $items->flatMap(function($categoryIds) {
            $categories = json_decode($categoryIds, true);
                return collect($categories)->pluck('id');
            })->unique();
        $categories= Category::whereIn('id',$get_popular_category_ids->toArray())
            ->where(['position'=>0,'status'=>1])
            ->when(config('module.current_module_data'), function($query){
                $query->module(config('module.current_module_data')['id']);
            })
            ->take(20)
            ->get();
        return response()->json($categories, 200);
    }

    /**
     * Get all categories with their items grouped by stores
     * Returns stores with their categories and items (like Uber Eats/Talabat)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_categories_with_items(Request $request)
    {
        // Validation: التحقق من وجود zoneId و moduleId في الـ header
        if (!$request->hasHeader('zoneId')) {
            return response()->json([
                'errors' => [['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]]
            ], 403);
        }

        if (!$request->hasHeader('moduleId')) {
            return response()->json([
                'errors' => [['code' => 'moduleId', 'message' => translate('messages.module_id_required')]]
            ], 403);
        }

        // التحقق من صحة البيانات
        $zone_id = json_decode($request->header('zoneId'), true);
        $module_id = $request->header('moduleId');

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($zone_id) || empty($zone_id)) {
            return response()->json([
                'errors' => [['code' => 'zoneId', 'message' => translate('messages.invalid_zone_id_format')]]
            ], 403);
        }

        if (!is_numeric($module_id)) {
            return response()->json([
                'errors' => [['code' => 'moduleId', 'message' => translate('messages.invalid_module_id_format')]]
            ], 403);
        }

        try {
            // Get all stores in the zone
            $stores = Store::withCount(['items', 'campaigns', 'reviews', 'orders'])
                ->with(['discount' => function($q) {
                    return $q->validate();
                }])
                ->whereHas('module', function($query) use ($module_id) {
                    return $query->where('id', $module_id)->active();
                })
                ->whereIn('zone_id', $zone_id)
                ->where('module_id', $module_id)
                ->whereHas('zone.modules', function($q) use ($module_id) {
                    $q->where('modules.id', $module_id);
                })
                ->active()
                ->get();

            // Get all categories for the module
            $categories = Category::where(['position' => 0, 'status' => 1])
                ->module($module_id)
                ->orderBy('priority', 'desc')
                ->get()
                ->keyBy('id');

            // Build stores with their categories and items
            $storesWithData = $stores->map(function($store) use ($zone_id, $module_id, $categories) {
                // Get all items for this store
                $storeItems = Item::active()
                    ->where('store_id', $store->id)
                    ->module($module_id)
                    ->get();

                // Group items by category using a keyed array
                $categoriesWithItemsArray = [];
                
                foreach ($storeItems as $item) {
                    $categoryId = $item->category_id;
                    
                    // Only include main categories (position = 0)
                    if (isset($categories[$categoryId])) {
                        $category = $categories[$categoryId];
                        
                        // Initialize category if not exists
                        if (!isset($categoriesWithItemsArray[$categoryId])) {
                            $categoryArray = $category->toArray();
                            $categoryArray['items'] = [];
                            $categoryArray['items_count'] = 0;
                            $categoriesWithItemsArray[$categoryId] = $categoryArray;
                        }
                        
                        // Add item to category
                        $formattedItem = Helpers::product_data_formatting($item, false, false, app()->getLocale());
                        $categoriesWithItemsArray[$categoryId]['items'][] = $formattedItem;
                    }
                }

                // Update items_count for each category and convert to array
                $categoriesWithItems = collect($categoriesWithItemsArray)->map(function($category) {
                    $category['items_count'] = count($category['items']);
                    return $category;
                })->values();

                // Format store data
                $formattedStore = Helpers::store_data_formatting($store, false);
                
                // Convert store to array and add categories
                $storeArray = is_array($formattedStore) ? $formattedStore : $formattedStore->toArray();
                $storeArray['categories'] = $categoriesWithItems->all();
                $storeArray['categories_count'] = $categoriesWithItems->count();
                $storeArray['total_items_count'] = $storeItems->count();

                return $storeArray;
            });

            return response()->json([
                'stores' => $storesWithData->values()->all(),
                'total_stores' => $storesWithData->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'errors' => [['code' => 'server_error', 'message' => $e->getMessage()]]
            ], 500);
        }
    }
}
