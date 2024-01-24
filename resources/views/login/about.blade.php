@extends('common.layout')

@section('title','当サイトについて')

@section('content')
<p id="top">当サイトは短文メモから生成AIを利用して文書を作成するウェブサービスの<strong>デモサイト</strong>です。</p>
<div class="list-group mt-5 px-2">
    <a class="list-group-item list-group-item-action" href="#begining">ユーザー作成から文書生成まで</a></li>
    <a class="list-group-item list-group-item-action" href="#input">メモ入力とタグ付与</a></li>
    <a class="list-group-item list-group-item-action" href="#xpost">Xへのポスト</a></li>
    <a class="list-group-item list-group-item-action" href="#note">デモサイト注意事項</a></li>
</div>
<p class="text-center mt-3"><a href="{{ route('home') }}" class="btn btn-secondary">トップページへ</a></p>
<h2 class="display-6 mt-5" id="begining">ユーザー作成から文書生成まで</h2>
<div class="steps">
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>1</em><span><a href="{{route('home')}}">＜トップページ＞</a>にアクセスし<a href="{{route('user.create')}}">「新規登録」</a>リンクをクリック</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_1.png" alt="step_1"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>2</em><span>必要情報を入力してユーザー登録を行う</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_2.png" alt="step_2"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>3</em><span>任意のメーラーで認証メールの受信を確認し、本文内のボタンをクリック</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_3.png" alt="step_3"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>4</em><span><a href="{{route('memo.create')}}">＜メモ作成＞</a>画面に移動するので、任意のメモをいくつか作成する</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_4.png" alt="step_4"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>5</em><span><a href="{{route('memo.index')}}">＜メモ一覧＞</a>画面に移動して、任意のメモの右にある「書類アイコン」をクリックしてパーツを増やす</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_5.png" alt="step_5"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>6</em><span><a href="{{route('parts.index')}}">＜パーツ一覧＞</a>画面に移動して、最下部の「文書生成」ボタンをクリック</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_6.png" alt="step_6"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>7</em><span><a href="{{route('job.index')}}">＜ジョブ一覧＞</a>画面に移動し新規ジョブが登録される。<br>処理には1～3分かかるが、処理中に別の画面へ移動しても問題ない</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_7.png" alt="step_7"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>8</em><span>1～3分後に<a href="{{route('job.index')}}">＜ジョブ一覧＞</a>画面に移動して、7.のジョブがsuccessになったのを確認する。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_8.png" alt="step_8"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>9</em><span>生成された文書のタイトルをクリックし、文書の内容を確認する</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_9.png" alt="step_9"></label></span>
        </p>
    </div>
    <p class="text-end mt-2">
        <a href="#top" class="text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up-square-fill" viewBox="0 0 16 16">
                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4 9h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5A.5.5 0 0 0 4 11z"></path>
            </svg>
        </a>
    </p>
</div>
<h2 class="display-6 mt-5" id="input">メモ入力とタグ付与</h2>
<div class="steps">
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>1</em><span><a href="{{route('memo.create')}}">＜メモ作成画面＞</a>へ移動し任意のメモを入力、フォーム下部で文字数がカウントされる</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_1.png" alt="input_step_1"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>2</em><span>全角は2文字、半角や改行は1文字としてカウントされ、最大280文字が入力可能</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_2.png" alt="input_step_2"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>3</em><span>URLを入力すると、24文字を超える場合はすべて23文字としてカウントされる。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_3.png" alt="input_step_3"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>4</em><span>「タグ登録」スイッチをオンにすると、タグ入力欄が表示される</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_4.png" alt="input_step_4"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>5</em><span>「新規タグ」入力欄に文字を入れて、欄外をクリック（タップ）するか、エンターキー、半角スペース、Escapeキーを入力するとタグが付与される。<br>
                タグは1つにつき20文字まで、全角半角ともに記号とスペースは入力不可となっている。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_5.gif" alt="input_step_5"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>6</em><span>タグを付与した場合はメモと同様に文字数をカウントして合算される。なおタグには#とスペース（単語区切りのため）が考慮されるため＋2文字される。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_6.png" alt="input_step_6"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>7</em><span>付与したタグの「ｘ」ボタンを押下すると、当該タグは除外され文字数が減算される。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_7.gif" alt="input_step_7"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>8</em><span>「タグ登録」スイッチをオフにするとタグ入力欄は非表示となり、タグの文字数が全て減算される。<br>
                再びスイッチをオンにすれば、入力していたタグが再表示され、文字数もタグの分加算される。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_8.gif" alt="input_step_8"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>9</em><span>作成済みの＜メモ編集＞画面ではタグの編集のみ可能。文字数のカウントは新規作成時と同様となっている。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_9.png" alt="input_step_9"></label></span>
        </p>
    </div>
    <p class="text-end mt-2">
        <a href="#top" class="text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up-square-fill" viewBox="0 0 16 16">
                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4 9h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5A.5.5 0 0 0 4 11z"></path>
            </svg>
        </a>
    </p>
</div>
<!--
<h2 class="display-6 mt-5" id="xpost">Xへのポスト</h2>
<div class="steps">
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>1</em><span><a href="{{route('memo.create')}}">＜メモ作成画面＞</a>へ移動し任意のメモを入力、フォーム下部で文字数がカウントされる</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/input_step_1.png" alt="input_step_1"></label></span>
        </p>
    </div>
    <p class="text-end mt-2">
        <a href="#top" class="text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up-square-fill" viewBox="0 0 16 16">
                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4 9h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5A.5.5 0 0 0 4 11z"></path>
            </svg>
        </a>
    </p>
</div>
-->
<h2 class="display-6 mt-5" id="note">デモサイト注意事項</h2>
<ul class="ms-5 mt-3">
    <li class="lead my-3">1日に一回データをすべてリセットしています。（ユーザーアカウントも消去されます）</li>
    <li class="lead my-3">生成さFれた文書は一般公開されず、作成者自身しか閲覧できません。</li>
    <li class="lead my-3">文書生成にはサイト全体で1日の回数制限を設けています。</li>
    <li class="lead my-3">API利用料が不足して文書生成できない場合は、状況に応じて対応します。</li>
    <li class="lead my-3">悪用された形跡があった場合、本サイトを閉鎖する事があります。</li>
</ul>
<p class="text-end mt-2">
    <a href="#top" class="text-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up-square-fill" viewBox="0 0 16 16">
            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4 9h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5A.5.5 0 0 0 4 11z"></path>
        </svg>
    </a>
</p>
<p class="text-center"><a href="{{ route('home') }}" class="btn btn-secondary">トップページへ</a></p>
@endsection