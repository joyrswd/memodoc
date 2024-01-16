@extends('common.layout')

@section('title','メモ作成')

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <form action="{{route('memo.store')}}" method="POST">
        @csrf
        <div class="mt-3">
            <label for="content">メモ</label>
            <textarea class="form-control" name="memo_content" minlength="2" maxlength="140" placeholder="2文字以上140文字以内" id="content" rows="7" required>{{old('memo_content')}}</textarea>
            @error('memo_content')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-3">
            <label for="tags">タグ</label>
            <input type="text" class="form-control" name="memo_tags" id="tags" minlength="2" maxlength="255" placeholder="空白区切りで複数登録（記号不可）" value="{{old('memo_tags')}}">
            @error('tags.*')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
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