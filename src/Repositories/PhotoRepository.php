<?php

namespace Photo\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Jenssegers\ImageHash\Hash;
use Photo\Jobs\PhotoProcessJob;
use Photo\Models\Photo;
use Photo\Services\PhotoService;
use Photo\Services\SortRelevantImage;

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
     * Create a new Photo.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array                         $data
     *
     * @return \Photo\Models\Photo
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create(UploadedFile $file, array $data = []): Photo
    {
        $caption = $data['caption'] ?? null;
        $crop = $data['crop'] ?? null;
        $this->photo->src = $this->photoService->store(config('photo.rootPath', 'images'), $file, $caption, $crop);
        $this->photo->caption = $caption ?: $file->getClientOriginalName();
        $this->photo->mime_type = $this->storage->mimeType($this->photo->src);
        $this->photo->save();
        dispatch(new PhotoProcessJob($this->photo));

        return $this->photo;
    }

    /**
     * Update an Existing Photo Model.
     *
     * @param \Photo\Models\Photo           $photo
     * @param string|null                   $caption
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param null                          $crop
     *
     * @return \Photo\Models\Photo
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(Photo $photo, ?string $caption = null, ?UploadedFile $file = null, $crop = null): Photo
    {
        if ($file) {
            $oldPhoto = $photo->src;
            $photo->src = $this->photoService->store(config('photo.rootPath', 'images'), $file, $caption, $crop);
            $photo->mime_type = $this->storage->mimeType($photo->src);
            $photo->save();
            $this->removeFiles($oldPhoto);
            dispatch(new PhotoProcessJob($photo));
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
     *
     * @throws \Exception
     */
    public function delete(Photo $photo)
    {
        $this->removeFiles($photo->src);

        return $photo->delete();
    }

    /**
     * Remove all associated images of a given image from the disk.
     *
     * @param string $source
     *
     * @return \Photo\Repositories\PhotoRepository
     */
    public function removeFiles(string $source): self
    {
        if ($this->storage->exists($source)) {
            $this->storage->delete($source);
        }

        return $this;
    }

    /**
     * Get photos by User.
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

    public function findSimilarids(Photo $photo, $total = 10)
    {
        $returnIds = [];
        $records = DB::select("SELECT id, HEX(hash), BIT_COUNT(hash ^ '$photo->hash') as score
            FROM photo_photos  order by score asc limit $total");
        foreach ($records as $record) {
            $returnIds[] = $record->id;
        }
        return $returnIds;
    }

    public function findSimilar(Photo $photo): Builder
    {
        return Photo::query()->whereNotNull('hash')
            ->selectRaw("*, HEX(hash), BIT_COUNT(hash ^ '$photo->hash') as score")
            ->whereNotIn('id', [$photo->id])
            ->orderByRaw('score asc');
    }

    public function compareSimilarities(Photo $photo, $distance = 20)
    {
        $matching = new SortRelevantImage();
        $hash = Hash::fromHex($photo->hash);
        $photos = $this->findSimilar($photo)->take(100)->get();

        foreach ($photos as $img) {
            $hash2 = Hash::fromHex($img->hash);
            $diff = $hash->distance($hash2);
            if ($diff <= $distance) {
                $img->score = $diff;
                $matching->insert($img);
            }
            unset($hash2);
        }

        return $matching;
    }
}
