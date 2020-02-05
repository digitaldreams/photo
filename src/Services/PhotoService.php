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
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoService
{
    /**
     * @var Photo
     */
    protected $photo;
    protected $folder;

    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
        $this->folder = config('photo.rootPath', 'photos');
    }

    /**
     * @param Request $request
     * @param string $name
     * @return Photo
     * @throws \Exception
     */
    public function save(Request $request, $name = 'file')
    {

        if ($request->hasFile($name) || $request->get('imageID')) {
            if ($request->hasFile($name)) {
                $url = (new PhotoLib())->setFolder($this->folder)
                    ->crop($request->get('crop', 'yes'))
                    ->upload($request->file($name))
                    ->resize()
                    ->getUrls();
                if (!empty($url)) {
                    $this->photo->src = array_shift($url);
                }
                $placeApiData = $request->get('place_api_data');
                if (!empty($placeApiData)) {
                    $data = json_decode($placeApiData, true);
                    if (is_array($data)) {
                        $location = new Location();
                        $location->fill($data);
                        if ($location->save()) {
                            $this->photo->location_id = $location->id;
                        }
                    }
                }
            }
            if (empty($this->photo->caption)) {
                $this->photo->caption = null;
            }
            if (empty($this->photo->title)) {
                $this->photo->title = null;
            }

            $this->photo->save();
        }


        return $this->photo;
    }

    public static function isValid(Request $request, $name = 'file')
    {
        $valid = true;
        $files = $request->file($name);
        if (is_array($files)) {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    if (!in_array($file->getClientMimeType(), \Photo\Photo::$mimeTypes)) {
                        $valid = false;
                        break;
                    }
                }
            }
        } else {
            if (!in_array($files->getClientMimeType(), \Photo\Photo::$mimeTypes)) {
                $valid = false;
            }
        }
        return $valid;
    }


    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }
}