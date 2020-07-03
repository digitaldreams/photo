<?php

namespace Photo\Services;

use Photo\Models\Photo;

class ExifDataService
{
    /**
     * @var array
     */
    protected $fillable = [
        'FileDateTime',
        'DateTimeOriginal',
        'FileSize',
        'FileType',
        'Orientation',
        'MimeType',
        'COMPUTED',
        'Model',
        'Make',
        'FocalLength',
        'ApertureValue',
        'ShutterSpeedValue',
        'ExposureTime',
        'Flash',
        'ISOSpeedRatings',
        'WhiteBalance',
    ];
    /**
     * @var \Photo\Models\Photo
     */
    protected $photo;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * ExifDataService constructor.
     *
     * @param Photo $photo
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
        $this->data = exif_read_data(storage_path('app/public/' . $this->photo->getSrc()));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_intersect_key($this->data, array_flip($this->fillable));
    }

    /**
     * Example coordinate values.
     *
     * Latitude - 49/1, 4/1, 2881/100, N
     * Longitude - 121/1, 58/1, 4768/100, W
     *
     * @param mixed $deg
     * @param mixed $min
     * @param mixed $sec
     * @param mixed $ref
     *
     * @return float|int
     */
    protected function toDecimal($deg, $min, $sec, $ref)
    {
        $float = function ($v) {
            return (count($v = explode('/', $v)) > 1) ? $v[0] / $v[1] : $v[0];
        };

        $d = $float($deg) + (($float($min) / 60) + ($float($sec) / 3600));

        return ('S' == $ref || 'W' == $ref) ? $d *= -1 : $d;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (is_array($this->data)) {
            $data = $this->toArray();
            if (is_array($data)) {
                $this->photo->exif = $data;
                $this->photo->taken_at = $data['DateTimeOriginal'] ?? null;
                $this->photo->save();
            }

            return true;
        }

        return false;
    }

    /**
     * @return array|null
     */
    public function getCoordinates()
    {
        $exif = $this->data;

        $coord = (isset($exif['GPSLatitude'], $exif['GPSLongitude'])) ? [
            'latitude' => sprintf('%.6f', $this->toDecimal($exif['GPSLatitude'][0], $exif['GPSLatitude'][1], $exif['GPSLatitude'][2], $exif['GPSLatitudeRef'])),
            'longitude' => sprintf('%.6f', $this->toDecimal($exif['GPSLongitude'][0], $exif['GPSLongitude'][1], $exif['GPSLongitude'][2], $exif['GPSLongitudeRef'])),
        ] : null;

        return $coord;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRawData()
    {
        return collect($this->data);
    }
}
