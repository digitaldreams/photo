@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.show',$model->id)}}">{{$model->caption}}</a>
    </li>
    <li class="breadcrumb-item">Edit</li>
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
@endsection
@section('header')
    <i class="fa fa-pencil"></i> {{!empty($model->caption)?$model->caption:$model->id}}
@endsection
@section('tools')
    <a class="btn btn-secondary" href="{{route('photo::photos.create')}}"><i class="fa fa-plus"></i> Create New
        Photo</a>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $("#photo_tags").select2({
            tags: true,
            tokenSeparators: [",",],
            createSearchChoice: function (term, data) {
                if ($(data).filter(function () {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                    return {
                        id: term,
                        text: term
                    };
                }
            },
            ajax: {
                url: '{{route('photo::tags.search')}}',
                dataType: "json",
            }
        });
    </script>
@endsection
