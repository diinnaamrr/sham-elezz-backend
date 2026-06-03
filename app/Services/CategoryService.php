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

    public function resolveSubParentId(int $mainCategoryId, ?int $parentSubCategoryId = null): int
    {
        return $parentSubCategoryId ?: $mainCategoryId;
    }

    public function getAddData($request, string|null|Object $parentCategory): array
    {
        $parentId = $request->position == 1
            ? $this->resolveSubParentId(
                (int) $request->main_category_id,
                $request->filled('parent_sub_category_id') ? (int) $request->parent_sub_category_id : null
            )
            : ($request->parent_id == null ? 0 : $request->parent_id);

        return [
            'name' => $request->name[array_search('default', $request->lang)],
            'image' => $this->upload('category/', 'png', $request->file('image')),
            'parent_id' => $parentId,
            'position' => $request->position,
            'module_id' => $parentCategory['module_id'] ?? Config::get('module.current_module_id'),
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

        if ($object->position == 1 && $request->filled('main_category_id')) {
            $data['parent_id'] = $this->resolveSubParentId(
                (int) $request->main_category_id,
                $request->filled('parent_sub_category_id') ? (int) $request->parent_sub_category_id : null
            );
        }

        return $data;
    }

    public function isValidSubCategoryAssignment(int $mainCategoryId, ?int $parentSubCategoryId = null, ?int $categoryId = null): bool
    {
        $main = Category::query()
            ->withoutGlobalScope('translate')
            ->where([
                'id' => $mainCategoryId,
                'position' => 0,
                'module_id' => Config::get('module.current_module_id'),
            ])
            ->first();

        if (!$main) {
            return false;
        }

        if (!$parentSubCategoryId) {
            return true;
        }

        if ($categoryId && (int) $parentSubCategoryId === $categoryId) {
            return false;
        }

        $parentSub = Category::query()
            ->withoutGlobalScope('translate')
            ->where([
                'id' => $parentSubCategoryId,
                'position' => 1,
                'module_id' => Config::get('module.current_module_id'),
            ])
            ->first();

        if (!$parentSub) {
            return false;
        }

        if ($categoryId && $parentSub->isDescendantOf($categoryId)) {
            return false;
        }

        return $parentSub->getRootCategoryId() === $mainCategoryId;
    }

    public function getSubCategoryOptionsForMain(int $mainCategoryId, ?int $excludeCategoryId = null): array
    {
        $subs = Category::query()
            ->withoutGlobalScope('translate')
            ->with('parent')
            ->where([
                'position' => 1,
                'module_id' => Config::get('module.current_module_id'),
            ])
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(fn (Category $category) => $category->getRootCategoryId() === $mainCategoryId);

        return $this->buildSubTreeOptions($subs, $mainCategoryId, 0, $excludeCategoryId);
    }

    public function getSubCategoriesByMainJson(?int $excludeCategoryId = null): string
    {
        $mainCategories = Category::query()
            ->withoutGlobalScope('translate')
            ->where([
                'position' => 0,
                'module_id' => Config::get('module.current_module_id'),
            ])
            ->orderBy('priority', 'desc')
            ->get();

        $payload = [];

        foreach ($mainCategories as $main) {
            $payload[$main->id] = $this->getSubCategoryOptionsForMain((int) $main->id, $excludeCategoryId);
        }

        return json_encode($payload);
    }

    private function buildSubTreeOptions(Collection $subs, int $parentId, int $depth, ?int $excludeCategoryId): array
    {
        $options = [];

        foreach ($subs->where('parent_id', $parentId) as $sub) {
            if ($excludeCategoryId && ((int) $sub->id === $excludeCategoryId || $sub->isDescendantOf($excludeCategoryId))) {
                continue;
            }

            $options[] = [
                'id' => $sub->id,
                'name' => str_repeat('— ', $depth) . $sub->name,
            ];

            $options = array_merge(
                $options,
                $this->buildSubTreeOptions($subs, (int) $sub->id, $depth + 1, $excludeCategoryId)
            );
        }

        return $options;
    }

    public function getSubCategoryFormDefaults(?Category $category = null): array
    {
        if (!$category || $category->position !== 1) {
            return [
                'main_category_id' => null,
                'parent_sub_category_id' => null,
            ];
        }

        $mainCategoryId = $category->getRootCategoryId();
        $parentSubCategoryId = $category->isDirectChildOfMain() ? null : (int) $category->parent_id;

        return [
            'main_category_id' => $mainCategoryId,
            'parent_sub_category_id' => $parentSubCategoryId,
        ];
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
