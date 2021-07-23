@extends(config('photo.layout'))
@section('breadcrumb')
    <a href="{{route('photo::photos.index')}}">Photos</a>
    <li class="breadcrumb-item">Fetch Photos from URL</li>
@endsection

@section('content')
    <form method="get">
        <div class="row">
            <div class="form-group col-10">
                <div class="input-group">
                    <div class="input-group-addon">
                        Url
                    </div>
                    <input type="url" name="url" value="{{request('url')}}" id="page_url" class="form-control">
                </div>
            </div>

            <div class="form-group col-2">
                <input type="submit" value="Go" class="btn btn-outline-primary">
            </div>
        </div>


    </form>


    @if(count($images) >0)
        @foreach(array_chunk($images,4) as $imgChunk)
            <div class="card-group">
                @foreach($imgChunk as $image)
                    <div class="card mb-2">
                        <?php if (isset($image['src']) && !empty($image['src'])): ?>
                        <img src="{{$image['src']}}" class="card-img-top img-responsive">
                        <?php endif; ?>
                        <div class="card-body mb-0 p-1">
                            <div class="card-title">
                                @if (isset($image['alt']) && !empty($image['alt']))
                                    <small class="text-muted">{{$image['alt']}}</small>
                                @else
                                    <i class="fa fa-remove text-danger fa-2x"></i> No Alt attribute found
                                @endif
                            </div>
                        </div>
                        <div class="card-footer p-0">
                            @if (isset($image['src']) && !empty($image['src']))

                                @if(isset($image['width']) && isset($image['height']))
                                    &nbsp;<label class="badge bg-secondary"> {{$image['width']}}px
                                        x {{$image['height']}}px</label>
                                @endif

                                @if(isset($image['mime']))
                                    <label class="badge bg-secondary">{{$image['mime']}}</label>
                                @endif

                                &nbsp;@if(isset($image['size']))
                                    <label class="badge bg-secondary">
                                        {{$image['size']}} kb
                                    </label>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <i class="fa fa-remove text-danger fa-2x"></i> No image found
    @endif
@endSection
