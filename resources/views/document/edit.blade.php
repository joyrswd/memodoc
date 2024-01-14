@extends('common.layout')

@section('title','書類更新')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<form action="{{route('doc.update', ['doc' => $document['id']])}}" method="POST">
    @csrf
    @method('PUT')
    <div class="mt-3">
        <label for="title">タイトル</label>
        <input type="text" class="form-control" name="doc_title" id="tags" value="{{old('doc_title', $document['title'])}}">
        @error('doc_title')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mt-3">
        <label for="tags">本文</label>
        <textarea class="form-control" name="doc_content" id="content" rows="14">{{old('doc_content', $document['content'])}}</textarea>
        @error('doc_content')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mt-5 col-lg-6 mx-auto">
        <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">更新</button></span>
    </div>
</form>

@endsection