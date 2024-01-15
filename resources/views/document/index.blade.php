@extends('common.layout')

@section('title','文書一覧')

@section('content')
<div class="text-end">
    <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#finder" aria-expanded="false" aria-controls="finder">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
        </svg>
    </button>
</div>
<div class="collapse{{ ($errors->any() || request()->hasAny('doc_content', 'doc_tags', 'doc_from', 'doc_to')) ? ' show' : '' }}" id="finder">
    <form action="{{route('doc.index')}}" method="GET" class="mb-3 p-4 small shadow">
        <div class="row mt-3">
            <label for="title" class="col-sm-2 col-form-label">タイトル</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="doc_title" id="title" value="{{old('doc_title', request()->input('doc_title'))}}">
                @error('doc_title')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mt-3">
            <label for="content" class="col-sm-2 col-form-label">本文</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="doc_content" id="content" value="{{old('doc_content', request()->input('doc_content'))}}">
                @error('doc_content')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mt-3">
            <label for="from" class="col-sm-2 col-form-label">日付</label>
            <div class="col-sm-10">
                <div class="row">
                    <span class="col-auto"><input type="text" class="form-control" name="doc_from" id="from" value="{{old('doc_from', request()->input('doc_from'))}}" placeholder="開始"></span>
                    <span class="col-auto"><input type="text" class="form-control" name="doc_to" id="to" value="{{old('doc_to', request()->input('doc_to'))}}" placeholder="終了"></span>
                </div>
                @error('doc_from')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                @error('doc_to')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mt-4 col-lg-6 mx-auto">
            <span class="d-grid gap-3"><button type="submit" class="btn btn-primary btn-sm">検索</button></span>
        </div>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th class="py-3" scope="col">日時</th>
            <th class="py-3" scope="col">タイトル</th>
            <th class="py-3" scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach($page['data'] as $row)
        <tr>
            <td class="py-3 text-nowrap">{{ $row['datetime'] }}</td>
            <td class="py-3"><a href="{{route('doc.edit', ['doc' => $row['id']])}}">{{ $row['listTitle'] }}</a></td>
            <td class="py-3 text-end">
                <form action="{{route('doc.destroy', ['doc' => $row['id']])}}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" value="削除" class="m-0 p-0 btn btn-link text-secondary" data-dialog='{"title":"削除確認","body":"削除します。よろしいですか？"}' data-bs-toggle="tooltip" data-bs-title="削除">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                        </svg>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="py-3">
    {{ $page['navigation'] }}
</div>
@endsection