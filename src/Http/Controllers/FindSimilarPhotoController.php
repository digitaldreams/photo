<?php


namespace Photo\Http\Controllers;


use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Photo\Models\Photo;
use Photo\Repositories\PhotoRepository;
use Photo\Services\PhotoRenderService;

class FindSimilarPhotoController
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected Filesystem $storage;
    /**
     * @var \Photo\Services\PhotoRenderService
     */
    protected PhotoRenderService $photoRenderService;
    /**
     * @var \Photo\Repositories\PhotoRepository
     */
    protected PhotoRepository $photoRepository;

    /**
     * FindSimilarPhotoController constructor.
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $storage
     * @param \Photo\Services\PhotoRenderService          $photoRenderService
     * @param \Photo\Repositories\PhotoRepository         $photoRepository
     */
    public function __construct(Filesystem $storage, PhotoRenderService $photoRenderService, PhotoRepository $photoRepository)
    {

        $this->storage = $storage;
        $this->photoRenderService = $photoRenderService;
        $this->photoRepository = $photoRepository;
    }

    public function similar(Photo $photo, Request $request, PhotoRenderService $photoRenderService)
    {
        return view('photo::pages.photos.similar', [
            'photoRender' => $this->photoRenderService,
            'original' => $photo,
            'records' => $this->photoRepository->compareSimilarities($photo, $request->get('distance', 25)),
        ]);
    }
}
