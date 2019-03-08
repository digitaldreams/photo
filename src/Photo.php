<?php
/**
 * Created by PhpStorm.
 * User: digitaldreams
 * Date: 14/01/18
 * Time: 17:22
 */

namespace Photo;


use Illuminate\Support\Facades\Log;
use Photo\Library\Resize;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\Facades\Image as ImageLib;

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
                    $ret[] = $this->doUpload();
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
            foreach ($sizes as $key => $size) {
                try {
                    $resize = (new Resize($url, $key))->setFolder($this->folder);
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
        if ($document->isValid()) {
            $fileName = uniqid(rand(1000, 99999)) . '.' . $document->getClientOriginalExtension();
            $path = $document->storeAs($this->folder, $fileName, $this->driver);
            $rootPath = $this->getRootPath();
            $fullPath = rtrim($rootPath, "/") . "/" . $path;
            $this->resizeOriginal($fullPath);
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
    protected function resizeOriginal($path)
    {
        if (config('photo.compressSize') && config('photo.exif') == false) {
            $width = config('photo.maxWidth');
            $height = config('photo.maxHeight');
            $img = ImageLib::make($path);
            $img->fit($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            return $img->save();
        }
        return false;
    }
}