@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{route('photo::albums.index')}}">Albums</a>
    </li>
    <li class="breadcrumb-item">
        Create New Album
    </li>
@endsection
@section('header')
     Create New Photo Album
@endsection
@section('tools')
    <a href="{{route('photo::albums.create')}}"><i class="fa fa-plus"></i> New Album</a>
@endsection
@section('content')
    @include('photo::forms.album')
@endSection