<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 4/4/2019
 * Time: 5:55 PM
 */

namespace Photo\Services;


use Illuminate\Support\Str;

class ImageDownload
{
    private $image;
    private $path;

    public function __construct($image, $path)
    {
        $this->image = trim($image);
        $this->path = $path;
    }

    private function getExtension($contentType)
    {
        $extension = '';
        switch ($contentType) {
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/jpeg':
                $extension = 'jpeg';
                break;
            default:
                $extension = 'jpg';
                break;
        }
        return $extension;
    }

    public function download()
    {
        $ch = curl_init($this->image);
        $headers = $this->exists();
        $contentType = $headers['Content-Type'] ?? '';
        $fileName = Str::random(32) . '.' . $this->getExtension($contentType);
        $path = $this->path . '/' . $fileName;
        $fp = fopen($path, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $path;
    }

    /**
     * @return bool|array
     */
    public function exists()
    {
        $file_headers = @get_headers($this->image, 1);
        if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden') {
            return false;
        } else {
            return $file_headers;
        }
    }
}
