@extends(config('photo.layout'))
@section('header')
    <h3 class="fa fa-images"> Create New Photo Album</h3>
@endsection
@section('tools')
    <a href="{{route('photo::albums.create')}}"><i class="fa fa-plus"></i> New Album</a>
@endsection
@section('content')
    @include('photo::forms.album')
@endSection