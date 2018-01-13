<?php

namespace Photo\Policies;

use \Photo\Models\Album;
use Permit\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function before(User $user)
    {
        //return true if user has super power
    }

        /**
     * @param User $user
     * @return bool
     */
    public function index(User $user)
    {
        return true;
    }
    /**
     * Determine whether the user can view the Album.
     *
     * @param  User  $user
     * @param  Album  $album
     * @return mixed
     */
    public function view(User $user, Album  $album)
    {
        return true;
    }
    /**
     * Determine whether the user can create Album.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }
    /**
     * Determine whether the user can update the Album.
     *
     * @param User $user
     * @param  Album  $album
     * @return mixed
     */
    public function update(User $user, Album  $album)
    {
        return true;
    }
    /**
     * Determine whether the user can delete the Album.
     *
     * @param User  $user
     * @param  Album  $album
     * @return mixed
     */
    public function delete(User $user, Album  $album)
    {
        return true;
    }

}
