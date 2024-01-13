<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateDocumentJob;
use App\Services\PartsService;
use App\Services\ApiJobService;
use Illuminate\Http\Request;


class DocumentController extends Controller
{
    /**
     * @var PartsService
     */
    private $partsService;

    /**
     * @var ApiJobService
     */
    private $apiJobService;

    /**
     * @param PartsService $partsService
     */
    public function __construct(PartsService $partsService, ApiJobService $apiJobService)
    {
        $this->partsService = $partsService;
        $this->apiJobService = $apiJobService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = auth()->id();
        $parts = $this->partsService->getMemoValues($userId, 'id');
        $jobId = $this->apiJobService->prepare($userId, $parts);
        GenerateDocumentJob::dispatch($userId, $jobId, $this->apiJobService);
        $this->partsService->deleteParts();
        return redirect()->route('memo.index')->with('success', '文書生成をバックグラウンドジョブに登録しました。');
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
    public function update(Request $request, string $id)
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
