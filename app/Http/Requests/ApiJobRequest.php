<?php

namespace App\Http\Requests;

use App\Services\PartsService;
use App\Services\ApiJobService;
use App\Services\MemoService;
use Illuminate\Foundation\Http\FormRequest;

class ApiJobRequest extends FormRequest
{
    private array $_rules = [
        'job.index' => [
            'job_status' => 'nullable|array',
            'job_status.*' => ['required'],
            'job_from' => 'nullable|date|before_or_equal:today|exclude_without:job_to|before_or_equal:job_to',
            'job_to' => 'nullable|date|before_or_equal:today|exclude_without:job_from|after_or_equal:job_from',
        ],
        'job.store' => [
            'regenerate' => ['nullable','integer'],
            'memos' => 'required|array',
            'memos.*' => ['required','integer'],
        ],
    ];

    private PartsService $partsService;
    private ApiJobService $apiJobService;
    private MemoService $memoService;

    public function __construct(PartsService $partsService, ApiJobService $apiJobService, MemoService $memoService)
    {
        $this->partsService = $partsService;
        $this->apiJobService = $apiJobService;
        $this->memoService = $memoService;
        $this->_rules['job.store']['memos.*'][] = function ($attribute, $value, $fail) {$this->isYourMemo($attribute, $value, $fail);};
        $this->_rules['job.store']['regenerate'][] = function ($attribute, $value, $fail) {$this->isRegeneratable($attribute, $value, $fail);};
        $this->_rules['job.index']['job_status.*'][] = function ($attribute, $value, $fail) {$this->inStatuses($attribute, $value, $fail);};
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->_rules[$this->route()->getName()];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $memos = [];
        $userId = auth()->id();
        if ($this->has('generate')) {
            //パーツから作成の場合はセッションからメモIDを取得
            $memos = $this->partsService->getMemoValues($userId, 'id');
        } elseif ($this->has('regenerate')) {
            //再生成の場合はapiJobからメモIDを取得
            $memos = $this->apiJobService->getMemoIds($this->input('regenerate'));
        }
        if (empty($memos) === false) {
            $this->merge(['memos' => $memos]);
        }
    }

    /**
     * Handle the request after validation.
     */
    protected function passedValidation()
    {
        //パーツから作成の場合はセッションを削除
        if ($this->has('generate') && $this->has('memos')) {
            $this->partsService->deleteParts();
        }
    }

    private function isRegeneratable($attribute, $value, $fail)
    {
        $userId = auth()->id();
        $apiJob = $this->apiJobService->getApiJob($userId, $value);
        if (empty($apiJob) === true || $this->apiJobService->isRegeneratable($apiJob['status']) === false) {
            $fail('再生成できないジョブです。');
        }
    }

    private function inStatuses($attribute, $value, $fail)
    {
        if (in_array($value, $this->apiJobService->getStatuses()) === false) {
            $fail('不正な値が含まれています。');
        }
    }

    private function isYourMemo($attribute, $value, $fail)
    {
        $userId = auth()->id();
        $memo = $this->memoService->getMemo($userId, $value);
        if (empty($memo)) {
            $fail('不正な値が含まれています。');
        }
    }
}
