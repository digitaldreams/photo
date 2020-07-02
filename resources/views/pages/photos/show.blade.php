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
    <div class="form-group form-group-sm">
        <div class="input-group">
            <input type="text" class="form-control" value="{{$record->src}}" id="photoFullAddress">
            <div class="input-group-btn">
                <button class="btn btn-secondary" onclick="copyToClipboard(this)">Copy path</button>
            </div>
        </div>
    </div>

@endsection

@section('content')
    <form method="post" id="photoUploadForm"
          form="photoUploadForm" enctype="multipart/form-data">
        {{csrf_field()}}
        {{method_field('PUT')}}
        <input type="hidden" name="caption" value="{{$record->caption}}">
        <div id="upload-demo"></div>
        <div class="form-group text-center">
            <button class="btn btn-primary" id="upload-image">Resize Image</button>
        </div>
    </form>


    <div class="row mb-5">
        @if($record->location)
            <div class="col-sm-8">
                <h3>Location Map</h3>
                <div style="width: 100%; height:500px" id="photoLocationMap"></div>
            </div>
        @endif
        <div class="col-sm-4">
            @if(!empty($record->exif))
                <table class="table table-bordered table-responsive table-striped table-hover">
                    <thead>
                    <tr>
                        <th colspan="2">Exif Data</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($record->captured_at)
                        <tr>
                            <th>Captured At</th>
                            <td>{{$record->captured_at->format('d M Y h:i A')}}</td>
                        </tr>
                    @endif
                    @if($record->exif['Make'])
                        <tr>
                            <th>Make</th>
                            <td>{{$record->exif['Make']}}</td>
                        </tr>
                    @endif
                    @if($record->exif['Model'])
                        <tr>
                            <th>Make</th>
                            <td>{{$record->exif['Model']}}</td>
                        </tr>
                    @endif
                    @if($record->exif['FileSize'])
                        <tr>
                            <th>Size</th>
                            <td>{{$record->exif['FileSize']}}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endSection

@section('scripts')
    @if($record->location)
        <script src="http://js.api.here.com/v3/3.0/mapsjs-core.js"
                type="text/javascript" charset="utf-8"></script>
        <script src="http://js.api.here.com/v3/3.0/mapsjs-service.js"
                type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            var platform = new H.service.Platform({
                'app_id': '{{config('photo.here.app_id')}}',
                'app_code': '{{config('photo.here.app_code')}}'
            });
            var defaultLayers = platform.createDefaultLayers();

            // Instantiate (and display) a map object:
            var map = new H.Map(
                document.getElementById('photoLocationMap'),
                defaultLayers.normal.map,
                {
                    zoom: 13,
                    center: {lat:{{$record->location->latitude}}, lng:{{$record->location->longitude}}}
                });
            // Create a marker icon from an image URL:
            var svgMarkup = '<svg width="24" height="24" ' +
                'xmlns="http://www.w3.org/2000/svg">' +
                '<rect stroke="white" fill="#1b468d" x="1" y="1" width="22" ' +
                'height="22" /><text x="12" y="18" font-size="12pt" ' +
                'font-family="Arial" font-weight="bold" text-anchor="middle" ' +
                'fill="white">M</text></svg>';
            var icon = new H.map.Icon('{{asset('marker.png')}}');

            // Create a marker using the previously instantiated icon:
            var marker = new H.map.Marker({
                lat: {{$record->location->latitude}},
                lng:{{$record->location->longitude}}}, {icon: icon});

            // Add the marker to the map:
            map.addObject(marker);
        </script>
    @endif
    <script type="text/javascript">
        function copyToClipboard(btn) {
            var copyText = document.getElementById("photoFullAddress");
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
            enforceBoundary:false,
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
            console.log('WHAT');
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

    </script>
@endsection
