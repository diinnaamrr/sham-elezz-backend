<?php

namespace App\Services;

use App\Enums\ViewPaths\Admin\Category as CategoryViewPath;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use App\Traits\FileManagerTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class CategoryService
{
    use FileManagerTrait;

    public function getViewByPosition(int $position): string
    {
        return match ($position) {
            1 => CategoryViewPath::SUB_CATEGORY_INDEX['view'],
            default => CategoryViewPath::INDEX['view'],
        };
    }

    public function getAddData($request, string|null|Object $parentCategory): array
    {
        return [
            'name' => $request->name[array_search('default', $request->lang)],
            'image' => $this->upload('category/', 'png', $request->file('image')),
            'parent_id' => $request->parent_id == null ? 0 : $request->parent_id,
            'position' => $request->position,
            'module_id' => isset($request->parent_id) ? $parentCategory['module_id'] : Config::get('module.current_module_id')
        ];
    }

    public function getUpdateData(CategoryUpdateRequest $request, object $object): array
    {
        $slug = Str::slug($request->name[array_search('default', $request->lang)]);
        $data = [
            'slug' => $object->slug ?? "{$slug}{$object->id}",
            'name' => $request->name[array_search('default', $request->lang)],
            'image' => $request->has('image') ? $this->updateAndUpload('category/', $object->image, 'png', $request->file('image')) : $object->image,
        ];

        if ($object->position == 1 && $request->filled('parent_id')) {
            $data['parent_id'] = (int) $request->parent_id;
        }

        return $data;
    }

    public function getParentCategoryOptions(?int $excludeCategoryId = null): Collection
    {
        $categories = Category::query()
            ->withoutGlobalScope('translate')
            ->with(['translations', 'module'])
            ->where('module_id', Config::get('module.current_module_id'))
            ->orderBy('parent_id')
            ->orderBy('priority', 'desc')
            ->get();

        return collect($this->buildParentCategoryOptions($categories, 0, 0, $excludeCategoryId));
    }

    private function buildParentCategoryOptions(Collection $categories, int $parentId, int $depth, ?int $excludeCategoryId): array
    {
        $options = [];

        foreach ($categories->where('parent_id', $parentId) as $category) {
            if ($excludeCategoryId && ((int) $category->id === $excludeCategoryId || $category->isDescendantOf($excludeCategoryId))) {
                continue;
            }

            $options[] = [
                'id' => $category->id,
                'name' => str_repeat('— ', $depth) . $category->name,
                'module_name' => $category->module?->module_name,
                'position' => $category->position,
            ];

            $options = array_merge(
                $options,
                $this->buildParentCategoryOptions($categories, (int) $category->id, $depth + 1, $excludeCategoryId)
            );
        }

        return $options;
    }

    public function isValidParentId(?int $parentId, ?int $categoryId = null): bool
    {
        if (!$parentId) {
            return false;
        }

        $parent = Category::query()
            ->withoutGlobalScope('translate')
            ->find($parentId);

        if (!$parent || $parent->module_id != Config::get('module.current_module_id')) {
            return false;
        }

        if ($categoryId && ((int) $parentId === $categoryId || $parent->isDescendantOf($categoryId))) {
            return false;
        }

        return true;
    }

    public function getImportData(Request $request, bool $toAdd = true): array
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (Exception) {
            return ['flag' => 'wrong_format'];
        }
        $moduleId = Config::get('module.current_module_id');

        $data = [];
        foreach ($collections as $collection) {
            if ($collection['Name'] === "") {
                return ['flag' => 'required_fields'];
            }
            $parentId = is_numeric($collection['ParentId']) ? $collection['ParentId'] : 0;
            $array = [
                'name' => $collection['Name'],
                'image' => $collection['Image'],
                'parent_id' => $parentId,
                'module_id' => $moduleId,
                'position' => $collection['Position'],
                'priority' => is_numeric($collection['Priority']) ? $collection['Priority'] : 0,
                'status' => $collection['Status'] == 'active' ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if(!$toAdd){
                $array['id'] = $collection['Id'];
            }

            $data[] = $array;
        }

        return $data;
    }

    public function getExportData(object $collection): array
    {
        $data = [];
        foreach($collection as $item){
            $data[] = [
                'Id'=>$item->id,
                'Name'=>$item->name,
                'Image'=>$item->image,
                'ParentId'=>$item->parent_id,
                'Position'=>$item->position,
                'Priority'=>$item->priority,
                'Status'=>$item->status == 1 ? 'active' : 'inactive',
            ];
        }
        return $data;
    }
}
