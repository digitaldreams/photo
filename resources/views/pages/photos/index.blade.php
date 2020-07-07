@extends(config('photo.layout'))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.1/basic.min.css"
          integrity="sha256-TE6npVxnrwCx22/P21S/0pZb9M0pUiZI3nUukZiI6pE=" crossorigin="anonymous"/>    @endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        Photos
    </li>
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
            @foreach($records as $photo)
                <div class="col-sm-2">
                    @include('photo::cards.photo',['record'=>$photo])
                </div>
            @endforeach
            
        </div>
        {!! $records->appends(['search'=>request('search')])->render() !!}
    </section>
@endSection
@section('scripts')
    <script type="text/javascript">
        $("body").bind("paste", function (e) {
            var pastedData = e.originalEvent.clipboardData.getData('text');

            if (confirm('Are you sure you want to download image from ' + pastedData)) {
                pasteUrl(pastedData, '{{route('photo::photos.downloadUrl')}}');
            }

        });


        function pasteUrl(pastedData, url) {
            pastedData = pastedData.split(/[?#]/)[0];
            var ext = pastedData.split('.').pop();
            var allowedExt = ["jpg", "jpeg", "png", 'gif', 'webp'];
            if (allowedExt.indexOf(ext) !== -1) {
                $("#downloading-status").text('Downloading....');
                $.get(url, {'url': pastedData}).then(function (response) {
                    if (response.success) {
                        window.location.href = response.url;
                    } else {
                        alert(response.message);
                    }
                });
            } else {
                alert(ext + ' extension are not allowed');
            }
        }

        function dropHandler(ev) {
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();

            if (ev.dataTransfer.items) {
                var imageUrl = ev.dataTransfer.getData('text/html');
                var rex = /src="?([^"\s]+)"?\s*/;
                var url, res;
                url = rex.exec(imageUrl);
                if (url.length > 0) {
                    if (confirm('Are you sure you want to download image from ' + url[1])) {
                        pasteUrl(url[1], '{{route('photo::photos.downloadUrl')}}');
                    }
                }
            }
        }

        function dragOverHandler(ev) {
            ev.preventDefault();
        }
    </script>
@endsection
