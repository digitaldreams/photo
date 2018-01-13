@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-sm-3">
            <h4>photo_photos</h4>
        </div>
        <div class="col-sm-6">

        </div>
        <div class="col-sm-1">
            <a href="{{route('photo::photos.create')}}"><span class="fa fa-plus"></span></a>
        </div>
    </div>
    @include('photo::tables.photo')
    {!! $records->render() !!}
@endSection