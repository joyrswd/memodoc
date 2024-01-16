@extends('common.layout')

@section('title','メール認証')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <div class="alert alert-secondary p-4">
        <p>ご登録いただいたメールアドレスに認証メールを送信しております。<br>
            メールに記載されたURLをクリックして、メールアドレスの認証を完了してください。</p>
        <p>下記のボタンからメールを再送することもできます。</p>
        <form action="{{ route('verification.resend') }}" method="POST">
            @csrf
            <div class="col-lg-3 mx-auto">
                <small class="d-grid gap-3"><button type="submit" class="btn btn-sm btn-info">認証メール再送</button></small>
            </div>
        </form>
    </div>
</div>
@endsection