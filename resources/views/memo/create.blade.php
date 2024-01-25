@extends('common.layout')

@section('title','メモ作成')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <form action="{{route('memo.store')}}" method="POST">
        @csrf
        <div class="mt-3">
            <label for="content">メモ</label>
            <textarea class="form-control" name="memo_content" placeholder="半角10文字以上280文字以内" id="content" rows="7" data-x="post" required>{{old('memo_content')}}</textarea>
            @error('memo_content')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <div data-x="tags">
                <div class="row">
                    <div class="col-10">
                        @foreach(old('tags', []) as $tag)
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
                    <input class="form-check-input m-0 align-text-bottom" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="has_tag" value="1"{{old('tags')?' checked':''}}>
                </small>
            </div>
        </div>
        <div class="mt-3 small">
            <input type="checkbox" class="form-check-input" name="add_next" id="add_next" value="1"{{old('add_next')?' checked' : ''}}>
            <label for="add_next" class="form-check-label">連続作成</label>
        </div>
        <div class="mt-3 col-lg-6 mx-auto">
            <span class="d-grid gap-3"><button type="submit" class="btn btn-primary">メモ保存</button></span>
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