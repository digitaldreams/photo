<?php

namespace Photo\Http\Controllers;

use App\Http\Controllers\Controller;
use Photo\Http\Requests\Photos\Create;
use Photo\Http\Requests\Photos\Destroy;
use Photo\Http\Requests\Photos\Edit;
use Photo\Http\Requests\Photos\Index;
use Photo\Http\Requests\Photos\Show;
use Photo\Http\Requests\Photos\Store;
use Photo\Http\Requests\Photos\Update;
use Photo\Models\Photo;
use Photo\Models\Location;


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
     * @param  Index $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('photo::pages.photos.index', ['records' => Photo::paginate(10)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Show $request
     * @param  Photo $photo
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
     * @param  Create $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {
        $photo_locations = Location::all(['id']);

        return view('photo::pages.photos.create', [
            'model' => new Photo,
            "photo_locations" => $photo_locations,
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
        $model = new Photo;
        $model->fill($request->all());

        if ($model->save()) {

            session()->flash('app_message', 'Photo saved successfully');
            return redirect()->route('photo::photos.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving Photo');
        }
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Edit $request
     * @param  Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, Photo $photo)
    {
        $photo_locations = Location::all(['id']);

        return view('photo::pages.photos.edit', [
            'model' => $photo,
            "photo_locations" => $photo_locations,

        ]);
    }

    /**
     * Update a existing resource in storage.
     *
     * @param  Update $request
     * @param  Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Photo $photo)
    {
        $photo->fill($request->all());

        if ($photo->save()) {

            session()->flash('app_message', 'Photo successfully updated');
            return redirect()->route('photo::photos.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating Photo');
        }
        return redirect()->back();
    }

    /**
     * Delete a  resource from  storage.
     *
     * @param  Destroy $request
     * @param  Photo $photo
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, Photo $photo)
    {
        if ($photo->delete()) {
            session()->flash('app_message', 'Photo successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting Photo');
        }

        return redirect()->back();
    }
}
