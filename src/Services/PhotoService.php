<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 2/7/2018
 * Time: 10:49 PM
 */

namespace Photo\Services;


use Photo\Models\Photo;

class PhotoService
{
    /**
     * @var Photo
     */
    protected $photo;

    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    public function save(array $data)
    {

    }
}