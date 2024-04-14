<?php

namespace Photo\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use Photo\Models\Photo;
use Photo\Repositories\PhotoRepository;
use Photo\Services\PhotoService;

class PhotoProcessJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \Photo\Models\Photo
     */
    private Photo $photo;

    /**
     * @var null
     */
    private $type;

    /**
     * Create a new job instance.
     *
     * @param mixed|null $type
     *
     * @return void
     *
     */
    public function __construct(Photo $photo, $type = null)
    {
        $this->photo = $photo;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PhotoService $service, PhotoRepository $fileRepository, Filesystem $filesystem)
    {
        $image_webp_src = $service->convertMaxDimensionToWebP($this->photo->src);
        $thumbnails = $service->generateThumbnails($this->photo->src)->getSizes();

        $this->photo->src_webp = $image_webp_src;
        $this->photo->thumbnails = $thumbnails;
        try {
            $hasher = new ImageHash(new DifferenceHash());
            $this->photo->hash = $hasher->hash($filesystem->url($this->photo->src));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        $wh=[];
        try {
            $wh = getimagesize($filesystem->url($this->photo->src));
        }catch (\Exception $e){

        }

        $info[$filesystem->url($this->photo->src)] = [
            'size' => round($filesystem->size($this->photo->src) / 1000) . 'kb',
            'width' => $wh[0] ?? null,
            'height' => $wh[1] ?? null,
        ];
        $info[$filesystem->url($image_webp_src)] = [
            'size' => round($filesystem->size($image_webp_src) / 1000) . 'kb',
        ];
        foreach ($thumbnails as $name => $thumbnail) {
            $info[$filesystem->url($thumbnail)] = [
                'size' => round($filesystem->size($thumbnail) / 1000) . 'kb',
            ];
        }
        $this->photo->info = $info;
        $this->photo->save();
    }

    protected function generateInfo(Photo $photo)
    {

    }
}
