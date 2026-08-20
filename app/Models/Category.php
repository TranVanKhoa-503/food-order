<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    public function foods()
    {
        return $this->hasMany(Food::class, 'category_id');
    }
}
