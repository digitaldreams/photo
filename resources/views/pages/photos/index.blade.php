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
        <div class="col-sm-3">
            <div class="card image-dropZone" style="min-height: 200px">
                <div class="card-body text-center image-dropZone px-5">
                    <i class="fa fa-plus image-dropZone fa-5x"></i>
                </div>
                <h5 class="card-title text-center image-dropZone"> Dropzone </h5>
            </div>
        </div>
    </div>
    {!! $records->appends(['search'=>request('search')])->render() !!}
@endSection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.1/dropzone.min.js"></script>
    <script type="text/javascript">
        $("body").bind("paste", function (e) {
            var pastedData = e.originalEvent.clipboardData.getData('text');

            pasteUrl(pastedData, '{{route('photo::photos.downloadUrl')}}');
        });
        $(document).ready(function () {
            $(".image-dropZone").dropzone(
                {
                    url: function () {
                        return '{{route('photo::photos.dropzone')}}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    },
                    paramName: "file",
                    maxFiles: 1,
                    maxFilesize: 2,
                    addRemoveLinks: !0,
                    acceptedFiles: 'image/*',
                    drop: function (e) {
                        var imageUrl = e.dataTransfer.getData('URL');
                        if (imageUrl.length > 30) {
                            var url = '{{route('photo::photos.downloadUrl')}}';
                            pasteUrl(imageUrl, url);
                        }

                    },
                    success: function (file, response) {
                        if (response.success) {
                            window.location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    complete: function (file) {
                        window.location.reload();
                    },
                    error: function () {
                        alert('something went wrong please try again')
                    },
                }
            );
        });

        function pasteUrl(pastedData, url) {
            pastedData = pastedData.split(/[?#]/)[0];
            var ext = pastedData.split('.').pop();
            var allowedExt = ["jpg", "jpeg", "png", 'gif', 'webp'];
            if (allowedExt.indexOf(ext) !== -1) {
                $.get(url, {'url': pastedData}).then(function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            } else {
                alert(ext + ' extension are not allowed');
            }
        }
    </script>
@endsection
