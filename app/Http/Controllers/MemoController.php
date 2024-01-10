<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Services\MemoService;
use App\Http\Requests\MemoRequest;
use Illuminate\Http\Request;

class MemoController extends Controller
{
    /**
     * @var MemoService
     */
    private $memoService;
    
    /**
     * @param MemoService $memoService
     */
    public function __construct(MemoService $memoService)
    {
        $this->memoService = $memoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MemoRequest $request)
    {
        return view('memo.index', [
            'page' => $this->memoService->getMemos($request->user()->id, [
                'content' => $request->input('memo_content'),
                'tags' => $request->input('tags'),
                'from' => $request->input('memo_from'),
                'to' => $request->input('memo_to'),
            ]),
        ]);
        //
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
            'content' => $request->input('memo_content'),
            'user_id' => $request->user()->id,
            'tags' => $request->input('tags'),
        ]);
        return back()->with('success', __('stored'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemoRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
