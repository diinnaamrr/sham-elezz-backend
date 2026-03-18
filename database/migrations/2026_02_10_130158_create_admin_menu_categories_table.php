<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminMenuCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('admin_menu_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('image', 100)->nullable();
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_menu_categories');
    }
}
