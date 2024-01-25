@extends('common.layout')

@section('title','メモ更新')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <form action="{{route('memo.update', ['memo' => $memo['id']])}}" method="POST">
        @csrf
        @method('PUT')
        <div class="mt-3">
            <p class="p-2" data-x="post">{!! nl2br($memo['content']) !!}</p>
            @error('memo_content')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <div data-x="tags">
                <div class="row">
                    <div class="col-10">
                        @foreach($memo['tags'] as $tag)
                        <input type="text" name="tags[]" value="{{$tag}}">
                        @endforeach
                        <input type="text" name="tags[]" value="">
                    </div>
                </div>
            </div>
            @error('tags.*')
            <p><span class="invalid-feedback">{{ $message }}</span></p>
            @enderror
            <div class="mt-2 text-end" data-x="controller">
                <small class="form-switch me-2">
                    <label class="form-check-label align-baseline" for="flexSwitchCheckDefault">タグ登録</label>
                    <input class="form-check-input m-0 align-text-bottom" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="has_tag" value="1" checked>
                </small>
            </div>
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

@section('asset')
<script src="/js/xpost.js"></script>
@endsection