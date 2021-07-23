<?php
/**
 * User: Tuhin
 * Date: 2/21/2018
 * Time: 11:16 PM
 */

namespace Photo\Services;

use Illuminate\Support\Facades\Cache;

class FetchImagesFromUrlService
{

    /**
     * @var string HTML Tags
     */
    protected $htmlContent;

    /**
     * @var
     */
    protected $success = false;

    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var \DOMXpath
     */
    protected $xpath;

    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var
     */
    protected $url;

    /**
     * @var int
     */
    protected $resourceSize = 0;

    protected $host;

    protected $scheme;

    protected $doman;

    /**
     * PageAnalysis constructor.
     *
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $this->htmlContent = @file_get_contents($this->url, false, stream_context_create($arrContextOptions));

        if ($this->htmlContent) {
            $this->success = true;
            $this->dom = @new \DOMDocument('1.0', 'UTF-8');
            libxml_use_internal_errors(true);
            $this->dom->loadHTML($this->htmlContent);
            $this->xpath = new \DOMXpath($this->dom);
            libxml_clear_errors();
            $this->doman = $this->getDomain();
        }

    }

    /**
     * @return null|string|string[]
     */
    public function textContent()
    {
        return preg_replace('/^[ \t]*[\r\n]+/m', '', strip_tags($this->htmlContent));
    }

    /**
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return (bool)$this->success;
    }

    /**
     * @param bool $size
     *
     * @return $this
     */
    public function fetch(bool $size = true): self
    {
        $this->images = $this->images($size);

        return $this;
    }

    /**
     * @param int $minutes
     *
     * @return $this
     * @throws \Exception
     */
    public function save($minutes = 30): self
    {
        cache([$this->url => json_encode($this->images, JSON_UNESCAPED_SLASHES)], now()->addMinutes($minutes));
        return $this;
    }

    /**
     * Try to get result from cache if exists otherwise run fetch
     *
     * @return self
     */
    public function fromCache(bool $size = false): self
    {
        if (Cache::has($this->url)) {
            $this->images = json_decode(Cache::get($this->url), true);
        } else {
            $this->fetch($size)->save();
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->images;
    }


    /**
     * @param $url
     *
     * @return bool
     */
    protected function isInternal($url)
    {
        return parse_url($url, PHP_URL_HOST) == $this->host;
    }

    /**
     * @param bool $size
     *
     * @return array
     */
    protected function images($size = true)
    {
        $imgs = [];
        $retImgs = [];
        $images = $this->dom->getElementsByTagName('img');

        if ($images->length > 0) {
            foreach ($images as $image) {
                $img = [];
                foreach ($image->attributes as $attr) {
                    $img[$attr->name] = $attr->nodeValue;
                }
                $imgs[] = $img;
            }
            foreach ($imgs as $mg) {
                $mg['width'] = '';
                $mg['height'] = '';
                $mg['mime'] = '';

                if (isset($mg['src']) && !empty($mg['src'])) {
                    $mg['src'] = $this->linkBuilder($mg['src']);
                    $info = @getimagesize($mg['src']);
                    if (!empty($info)) {
                        $mg['width'] = $info[0] ?? '';
                        $mg['height'] = $info[1] ?? '';
                        $mg['mime'] = $info['mime'] ?? '';

                        if ($size) {
                            $sizeKb = round(static::fileSize($mg['src']) / 1000);
                            $this->resourceSize += $sizeKb;
                            $mg['size'] = $sizeKb;
                        }
                    }
                }
                $retImgs[] = $mg;
            }
        }
        return $retImgs;
    }

    /**
     * @param $url
     *
     * @return bool
     */
    protected function fileExists($url)
    {
        $file_headers = @get_headers($url);
        return $file_headers[0] == 'HTTP/1.1 404 Not Found' ? false : true;
    }

    protected function getDomain()
    {
        $ret = '';
        $parts = parse_url($this->url);
        if (isset($parts['scheme']) && !empty($parts['scheme'])) {
            $this->scheme = $parts['scheme'];
            $ret = $this->scheme . "://";
        }
        if (isset($parts['host']) && !empty($parts['host'])) {
            $this->host = $parts['host'];
            $ret .= $this->host;
        }
        return !empty($ret) ? $ret : url($this->url);
    }

    /**
     * Make partial url to absolute
     *
     * @param $url
     *
     * @return string
     */
    protected function linkBuilder($url)
    {
        $linkHost = parse_url($url, PHP_URL_HOST);
        return empty($linkHost) ? $this->doman . '/' . ltrim($url, "/") : $url;
    }

    public static function fileSize($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

            return $size;
        } catch (\Exception $e) {
            return false;
        }
    }
}
