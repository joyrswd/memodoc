<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Services\MemoService;
use App\Services\PartsService;
use App\Http\Requests\MemoRequest;
use Illuminate\Http\Request;

use function Psy\debug;

class MemoController extends Controller
{
    /**
     * @var MemoService
     */
    private $memoService;

    /**
     * @var PartsService
     */
    private $partsService;

    /**
     * @param MemoService $memoService
     * @param PartsService $partsService
     */
    public function __construct(MemoService $memoService, PartsService $partsService)
    {
        $this->memoService = $memoService;
        $this->partsService = $partsService;
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
            'parts' => $this->partsService->getStatus('items')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('memo.create');
        //
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
        return back()->with('success', __('stored'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('memo.edit', [
            'memo' => $this->memoService->getMemo(auth()->id(), $id),
        ]);
        //
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
