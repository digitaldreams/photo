<?php

namespace Photo\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;

class PhotoService
{
    /**
     * @var int
     */
    public int $quality = 70;

    /**
     * Dimensions.
     *
     * @var array [height,width]
     */
    protected array $dimensions = [
        [
            'height' => 64,
            'width' => 64,
        ],
    ];

    /**
     * @var array
     */
    protected array $maxDimension = [
        'height' => 512,
        'width' => 512,
    ];

    /**
     * @var array
     */
    protected array $formats = [
        'jpeg',
        'webp',
    ];

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
     * @var string
     */
    protected string $visibility = 'public';

    /**
     * ImageOptimizationService constructor.
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->storage = $filesystem;
        $this->image = ImageManager::gd();
        $this->maxDimension['height'] = config('photo.maxHeight', 450);
        $this->maxDimension['width'] = config('photo.maxWidth', 800);
        $this->dimensions = config('photo.dimensions', []);
    }

    /**
     * Convert, crop and finally upload image to s3 disk.
     *
     * @param string                        $path
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(string $path, UploadedFile $uploadedFile, ?string $fileName = null, $crop = null): string
    {
        $uniqueFileName = !empty($fileName) ? $this->getUniqueFileName($path, $fileName, $uploadedFile->guessExtension()) . '.' . $uploadedFile->guessExtension() : $uploadedFile->hashName($path);

        if ('yes' == $crop) {
            $fullPath = $this->resizeAndConvert(
                $uploadedFile,
                $path . '/' . $uniqueFileName,
                $this->maxDimension['width'],
                $this->maxDimension['height'],
                'jpeg'
            );
        } else {
            $fullPath = $this->storage->putFileAs($path, $uploadedFile, $uniqueFileName, 'public');
        }

        return $fullPath;
    }

    /**
     * Convert all defined dimensions into jpeg and webp.
     *
     * @param $source
     *
     * @return \Photo\Services\PhotoService
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function generateThumbnails(string $source): self
    {
        foreach ($this->dimensions as $dimension) {
            $name = sprintf('%dx%d', $dimension['width'], $dimension['height']);
            $pathInfo = pathinfo($source);
            foreach ($this->formats as $format) {
                $destination = sprintf('%s/%s-%s.%s', $pathInfo['dirname'], $pathInfo['filename'], $name, $format);
                $this->convertedSizes[$name . '-' . $format] = $this->resizeAndConvert(
                    $source,
                    $destination,
                    $dimension['width'],
                    $dimension['height'],
                    $format
                );
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
        $imageStream = $this->image->read($this->getImageSource($source))
            ->resizeDown($width, $height)
            ->toWebp( $this->quality);

        $this->storage->put($destination, $imageStream, $this->visibility);

        return $destination;
    }

    public function encode($source, string $destination, string $format)
    {
        $imageStream = $this->image->read($this->getImageSource($source))
            ->encodeByExtension($format, $this->quality);

        return $this->storage->put($destination, $imageStream, $this->visibility);
    }

    /**
     * store method will only resize and upload jpeg version but still we need to convert it webp format. This method
     * will do this.
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

        $this->encode($path, $destination, 'webp');

        return $destination;
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
     * @param string $format
     *
     * @return mixed|null
     */
    public function getSize(string $format = 'jpeg'): ?string
    {
        return $this->convertedSizes[$format] ?? null;
    }

    /**
     * Set max dimensions of original image.
     *
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function setMaxDimension(int $width, int $height): self
    {
        $this->maxDimension['width'] = $width;
        $this->maxDimension['height'] = $height;

        return $this;
    }

    /**
     * Set Thumbnails Size.
     *
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function setDimensions(int $width, int $height): self
    {
        $this->dimensions[] = [
            'width' => $width,
            'height' => $height,
        ];

        return $this;
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
    public function getUniqueFileName(string $path, string $fileName, string $extension = 'jpeg')
    {
        $name = $this->sanitizeFileName($fileName);
        $relativePath = $path . '/' . $name . '.' . $extension;
        if (!$this->storage->exists($relativePath)) {
            return $name;
        }

        return $name . '-' . uniqid();
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

}
