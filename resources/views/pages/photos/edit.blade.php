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
@section('header')
<i class="fa fa-pencil"></i> {{!empty($model->caption)?$model->caption:$model->id}}
@endsection
@section('tools')
    <a class="btn btn-secondary" href="{{route('photo::photos.create')}}"><i class="fa fa-plus"></i> Create New Photo</a>
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

    <script type="text/javascript">
        $("#photo_album").select2();
    </script>
@endsection
