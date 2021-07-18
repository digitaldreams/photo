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
    protected array
        $info = [];

    /**
     * @param \Photo\Models\Photo $photo
     *
     * @return string
     */
    public function render(Photo $photo): string
    {
        $tag = '<picture>';
        $mainUrls = $this->getMainUrls($photo);
        if (isset($mainUrls[1])) {
            $tag .= '<source media="(min-width:577px)" type="' . $mainUrls[1]['type'] . '" srcset="' . $mainUrls[1]['url'] . '">';
        }
        $thumbs = $this->getThumbnailUrls($photo->thumbnails);

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
        $thumbnails= !is_array($photo->thumbnails)?[$photo->thumbnails]:$photo->thumbnails;
        $tag = '<picture>';
        $thumbs = $this->getThumbnailUrls($thumbnails);
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
    public function getMainUrls(Photo $photo, $size = false): array
    {
        $mainUrl = $this->storage->url($photo->src);
        $sourceSets[] = [
            'type' => 'image/' . pathinfo($photo->src, PATHINFO_EXTENSION),
            'url' => $mainUrl,
        ];

        if (!empty($photo->src_webp) && $this->storage->exists($photo->src_webp)) {
            $mainWebP = $this->storage->url($photo->src_webp);
            $sourceSets[] = [
                'type' => 'image/webp',
                'url' => $mainWebP,
            ];
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
    public function getThumbnailUrls($sources, $size = false): array
    {
        $sourceSets = [];

        foreach ($sources as $dimensionFormat => $path) {

            if ($this->storage->exists($path)) {
                $sourceSets[] = [
                    'type' => 'image/' . pathinfo($path, PATHINFO_EXTENSION),
                    'url' => $thumbUrl = $this->storage->url($path),
                ];
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
    public function getUrls($source, array $thumbnails = []): array
    {
        return array_merge($this->getMainUrls($source), $this->getThumbnailUrls($thumbnails));
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
    public function getImageDetailsInfo(Photo $photo, array $thumbnails = []): array
    {
        $this->getMainUrls($photo, true);
        $this->getThumbnailUrls($photo->thumbnails, true);

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
