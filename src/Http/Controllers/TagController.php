<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Photo\Repositories\TagRepository;

class TagController extends Controller
{
    /**
     * @var \Photo\Repositories\TagRepository
     */
    protected $tagRepository;

    /**
     * TagController constructor.
     *
     * @param \Photo\Repositories\TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $data = [];
        $tags = $this->tagRepository->search($request->get('q'));
        foreach ($tags as $tag) {
            $data[] = [
                'id' => $tag->name,
                'text' => $tag->name,
            ];
        }
        return response()->json([
            'results' => $data,
        ]);
    }
}
