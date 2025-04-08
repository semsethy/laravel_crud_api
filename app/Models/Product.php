<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'stock_quantity',
        'status',
        'main_image_url',
        'collection_image_url',
        'category_id'
    ];

    protected $casts = [
        'collection_image_url' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
