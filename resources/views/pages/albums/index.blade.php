@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item">
        Albums
    </li>
@endsection
@section('header')
    Photo Album
@endsection
@section('tools')
    <a href="{{route('photo::albums.create')}}"><i class="fa fa-plus"></i> New Album</a>
@endsection
@section('content')

    @include('photo::tables.album')
    {!! $records->render() !!}
@endSection