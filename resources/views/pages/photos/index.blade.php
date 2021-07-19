@extends(config('photo.layout'))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.1/basic.min.css"
          integrity="sha256-TE6npVxnrwCx22/P21S/0pZb9M0pUiZI3nUukZiI6pE=" crossorigin="anonymous"/>    @endsection
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
    <i class="fa fa-images text-muted" style="font-size: 18px"></i> Photos

@endsection
@section('tools')
    <span id="downloading-status"></span>
    <div class="btn-group">
        <a class="btn btn-secondary" href="{{route('photo::photos.create')}}"><span class="fa fa-plus"></span>
            Create New Photo
        </a>
    </div>
@endsection
@section('content')

    <section id="drop_zone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
        <div class="row">
            <div class="col-md-6">
                <form>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-append">
                                <select name="folder" class="form-control">
                                    <option value="">All</option>
                                </select>
                            </div>
                            <input class="form-control" type="text" name="search" value="{{request('search')}}"
                                   placeholder="image name or url e.g. image.jpg or businesses/images/abc_pluming.jog">
                            <div class="input-group-btn">
                                <button class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                {!! $records->appends(['search'=>request('search')])->links() !!}
            </div>
        </div>
        <hr/>
        <div class="row">
            @foreach($records->chunk(2) as $photos)
                <div class="col-sm-2">
                    <div class="d-flex flex-column align-items-start flex">
                        @foreach($photos as $photo)
                            <div class="">
                                @include('photo::cards.photo',['record'=>$photo])
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <div class="clearfix"></div>
        {!! $records->appends(['search'=>request('search')])->render() !!}
    </section>
@endSection
@section('scripts')
    @include('photo::pages.photos.drag_drop_scripts')
@endsection
