<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'product_id' => $this->product_id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'individual_price' => $this->individual_price,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
