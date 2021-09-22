<html>

<head>
    <title>App Name - @yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/default.min.css')}}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-lite.css" rel="stylesheet">
</head>

<body>
    @auth
    @include('layouts.admin.menu') @endauth
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                @yield('content')
            </div>
        </div>
    </div>
    <script src="{{ asset('admin/default.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-lite.js"></script>
    <script>
        $(document).ready(function() {
            $('#texteditor').summernote({height: 300});
        });
    </script>
</body>

</html>