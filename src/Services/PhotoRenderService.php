<?php

namespace Photo\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
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
     * @var string
     */
    protected $class = 'card-img-top img img-responsive';
    /**
     * @var string
     */
    protected $style;

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
            $tag .= '<source media="(min-width:577px)" type="' . $mainUrls[1]['type'] . '" srcset="' . $mainUrls[1]['url'] . '">';
        }
        $thumbs = $this->getThumbnailUrls($photo->src);

        foreach ($thumbs as $thumb) {
            $tag .= '<source media="(max-width:576px)" type="' . $thumb['type'] . '" srcset="' . $thumb['url'] . '">';
        }

        $tag .= '<img src="' . $mainUrls[0]['url'] . '" alt="' . $photo->caption . '" class="' . $this->class . '" style="' . $this->style . '">';
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
            $tag .= '<img src="' . config('photo.default') . '" alt="Our Default Image source" class="card-img-top  img img-responsive">';
            $tag .= '</picture>';

            return $tag;
        }
        if (isset($thumbs[1])) {
            $tag .= '<source type="' . $thumbs[1]['type'] . '" srcset="' . $thumbs[1]['url'] . '">';
        }

        $tag .= '<img src="' . $thumbs[0]['url'] . '" alt="' . $photo->caption . '" class="' . $this->class . '" style="' . $this->style . '">';
        $tag .= '</picture>';

        return $tag;
    }

    /**
     * @param      $source
     *
     * @param bool $size
     *
     * @return array
     */
    public function getMainUrls($source, $size = false): array
    {
        $mainUrl = $this->storage->url($source);
        $sourceSets[] = [
            'type' => 'image/' . pathinfo($source, PATHINFO_EXTENSION),
            'url' => $mainUrl,
        ];

        if ($size) {
            $info = [];
            $info['size'] = round($this->storage->size($source) / 1000) . ' kb';
            $this->info[$mainUrl] = $info;
        }

        $pathInfo = pathinfo($source);
        $webP = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

        if ($this->exists($webP)) {
            $mainWebP = $this->storage->url($webP);
            $sourceSets[] = [
                'type' => 'image/webp',
                'url' => $mainWebP,
            ];

            if ($size) {
                $info = [];
                $info['size'] = round($this->storage->size($webP) / 1000) . ' kb';
                $this->info[$mainWebP] = $info;
            }
        }

        return $sourceSets;
    }

    /**
     * @param      $source
     *
     * @param bool $size
     *
     * @return array
     */
    public function getThumbnailUrls($source, $size = false): array
    {
        $sourceSets = [];
        $pathInfo = pathinfo($source);

        foreach (config('photo.sizes', []) as $name => $info) {
            $thumbWebPPath = $pathInfo['dirname'] . '/' . $info['path'] . '/' . $pathInfo['filename'] . '.webp';
            $thumbPath = $pathInfo['dirname'] . '/' . $info['path'] . '/' . $pathInfo['basename'];

            if ($this->exists($thumbPath)) {
                $sourceSets[] = [
                    'type' => 'image/' . $pathInfo['extension'],
                    'url' => $thumbUrl = $this->storage->url($thumbPath),
                ];

                if ($size) {
                    $info = [];
                    $info['size'] = round($this->storage->size($thumbPath) / 1000) . ' kb';
                    $this->info[$thumbUrl] = $info;
                }
            }

            if ($this->exists($thumbWebPPath)) {
                $thumbWUrl = $this->storage->url($thumbWebPPath);

                $sourceSets[] = [
                    'type' => 'image/webp',
                    'url' => $thumbWUrl,
                ];

                if ($size) {
                    $info = [];
                    $info['size'] = round($this->storage->size($thumbWebPPath) / 1000) . ' kb';
                    $this->info[$thumbWUrl] = $info;
                }
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
        $this->getMainUrls($source, true);
        $this->getThumbnailUrls($source, true);

        return $this->info;
    }

    /**
     * @param string $class
     *
     * @return \Photo\Services\PhotoRenderService
     */
    public function setClass(string $class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param string $style
     *
     * @return \Photo\Services\PhotoRenderService
     */
    public function setStyle(string $style)
    {
        $this->style = $style;

        return $this;
    }
}
