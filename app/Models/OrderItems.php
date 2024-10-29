<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeFilterDate($query, $period = 'this_month')
    {
        if ($period == 'today') {
            return $query->where('created_at', '>=', now()->startOfDay())->where('created_at', '<=', now()->endOfDay());
        } elseif ($period == 'this_week') {
            return $query->where('created_at', '>=', now()->startOfWeek())->where('created_at', '<=', now()->endOfWeek());
        } elseif ($period == 'this_month') {
            return $query->where('created_at', '>=', now()->startOfMonth())->where('created_at', '<=', now()->endOfMonth());
        }
    }
}
