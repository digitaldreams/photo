<?php


namespace Photo\Services;


class SortRelevantImage extends \SplHeap
{

    public function compare($photo1, $photo2)
    {
        if ($photo1->score < $photo2->score) {
            return 1;
        } elseif ($photo1->score == $photo2->score) {
            return 0;
        } else {
            return -1;
        }
    }
}
