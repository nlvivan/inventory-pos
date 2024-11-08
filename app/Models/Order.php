<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $searchable = [
        'order_number', 'customer.name',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class);
    }

    public function scopeFilterDate($query, $period = 'today')
    {
        if ($period == 'today') {
            return $query->where('created_at', '>=', now()->startOfDay())->where('created_at', '<=', now()->endOfDay());
        } elseif ($period == 'this_week') {
            return $query->where('created_at', '>=', now()->startOfWeek())->where('created_at', '<=', now()->endOfWeek());
        } elseif ($period == 'this_month') {
            return $query->where('created_at', '>=', now()->startOfMonth())->where('created_at', '<=', now()->endOfMonth());
        } elseif ($period == 'this_year') {
            return $query->where('created_at', '>=', now()->startOfYear())->where('created_at', '<=', now()->endOfYear());
        }
    }
}
