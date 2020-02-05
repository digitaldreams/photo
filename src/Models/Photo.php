<?php

namespace Photo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $user_id user id
 * @property varchar $caption caption
 * @property varchar $title title
 * @property varchar $mime_type mime type
 * @property varchar $src src
 * @property int $location_id location id
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 * @property PhotoLocation $photoLocation belongsTo
 * @property \Illuminate\Database\Eloquent\Collection $albumphoto belongsToMany
 */
class Photo extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    /**
     * Database table name
     */
    protected $table = 'photo_photos';
    /**
     * Protected columns from mass assignment
     */
    protected $fillable = ['caption', 'title', 'src', 'exif'];

    protected $dates = ['captured_at'];
    /**
     * @var array
     */
    protected $casts = ['exif' => 'array'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->user_id) && auth()->check()) {
                $model->user_id = auth()->user()->id;
            }
            if (empty($model->status)) {
                $model->status = static::STATUS_PENDING;
            }
            return true;
        });
    }

    public function photoable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $userModel = config('auth.providers.users.model');
        return $this->belongsTo($userModel);
    }

    /**
     * photoLocation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * albumphotos
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_photo');
    }

    /**
     * caption column mutator.
     */
    public function setCaptionAttribute($value)
    {
        $this->attributes['caption'] = htmlspecialchars($value);
    }

    /**
     * title column mutator.
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = htmlspecialchars($value);
    }

    public function getSrc()
    {
        return !empty($this->src) ? $this->src : config('photo.default');
    }

    public function scopeQ($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->orWhere('caption', 'LIKE', '%' . $keyword . '%')
                ->orWhere('title', 'LIKE', '%' . $keyword . '%')
                ->orWhere('src', 'LIKE', '%' . $keyword . '%');
        });
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Routing\UrlGenerator|mixed|string
     */
    public function getUrl($webP = false)
    {
        // dd($webP, $this->src, $this);
        if (empty($this->src)) {
            return config('photo.default');
        }
        $prefix = config('photo.prefix');

        return url(rtrim($prefix . '/' . $this->src));
    }

    public function getWebP($size = '')
    {
        $info = pathinfo($this->src);
        if ($size == 'thumbnail') {
            $size = config('photo.sizes.' . $size, false);
            $webP = $info['dirname'] . '/' . $size['path'] . '/' . $info['filename'] . ".webp";
        } else {
            $webP = $info['dirname'] . "/" . $info['filename'] . ".webp";
        }

        $prefix = config('photo.prefix');
        if (file_exists(storage_path('app/public/' . $webP))) {
            $url = url(rtrim($prefix . '/' . $webP));
        } else {
            $url = url(rtrim($prefix . '/' . $this->src));
        }
        return $url;
    }

    /**
     * @param string $size
     * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function hasWebP($size = '')
    {
        $info = pathinfo($this->src);
        if ($size) {
            $size = config('photo.sizes.' . $size, false);
            $webP = $info['dirname'] . '/' . $size['path'] . '/' . $info['filename'] . ".webp";
        } else {
            $webP = $info['dirname'] . "/" . $info['filename'] . ".webp";
        }

        if (file_exists(storage_path('app/public/' . $webP))) {
            $prefix = config('photo.prefix');
            return url(rtrim($prefix . '/' . $webP));
        }
        return false;
    }

    /**
     * Get other sizes of the photo
     * @param string $size key from photo.sizes
     * @return string
     */
    public function getFormat($size = 'thumbnail')
    {
        if (empty($this->src)) {
            return config('photo.default');
        }
        $name = pathinfo($this->src);
        $size = config('photo.sizes.' . $size, false);

        if (!empty($name) && is_array($size) && isset($size['path'])) {
            $prefix = config('photo.prefix');
            $thumbnailPath = $name['dirname'] . '/' . $size['path'] . '/' . $name['basename'];
            if (file_exists(storage_path("app/public/" . $thumbnailPath))) {
                return url($prefix . '/' . $thumbnailPath);

            } else {
                return $this->getUrl();
            }
        }
        return $this->getUrl();
    }

    public function getLocationName()
    {
        try {
            return $this->location->name;

        } catch (\Exception $e) {
            return '';
        }
    }

    public function getLocationAddress()
    {
        try {
            return $this->location->address;

        } catch (\Exception $e) {
            return '';
        }
    }

    public function getLocationPlaceId()
    {
        try {
            return $this->location->place_id;

        } catch (\Exception $e) {
            return '';
        }
    }

    public function getCaption()
    {
        return !empty($this->caption) ? $this->caption : pathinfo($this->src, PATHINFO_BASENAME);
    }

    public function getTitle()
    {
        return !empty($this->title) ? $this->title : pathinfo($this->src, PATHINFO_BASENAME);
    }

    public function apiData()
    {
        return [
            'url' => $this->getUrl(),
            'thumbnail' => $this->getFormat(),
            'caption' => $this->getCaption(),
            'title' => $this->getTitle(),
            'location' => $this->getLocationAddress()
        ];
    }

    public function destroyAndRemove()
    {
        if (Storage::disk('public')->exists($this->src)) {
            Storage::disk('public')->delete($this->src);
            $name = pathinfo($this->src);
            $thumbnails = $name['dirname'] . '/thumbnails/' . $name['basename'];
            if (Storage::disk('public')->exists($thumbnails)) {
                Storage::disk('public')->delete($thumbnails);
            }
        }
        return $this->delete();
    }

    public function isExists()
    {
        return Storage::disk('public')->exists($this->src);
    }

    public function getFullPath()
    {
        return storage_path('app/public/' . $this->src);
    }

    public function getThumbnailPath($size = 'thumbnail')
    {
        $name = pathinfo($this->src);
        $size = config('photo.sizes.' . $size, false);
        $thumbnailPath = $name['dirname'] . '/' . $size['path'] . '/' . $name['basename'];
        return storage_path("app/public/" . $thumbnailPath);
    }

}