<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center py-4">
            <div class="col-md-7">
                <div class="container">
                    @yield('content')
                </div>
                <nav class="navbar navbar-expand-md navbar-dark fixed-bottom bg-dark">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="{{route('home')}}">MemoG</a>
                        @auth
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarCollapse">
                            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                                <li class="nav-item">
                                    <a class="nav-link{{request()->routeIs('memo.create')?' active':''}}"{{request()->routeIs('memo.create')?' aria-current="page"':''}} href="{{route('memo.create')}}">Write</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link{{request()->routeIs('memo.index')||request()->routeIs('memo.edit')?' active':''}}"{{request()->routeIs('memo.index')?' aria-current="page"':''}}  href="{{route('memo.index')}}">Memo</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Blog</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Parts</a>
                                </li>
                            </ul>
                            <div class="d-lg-flex col-lg-3 justify-content-lg-end">
                                <a class="btn btn-sm btn-secondary" href="{{route('logout')}}">ログアウト</a>
                            </div>
                        </div>
                        @endauth
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        (function() {
            // Add the "is_invalid" class to the input element
            document.querySelectorAll('.invalid-feedback').forEach(element => element.previousElementSibling.classList.add('is-invalid'));
        })()
    </script>
</body>

</html>