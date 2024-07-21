<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $searchable = [
        'name',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
