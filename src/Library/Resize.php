<?php

namespace Photo\Library;

use Intervention\Image\Facades\Image as ImageLib;
use Intervention\Image\Image;
use Storage;

class Resize
{
    protected $filePath;
    /**
     * @var
     */
    protected $width;
    /**
     * @var
     */
    protected $height;

    /**
     * @var
     */
    protected $path;

    /**
     * Resize constructor.
     * @param $filePath
     * @param string $size
     * @throws \Exception
     */
    public function __construct($filePath, $size = "thumbnail")
    {
        if (!file_exists($filePath)) {
            throw new \Exception($filePath . ' does not exists');
        }
        $this->filePath = $filePath;
        $this->setSize($size);

    }

    /**
     * Resize an image
     *
     * @return  Image
     */
    public function save()
    {
        $img = ImageLib::make($this->filePath);

        $img->fit($this->width, $this->height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        if (!empty($this->path)) {
            return $img->save($this->path . "/" . $this->getBaseName());
        }
        return $img->save();
    }

    /**
     * @return mixed
     */
    public function getBaseName()
    {
        return pathinfo($this->filePath, PATHINFO_BASENAME);
    }

    /**
     * @param $size
     * @return $this
     */
    private function setSize($size)
    {
        $sizes = config('photo.sizes');
        $driver = config('photo.driver');
        $rootPath = config('filesystems.disks.' . $driver . '.root');

        if (isset($sizes[$size])) {
            $this->width = isset($sizes[$size]['width']) ? $sizes[$size]['width'] : null;
            $this->height = isset($sizes[$size]['height']) ? $sizes[$size]['height'] : null;
            $path = isset($sizes[$size]['path']) ? $sizes[$size]['path'] : null;

            if (!empty($path)) {
                $this->path = rtrim($rootPath, "/") . "/" . $path;
                if (!file_exists($this->path)) {
                    mkdir($this->path,0777,true);
                }
            }
        } else {
            $this->width = config('photo.maxWidth');
            $this->height = config('photo.maxHeight');
        }
        return $this;
    }
}