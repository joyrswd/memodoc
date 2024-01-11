@extends('common.layout')

@section('title','メモ一覧')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<table class="table">
    <thead>
        <tr>
            <th class="py-3" scope="col">日時</th>
            <th class="py-3" scope="col">メモ</th>
            <th class="py-3" scope="col">タグ</th>
            <th class="py-3" scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach($items as $row)
        <tr>
            <td class="py-3">{{ \Carbon\Carbon::parse($row['created_at'])->format('Y-m-d H:i') }}</td>
            <td class="py-3"><a href="{{route('memo.edit', ['memo' => $row['id']])}}">{{ Str::limit($row['content'], 30, '...') }}</a></td>
            <td class="py-3">
                @foreach($row['tags'] as $tag)
                <a href="{{route('memo.index', ['memo_tags' => $tag])}}" class="badge bg-secondary">{{ $tag }}</a>
                @endforeach
            </td>
            <td class="py-3">
                <form action="{{route('parts.remove', ['memo' => $row['id']])}}" method="POST" data-parts="remove">
                    @csrf
                    @method('DELETE')
                    <button type="submit" value="削除" class="m-0 p-0 btn btn-link" data-bs-toggle="tooltip" data-bs-title="パーツ除去">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-minus-fill" viewBox="0 0 16 16">
                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM6 8.5h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                        </svg>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection