<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 2/7/2018
 * Time: 10:53 PM
 */

namespace Photo\Services;


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

    public function save()
    {

    }
}