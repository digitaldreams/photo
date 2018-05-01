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
    <div class="row">
        @foreach($records as $photo)
            <div class="col-sm-4">
                @include('photo::cards.photo',['record'=>$photo])
            </div>
        @endforeach
    </div>
    {!! $records->render() !!}
@endSection