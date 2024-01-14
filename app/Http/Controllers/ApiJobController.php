<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateDocumentJob;
use App\Services\ApiJobService;
use App\Http\Requests\ApiJobRequest as Request;

class ApiJobController extends Controller
{

    /**
     * @var ApiJobService
     */
    private $apiJobService;

    /**
     * @param ApiJobService $apiJobService
     */
    public function __construct(ApiJobService $apiJobService)
    {
        $this->apiJobService = $apiJobService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = auth()->id();
        $jobId = $this->apiJobService->prepare($userId, $request->input('memos'));
        GenerateDocumentJob::dispatch($userId, $jobId, $this->apiJobService);
        return redirect()->route('memo.index')->with('success', '文書生成をバックグラウンドジョブに登録しました。');
    }

}
