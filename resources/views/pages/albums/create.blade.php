@extends('layouts.app')
@section('content')
    <div class="row">
        <div class='col-md-12'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <div class="row">
                        <div class="col-sm-8">
                            <h4>
                                Create photo_albums
                            </h4>
                        </div>
                        <div class="col-sm-4 text-right">
                            <a href="{{route('photo::albums.index')}}">
                                <span class="fa fa-list"> photo_albums</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    @include('photo::forms.album')
                </div>
            </div>
        </div>
    </div>
@endSection