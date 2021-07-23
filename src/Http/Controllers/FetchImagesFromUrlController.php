<?php


namespace Photo\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Photo\Services\FetchImagesFromUrlService;

class FetchImagesFromUrlController extends Controller
{

    public function index(Request $request)
    {
        $url = $request->get('url');
        $fetchSize = $request->get('size', false);
        $data = [
            'success' => false,
            'images' => [],
        ];
        if (!empty($url)) {
            $pageAnalysis = new FetchImagesFromUrlService($url);

            if ($pageAnalysis->isSuccess()) {
                $data['success'] = true;
                $data['images'] = $pageAnalysis->fromCache($fetchSize)->toArray();
            }
        }

        return view('photo::pages.fetch.index', $data);
    }

    public function store()
    {

    }
}
