<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'type' => $this->type,
            'name' => $this->name,
            'size_label' => $this->size_label,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'formatted_price' => number_format($this->price, 0, ',', ' '),
            'weight' => $this->weight,
            'image' => $this->image,
            'is_active' => $this->is_active,
            'is_on_sale' => !is_null($this->sale_price),
            'stock' => new StockResource($this->whenLoaded('stock')),
            'in_stock' => $this->stock?->is_in_stock ?? false,
            'quantity' => $this->stock?->quantity ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
