<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * منيو مشترك: منتج واحد يظهر لكل المطاعم، والـ stock لكل مطعم في store_item_stock
     */
    public function up(): void
    {
        Schema::create('store_item_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('stock')->default(0);
            $table->timestamps();
            $table->unique(['store_id', 'item_id']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_shared_menu')->default(false)->after('store_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // قبل الـ rollback: تأكد أن لا يوجد items.store_id = null (أعد تعيينهم لمطعم إن وجدت)
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable(false)->change();
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('is_shared_menu');
        });
        Schema::dropIfExists('store_item_stock');
    }
};
