<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;

class StoreScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * يعرض: منتجات المطعم الحالي + منتجات المنيو المشترك المسجّلة له
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $storeId = Helpers::get_store_id();
        if (!$storeId) {
            return;
        }

        $table = $model->getTable();

        // الموديلات التي تدعم منيو مشترك (يحتوي جدولها على عمود is_shared_menu)
        $supportsSharedMenu = in_array($table, ['items', 'temp_products'], true);

        if ($supportsSharedMenu) {
            $builder->where(function ($q) use ($storeId, $table) {
                $q->where($table . '.store_id', $storeId)
                    ->orWhere(function ($q2) use ($storeId, $table) {
                        $q2->where($table . '.is_shared_menu', true)
                            ->whereExists(function ($sub) use ($storeId, $table) {
                                $sub->select(DB::raw(1))
                                    ->from('store_item_stock')
                                    ->whereColumn('store_item_stock.item_id', $table . '.id')
                                    ->where('store_item_stock.store_id', $storeId);
                            });
                    });
            });
        } else {
            // موديلات أخرى (مثل AddOn ، MailConfig): فلترة بسيطة بحسب المطعم الحالي
            $builder->where($table . '.store_id', $storeId);
        }
    }
}