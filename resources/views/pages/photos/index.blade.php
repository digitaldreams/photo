@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        Photos
    </li>
@endsection
@section('tools')
    <a href="{{route('photo::photos.create')}}"><span class="fa fa-plus"></span></a>
@endsection
@section('content')
    @foreach($records as $photo)
        @include('photo::cards.photo',['record'=>$photo])
    @endforeach
    {!! $records->render() !!}
@endSection