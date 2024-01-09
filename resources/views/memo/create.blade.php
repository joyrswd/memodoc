@extends('common.content')

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
        <textarea class="form-control" name="memo[add][content]" id="content" rows="7">{{old('memo.add.content')}}</textarea>
        @error('memo.add.content')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    <div class="mt-3">
        <label for="tags">タグ</label>
        <input type="text" class="form-control" name="memo[add][tags]" id="tags" value="{{old('memo.add.tags')}}">
        @error('tags.*')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    <div class="mt-5 col-lg-6 mx-auto">
        <p class="d-grid gap-3"><button type="submit" class="btn btn-primary">作成</button></p>
    </div>
</form>

@endsection