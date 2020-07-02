<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Photo\Http\Requests\Photos\Download;
use Photo\Http\Requests\Photos\Dropzone;
use Photo\Models\Photo;
use Photo\Repositories\PhotoRepository;
use Photo\Services\ImageDownloadService;
use Photo\Services\PhotoService;

class DownloadController extends Controller
{
    /**
     * @var \Photo\Services\ImageDownloadService
     */
    protected ImageDownloadService $imageDownloadService;

    /**
     * @var
     */
    protected PhotoService $photoService;

    /**
     * @var \Photo\Repositories\PhotoRepository
     */
    protected PhotoRepository $photoRepository;

    /**
     * DownloadController constructor.
     *
     * @param \Photo\Services\ImageDownloadService $imageDownloadService
     * @param \Photo\Services\PhotoService         $photoService
     * @param \Photo\Repositories\PhotoRepository  $photoRepository
     */
    public function __construct(ImageDownloadService $imageDownloadService, PhotoService $photoService, PhotoRepository $photoRepository)
    {
        $this->imageDownloadService = $imageDownloadService;
        $this->photoService = $photoService;
        $this->photoRepository = $photoRepository;
    }

    /**
     * Download Image from image source URL. e.g. https://example.com/image.png.
     *
     * @param \Photo\Http\Requests\Photos\Download $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadUrl(Download $request)
    {
        try {
            $url = $request->get('url');
            $this->imageDownloadService->setImageUrl($url);

            $path = $this->imageDownloadService->download('images');
            $photo = new Photo();
            $photo->caption = null;
            $photo->src = $path;
            $photo->save();
            $this->photoService->convertMaxDimensionToWebP($path);
            foreach (config('photo.sizes') as $name => $info) {
                $this->photoService->setDimension($info['width'], $info['height'], $info['path']);
            }
            $this->photoService->convert($path);

            return response()->json([
                'file' => $photo->getUrl(),
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
     * @param \Photo\Http\Requests\Photos\Dropzone $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function dropzone(Dropzone $request)
    {
        if ($request->file('file')->isValid()) {

            $model = $this->photoRepository->create($request->file('file'));

            session()->flash('message', 'Photo saved');

            return response()->json([
                'file' => $model->getUrl(),
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
