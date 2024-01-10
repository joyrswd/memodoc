@extends('common.layout')

@section('title','メモ作成')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<form action="{{route('memo.store')}}" method="POST">
    @csrf
    <div class="mt-3">
        <label for="content">メモ</label>
        <textarea class="form-control" name="memo_content" id="content" rows="7">{{old('memo_content')}}</textarea>
        @error('memo_content')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mt-3">
        <label for="tags">タグ</label>
        <input type="text" class="form-control" name="memo_tags" id="tags" value="{{old('memo_tags')}}">
        @error('tags.*')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mt-5 col-lg-6 mx-auto">
        <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">作成</button></span>
    </div>
</form>

@endsection