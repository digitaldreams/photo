<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{config('app.name')}}</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{asset('photo/css/app.css')}}" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('photo/css/photo.css')}}" crossorigin="anonymous">


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

<script type="text/javascript" src="{{asset('photo/js/manifest.js')}}"></script>
<script type="text/javascript" src="{{asset('photo/js/vendor.js')}}"></script>
<script type="text/javascript" src="{{asset('photo/js/photo.js')}}"></script>
@yield('scripts')

<script>

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
