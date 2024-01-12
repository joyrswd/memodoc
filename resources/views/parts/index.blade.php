@extends('common.layout')

@section('title','パーツ一覧')

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
<div class="mt-5 mx-3">
    <div class="float-end">
        <form action="{{route('parts.remove')}}" method="POST" data-parts="remove">
            @csrf
            @method('DELETE')
            <button type="submit" value="消去" {{empty($items)?' disabled':''}} data-dialog='{"texts":{"title":"パーツを空にする","body":"パーツ内のメモを空にします。よろしいですか？"}}' class="m-0 p-0 btn btn-link position-relative" data-bs-toggle="tooltip" data-bs-title="空にする">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-trash2-fill" viewBox="0 0 16 16">
                    <path d="M2.037 3.225A.703.703 0 0 1 2 3c0-1.105 2.686-2 6-2s6 .895 6 2a.702.702 0 0 1-.037.225l-1.684 10.104A2 2 0 0 1 10.305 15H5.694a2 2 0 0 1-1.973-1.671L2.037 3.225zm9.89-.69C10.966 2.214 9.578 2 8 2c-1.58 0-2.968.215-3.926.534-.477.16-.795.327-.975.466.18.14.498.307.975.466C5.032 3.786 6.42 4 8 4s2.967-.215 3.926-.534c.477-.16.795-.327.975-.466-.18-.14-.498-.307-.975-.466z" />
                    <text x="5" y="12" fill="black" font-size="6">空</text>
                </svg>
            </button>
        </form>
    </div>
    <div class="col-lg-7 mx-auto">
        <span class="d-grid gap-3"><button type="submit" {{empty($items)?' disabled':''}} class="btn btn-primary">作成</button></span>
    </div>
</div>
@endsection

@section('asset')
<script>
    (function() {
        // tableが空になったらsubmitボタンを無効化する
        const observer = new MutationObserver(records => {
            const tbody = records[0].target;
            if (tbody.childElementCount === 0) {
                document.querySelectorAll('button[type="submit"]').forEach(button => button.disabled = true);
            }
        });
        observer.observe(document.querySelector('table.table'), {
            childList: true,
            subtree: true
        });
    })();
</script>
@endsection