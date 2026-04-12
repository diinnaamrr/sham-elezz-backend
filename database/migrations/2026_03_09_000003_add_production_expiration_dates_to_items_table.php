<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة أعمدة تاريخ الإنتاج وانتهاء الصلاحية لجدول items
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->date('production_date')->nullable();
            $table->date('expiration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['production_date', 'expiration_date']);
        });
    }
};
