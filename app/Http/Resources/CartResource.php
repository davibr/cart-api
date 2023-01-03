<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CartResource extends JsonResource
{

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'cart';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->closed_at ? 'Closed' : 'Open',
            'closed_at' => $this->closed_at,
            'total' => $this->items->count() > 0 ? $this->items->sum('total') : 0,
            'items' => ItemResource::collection($this->items),
        ];
    }

}
