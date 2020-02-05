@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        Photos
    </li>
@endsection
@section('header')
    <h3>
        <i class="fa fa-images text-muted" style="font-size: 18px"></i> Photos
    </h3>
@endsection
@section('tools')
    <div class="btn-group">
        <a class="btn btn-secondary" href="{{route('photo::photos.create')}}"><span class="fa fa-plus"></span></a>
        <a class="btn btn-secondary" href="{{route('photo::albums.index')}}"><span class="fa fa-images"></span>
            Albums
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
                                <option value="products" {{request('folder')=='products'?'selected':'' }}>Products
                                </option>
                                <option value="businesses" {{request('folder')=='businesses'?'selected':'' }}>Businesses
                                </option>
                                <option value="pages" {{request('folder')=='pages'?'selected':'' }}>Pages</option>
                                <option value="banners" {{request('folder')=='banners'?'selected':'' }}>Banners</option>
                                <option value="prizes" {{request('folder')=='prizes'?'selected':'' }}>Prizes</option>
                                <option value="sliders" {{request('folder')=='sliders'?'selected':'' }}>Sliders</option>
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
            <div class="col-sm-4">
                @include('photo::cards.photo',['record'=>$photo])
            </div>
        @endforeach
    </div>
    {!! $records->appends(['search'=>request('search')])->render() !!}
@endSection