<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    use HasFactory, Searchable;

    protected $searchable = [
        'batch_number',
    ];

    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
