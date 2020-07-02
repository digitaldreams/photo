<?php


namespace Photo\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Photo\Models\Photo;
use Photo\Services\PhotoService;

class PhotoRepository
{
    /**
     * @var \Photo\Models\Photo
     */
    protected Photo $photo;

    /**
     * @var \Photo\Services\PhotoService
     */
    protected PhotoService $photoService;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private Filesystem $storage;

    /**
     * PhotoRepository constructor.
     *
     * @param \Photo\Models\Photo                         $photo
     * @param \Photo\Services\PhotoService                $photoService
     * @param \Illuminate\Contracts\Filesystem\Filesystem $storage
     */
    public function __construct(Photo $photo, PhotoService $photoService, Filesystem $storage)
    {
        $this->photo = $photo;
        $this->photoService = $photoService;
        $this->storage = $storage;
    }

    /**
     * Create a new Photo
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array                         $data
     *
     * @return \Photo\Models\Photo
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create(UploadedFile $file, array $data = []): Photo
    {
        $caption = $data['caption'] ?? null;
        $this->photo->src = $this->uploadAndGenerateThumbnails($file, $caption);
        $this->photo->caption = $caption ?: $file->getClientOriginalName();
        $this->photo->mime_type = $this->storage->mimeType($this->photo->src);
        $this->photo->save();

        return $this->photo;
    }

    /**
     * Update an Existing Photo Model.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param \Photo\Models\Photo           $photo
     * @param string|null                   $caption
     *
     * @return \Photo\Models\Photo
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(Photo $photo, ?string $caption = null, ?UploadedFile $file = null): Photo
    {
        if ($file) {
            $photo->src = $this->uploadAndGenerateThumbnails($file, $caption);
            $photo->mime_type = $this->storage->mimeType($photo->src);
        }

        $photo->caption = $caption ?: $file->getClientOriginalName();
        $photo->save();

        return $photo;
    }

    /**
     * Remove Photo Records and delete images from storage.
     *
     * @param \Photo\Models\Photo $photo
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Photo $photo)
    {
        if ($this->storage->exists($photo->src)) {
            $this->storage->delete($photo->src);
            $pathInfo = pathinfo($photo->src);

            foreach (config('photo.sizes', []) as $name => $info) {
                $thumbnails = $pathInfo['dirname'] . '/' . $info['path'] . '/' . $pathInfo['basename'];
                if ($this->storage->exists($thumbnails)) {
                    $this->storage->delete($thumbnails);
                }
            }
        }
        return $photo->delete();
    }

    /**
     * @param             $file
     * @param             $caption
     *
     * @param string|null $crop
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function uploadAndGenerateThumbnails($file, $caption, string $crop = null)
    {
        $thumbnail = config('photo.sizes.thumbnail');

        return $this->photoService->setDimension($thumbnail['width'], $thumbnail['height'], $thumbnail['path'])
            ->store(config('photo.rootPath', 'images'), $file, $caption, $crop)
            ->convert()
            ->getStoredImagePath();
    }

    /**
     * Get photos by User
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param int                              $perPage
     * @param string|null                      $search
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(User $user, int $perPage = 100, ?string $search = null)
    {
        return $this->photo->newQuery()
            ->where('user_id', $user->id)
            ->when($search, function ($query) use ($search) {
                $query->q($search);
            })->paginate($perPage);

    }

}
