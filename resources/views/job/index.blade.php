@extends('common.layout')

@section('title','ジョブ一覧')

@section('content')
<div class="text-end">
    <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#finder" aria-expanded="false" aria-controls="finder">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
        </svg>
    </button>
</div>
<div class="collapse{{ ($errors->any() || request()->hasAny('job_content', 'job_tags', 'job_from', 'job_to')) ? ' show' : '' }}" id="finder">
    <form action="{{route('job.index')}}" method="GET" class="mb-3 p-4 small shadow">
        <div class="row mt-3">
            <label for="from" class="col-sm-2 col-form-label">日付</label>
            <div class="col-sm-10">
                <div class="row">
                    <span class="col-auto"><input type="text" class="form-control" name="job_from" id="from" value="{{old('job_from', request()->input('job_from'))}}" placeholder="開始"></span>
                    <span class="col-auto"><input type="text" class="form-control" name="job_to" id="to" value="{{old('job_to', request()->input('job_to'))}}" placeholder="終了"></span>
                </div>
                @error('job_from')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                @error('job_to')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mt-3">
            <span class="col-sm-2 col-form-label">ステータス</span>
            <div class="col-sm-10">
                <!--チェックボックスで条件選択-->
                @foreach($statuses as $status)
                <span class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="job_status[]" value="{{$status}}" id="status_{{$status}}" {{in_array($status, request()->input('job_status', [])) ? 'checked' : ''}}>
                    <label class="form-check-label" for="status_{{$status}}">{{$status}}</label>
                </span>
                @endforeach
                @error('job_status')
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
            <th class="py-3 text-nowrap" scope="col">登録日時</th>
            <th class="py-3 text-nowrap" scope="col">ステータス</th>
            <th class="py-3 text-nowrap" scope="col">メッセージ</th>
            <th class="py-3 text-nowrap" scope="col">作成文書</th>
            <th class="py-3" scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach($page['data'] as $row)
        <tr>
            <td class="py-3 text-nowrap">{{ $row['datetime'] }}</td>
            <td class="py-3">{{ $row['status'] }}</td>
            <td class="py-3">{{ $row['error_message'] }}</td>
            <td class="py-3">
                @if(is_null($row['docId']) === false)
                <a href="{{route('doc.edit', ['doc' => $row['docId']])}}">{{ $row['listTitle'] }}</a>
                @endif
            </td>
            <td class="py-3 text-end">
                @if($row['deletable'])
                <form action="{{route('job.destroy', ['job' => $row['id']])}}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" value="削除" class="m-0 p-0 btn btn-link text-secondary" data-dialog='{"title":"削除確認","body":"削除します。よろしいですか？"}' data-bs-toggle="tooltip" data-bs-title="削除">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                        </svg>
                    </button>
                </form>
                @endif
                @if($row['regeneratable'])
                <form action="{{route('job.store')}}" method="POST">
                    @csrf
                    <input type="hidden" name="regenerate" value="{{$row['id']}}">
                    <button type="submit" value="再作成" class="m-0 p-0 btn btn-link text-secondary" data-dialog='{"title":"再作成確認","body":"文書を再作成します。よろしいですか？"}' data-bs-toggle="tooltip" data-bs-title="再作成">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
                        </svg>
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="py-3">
    {{ $page['navigation'] }}
</div>
@endsection