<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['fname','lname','phone','wilaya','commune','address','status','total'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product')
        ->withPivot('product_title', 'product_color', 'product_size', 'product_price', 'quantity')
        ->withTimestamps();
    }
}
