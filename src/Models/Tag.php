<?php

namespace Photo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int                                      $user_id     user id
 * @property string                                   $name        name
 * @property \Carbon\Carbon                           $created_at  created at
 * @property \Carbon\Carbon                           $updated_at  updated at
 * @property \Illuminate\Database\Eloquent\Collection $photos  belongsToMany
 */
class Tag extends Model
{
    /**
     * Database table name.
     */
    protected $table = 'photo_tags';
    /**
     * Protected columns from mass assignment.
     */
    protected $fillable = ['name'];

    /**
     * photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'photo_tag');
    }
}
