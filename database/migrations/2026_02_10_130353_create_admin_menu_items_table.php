<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminMenuItemsTable extends Migration
{
    public function up()
    {
        Schema::create('admin_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('admin_menu_categories')->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('image', 100)->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_menu_items');
    }
}
