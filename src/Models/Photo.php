<?php

namespace Photo\Models;

use Illuminate\Database\Eloquent\Model;
use Photo\Services\PhotoRenderService;

/**
 * @property int                                      $user_id     user id
 * @property string                                   $caption     caption
 * @property string                                   $mime_type   mime type
 * @property string                                   $src         src
 * @property \Carbon\Carbon                           $created_at  created at
 * @property \Carbon\Carbon                           $updated_at  updated at
 * @property \Illuminate\Database\Eloquent\Collection $tags        belongsToMany
 */
class Photo extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';

    /**
     * Database table name.
     */
    protected $table = 'photo_photos';

    /**
     * Protected columns from mass assignment.
     */
    protected $fillable = [
        'user_id',
        'status',
        'caption',
        'src',
        'src_webp',
        'thumbnails',
        'mime_type',
        'disk',
        'hash',
        'info',
        'exif',
    ];

    protected $dates = [
        'captured_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'exif' => 'array',
        'thumbnails' => 'array',
        'info' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
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
     * @param $query
     * @param $keyword
     *
     * @return mixed
     */
    public function scopeQ($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->orWhere('caption', 'LIKE', '%' . $keyword . '%')
                ->orWhere('src', 'LIKE', '%' . $keyword . '%');
        });
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        $default = config('photo.filesystem');
        $storage = app('filesystem.' . $default);

        return $storage->url($this->src);
    }

    /**
     * @param string $class
     * @param string $style
     *
     * @return mixed
     */
    public function render(string $class = '', string $style = ''): string
    {
        $photoRender = app(PhotoRenderService::class);

        return $photoRender->setClass($class)->setStyle($style)->render($this);
    }

    /**
     * @param string $class
     * @param string $style
     *
     * @return mixed
     */
    public function renderThumbnails(string $class = '', string $style = ''): string
    {
        $photoRender = app(PhotoRenderService::class);

        return $photoRender->setClass($class)->setStyle($style)->renderThumbnails($this);
    }

    /**
     * @return mixed
     */
    public function getUrls(): array
    {
        $photoRender = app(PhotoRenderService::class);

        return $photoRender->getUrls($this);
    }

    /**
     * Tags.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'photo_tag');
    }
}
