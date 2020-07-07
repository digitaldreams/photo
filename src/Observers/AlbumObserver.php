<?php

namespace Photo\Observers;

use Photo\Models\Album;

class AlbumObserver
{
    /**
     * Handling the "creating" event of Photo.
     *
     * @param \Photo\Models\Album $album
     */
    public function creating(Album $album): void
    {
        if (empty($album->user_id) && auth()->check()) {
            $album->user_id = auth()->id();
        }
    }
}
