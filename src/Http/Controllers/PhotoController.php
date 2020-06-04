<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Photo\Http\Requests\Photos\Create;
use Photo\Http\Requests\Photos\Destroy;
use Photo\Http\Requests\Photos\Edit;
use Photo\Http\Requests\Photos\Index;
use Photo\Http\Requests\Photos\Show;
use Photo\Http\Requests\Photos\Store;
use Photo\Http\Requests\Photos\Update;
use Photo\Library\Resize;
use Photo\Models\Album;
use Photo\Models\Photo;
use Photo\Services\ImageDownload;
use Photo\Services\PhotoService;

/**
 * Description of PhotoController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */
class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Index $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $photos = new Photo();
        $search = $request->get('search');
        $folder = $request->get('folder');
        if (!empty($search)) {
            $search = pathinfo($search, PATHINFO_BASENAME);
            $photos = $photos->where('src', 'LIKE', "%" . $search . '%');
        }
        if (!empty($folder)) {
            $photos = $photos->where('src', 'LIKE', "%" . $folder . '%');
        }
        return view('photo::pages.photos.index', [
            'records' => $photos->latest()->paginate(6),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Show  $request
     * @param Photo $photo
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, Photo $photo)
    {
        return view('photo::pages.photos.show', [
            'record' => $photo,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {
        return view('photo::pages.photos.create', [
            'model' => new Photo,
            'allRelatedIds' => [$request->get('album_id')],
            'albums' => Album::get(['name', 'id']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Store $request)
    {
        $model = new Photo;
        $model->fill($request->all());
        $photoService = new PhotoService($model);
        if (!$photoService->isValid($request)) {
            return redirect()->back()->withInput($request->all())->with('app_error', 'Unsupported file type');
        }

        if ($model = $photoService->save($request)) {
            $model->albums()->sync($request->get('album_ids', []));
            session()->flash('app_message', 'Photo saved successfully');
            return redirect()->route('photo::photos.index');
        } else {
            session()->flash('app_message', 'Oops something went wrong while saving your photo');
        }
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Edit  $request
     * @param Photo $photo
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, Photo $photo)
    {
        return view('photo::pages.photos.edit', [
            'model' => $photo,
            'albums' => Album::get(['name', 'id']),
            'allRelatedIds' => $photo->albums()->allRelatedIds()->toArray(),
        ]);
    }

    /**
     * Update a existing resource in storage.
     *
     * @param Update $request
     * @param Photo  $photo
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Update $request, Photo $photo)
    {
        $photo->fill($request->all());
        $photo->save();
        $photoService = new PhotoService($photo);
        if ($photo = $photoService->save($request)) {
            $photo->albums()->sync($request->get('album_ids', []));
            session()->flash('app_message', 'Photo successfully updated');
            return redirect()->route('photo::photos.index');
        } else {
            session()->flash('app_error', 'Oops something went wrong while updating your photo');
        }
        return redirect()->back();
    }

    /**
     * Delete a  resource from  storage.
     *
     * @param Destroy $request
     * @param Photo   $photo
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, Photo $photo)
    {
        if ($photo->delete()) {
            session()->flash('app_message', 'Photo successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting your photo');
        }
        return redirect()->route('photo::photos.index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadUrl(Request $request)
    {
        try {
            $url = $request->get('url');
            $storagePath = storage_path('app/public/images');
            $downloadImage = new ImageDownload($url, $storagePath);
            if (!$downloadImage->exists()) {
                return response()->json([
                    'message' => 'File does not exists',
                    'success' => false,
                ]);
            } else {
                config([
                    'photo.maxWidth' => 505,
                    'photo.maxHeight' => 426,
                ]);

                $path = $downloadImage->download();
                $photo = new Photo();
                $photo->caption = null;
                $photo->src = 'images/' . pathinfo($path, PATHINFO_BASENAME);
                $photo->save();
                if (pathinfo($photo->src, PATHINFO_EXTENSION) !== 'svg') {
                    if (!file_exists($storagePath . '/thumbnails')) {
                        mkdir($storagePath . '/thumbnails');
                    }
                    $resize = (new Resize($path, 'thumbnail'))->setPath($storagePath . '/thumbnails');
                    $resize->save();
                }

                return response()->json([
                    'file' => $photo->getFormat(),
                    'success' => true,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ]);
        }


    }

    /**
     * Rename Filename
     *
     * @param Edit  $request
     * @param Photo $photo
     *
     * @return \Illuminate\Http\Response
     */
    public function rename(Edit $request, Photo $photo)
    {
    }
}
