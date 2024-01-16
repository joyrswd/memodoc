@extends('common.layout')

@section('title','パスワード再設定')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <div class="p-4">
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="row mt-3">
                <label for="email" class="col-sm-3 col-form-label text-end text-nowrap">メールアドレス</label>
                <div class="col-sm-9">
                    <div class="col-md-8"><input type="email" class="form-control" name="email" id="email" value="{{old('email')}}" required></div>
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mt-3">
                <label for="password" class="col-sm-3 col-form-label text-end text-nowrap">パスワード</label>
                <div class="col-sm-9">
                    <div class="col-md-6"><input type="password" class="form-control" minlength="8" maxlength="255" placeholder="半角英数記号8文字以上" name="password" id="password" value="" required></div>
                    @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <div class="col-md-6"><input type="password" class="form-control mt-2" minlength="8" maxlength="255" placeholder="（確認）" name="password_confirmation" id="password_confirmation" value="" required></div>
                    @error('password_confirmation')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mt-5 col-lg-6 mx-auto">
                <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">パスワード再設定</button></span>
            </div>
        </form>
    </div>
</div>
@endsection