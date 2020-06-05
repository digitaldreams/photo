<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{config('app.name')}}</title>


    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>

@yield('css')

<!-- Custom styles for this template -->
</head>
<body style="padding-top: 45px">
<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="{{url('/')}}">{{config('app.name')}}</a>
    <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
    <ul class="navbar-nav px-3">
        @if (auth()->check())
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="{{ url('/logout') }}"
                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                      style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        @else
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="{{route('login')}}"><i class="fa fa-sign-in"></i> Login</a>
            </li>
        @endif
    </ul>
</nav>

<div class="container-fluid">

    @if(session()->has('message'))
        <div class="alert alert-success">
            {{session()->get('message')}}
        </div>
    @elseif(session()->has('error'))
        <div class="alert alert-warning">
            {{session()->get('error')}}
        </div>
    @endif
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{url('/')}}">
                            <i class="fa fa-home"></i>
                            Home <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('photo::photos.index')}}">
                            <i class="fa fa-image"></i>
                            Photos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('photo::photos.create')}}">
                            <i class="fa fa-plus"></i>
                            New Photo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('photo::albums.index')}}">
                            <i class="fa fa-file-image-o"></i>
                            Albums
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('photo::albums.create')}}">
                            <i class="fa fa-plus"></i>
                            New Album
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span><i class="fa fa-user"></i> {{auth()->user()->name}}</span>
                    <a class="d-flex align-items-center text-muted" href="#">
                        <span data-feather="plus-circle"></span>
                    </a>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/logout') }}"
                           onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                              style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>

                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{url('/')}}">Home</a>
                    </li>
                    @yield('breadcrumb')
                </ul>
            </div>
            <div
                class="d-flex flex-wrap justify-content-between flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">


                <h1 class="h2 text-left">
                    @yield('header')
                </h1>

                <div class="btn-toolbar mb-2 mb-md-0">
                    @yield('tools')
                </div>
            </div>
            @yield('content')

        </main>
    </div>
</div>
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

@yield('scripts')

<script>
    $(document).ready(function () {
        $('#summernote').summernote({
            height: '300px'
        });
    });

    function checkSize(max_img_size, id) {
        var input = document.getElementById(id);
        var allowedImageMimeType = [
            'image/svg+xml',
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/webp'
        ];

        if (input.files && input.files.length == 1) {
            if (allowedImageMimeType.indexOf(input.files[0].type) == -1) {
                alert('File Type Not allowed. Only jpg,jpeg,png,webp,svg allowed');
                input.value = '';
                return false;
            }
            if (input.files[0].size > max_img_size) {
                var yourFileSize = (input.files[0].size / 1024 / 1024);
                input.value = '';
                alert("The file must be less than " + (max_img_size / 1024 / 1024) + "MB", "Your file size is " + yourFileSize.toFixed(2) + 'MB', "warning")
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
</body>
</html>
