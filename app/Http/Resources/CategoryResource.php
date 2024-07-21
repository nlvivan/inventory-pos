<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Storage */
        $storage = Storage::disk('public');

        $record = parent::toArray($request);

        $record['image_url'] = $this->image_url ? $storage->url($this->image_url) : null;

        return $record;
    }
}
