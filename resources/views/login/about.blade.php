@extends('common.layout')

@section('title','当サイトについて')

@section('content')
<p>当サイトは短文メモから生成AIを利用して文書を作成するウェブサービスの<strong>デモサイト</strong>です。</p>
<h2 class="display-6 mt-4">ユーザー作成から文書生成まで</h2>
<div class="steps">
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>1</em><span><a href="{{route('home')}}">＜トップページ＞</a>にアクセスし<a href="{{route('user.create')}}">「新規登録」</a>リンクをクリック</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_1.png" alt="about_1"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>2</em><span>必要情報を入力してユーザー登録を行う</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_2.png" alt="about_2"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>3</em><span>任意のメーラーで認証メールの受信を確認し、本文内のボタンをクリック</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_3.png" alt="about_3"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>4</em><span><a href="{{route('memo.create')}}">＜メモ作成＞</a>画面に移動するので、任意のメモをいくつか作成する</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_4.png" alt="about_4"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>5</em><span><a href="{{route('memo.index')}}">＜メモ一覧＞</a>画面に移動して、任意のメモの右にある「書類アイコン」をクリックしてパーツを増やす</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_5.png" alt="about_5"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>6</em><span><a href="{{route('parts.index')}}">＜パーツ一覧＞</a>画面に移動して、最下部の「文書生成」ボタンをクリック</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_6.png" alt="about_6"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>7</em><span><a href="{{route('job.index')}}">＜ジョブ一覧＞</a>画面に移動し新規ジョブが登録される。<br>処理には1～3分かかるが、処理中に別の画面へ移動しても問題ない</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_7.png" alt="about_7"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>8</em><span>1～3分後に<a href="{{route('job.index')}}">＜ジョブ一覧＞</a>画面に移動して、7.のジョブがsuccessになったのを確認する。</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_8.png" alt="about_8"></label></span>
        </p>
    </div>
    <div class="row">
        <p class="col-sm-8 d-flex align-items-center lead">
            <em>9</em><span>生成された文書のタイトルをクリックし、文書の内容を確認する</span>
        </p>
        <p class="col-sm-3">
            <span class="viewer"><label><input type="checkbox"><img src="/img/about/step_9.png" alt="about_9"></label></span>
        </p>
    </div>
</div>
<h2 class="display-6 mt-5">デモサイト注意事項</h2>
<ul class="ms-5 mt-3">
    <li class="lead my-3">1日に一回データをすべてリセットしています。（ユーザーアカウントも消去されます）</li>
    <li class="lead my-3">生成された文書は一般公開されず、作成者自身しか閲覧できません。</li>
    <li class="lead my-3">文書生成にはサイト全体で1日の回数制限を設けています。</li>
    <li class="lead my-3">API利用料が不足して文書生成できない場合は、状況に応じて対応します。</li>
    <li class="lead my-3">悪用された形跡があった場合、本サイトを閉鎖する事があります。</li>
</ul>
<p class="text-center"><a href="{{ route('home') }}" class="btn btn-secondary">トップページへ</a></p>
@endsection