@extends('common.layout')

@section('title','ユーザー登録')

@section('content')
<form action="{{route('user.register')}}" method="POST">
    @csrf
    <div class="row mt-3">
        <label for="name" class="col-sm-2 col-form-label">ユーザーID</label>
        <div class="col-sm-10">
            <div class="col-md-4"><input type="text" class="form-control" placeholder="半角英数_-のみ" name="user_name" id="name" value="{{old('user_name')}}"></div>
            @error('user_name')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row mt-3">
        <label for="email" class="col-sm-2 col-form-label">メールアドレス</label>
        <div class="col-sm-10">
            <div class="col-md-8"><input type="email" class="form-control" name="user_email" id="email" value="{{old('user_email')}}"></div>
            @error('user_email')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row mt-3">
        <label for="password" class="col-sm-2 col-form-label">パスワード</label>
        <div class="col-sm-10">
            <div class="col-md-6"><input type="password" class="form-control" placeholder="半角英数記号8文字以上" min="8" name="user_password" id="password" value=""></div>
            @error('user_password')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <div class="col-md-6"><input type="password" class="form-control mt-2" placeholder="（確認）" name="user_password_confirmation" id="password_confirmation" value=""></div>
            @error('user_password_confirmation')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="mt-5 col-lg-6 mx-auto">
        <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">登録</button></span>
    </div>
</form>

@endsection