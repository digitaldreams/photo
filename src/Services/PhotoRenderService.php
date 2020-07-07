<?php

namespace Photo\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Photo\Models\Photo;

class PhotoRenderService
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private Filesystem $storage;

    /**
     * @var Photo
     */
    protected Photo $photo;

    /**
     * PhotoRenderService constructor.
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $storage
     */
    public function __construct(Filesystem $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @var array
     */
    protected array $info = [];

    /**
     * @param \Photo\Models\Photo $photo
     *
     * @return string
     */
    public function render(Photo $photo): string
    {
        $tag = '<picture>';
        $mainUrls = $this->getMainUrls($photo->src);
        if (isset($mainUrls[1])) {
            $tag .= '<source media="(min-width::992px)" srcset="'.$mainUrls[1].'">';
        }
        $thumbs = $this->getThumbnailUrls($photo->src);

        foreach ($thumbs as $thumb) {
            $tag .= '<source media="(min-width::576px)" srcset="'.$thumb.'">';
        }

        $tag .= '<img src="'.$mainUrls[0].'" alt="'.$photo->caption.'" class="img img-responsive">';
        $tag .= '</picture>';

        return $tag;
    }

    /**
     * @param \Photo\Models\Photo $photo
     *
     * @return string
     */
    public function renderThumbnails(Photo $photo): string
    {
        $tag = '<picture>';
        $thumbs = $this->getThumbnailUrls($photo->src);
        if (empty($thumbs)) {
            $tag .= '<img src="'.config('photo.default').'" alt="Our Default Image source" class="card-img-top  img img-responsive">';
            $tag .= '</picture>';

            return $tag;
        }
        if (isset($thumbs[1])) {
            $tag .= '<source srcset="'.$thumbs[1].'">';
        }

        $tag .= '<img src="'.$thumbs[0].'" alt="'.$photo->caption.'" class="card-img-top img img-responsive">';
        $tag .= '</picture>';

        return $tag;
    }

    /**
     * @param $source
     *
     * @return array
     */
    public function getMainUrls($source): array
    {
        $mainUrl = $this->storage->url($source);
        $sourceSets = [$mainUrl];
        $info = [];
        $info['size'] = round($this->storage->size($source) / 1000).' kb';
        $this->info[$mainUrl] = $info;

        $pathInfo = pathinfo($source);
        $webP = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';

        if ($this->exists($webP)) {
            $sourceSets[] = $mainWebP = $this->storage->url($webP);
            $info = [];
            $info['size'] = round($this->storage->size($webP) / 1000).' kb';
            $this->info[$mainWebP] = $info;
        }

        return $sourceSets;
    }

    /**
     * @param $source
     *
     * @return array
     */
    public function getThumbnailUrls($source): array
    {
        $sourceSets = [];
        $pathInfo = pathinfo($source);

        foreach (config('photo.sizes', []) as $name => $info) {
            $thumbWebPPath = $pathInfo['dirname'].'/'.$info['path'].'/'.$pathInfo['filename'].'.webp';
            $thumbPath = $pathInfo['dirname'].'/'.$info['path'].'/'.$pathInfo['basename'];
            Log::error($thumbPath);
            if ($this->exists($thumbPath)) {
                $sourceSets[] = $thumbUrl = $this->storage->url($thumbPath);
                $info = [];
                $info['size'] = round($this->storage->size($thumbPath) / 1000).' kb';
                $this->info[$thumbUrl] = $info;
            }
            if ($this->exists($thumbWebPPath)) {
                $sourceSets[] = $thumbWUrl = $this->storage->url($thumbWebPPath);
                $info = [];
                $info['size'] = round($this->storage->size($thumbWebPPath) / 1000).' kb';
                $this->info[$thumbWUrl] = $info;
            }
        }

        return $sourceSets;
    }

    /**
     * Get all possible URLs of a given image.
     *
     * @param $source
     *
     * @return array
     */
    public function getUrls($source): array
    {
        return array_merge($this->getMainUrls($source), $this->getThumbnailUrls($source));
    }

    /**
     * @param string $source
     *
     * @return bool
     */
    protected function exists(string $source)
    {
        return $this->storage->exists($source);
    }

    /**
     * @param string $source
     *
     * @return array
     */
    public function getImageDetailsInfo(string $source): array
    {
        $this->getMainUrls($source);
        $this->getThumbnailUrls($source);

        return $this->info;
    }
}
