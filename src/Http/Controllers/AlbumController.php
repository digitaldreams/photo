<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Photo\Http\Requests\Albums\Create;
use Photo\Http\Requests\Albums\Destroy;
use Photo\Http\Requests\Albums\Edit;
use Photo\Http\Requests\Albums\Index;
use Photo\Http\Requests\Albums\Show;
use Photo\Http\Requests\Albums\Store;
use Photo\Http\Requests\Albums\Update;
use Photo\Models\Album;

/**
 * Description of AlbumController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */
class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('photo::pages.albums.index', ['records' => Album::paginate(10)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Show $request
     * @param  Album $album
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, Album $album)
    {
        return view('photo::pages.albums.show', [
            'record' => $album,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  Create $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {
        return view('photo::pages.albums.create', [
            'model' => new Album,
            'enableVoice'=>true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Store $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model = new Album;
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
     * @param  Edit $request
     * @param  Album $album
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, Album $album)
    {
        return view('photo::pages.albums.edit', [
            'model' => $album,
        ]);
    }

    /**
     * Update a existing resource in storage.
     *
     * @param  Update $request
     * @param  Album $album
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
     * @param  Destroy $request
     * @param  Album $album
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, Album $album)
    {
        if ($album->delete()) {
            session()->flash('message', 'Album successfully deleted');
        } else {
            session()->flash('error', 'Error occurred while deleting the Album');
        }
        return redirect()->back();
    }
}
