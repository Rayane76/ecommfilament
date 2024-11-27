<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;


class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['title', 'price', 'mainImage', 'images', 'isOut', 'type'];

    protected $casts = [
        'images' => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_color')
            ->withPivot( 'isOut', 'sizes')
            ->using(ProductColor::class)
            ->withTimestamps()
        ;
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product')
        ->withPivot('product_title', 'product_color', 'product_size', 'product_price', 'quantity')
        ->withTimestamps();
    }

    public function sizes(): HasMany {
        return $this->hasMany(Size::class);
    }
}
