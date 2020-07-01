<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Photo\Http\Requests\Photos\Create;
use Photo\Http\Requests\Photos\Destroy;
use Photo\Http\Requests\Photos\Edit;
use Photo\Http\Requests\Photos\Index;
use Photo\Http\Requests\Photos\Show;
use Photo\Http\Requests\Photos\Store;
use Photo\Http\Requests\Photos\Update;
use Photo\Models\Photo;
use Photo\Repositories\PhotoRepository;

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
            $photos = $photos->where('src', 'LIKE', '%' . $search . '%');
        }
        if (!empty($folder)) {
            $photos = $photos->where('src', 'LIKE', '%' . $folder . '%');
        }

        return view('photo::pages.photos.index', [
            'records' => $photos->latest()->paginate(11),
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
        $this->photoRepository->create($request->file('file'), $request->get('caption'));

        return redirect()->route('photo::photos.index')->with('message', 'Image successfully uploaded.');

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
     * @param Destroy $request
     * @param Photo   $photo
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Destroy $request, Photo $photo)
    {
        $this->photoRepository->delete($photo);

        return redirect()->route('photo::photos.index')->with('message', 'photo successfully deleted.');
    }

}
