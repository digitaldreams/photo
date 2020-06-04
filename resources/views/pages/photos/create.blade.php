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

    <script type="text/javascript">
        $("#photo_album").select2();

        function checkSize(max_img_size, id) {
            var input = document.getElementById(id);
            if (input.files && input.files.length == 1) {
                if (input.files[0].size > max_img_size) {
                    var yourFileSize = (input.files[0].size / 1024 / 1024);
                    input.value = '';
                    swal("The file must be less than " + (max_img_size / 1024 / 1024) + "MB", "Your file size is " + yourFileSize.toFixed(2) + 'MB', "warning")
                    return false;
                } else {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#' + id + '_preview').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
            return true;
        }

        function disableBtn() {
            formSubmitted = true;
            $("input[type=submit]").prop('disabled', true).val('saving...');
            setTimeout(function () {
                $("input[type=submit]").removeAttr('disabled');
            }, 5000);
            return true;
        }
    </script>
@endsection
