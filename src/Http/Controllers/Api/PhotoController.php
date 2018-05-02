<?php
/**
 * Created by PhpStorm.
 * User: Tuhin
 * Date: 5/2/2018
 * Time: 9:44 AM
 */

namespace Photo\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Photo\Http\Requests\Api\Photos\Index;
use Photo\Http\Requests\Api\Photos\Store;
use Photo\Models\Photo;
use Photo\Photo as PhotoLib;


class PhotoController extends Controller
{
    /**
     * @param Index $index
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Index $index)
    {
        $data = [];
        $photos = Photo::where('user_id', auth()->user()->id)->paginate(100);
        foreach ($photos as $photo) {
            $data[] = $photo->apiData();
        }
        return response()->json($data);
    }

    /**
     * @param Store $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Store $request)
    {
        $data = [];
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            $files = !is_array($files) ? [$files] : $files;
            foreach ($files as $file) {
                $url = (new PhotoLib())->upload($file)->resize()->getUrls();
                if (!empty($url)) {
                    $photo = new Photo([
                        'src' => array_shift($url),
                        'caption' => $file->getClientOriginalName()
                    ]);
                    $photo->save();
                    $data[] = $photo->apiData();
                }
            }
        }
        return response()->json($data);
    }
}