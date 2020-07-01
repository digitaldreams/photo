<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 2/7/2018
 * Time: 10:53 PM.
 */

namespace Photo\Services;

use GooglePlace\Services\Geocoding;
use GooglePlace\Services\Place;
use Photo\Models\Location;

class LocationService
{
    /**
     * @var Location;
     */
    protected $location;

    protected $place_id;

    protected $address = '';

    public function __construct(array $data)
    {
        $this->address = isset($data['address']) ? $data['address'] : false;
        $this->place_id = isset($data['place_id']) ? $data['place_id'] : false;
    }

    public function fetchPlaceDetails()
    {
        if (!empty($this->place_id)) {
            $place = new Place(['placeid' => $this->place_id]);
            $place->get();
            print_r($place->address());
        } elseif (!empty($this->address)) {
            $geocoding = new Geocoding(['address' => $this->address]);
            print_r($geocoding);
        }
    }

    public function save()
    {
    }
}
