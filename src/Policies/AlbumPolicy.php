<?php

namespace Photo\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Photo\Models\Tag;

class AlbumPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     *
     * @return bool
     */
    public function before($user)
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function viewAny($user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the Album.
     *
     * @param User $user
     * @param Tag  $album
     *
     * @return mixed
     */
    public function view($user, Tag $album)
    {
        return $user->id == $album->user_id;
    }

    /**
     * Determine whether the user can create Album.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function create($user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the Album.
     *
     * @param User $user
     * @param Tag  $album
     *
     * @return mixed
     */
    public function update($user, Tag $album)
    {
        return $user->id == $album->user_id;
    }

    /**
     * Determine whether the user can delete the Album.
     *
     * @param User $user
     * @param Tag  $album
     *
     * @return mixed
     */
    public function delete($user, Tag $album)
    {
        return $user->id == $album->user_id;
    }
}
