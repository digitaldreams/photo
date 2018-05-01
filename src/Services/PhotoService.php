<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 2/7/2018
 * Time: 10:49 PM
 */

namespace Photo\Services;


use Illuminate\Http\Request;
use Photo\Models\Location;
use Photo\Models\Photo;
use Photo\Photo as PhotoLib;

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

    /**
     * @param Request $request
     * @return Photo
     * @throws \Exception
     */
    public function save(Request $request)
    {
        if ($request->hasFile('file')) {
            $url = (new PhotoLib())->upload($request->file('file'))->resize()->getUrls();
            if (!empty($url)) {
                $this->photo->src = array_shift($url);
            }
            if ($request->has('place_api_data')) {
                $data = json_decode($request->get('place_api_data'), true);
                if (is_array($data)) {
                    $location = new Location();
                    $location->fill($data);
                    if ($location->save()) {
                        $this->photo->location_id = $location->id;
                    }
                }
            }
        }

        $this->photo->save();
        return $this->photo;
    }
}