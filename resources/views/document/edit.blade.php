@extends('common.layout')

@section('title','文書更新')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <form action="{{route('doc.update', ['doc' => $document['id']])}}" method="POST">
        @csrf
        @method('PUT')
        <div class="mt-3">
            <label for="title">タイトル</label>
            <input type="text" class="form-control" name="doc_title" id="tags" maxlength="255" value="{{old('doc_title', $document['title'])}}" required>
            @error('doc_title')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-3">
            <label for="tags">本文</label>
            <textarea class="form-control" name="doc_content" minlength="5" id="content" rows="14" required>{{old('doc_content', $document['content'])}}</textarea>
            @error('doc_content')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-5 col-lg-6 mx-auto">
            <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">文書更新</button></span>
        </div>
    </form>
    <div class="mt-3 col-lg-3 mx-auto">
        <span class="d-grid gap-3"><a href="{{route('doc.index')}}" class="btn btn-sm btn-secondary">文書一覧へ</a></span>
    </div>
</div>
@endsection