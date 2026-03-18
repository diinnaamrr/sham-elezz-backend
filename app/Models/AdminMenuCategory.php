<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMenuCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'status'];

    public function items()
    {
        return $this->hasMany(AdminMenuItem::class, 'category_id');
    }
}
