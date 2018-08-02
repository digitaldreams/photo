@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item active">{{$record->caption}}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @include('photo::cards.photo',['fullSize'=>true])
        </div>
    </div>
@endSection