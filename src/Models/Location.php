<?php

namespace Photo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string                                   $name       name
 * @property string                                   $place_id   place id
 * @property string                                   $address    address
 * @property string                                   $locality   locality
 * @property string                                   $city       city
 * @property string                                   $state      state
 * @property string                                   $country    country
 * @property \Carbon\Carbon                           $created_at created at
 * @property \Carbon\Carbon                           $updated_at updated at
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
        'longitude',
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
