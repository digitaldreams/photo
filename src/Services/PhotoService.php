<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 2/7/2018
 * Time: 10:49 PM.
 */

namespace Photo\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class PhotoService
{
    /**
     * @var int
     */
    protected int $quality = 80;

    /**
     * @var string
     */
    protected string $imageSource;

    /**
     * Dimensions.
     *
     * @var array [height,width]
     */
    protected array $dimensions = [];

    /**
     * @var array
     */
    protected array $maxDimension = [];

    /**
     * @var array
     */
    protected array $formats = ['webp'];

    /**
     * @var array
     */
    protected array $convertedSizes = [];

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private Filesystem $storage;

    /**
     * @var \Intervention\Image\ImageManager
     */
    protected ImageManager $image;

    /**
     * PhotoService constructor.
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $filesystem
     * @param \Intervention\Image\ImageManager            $imageManager
     */
    public function __construct(Filesystem $filesystem, ImageManager $imageManager)
    {
        $this->storage = $filesystem;
        $this->image = $imageManager;

        $this->maxDimension['height'] = config('photo.maxHeight', 450);
        $this->maxDimension['width'] = config('photo.maxWidth', 800);
    }

    /**
     * Convert, crop and finally upload image.
     *
     * @param string                        $path
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @param string|null                   $fileName
     * @param null                          $crop
     *
     * @return \Photo\Services\PhotoService
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(string $path, UploadedFile $uploadedFile, ?string $fileName = null, $crop = null): PhotoService
    {
        $uniqueFileName = !empty($fileName) ? $this->getUniqueFileName($path, $fileName) . '.' . $uploadedFile->guessExtension() : $uploadedFile->hashName($path);

        if ('yes' == $crop) {
            $this->imageSource = $this->resizeAndConvert($uploadedFile, $path . '/' . $uniqueFileName, $this->maxDimension['width'], $this->maxDimension['height'], $uploadedFile->guessExtension());
        } else {
            $this->imageSource = $this->storage->putFileAs($path, $uploadedFile, $uniqueFileName, 'public');
        }
        $this->convertMaxDimensionToWebP($this->imageSource);

        return $this;
    }

    /**
     * Convert all defined dimensions into jpeg and webp.
     *
     * @param $source
     *
     * @return self
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function convert(?string $source = null): self
    {
        $source = $source ?: $this->imageSource;
        foreach ($this->dimensions as $path => $dimension) {
            $pathInfo = pathinfo($source);
            $this->formats[] = $pathInfo['extension'];

            foreach ($this->formats as $format) {
                $destination = sprintf('%s/%s.%s', $pathInfo['dirname'] . '/' . $path, $pathInfo['filename'], $format);
                $this->convertedSizes[$format][$path] = $this->resizeAndConvert($source, $destination, $dimension['width'], $dimension['height'], $format);
            }
        }

        return $this;
    }

    /**
     * Crop image into a given width and height and finally save a format.
     *
     * @param string|UploadedFile $source
     * @param string              $destination
     * @param int                 $width
     * @param int                 $height
     * @param string              $format
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function resizeAndConvert($source, string $destination, int $width, int $height, string $format): string
    {
        $imageStream = $this->image->make($this->getImageSource($source))
            ->fit($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($format, $this->quality)->stream();

        $this->storage->put($destination, $imageStream, 'public');

        return $destination;
    }

    /**
     * store method will only resize and upload jpeg version but still we need to convert it webp format. This method will do this.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function convertMaxDimensionToWebP(string $path): string
    {
        $pathInfo = pathinfo($path);
        $destination = sprintf('%s/%s.%s', $pathInfo['dirname'], $pathInfo['filename'], 'webp');

        return $this->resizeAndConvert($path, $destination, $this->maxDimension['width'], $this->maxDimension['height'], 'webp');
    }

    /**
     * Get image stream.
     *
     * @param string|UploadedFile $source
     *
     * @return \Illuminate\Http\UploadedFile|resource
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getImageSource($source)
    {
        if (is_string($source)) {
            return $this->storage->readStream($source);
        }
        if ($source instanceof UploadedFile) {
            return $source;
        }
        throw new \Exception('Source can be either a file path or UploadedFile Object.');
    }

    /**
     * Set original image height and width.
     *
     * @param int $width
     * @param int $height
     *
     * @return \Photo\Services\PhotoService
     */
    public function setMaxDimension(int $width, int $height): self
    {
        $this->maxDimension = [
            'width' => $width,
            'height' => $height,
        ];

        return $this;
    }

    /**
     * Set a Dimension e.g. thumbnails 64px x 64px.
     *
     * @param int    $width
     * @param int    $height
     * @param string $folder
     *
     * @return \Photo\Services\PhotoService
     */
    public function setDimension(int $width, int $height, string $folder): self
    {
        $this->dimensions[$folder] = [
            'height' => $height,
            'width' => $width,
        ];

        return $this;
    }

    /**
     * Get list of all sizes.
     *
     * @return array
     */
    public function getSizes(): array
    {
        return $this->convertedSizes;
    }

    /**
     * Get a specific image url based on size and format.
     *
     * @param        $size
     * @param string $format
     *
     * @return mixed|null
     */
    public function getSize($size, $format = 'jpeg'): string
    {
        return $this->convertedSizes[$size][$format] ?? null;
    }

    /**
     * Delete the original file.
     *
     * @param string $source
     *
     * @return self
     */
    public function deleteOriginal(string $source): self
    {
        $this->storage->delete($source);

        return $this;
    }

    /**
     * Get path of newly stored image.
     *
     * @return string
     */
    public function getStoredImagePath(): string
    {
        return $this->imageSource;
    }

    /**
     * Get full image source url.
     *
     * @return mixed
     */
    public function getStoredImageUrl()
    {
        return $this->storage->url($this->imageSource);
    }

    /**
     * @param string $fileName
     *
     * @return \Illuminate\Support\Stringable
     */
    protected function sanitizeFileName(string $fileName)
    {
        return Str::of($fileName)
            ->lower()
            ->replaceMatches('/[^a-z0-9-\s]+/', '')
            ->replaceMatches('/[\s-]+/', '-');
    }

    /**
     * Generate caption based Unique file name.
     *
     * @param string $path
     * @param string $fileName
     * @param string $extension
     *
     * @return string
     */
    public function getUniqueFileName(string $path, string $fileName, $extension = 'jpeg')
    {
        $name = $this->sanitizeFileName($fileName);
        $relativePath = $path . '/' . $name . '.' . $extension;
        if (!$this->storage->exists($relativePath)) {
            return $name;
        }

        return $name . '-' . uniqid();
    }
}
