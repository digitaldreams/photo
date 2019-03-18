@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item active">{{$record->caption}}</li>
@endsection
@section('tools')
    <div class="form-group form-group-sm">
        <div class="input-group">
            <input type="text" class="form-control" value="{{$record->getUrl()}}" id="photoFullAddress">
            <div class="input-group-btn">
                <button class="btn btn-secondary" onclick="copyToClipboard(this)">Copy path</button>

            </div>
        </div>
    </div>

@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            @include('photo::cards.photo',['fullSize'=>true])
        </div>
    </div>
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
@endsection