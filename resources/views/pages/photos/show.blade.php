@extends(config('photo.layout'))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item active">{{$record->caption}}</li>
@endsection
@section('header')
    <h3>{{$record->caption}}</h3>
@endsection

@section('tools')
    <div class="btn-group btn-group-sm">
        @can('update',$record)
            <a class="btn btn-light" href="{{route('photo::photos.edit',$record->id)}}">
                <span class="fa fa-pencil"></span> Edit
            </a>
        @endcan
        @can('delete',$record)
            <form class="btn btn-light" onsubmit="return confirm('Are you sure you want to delete?')"
                  action="{{route('photo::photos.destroy',$record->id)}}" method="post" style="display: inline">
                {{csrf_field()}}
                {{method_field('DELETE')}}
                <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                        class="text-danger fa fa-times"></i>Delete
                </button>
            </form>
            <a class="btn btn-light" href="{{route('photo::photos.download',$record->id)}}"><i
                    class="fa fa-download"></i> Download</a>
        @endcan

    </div>
@endsection

@section('content')

    <form method="post" id="photoUploadForm"
          form="photoUploadForm" enctype="multipart/form-data">
        {{csrf_field()}}
        {{method_field('PUT')}}
        <div class="form-group text-center">
            <input type="text" class="form-control" name="caption" id="caption" value="{{$record->caption}}"
                   placeholder="e.g. Photo from Bandarban tour" required>
        </div>
        <div id="upload-demo"></div>
        <div class="form-group text-center">
            <button class="btn btn-primary" id="upload-image">Resize Image</button>
        </div>
    </form>
    <hr/>
    <div class="card">
        <h3 class="card-header text-center">Image Sources</h3>
        <div class="card-body">
            @foreach($photoRenderService->getImageDetailsInfo($record->src) as $url=>$info)
                <div class="form-group form-group-sm">
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{$url}}" id="photoFullAddress_{{$loop->index}}">
                        <div class="input-group-btn">
                            <button class="btn btn-secondary"
                                    onclick="copyToClipboard(this,'photoFullAddress_{{$loop->index}}')">Copy path
                            </button>
                        </div>
                    </div>
                    @foreach($info as $name=>$value)
                        {{$name}}:<span class="badge badge-light">{{$value}}</span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
<hr/>
    <div class="row mb-5">
        @if(!empty($record->exif))
            <div class="col-sm-4">
                @include('photo::pages.photos.exif_data_table')
            </div>
        @endif
            <div class="col-sm-8">
                <div class="card ">
                    <div class="card-header">
                        Html Code <button class="btn btn-link"
                                     onclick="copyToClipboard(this,'htmlCodeForPicture')"><i class="fa fa-copy"></i> Copy Html
                        </button>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="6" id="htmlCodeForPicture">{{ $photoRenderService->render($record)}}</textarea>
                    </div>
                </div>
            </div>

    </div>
@endSection

@section('scripts')

    <script type="text/javascript">
        function copyToClipboard(btn, inputId) {
            var copyText = document.getElementById(inputId);
            copyText.select();
            document.execCommand("copy");
            btn.innerText = "Copied";
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>

    <script type="text/javascript">

        var resize = $('#upload-demo').croppie({
            enableExif: true,
            enableOrientation: true,
            enableZoom: true,
            enableResize: true,
            enforceBoundary: false,
            url: '{{$record->getUrl()}}',
            viewport: { // Default { width: 100, height: 100, type: 'square' }
                width: "{{config('photo.maxWidth')}}",
                height: '{{config('photo.maxHeight')}}',
                type: 'square' //square
            },
            boundary: {
                width: {{config('photo.maxWidth')}}+100,
                height: {{config('photo.maxHeight')}}+100
            },

        });

        $('#upload-image').on('click', function (ev) {
            ev.preventDefault();
            var caption = $("#caption").val();
            if (caption.length < 1) {
                $("#caption").focus();
                return
            }
            var formData = new FormData($('form#photoUploadForm')[0]);
            resize.croppie('result', {
                type: 'canvas',
                size: {
                    width: {{config('photo.maxWidth')}},
                    height: {{config('photo.maxHeight')}}
                }
            }).then(function (img) {
                $('.btn-upload-image').prop('disabled', true);
                var dataURL = img;
                var blob = dataURItoBlob(dataURL);
                formData.append("file", blob, '{{pathinfo($record->src,PATHINFO_BASENAME)}}');

                $.ajax({
                    url: '{{route('photo::photos.update',$record->id)}}',
                    method: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        window.location.reload();
                        $('.btn-upload-image').prop('disabled', false);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('.btn-upload-image').prop('disabled', false);
                    }
                });
            });
        });

        function dataURItoBlob(dataURI) {
            // convert base64 to raw binary data held in a string
            var byteString = atob(dataURI.split(',')[1]);

            // separate out the mime component
            var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

            // write the bytes of the string to an ArrayBuffer
            var arrayBuffer = new ArrayBuffer(byteString.length);
            var _ia = new Uint8Array(arrayBuffer);
            for (var i = 0; i < byteString.length; i++) {
                _ia[i] = byteString.charCodeAt(i);
            }

            var dataView = new DataView(arrayBuffer);
            var blob = new Blob([dataView], {type: mimeString});
            return blob;
        }

        function dropHandler(ev) {
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();

            if (ev.dataTransfer.items) {
                var imageUrl = ev.dataTransfer.getData('text/html');
                var rex = /src="?([^"\s]+)"?\s*/;
                var url, res;
                url = rex.exec(imageUrl);
                alert(url[1]);
            }
        }

        function dragOverHandler(ev) {
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();
        }
    </script>
@endsection
