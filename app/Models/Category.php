<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Category extends Model
{
    use HasFactory, HasUuids;



    protected $fillable = ['name', 'image'];

    public function parents(): BelongsToMany {
        return $this->belongsToMany(Category::class,'category_parent', 'child_id', 'parent_id');
    } 

    public function children(): BelongsToMany {
        return $this->belongsToMany(Category::class,'category_parent', 'parent_id', 'child_id');
    } 

    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    public static function getDescendantIds(string $categoryId): array
    {
        $descendants = [];
        
        $children = static::whereHas('parents', function ($query) use ($categoryId) {
            $query->where('parent_id', $categoryId);
        })->get();

        foreach ($children as $child) {
            // Add the child's ID to the descendants array
            $descendants[] = $child->id;

            // Recursively get all descendants of this child and merge the results
            $descendants = array_merge($descendants, static::getDescendantIds($child->id));
        }

        return $descendants;
    }

    public static function getAncestorIds(string $categoryId): array
    {
        $ancestors = [];

        // Retrieve direct parents based on the given UUID
        $parents = static::whereHas('children', function ($query) use ($categoryId) {
            $query->where('child_id', $categoryId);
        })->get();

        foreach ($parents as $parent) {
        // Add the parent's ID to the ancestors array
        $ancestors[] = $parent->id;

        // Recursively get all ancestors of this parent and merge the results
        $ancestors = array_merge($ancestors, static::getAncestorIds($parent->id));
        }

        return $ancestors;
    }
}
