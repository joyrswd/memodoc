<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{env('APP_NAME')}}</title>
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
                        <a class="navbar-brand" href="{{route('home')}}">{{env('APP_NAME')}}</a>
                        @auth
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarCollapse">
                            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                                <li class="nav-item">
                                    <a class="nav-link{{request()->routeIs('memo.create')?' active':''}}" {{request()->routeIs('memo.create')?' aria-current="page"':''}} href="{{route('memo.create')}}">Write</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link{{request()->routeIs('memo.index')||request()->routeIs('memo.edit')?' active':''}}" {{request()->routeIs('memo.index')?' aria-current="page"':''}} href="{{route('memo.index')}}">Memo</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Doc</a>
                                </li>
                                <li class="nav-item">
                                    @inject('partsService', 'App\Services\PartsService')
                                    @php $count = $partsService->getStatus('count'); @endphp
                                    <a class="position-relative nav-link{{request()->routeIs('parts.index')?' active':''}}" {{request()->routeIs('parts.index')?' aria-current="page"':''}} href="{{route('parts.index')}}">Parts<small><span class="position-absolute mx-1 badge rounded-pill text-bg-success" id="parts_badge">{{empty($count)?'':$count}}</span></small></a>
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
    <script>
        (function() {
            // エラー時当該フォーム強調
            document.querySelectorAll('.invalid-feedback').forEach(element => element.previousElementSibling.classList.add('is-invalid'));
            // ツールチップ
            [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].map(element => new bootstrap.Tooltip(element));
            // モーダルダイアログ
            const modal = document.querySelector('#modal');
            const myModal = new bootstrap.Modal(modal);
            modal.button = modal.querySelector('.modal-footer .btn-primary');
            modal.setTexts = (properties) => {
                for (const [key, text] of Object.entries(properties)) {
                    const query = '.modal-' + key;
                    modal.querySelector(query).textContent = text;
                }
            };
            document.querySelectorAll('[data-dialog]').forEach(element => {
                element.addEventListener('click', event => {
                    let texts = {};
                    event.preventDefault();
                    try {
                        const config = JSON.parse(element.dataset.dialog);
                        texts = config.texts;
                        modal.button.classList.remove('d-none');
                        modal.button.addEventListener('click', modalEvent => {
                            // 参照元のフォームに送信処理を設定しなおしてsubmitイベントを発火させる
                            // (フォームに設定されている他のsubmitイベントを発火させた後に通常の送信処理を実行させるため)
                            element.form.addEventListener('submit', ev => element.form.submit(), {once: true});
                            element.form.dispatchEvent(new Event('submit'));
                            // モーダルを閉じる
                            myModal.hide();
                        }, {
                            once: true
                        });
                    } catch (e) {
                        texts = {
                            title: 'エラー',
                            body: e.message,
                        };
                        modal.button.classList.add('d-none');
                    }
                    modal.setTexts(texts);
                    myModal.show();
                });
            }, false);

            // パーツ追加・削除
            document.querySelectorAll('[data-parts]').forEach(element => {
                const command = element.dataset.parts;
                element.addEventListener('submit', event => {
                    event.preventDefault();
                    event.stopImmediatePropagation(); //以降のsubmitイベントを強制キャンセル
                    const form = event.target;
                    const request = new Request(form.action, {
                        method: form.method,
                        body: new FormData(form),
                    });
                    fetch(request).then(response => {
                        if (response.ok) {
                            return response.json();
                        } else {
                            throw new Error('Network response was not ok.');
                        }
                    }).then(data => {
                        if (data.status === 'success') {
                            if (command === 'add') {
                                form.querySelector('button').disabled = true;
                            } else if (command === 'remove') {
                                //elementに設定されたtooltipを削除
                                form.querySelectorAll('button').forEach(button => {
                                    const tooltip = bootstrap.Tooltip.getInstance(button);
                                    if (tooltip) {
                                        tooltip.dispose();
                                    }
                                });
                                const tr = form.closest('tr');
                                if (tr) {
                                    tr.remove();
                                } else if (data.count == 0) {
                                    document.querySelectorAll('tbody.table-group-divider>tr').forEach(tr => tr.remove());
                                }
                            }
                            document.querySelector('#parts_badge').textContent = (data.count) ? data.count : '';
                        } else {
                            throw new Error(data.message);
                        }
                    }).catch(error => {
                        alert(error.message);
                    });
                });
            });
        })();
    </script>
    @yield('asset')
</body>

</html>