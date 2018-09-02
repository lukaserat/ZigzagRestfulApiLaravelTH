<?php

namespace App\Http\Resource;

use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->apiResponseItem();
    }

    /**
     * @return array
     */
    public function apiResponseItem() {
        // TODO: implement formatting based on model type
        return $this->resource->toArray();
    }
}