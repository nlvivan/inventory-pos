<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductInStock implements ValidationRule
{
    private $productId;

    private $quantity;

    public function __construct($productId, $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        dd('Hello');
        $product = Product::find($this->productId);

        if (! $product || $product?->stock?->stock < $this->quantity) {
            $fail('The product does not have enough stock');
        }
    }
}
