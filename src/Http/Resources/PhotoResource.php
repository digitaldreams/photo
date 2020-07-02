<?php

namespace Photo\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Photo\Services\PhotoRenderService;

class PhotoResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $photoRender = app(PhotoRenderService::class);

        return [
            'id' => $this->id,
            'user_id' => $this->id,
            'urls' => $photoRender->getMainUrls($this->src),
            'thumbnails' => $photoRender->getThumbnailUrls($this->src),
            'caption' => $this->caption,
        ];
    }
}
