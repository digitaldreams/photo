<?php

namespace Photo\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Photo\Http\Requests\Api\Photos\Index;
use Photo\Http\Requests\Api\Photos\Store;
use Photo\Http\Resources\PhotoResource;
use Photo\Repositories\PhotoRepository;

class PhotoController extends Controller
{
    /**
     * @var \Photo\Repositories\PhotoRepository
     */
    protected PhotoRepository $photoRepository;

    /**
     * PhotoController constructor.
     *
     * @param \Photo\Repositories\PhotoRepository $photoRepository
     */
    public function __construct(PhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    /**
     * Get List of Photos
     *
     * @param \Illuminate\Http\Request $index
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $index): AnonymousResourceCollection
    {
        $photos = $this->photoRepository->index(auth()->user(), $index->get('limit', 100), $index->get('q'));

        return PhotoResource::collection($photos);
    }

    /**
     * Store a Photo
     *
     * @param Store $request
     *
     * @return \Photo\Http\Resources\PhotoResource
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(Store $request): PhotoResource
    {
        $model = $this->photoRepository->create($request->file('file'), $request->get('caption'));

        return new PhotoResource($model);
    }
}
