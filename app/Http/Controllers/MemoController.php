<?php

namespace App\Http\Controllers;

use App\Services\MemoService;
use App\Services\PartsService;
use App\Http\Requests\MemoRequest;
use App\Services\UserService;

class MemoController extends Controller
{
    private MemoService $memoService;
    private PartsService $partsService;
    private UserService $userService;

    public function __construct(MemoService $memoService, PartsService $partsService, UserService $userService)
    {
        $this->memoService = $memoService;
        $this->partsService = $partsService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MemoRequest $request)
    {
        return view('memo.index', [
            'page' => $this->memoService->getMemos(auth()->id(), [
                'content' => $request->input('memo_content'),
                'tags' => $request->input('tags'),
                'from' => $request->input('memo_from'),
                'to' => $request->input('memo_to'),
            ]),
            'parts' => $this->partsService->getStatus('items'),
            'datalist' => $this->userService->getTags(auth()->id()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('memo.create', [
            'datalist' => $this->userService->getTags(auth()->id()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemoRequest $request)
    {
        $this->memoService->addMemoAndTags([
            'user_id' => auth()->id(),
            'content' => $request->input('memo_content'),
            'tags' => $request->input('tags'),
        ]);
        // 連続作成の場合は入力画面へ、それ以外は一覧画面へ
        return $request->has('add_next') ? back()->with('success', __('stored'))->withInput(['add_next' => $request->input('add_next'), 'tags' => $request->input('tags')])
                : redirect()->route('memo.index')->with('success', __('stored'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('memo.edit', [
            'memo' => $this->memoService->getMemo(auth()->id(), $id),
            'datalist' => $this->userService->getTags(auth()->id()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemoRequest $request, string $id)
    {
        $this->memoService->updateTags([
            'memo_id' => $id,
            'tags' => $request->input('tags'),
        ]);
        return back()->with('success', __('updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->memoService->deleteMemo(auth()->id(), $id);
        return back()->with('success', __('deleted'));
    }
}
