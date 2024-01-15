<?php

namespace App\Http\Requests;

use App\Services\PartsService;
use App\Services\ApiJobService;
use Illuminate\Foundation\Http\FormRequest;

class ApiJobRequest extends FormRequest
{
    private $_rules = [
        'job.index' => [
            'job_status' => 'nullable|array',
            'job_from' => 'nullable|date|before_or_equal:today|exclude_if:job_to,null|before_or_equal:job_to',
            'job_to' => 'nullable|date|before_or_equal:today|exclude_if:job_from,null|after_or_equal:job_from',
        ],
        'job.store' => [
            'regenerate' => ['nullable','integer'],
            'memos' => 'required|array',
            'memos.*' => 'required|integer|exists:memos,id',
        ],
    ];

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
        $this->_rules['job.store']['regenerate'][] = function ($attribute, $value, $fail) {$this->isRegeneratable($attribute, $value, $fail);};
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
    protected function prepareForValidation() : void
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
        if (empty($apiJob) === false && $this->apiJobService->isRegeneratable($apiJob['status']) === false) {
            $fail('再生成できないジョブです。');
        }
    }

}
