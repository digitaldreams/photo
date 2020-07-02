<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Photo\Http\Requests\Albums\Store;
use Photo\Http\Requests\Albums\Update;
use Photo\Models\Album;
use Photo\Models\Photo;

/**
 * Description of AlbumController.
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */
class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Album::class);

        return view('photo::pages.albums.index', ['records' => Album::paginate(10)]);
    }

    /**
     * Display the specified resource.
     *
     * @param Album $album
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Album $album)
    {
        $this->authorize('view', $album);

        return view('photo::pages.albums.show', [
            'record' => $album,
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

        return view('photo::pages.albums.create', [
            'model' => new Album(),
            'enableVoice' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model = new Album();
        $model->fill($request->all());
        if ($model->save()) {
            session()->flash('message', 'Album saved successfully');

            return redirect()->route('photo::albums.index');
        } else {
            session()->flash('message', 'Oops something went wrong while saving the Album');
        }

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Album $album
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Album $album)
    {
        $this->authorize('update', $album);

        return view('photo::pages.albums.edit', [
            'model' => $album,
        ]);
    }

    /**
     * Update a existing resource in storage.
     *
     * @param Update $request
     * @param Album  $album
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Album $album)
    {
        $album->fill($request->all());
        if ($album->save()) {
            session()->flash('message', 'Album successfully updated');

            return redirect()->route('photo::albums.index');
        } else {
            session()->flash('error', 'Oops something went wrong while updating the Album');
        }

        return redirect()->back();
    }

    /**
     * Delete a  resource from  storage.
     *
     * @param Album $album
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);

        if ($album->delete()) {
            session()->flash('message', 'Album successfully deleted');
        } else {
            session()->flash('error', 'Error occurred while deleting the Album');
        }

        return redirect()->back();
    }
}
