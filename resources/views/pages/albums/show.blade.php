@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{route('photo::albums.index')}}"> Albums</a>
    </li>
    <li class="breadcrumb-item">
        {{$record->name}}
    </li>
@endsection
@section('header')
{{$record->name}}
@endsection
@section('tools')
    <div class="btn-group btn-group-sm">
        <a class="btn btn-secondary" href="{{route('photo::photos.create',['album_id'=>$record->id])}}">
            <span class="fa fa-plus"></span>
        </a>
        <a class="btn btn-secondary" href="{{route('photo::albums.edit',$record->id)}}">
            <span class="fa fa-pencil-alt"></span>
        </a>
        <form onsubmit="return confirm('Are you sure you want to delete?')"
              action="{{route('photo::albums.destroy',$record->id)}}" method="post" style="display: inline">
            {{csrf_field()}}
            {{method_field('DELETE')}}
            <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                        class="text-danger fa fa-trash"></i></button>
        </form>

    </div>
@endsection
@section('content')
    <div class="row">
        @foreach($record->photos as $photo)
            <div class="col-sm-4">
                @include('photo::cards.photo',['record'=>$photo])
            </div>
        @endforeach
    </div>
@endSection