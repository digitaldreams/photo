@extends(config('photo.layout'))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.1/basic.min.css"
          integrity="sha256-TE6npVxnrwCx22/P21S/0pZb9M0pUiZI3nUukZiI6pE=" crossorigin="anonymous"/>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}"> Photos</a>
    </li>
    @if(request()->has('tag'))
        <li class="breadcrumb-item active">
            {{request('tag')}}
        </li>
    @endif
@endsection
@section('header')
    <i class="fa fa-images text-muted" style="font-size: 18px"></i>Find Similar Photos

@endsection
@section('tools')
    <span id="downloading-status"></span>
    <form class="d-inline">
        <input class="form-control" type="number" name="distance" value="{{request('distance',15)}}" required>
    </form>
    <div class="btn-group">
        <a class="btn btn-secondary" href="{{route('photo::photos.create')}}"><span class="fa fa-plus"></span>
            Create New Photo
        </a>
    </div>
@endsection
@section('content')

    <section id="drop_zone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
        <div class="row">
            <div class="col-sm-2 bg-primary">
                @include('photo::cards.photo',['record'=>$original])
            </div>
            <div class="col-sm-9">
                <div class="row">
                    @foreach($records as $photo)
                        <div class="col-sm-3">
                            @include('photo::cards.photo',['record'=>$photo])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </section>
@endSection
@section('scripts')
    @include('photo::pages.photos.drag_drop_scripts')
@endsection
