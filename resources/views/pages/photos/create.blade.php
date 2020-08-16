@extends(config('photo.layout'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{route('photo::photos.index')}}">Photos</a>
    </li>
    <li class="breadcrumb-item">
        Create
    </li>
@endsection
@section('header')
    <i class="fa fa-image"></i> New Photo
@endsection
@section('tools')

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
    <script>
        $("#photo_tags").select2({
            tags: true,
            tokenSeparators: [",",],
            createSearchChoice: function(term, data) {
                if ($(data).filter(function() {
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

