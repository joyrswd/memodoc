@extends('common.layout')

@section('title','メモ一覧')

@section('content')
<div class="text-end">
    <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#finder" aria-expanded="false" aria-controls="finder">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
        </svg>
    </button>
</div>
<div class="collapse{{ ($errors->any() || request()->hasAny('memo_content', 'tags', 'memo_from', 'memo_to')) ? ' show' : '' }}" id="finder">
    <form action="{{route('memo.index')}}" method="GET" class="mb-3 p-4 small shadow">
        <div class="row mt-3">
            <label for="content" class="col-sm-2 col-form-label">メモ</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" minlength="2" maxlength="100" name="memo_content" id="content" value="{{old('memo_content', request()->input('memo_content'))}}">
                @error('memo_content')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mt-3">
            <label for="tags" class="col-sm-2 col-form-label">タグ</label>
            <div class="col-sm-10">
                <div data-x="tags">
                    <div>
                        @foreach (request()->input('tags', []) as $tag)
                        <input type="text" maxlength="20" list="tag_suggestions" name="tags[]" value="{{ $tag }}">
                        @endforeach
                        <input type="text" maxlength="20" list="tag_suggestions" name="tags[]">
                    </div>
                    <datalist id="tag_suggestions">
                        @foreach($datalist as $suggest)
                        <option value="{{$suggest}}">
                        @endforeach
                    </datalist>
                </div>
                @error('tags.*')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mt-3">
            <label for="from" class="col-sm-2 col-form-label">日付</label>
            <div class="col-sm-10">
                <div class="row">
                    <span class="col-auto"><input type="date" max="{{date('Y-m-d')}}" class="form-control" name="memo_from" id="from" value="{{old('memo_from', request()->input('memo_from'))}}" placeholder="開始"></span>
                    <span class="col-auto"><input type="date" max="{{date('Y-m-d')}}" class="form-control" name="memo_to" id="to" value="{{old('memo_to', request()->input('memo_to'))}}" placeholder="終了"></span>
                </div>
                @error('memo_from')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                @error('memo_to')
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
            <th class="py-3" scope="col">メモ</th>
            <th class="py-3" scope="col">タグ</th>
            <th class="py-3" scope="col"></th>
            <th class="py-3" scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach($page['data'] as $row)
        <tr>
            <td class="py-3 text-nowrap">{{ $row['datetime'] }}</td>
            <td class="py-3"><a href="{{route('memo.edit', ['memo' => $row['id']])}}">{{ $row['intro'] }}</a></td>
            <td class="py-3">
                @foreach($row['tagNames'] as $tag)
                <a href="{{route('memo.index', ['tags[]' => $tag])}}" class="badge bg-secondary">{{ $tag }}</a>
                @endforeach
            </td>
            <td class="py-3">
                <form action="{{route('parts.add', ['memo' => $row['id']])}}" method="POST" data-parts="add">
                    @csrf
                    @method('PUT')
                    <button type="submit" value="追加" class="m-0 p-0 btn btn-link" {{in_array($row['id'], $parts)?' disabled':''}} data-bs-toggle="tooltip" data-bs-title="パーツ追加">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0z" />
                        </svg>
                    </button>
                </form>
            </td>
            <td class="py-3 text-end">
                <form action="{{route('memo.destroy', ['memo' => $row['id']])}}" method="POST">
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
<div class="mt-3 col-lg-3 mx-auto">
    <span class="d-grid gap-3"><a href="{{route('parts.index')}}" class="btn btn-sm btn-secondary">パーツ一覧へ</a></span>
</div>
@endsection