<?php

namespace Photo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property varchar $name name
 * @property varchar $place_id place id
 * @property varchar $address address
 * @property varchar $locality locality
 * @property varchar $city city
 * @property varchar $state state
 * @property varchar $country country
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property \Illuminate\Database\Eloquent\Collection $photoPhoto hasMany
 */
class Location extends Model
{

    /**
     * Database table name
     */
    protected $table = 'photo_locations';
    
    /**
     * Protected columns from mass assignment
     */
    protected $fillable = [
        'name',
        'place_id',
        'address',
        'locality',
        'city',
        'state',
        'country',
        'latitude',
        'longitude'
    ];


    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * photoPhotos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, 'location_id');
    }

    /**
     * name column mutator.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = htmlspecialchars($value);
    }
}