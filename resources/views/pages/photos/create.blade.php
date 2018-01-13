@extends('layouts.app')
@section('content')
<div class="row">
    <div class='col-md-12'>
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <div class="row">
                    <div class="col-sm-8">
                        <h4>
                            Create photo_photos
                        </h4>
                    </div>
                    <div class="col-sm-4 text-right">
                        <a href="{{route('photo::photos.index')}}">
                            <span class="fa fa-list"> photo_photos</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @include('photo::forms.photo')
            </div>
        </div>
    </div>
</div>
@endSection