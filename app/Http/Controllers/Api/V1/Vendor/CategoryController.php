<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function get_categories()
    {
        try {
            $categories = Category::with(Category::nestedActiveChildesRelation())
            ->where(['position'=>0,'status'=>1])
            ->when(config('module.current_module_data'), function($query){
                $query->module(config('module.current_module_data')['id']);
            })
            ->orderBy('priority','desc')->get();
            
            $formattedCategories = $this->formatCategoryTree($categories);
            return response()->json($formattedCategories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_childes($id)
    {
        try {
            $categories = Category::query();
            if(is_numeric($id)){
                $categories = $categories->where('parent_id', $id);
            } else {
                $categories = $categories->whereHas('parent',function($query)use($id){
                    $query->where('slug',$id);
                });
            }
            $categories = $categories->where('status', 1)
                ->orderBy('priority', 'desc')
                ->with(Category::nestedActiveChildesRelation())
                ->get();
                
            $formattedCategories = $this->formatCategoryTree($categories);
            return response()->json($formattedCategories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    
    private function formatCategoryTree($categories)
    {
        $formatted = [];
        foreach ($categories as $category) {
            // Get category as array
            $catData = $category->toArray();
            
            // Handle sub categories
            if (isset($catData['childes'])) {
                $catData['childes'] = $this->formatCategoryTree($category->childes);
            } else {
                $catData['childes'] = [];
            }
            
            $formatted[] = $catData;
        }
        return $formatted;
    }
}
