<?php

namespace Photo\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Photo\Models\Photo;
use Photo\Services\ExifDataService;

class GetExifDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    /**
     * @var Photo
     */
    private $photo;
    private $driver;

    /**
     * Create a new job instance.
     *
     * @param Photo $photo
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
        $this->driver = config('photo.driver', 'public');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fullPath = rtrim($this->getRootPath(), '/').'/'.$this->photo->src;
        $exifServie = new ExifDataService($this->photo);
        $data = $exifServie->toArray();
        if ($location = $exifServie->location()) {
            $this->photo->location_id = $location->id;
        }
        if (!empty($data)) {
            $this->photo->exif = array_intersect_key($data, array_flip(['FileSize', 'Make', 'Model', 'COMPUTED']));
            $this->photo->captured_at = $data['DateTime'] ?? null;
        }
        $this->photo->save();
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getRootPath()
    {
        return config('filesystems.disks.'.$this->driver.'.root');
    }
}
