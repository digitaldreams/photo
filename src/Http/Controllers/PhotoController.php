<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Photo\Http\Requests\Photos\Store;
use Photo\Http\Requests\Photos\Update;
use Photo\Models\Photo;
use Photo\Repositories\PhotoRepository;
use Photo\Services\PhotoRenderService;

/**
 * Description of PhotoController.
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */
class PhotoController extends Controller
{

    /**
     * @var \Photo\Repositories\PhotoRepository
     */
    protected PhotoRepository $photoRepository;

    /**
     * PhotoController constructor.
     *
     * @param \Photo\Repositories\PhotoRepository $photoRepository
     */
    public function __construct(PhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request           $request
     *
     * @param \Photo\Services\PhotoRenderService $photoRenderService
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, PhotoRenderService $photoRenderService)
    {
        $this->authorize('viewAny', Photo::class);

        $photos = Photo::query();
        $search = $request->get('search');
        $folder = $request->get('folder');
        if (!empty($search)) {
            $search = pathinfo($search, PATHINFO_BASENAME);
            $photos = $photos->where('src', 'LIKE', '%' . $search . '%');
        }
        if (!empty($folder)) {
            $photos = $photos->where('src', 'LIKE', '%' . $folder . '%');
        }

        return view('photo::pages.photos.index', [
            'photoRender' => $photoRenderService,
            'records' => $photos->latest()->paginate(11),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Photo                              $photo
     *
     * @param \Photo\Services\PhotoRenderService $photoRenderService
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Photo $photo, PhotoRenderService $photoRenderService)
    {
        $this->authorize('view', $photo);

        return view('photo::pages.photos.show', [
            'record' => $photo,
            'photoRenderService' => $photoRenderService,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Photo::class);

        return view('photo::pages.photos.create', [
            'model' => new Photo(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $this->photoRepository->create($request->file('file'), $request->all());

        return redirect()->route('photo::photos.index')->with('message', 'Image successfully uploaded.');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Photo                              $photo
     *
     * @param \Photo\Services\PhotoRenderService $photoRenderService
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Photo $photo, PhotoRenderService $photoRenderService)
    {
        $this->authorize('update', $photo);
        return view('photo::pages.photos.edit', [
            'model' => $photo,
            'photoRender' => $photoRenderService,
        ]);
    }

    /**
     * Update a existing resource in storage.
     *
     * @param Update $request
     * @param Photo  $photo
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function update(Update $request, Photo $photo)
    {
        $this->photoRepository->update($photo, $request->get('caption'), $request->file('file'));

        return redirect()->route('photo::photos.show', $photo->id);
    }

    /**
     * Delete a  resource from  storage.
     *
     * @param Photo $photo
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Photo $photo)
    {
        $this->authorize('delete', $photo);

        $this->photoRepository->delete($photo);

        return redirect()->route('photo::photos.index')->with('message', 'photo successfully deleted.');
    }

}
