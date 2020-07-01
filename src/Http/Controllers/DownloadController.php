<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Photo\Library\Resize;
use Photo\Models\Photo;
use Photo\Services\ImageDownloadService;
use Photo\Services\PhotoService;

class DownloadController extends Controller
{
    /**
     * @var \Photo\Services\ImageDownloadService
     */
    protected ImageDownloadService $imageDownloadService;

    /**
     * DownloadController constructor.
     *
     * @param \Photo\Services\ImageDownloadService $imageDownloadService
     */
    public function __construct(ImageDownloadService $imageDownloadService)
    {
        $this->imageDownloadService = $imageDownloadService;
    }

    /**
     * Download Image from image source URL. e.g. https://example.com/image.png.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadUrl(Request $request)
    {
        try {
            $url = $request->get('url');
            $this->imageDownloadService->setImageUrl($url);

            $path = $this->imageDownloadService->download('images');
            $photo = new Photo();
            $photo->caption = null;
            $photo->src = 'images/' . pathinfo($path, PATHINFO_BASENAME);
            $photo->save();
            if ('svg' !== pathinfo($photo->src, PATHINFO_EXTENSION)) {
                if (!file_exists($storagePath . '/thumbnails')) {
                    mkdir($storagePath . '/thumbnails');
                }
                $resize = (new Resize($path, 'thumbnail'))->setPath($storagePath . '/thumbnails');
                $resize->save();
            }

            return response()->json([
                'file' => $photo->getFormat(),
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function dropzone(Request $request)
    {
        if ($request->file('file')->isValid() && PhotoService::isValid($request, 'file')) {
            $photo = new Photo();
            config([
                'photo.maxWidth' => 505,
                'photo.maxHeight' => 426,
            ]);
            $model = (new PhotoService($photo))->setFolder('products')->save($request);

            session()->flash('message', 'Photo saved');

            return response()->json([
                'file' => $model->getFormat(),
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
