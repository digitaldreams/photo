<?php

namespace Photo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id user id
 * @property varchar $name name
 * @property varchar $description description
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property \Illuminate\Database\Eloquent\Collection $albumphoto belongsToMany
 */
class Album extends Model
{

    /**
     * Database table name
     */
    protected $table = 'photo_albums';
    /**
     * Protected columns from mass assignment
     */
    protected $guarded = ['id'];


    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * albumphotos
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'album_photo');
    }

    /**
     * name column mutator.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = htmlspecialchars($value);
    }

    /**
     * description column mutator.
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = htmlspecialchars($value);
    }


}