<?php


namespace Photo\Repositories;


use Illuminate\Database\DatabaseManager;
use Photo\Models\Photo;
use Photo\Models\Tag;

class TagRepository
{
    /**
     * @var \Photo\Models\Tag
     */
    protected $tag;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $databaseManager;


    /**
     * TagRepository constructor.
     *
     * @param \Photo\Models\Tag                    $tag
     * @param \Illuminate\Database\DatabaseManager $databaseManager
     */
    public function __construct(Tag $tag, DatabaseManager $databaseManager)
    {
        $this->tag = $tag;
        $this->databaseManager = $databaseManager;
    }

    /**
     * @param \Photo\Models\Photo $photo
     * @param array               $tags
     *
     * @return bool
     */
    public function save(Photo $photo, array $tags)
    {
        $dbTags = $this->tag->whereIn('name', $tags)->get();
        if (count($dbTags) < count($tags)) {
            $remainingTags = array_diff($tags, $dbTags->pluck('name')->toArray());
            $insertAbleTag = [];
            foreach ($remainingTags as $rtag) {
                $insertAbleTag[] = [
                    'name' => $rtag,
                ];
            }
            $this->databaseManager->table($this->tag->getTable())->insert($insertAbleTag);
        }
        $dbTags = $this->tag->whereIn('name', $tags)->get();
        $photo->tags()->sync($dbTags);

        return true;
    }
}
