<?php

namespace Photo\Observers;

use Photo\Models\Photo;

class PhotoObserver
{
    /**
     * Handling the "creating" event of Photo.
     *
     * @param \Photo\Models\Photo $photo
     */
    public function creating(Photo $photo): void
    {
        if (empty($photo->user_id) && auth()->check()) {
            $photo->user_id = auth()->id();
        }
        if (empty($photo->status)) {
            $photo->status = Photo::STATUS_ACTIVE;
        }
    }
}
