<?php

namespace App\Http\Controllers;

use App\Services\ApiJobService;
use App\Http\Requests\ApiJobRequest;
use App\Jobs\GenerateDocumentJob;

class ApiJobController extends Controller
{

    private ApiJobService $apiJobService;

    public function __construct(ApiJobService $apiJobService)
    {
        $this->apiJobService = $apiJobService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ApiJobRequest $request)
    {
        return view('job.index', [
            'page' => $this->apiJobService->getApiJobs(auth()->id(), [
                'status' => $request->input('job_status'),
                'from' => $request->input('job_from'),
                'to' => $request->input('job_to'),
            ]),
            'statuses' => $this->apiJobService->getStatuses(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApiJobRequest $request)
    {
        $userId = auth()->id();
        $result = $this->apiJobService->prepare($userId, $request->input('memos'), fn(...$args) => GenerateDocumentJob::dispatch(...$args));
        if ($result === true) {
            if ($request->has('regenerate')) {
                //　再作成の場合は元のジョブを削除
                $this->apiJobService->deleteApiJob($userId, $request->input('regenerate'));
            }
            return redirect()->route('job.index')->with('success', '文書生成をジョブに登録しました。');
        }
        return redirect()->back()->with('failed', '文書生成のジョブ登録に失敗しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->apiJobService->deleteApiJob(auth()->id(), $id);
        return back()->with('success', 'ジョブを削除しました。');
    }

}
