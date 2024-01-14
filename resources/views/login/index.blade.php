@extends('common.layout')

@section('title','ログイン')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@auth
<div class="text-center">
    <p><strong>{{ auth()->user()->name }}</strong>でログイン中です。</p>
    <p><a href="{{ route('logout') }}" class="btn btn-secondary">ログアウト</a></p>
</div>
@else
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mt-3">
        <label for="name" class="form-label">{{__('validation.attributes.name')}}</label>
        <input type="name" class="form-control" id="name" name="name" value="{{ old('name')}}" required autofocus>
        @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="mt-3">
        <label for="password" class="form-label">{{__('validation.attributes.password')}}</label>
        <input type="password" class="form-control" id="password" name="password" required>
        @error('password')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="mt-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">ログイン情報を記憶する</label>
    </div>
    <div class="mt-3 col-lg-6 mx-auto">
        <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">ログイン</button></span>
    </div>
</form>
@endauth

@endsection