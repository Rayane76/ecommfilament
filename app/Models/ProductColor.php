<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;


class ProductColor extends Pivot
{
    protected $table = 'product_color';

    protected $casts = [
        'sizes' => 'array'
    ];
}
