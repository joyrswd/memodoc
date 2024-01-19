@extends('common.layout')

@section('title','ログイン')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-3">
    @auth
    <p class="text-center"><strong>{{ auth()->user()->name }}</strong>でログイン中です。</p>
    <p class="text-center"><a href="{{ route('logout') }}" class="btn btn-secondary">ログアウト</a></p>
    @else
    <form method="POST" action="{{ route('login') }}" class="mb-4">
        @csrf
        <div class="mt-3 overflow-hidden">
            <label for="name" class="form-label">{{__('validation.attributes.name')}}</label>
            <input type="name" class="form-control" id="name" name="name" value="{{ old('name')}}" required autofocus>
            @error('name')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <a class="float-end mt-2 text-info-emphasis" style="font-size:x-small;" href="{{ route('user.create') }}">新規登録</a>
        </div>
        <div class="overflow-hidden">
            <label for="password" class="form-label">{{__('validation.attributes.password')}}</label>
            <input type="password" class="form-control" id="password" name="password" required>
            @error('password')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <a class="float-end mt-2 text-info-emphasis" style="font-size:x-small;" href="{{ route('password.request') }}">再設定</a>
        </div>
        <div class="mt-4 col-lg-6 mx-auto">
            <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">ログイン</button></span>
        </div>
    </form>
    @endauth
    <p class="text-center"><a href="{{ route('about') }}" class="text-secondary-emphasis" style="font-size:x-small;">当サイトについて</a></p>
</div>
@endsection