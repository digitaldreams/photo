@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        Photos
    </li>
@endsection
@section('header')
    <i class="fa fa-images text-muted" style="font-size: 18px"></i> Photos

@endsection
@section('tools')
    <div class="btn-group">
        <a class="btn btn-secondary" href="{{route('photo::photos.create')}}"><span class="fa fa-plus"></span>
            Create New Photo
        </a>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <form>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-append">
                            <select name="folder" class="form-control">
                                <option>All</option>

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
        @foreach($records as $photo)
            <div class="col-sm-3">
                @include('photo::cards.photo',['record'=>$photo])
            </div>
        @endforeach
    </div>
    {!! $records->appends(['search'=>request('search')])->render() !!}
@endSection
