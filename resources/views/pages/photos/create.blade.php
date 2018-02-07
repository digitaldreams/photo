@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item">
        Create
    </li>
@endsection

@section('content')
    <div class="row">
        <div class='col-md-12'>
            <div class='panel panel-default'>
                <div class="panel-body">
                    @include('photo::forms.photo')
                </div>
            </div>
        </div>
    </div>
@endSection
@section('scripts')
    <script type="text/javascript"
            src="http://maps.google.com/maps/api/js?key=<?php echo config('photo.googleMapApiKey') ?>&libraries=places">
    </script>

    @include('photo::pages.photos.locationjs')
@endsection