@extends('common.layout')

@section('title','メモ更新')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <form action="{{route('memo.update', ['memo' => $memo['id']])}}" method="POST">
        @csrf
        @method('PUT')
        <div class="mt-3">
            <p class="p-2">{!! nl2br($memo['content']) !!}</p>
        </div>
        <div class="mt-3">
            <label for="tags">タグ</label>
            <input type="text" class="form-control" name="memo_tags" id="tags" value="{{old('memo_tags', implode(' ', $memo['tags']))}}">
            @error('tags.*')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-5 col-lg-6 mx-auto">
            <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">タグ再設定</button></span>
        </div>
    </form>
    <div class="mt-3 col-lg-3 mx-auto">
        <span class="d-grid gap-3"><a href="{{route('memo.index')}}" class="btn btn-sm btn-secondary">メモ一覧へ</a></span>
    </div>
</div>
@endsection