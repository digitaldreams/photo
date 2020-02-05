<?php

namespace Photo\Library;

use Intervention\Image\Facades\Image as ImageLib;
use Intervention\Image\Image;
use Photo\Photo;
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

    protected $folder;
    /**
     * @var
     */
    protected $thumbnailPath;

    protected $crop = 'yes';

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

    public function crop($crop = 'yes')
    {
        $this->crop = $crop;
        return $this;
    }

    /**
     * Resize an image
     *
     * @return  Image
     */
    public function save()
    {
        $img = ImageLib::make($this->filePath);
        $originalHeight = $img->height();
        $originalWidth = $img->width();
        $width = $this->width;
        $height = $this->height;
        $canvas = false;
        if ($width > $originalWidth && $this->height > $originalHeight) {

        } elseif ($this->width > $originalWidth) {
            $width = null;
            $canvas = true;
        } elseif ($height > $originalHeight) {
            $height = null;
            $canvas = true;
        }
        if ($this->crop == 'yes') {
            $img->resize($width, $height, function ($constraint) {
                //  $constraint->aspectRatio();
                //  $constraint->upsize();
            });
            if ($canvas) {
                $img->resizeCanvas($this->width, $this->height);
            }
        }
        if (!file_exists($this->path)) {
            $this->path = $this->getFullPath($this->folder) . '/' . $this->thumbnailPath;
        }


        if (!empty($this->path)) {
            $ig = $img->save($this->path . "/" . $this->getBaseName());
            Photo::convertToWebP($this->path . "/" . $this->getBaseName());
            return $ig;
        }
        Photo::convertToWebP($this->filePath);
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

        if (isset($sizes[$size])) {
            $this->width = isset($sizes[$size]['width']) ? $sizes[$size]['width'] : null;
            $this->height = isset($sizes[$size]['height']) ? $sizes[$size]['height'] : null;
            $this->thumbnailPath = $sizes[$size]['path'] ?? null;

        } else {
            $this->width = config('photo.maxWidth');
            $this->height = config('photo.maxHeight');
        }
        return $this;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * @param string $folder
     * @return string
     */
    protected function getFullPath($folder = '')
    {
        $path = '';
        $driver = config('photo.driver');
        $rootPath = config('filesystems.disks.' . $driver . '.root');
        $folder = empty($folder) ? config('photo.rootPath', 'photos') : $folder;

        if (!empty($folder)) {
            $path = rtrim($rootPath, "/") . "/" . $folder;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }
        return $path;
    }
}