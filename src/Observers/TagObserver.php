<?php

namespace Photo\Observers;

use Photo\Models\Tag;

class TagObserver
{
    /**
     * Handling the "creating" event of Photo.
     *
     * @param \Photo\Models\Tag $album
     */
    public function creating(Tag $album): void
    {
        if (empty($album->user_id) && auth()->check()) {
            $album->user_id = auth()->id();
        }
    }
}
