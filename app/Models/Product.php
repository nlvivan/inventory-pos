<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $guarded = [];

    protected $searchable = [
        'name', 'sku', 'notes', 'category.name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function productReturn(): HasOne
    {
        return $this->hasOne(ProductReturn::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
