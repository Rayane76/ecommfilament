<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Color extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name','hex'];

    public function products() : BelongsToMany {
        return $this->belongsToMany(Product::class,'product_color')
        ->withPivot('isOut', 'sizes')
        ->using(ProductColor::class)
        ->withTimestamps();
    }
}
