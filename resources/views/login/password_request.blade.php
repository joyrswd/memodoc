@extends('common.layout')

@section('title','パスワードリセット')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="row mt-3">
            <label for="email" class="col-sm-3 col-form-label text-end text-nowrap">メールアドレス</label>
            <div class="col-sm-9">
                <div class="col-md-8"><input type="email" class="form-control" name="email" id="email" value="{{old('email')}}"></div>
                @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mt-5 col-lg-6 mx-auto">
            <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">パスワードリセット</button></span>
        </div>
    </form>
</div>
@endsection