<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Searchable
{
    public function scopeSearch($query, $search)
    {
        $search = strtolower(trim($search)); // Clean up white space
        $fields = $this->searchable ?: [];

        return $query->where(function (Builder $query) use ($search, $fields) {
            foreach ($fields as $key => $field) {
                if (Str::contains($field, '.')) {
                    [$relationship, $relationshipField] = explode('.', $field);
                    $query->orWhereHas($relationship, function ($query) use ($relationshipField, $search) {
                        $query->whereRaw("LOWER($relationshipField) like ?", "%$search%");
                    });
                } else {
                    $query->orWhereRaw("LOWER($field) like ?", "%$search%");
                }
            }
        });
    }
}
