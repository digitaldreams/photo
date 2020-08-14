<?php

namespace Photo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int                                      $user_id     user id
 * @property string                                   $name        name
 * @property string                                   $description description
 * @property \Carbon\Carbon                           $created_at  created at
 * @property \Carbon\Carbon                           $updated_at  updated at
 * @property \Illuminate\Database\Eloquent\Collection $albumphoto  belongsToMany
 */
class Album extends Model
{
    /**
     * Database table name.
     */
    protected $table = 'photo_albums';
    /**
     * Protected columns from mass assignment.
     */
    protected $fillable = ['name', 'description'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $userModel = config('auth.providers.users.model');

        return $this->belongsTo($userModel);
    }

    /**
     * photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'album_photo');
    }
}
