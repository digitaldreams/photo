@extends('layouts.app')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.edit',$model->id)}}">{{$photo->caption}}</a>
    </li>
    <li class="breadcrumb-item">Edit</li>
@endsection
@section('content')
    <div class="row">
        <div class='col-md-12'>
            <div class='panel panel-default'>
                <div class="panel-body">
                    @include('photo::forms.photo',[
                    'route'=>route('photo::photos.update',$model->id),
                    'method'=>'PUT'
                    ])
                </div>
            </div>
        </div>
    </div>
@endSection

@section('scripts')
    <script type="text/javascript"
            src="http://maps.google.com/maps/api/js?key=<?php echo config('photo.googleMapApiKey') ?>&libraries=places"></script>

    <script type="text/javascript">
        var jq = $;

        function placeAutoComplete(inputId, dropdown, country) {
            var btn = jq("#address");
            jq("body").on('keyup', inputId, function () {
                var queryString = jq(this).val();
                btn = jq(this);
                var service = new google.maps.places.AutocompleteService();
                //componentRestrictions: {country: 'bd'}
                service.getPlacePredictions({
                    input: queryString,
                }, function (data, status) {
                    var htmlSuggestion = '';
                    for (var i = 0; i < data.length; i++) {

                        htmlSuggestion += '<li class="list-group-item" data-lat="" data-long="">' + data[i]['description'] + '</li>';
                    }
                    jq(dropdown).html(htmlSuggestion);
                })
            })//End of on keyup function
            jq("body").on('click', dropdown + " li", function (e) {
                var newPlace = jq(this).text();
                jq(btn).val(newPlace);
                jq(dropdown).empty();
            });
            jq(inputId).on('blur', function (e) {
                e.preventDefault();
                if (jq(this).val() == "") {
                    jq(dropdown).empty();
                }
            })
        }

        placeAutoComplete("#address", '#locationDropdown')
    </script>
@endsection