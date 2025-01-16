<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_enabled' => 'bool',
        ];
    }

    protected $guarded = [];
}
