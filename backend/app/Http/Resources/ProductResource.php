<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_line_id' => $this->product_line_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'bienfaits' => $this->bienfaits,
            'composition' => $this->composition,
            'conseils_utilisation' => $this->conseils_utilisation,
            'skin_type' => $this->skin_type,
            'effet' => $this->effet,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'product_line' => new ProductLineResource($this->whenLoaded('productLine')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'primary_image' => $this->primaryImage?->image_url,
            'retail_variants' => ProductVariantResource::collection($this->whenLoaded('retailVariants')),
            'wholesale_variants' => ProductVariantResource::collection($this->whenLoaded('wholesaleVariants')),
            'min_price' => $this->when(isset($this->min_price), $this->min_price),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
