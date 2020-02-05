<?php

namespace Photo\Policies;

use Photo\Models\Photo;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class PhotoPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
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
     * @return bool
     */
    public function index($user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the Album.
     *
     * @param User $user
     * @param Photo $photo
     * @return mixed
     */
    public function view($user, Photo $photo)
    {
        return false;
    }

    /**
     * Determine whether the user can create Album.
     *
     * @param User $user
     * @param Photo $photo
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
     * @param Photo $photo
     * @return mixed
     */
    public function update($user, Photo $photo)
    {
        return $user->id == $photo->user_id;
    }

    /**
     * Determine whether the user can delete the Album.
     *
     * @param User $user
     * @param Photo $photo
     * @return mixed
     */
    public function delete($user, Photo $photo)
    {
        return $user->id == $photo->user_id;
    }

}
