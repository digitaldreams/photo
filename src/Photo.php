<?php
/**
 * Created by PhpStorm.
 * User: digitaldreams
 * Date: 14/01/18
 * Time: 17:22
 */

namespace Photo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Photo\Library\Resize;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\Facades\Image as ImageLib;
use Illuminate\Support\Str;

class Photo
{
    /**
     * @var
     */
    protected $folder;

    /**
     * @var
     */
    protected $fullPath;

    /**
     * @var
     */
    protected $driver;
    /**
     * @var array
     */
    protected $urls = [];

    private $urlPrefix = '';
    protected $originalFilePath;

    protected $crop = 'yes';

    public static $mimeTypes = [
        'image/svg+xml',
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp'
    ];

    /**
     * Photo constructor.
     */
    public function __construct()
    {
        $this->folder = config('photo.rootPath', 'photos');
        $this->driver = config('photo.driver', 'public');

        //Creating root folder if not exists
        $this->makeRootPath();
    }

    public function crop($crop = 'yes')
    {
        $this->crop = $crop;
        return $this;
    }

    /**
     * @param $files
     * @return Photo
     */
    public function upload($files)
    {
        $ret = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $ret[] = $this->doUpload($file);
                }
            }
        } else {
            $ret[] = $this->doUpload($files);
        }
        $this->urls = $ret;
        return $this;
    }

    /**
     * @param string $size
     * @return $this
     * @throws \Exception
     */
    public function resize($size = '')
    {
        $sizes = !empty($size) ? [$size] : config('photo.sizes', []);
        $absUrls = $this->getAbsoluteUrls();
        foreach ($absUrls as $url) {
            if (pathinfo($url, PATHINFO_EXTENSION) == 'svg') {
                continue;
            }
            foreach ($sizes as $key => $size) {
                try {
                    $resize = (new Resize($url, $key))->crop($this->crop)->setFolder($this->folder);
                    $resize->save();
                } catch (\Exception $e) {
                    Log::error($e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
                    continue;
                }
            }
        }
        return $this;
    }

    /**
     * @param UploadedFile $document
     * @return bool|string
     */
    protected function doUpload(UploadedFile $document)
    {
        if ($document->isValid() && in_array($document->getClientMimeType(), static::$mimeTypes)) {
            $fileName = Str::slug(pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . rand(999, 9999) . '.' . $document->getClientOriginalExtension();
            $path = $document->storeAs($this->folder, $fileName, $this->driver);
            $rootPath = $this->getRootPath();
            $fullPath = rtrim($rootPath, "/") . "/" . $path;
            if ($this->crop == 'yes' && $document->getClientOriginalExtension() !== 'svg') {
                $this->resizeOriginal($fullPath);
            }
            $this->convertToWebP($fullPath);

            return $path;
        }
        return false;
    }

    /**
     *
     */
    private function makeRootPath()
    {
        if (in_array($this->driver, ['local', 'public'])) {
            if ($this->driver == 'public') {
                $this->urlPrefix = 'storage/';
            }
            $rootPath = $this->getRootPath();
            $this->fullPath = rtrim($rootPath, "/") . "/" . $this->folder;
            if (!file_exists($this->fullPath)) {
                mkdir($this->fullPath);
            }
        }
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getRootPath()
    {
        return config('filesystems.disks.' . $this->driver . '.root');
    }

    /**
     * Return Relative URL of the file
     * @return array
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @return array
     */
    public function getAbsoluteUrls()
    {
        $fullUrls = [];
        $urls = $this->urls;
        $rootPath = $this->getRootPath();
        foreach ($urls as $url) {
            $fullUrls[] = rtrim($rootPath, "/") . "/" . $url;
        }
        return $fullUrls;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * @param $path
     * @return bool
     */
    protected function resizeOriginal($path, $webp = true)
    {
        if (config('photo.compressSize') && config('photo.exif') == false) {
            $width = config('photo.maxWidth');
            $height = config('photo.maxHeight');

            $img = ImageLib::make($path);
            $originalHeight = $img->height();
            $originalWidth = $img->width();
            $canvas = false;
            if ($width > $originalWidth && $height > $originalHeight) {
            } elseif ($width > $originalWidth) {
                $width = null;
                $canvas = true;
            } elseif ($height > $originalHeight) {
                $height = null;
                $canvas = true;
            }

            if ($canvas) {
                $img->resize($width, $height, function ($constraint) {
                    //   $constraint->aspectRatio();
                    //  $constraint->upsize();
                });
                $img->resizeCanvas(config('photo.maxWidth'), config('photo.maxHeight'));
            }
            return $img->save();
        }
        return false;
    }

    /**
     * @param $path
     * @param int $quality
     * @return bool
     */
    public static function convertToWebP($path, $quality = 80)
    {
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'svg') {
            $info = pathinfo($path);
            $webP = $info['dirname'] . "/" . $info['filename'] . ".webp";
            ImageLib::make($path)->save($webP, $quality, 'webp');
            return true;
        }
        return false;
    }
}
