<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * مخزون المنتجات المشتركة (المنيو الموحد) لكل مطعم
 * يستخدم فقط عندما item.is_shared_menu = true
 */
class StoreItemStock extends Model
{
    protected $table = 'store_item_stock';

    protected $fillable = ['store_id', 'item_id', 'stock'];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
