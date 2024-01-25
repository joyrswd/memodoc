<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{config('app.name')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    @includeIf('common.outsource')
    <link rel="stylesheet" href="/css/main.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center pb-5">
            <div class="col-md-7">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-small" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('failed') }}
                    <button type="button" class="btn-close btn-small" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <main class="row justify-content-center pt-5">
                    @yield('content')
                </main>
            </div>
            <nav class="navbar navbar-expand-md navbar-dark fixed-bottom bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{route('home')}}">{{config('app.name')}}</a>
                    @auth
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav me-auto mb-2 mb-md-0">
                            @if(auth()->user()->hasVerifiedEmail())
                            <li class="nav-item pe-2">
                                <a class="nav-link{{request()->routeIs('memo.create')?' active':''}}" {{request()->routeIs('memo.create')?' aria-current="page"':''}} href="{{route('memo.create')}}">Write</a>
                            </li>
                            <li class="nav-item pe-1">
                                <a class="nav-link{{request()->routeIs('memo.index')||request()->routeIs('memo.edit')?' active':''}}" {{request()->routeIs('memo.index')||request()->routeIs('memo.edit')?' aria-current="page"':''}} href="{{route('memo.index')}}">Memos</a>
                            </li>
                            <li class="nav-item pe-2">
                                @inject('partsService', 'App\Services\PartsService')
                                @php $count = $partsService->getStatus('count'); @endphp
                                <a class="position-relative nav-link{{request()->routeIs('parts.index')?' active':''}}" {{request()->routeIs('parts.index')?' aria-current="page"':''}} href="{{route('parts.index')}}">Parts<small><span class="position-absolute badge rounded-pill text-bg-success" id="parts_badge">{{empty($count)?'':$count}}</span></small></a>
                            </li>
                            <li class="nav-item pe-2">
                                @inject('jobService', 'App\Services\ApiJobService')
                                @php $count = $jobService->getUpcomingCount(auth()->id()); @endphp
                                <a class="position-relative nav-link{{request()->routeIs('job.index')?' active':''}}" {{request()->routeIs('job.index')?' aria-current="page"':''}} href="{{route('job.index')}}">Jobs<small><span class="position-absolute badge rounded-pill text-bg-success" id="job_badge">{{empty($count)?'':$count}}</span></small></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link{{request()->routeIs('doc.index')||request()->routeIs('doc.edit')?' active':''}}" {{request()->routeIs('doc.index')||request()->routeIs('doc.edit')?' aria-current="page"':''}} href="{{route('doc.index')}}">Docs</a>
                            </li>
                            @endif
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
    <div class="modal" id="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary">送信</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script type="module" src="/js/main.js"></script>
    @yield('asset')
</body>
</html>