<?php

namespace App\Http\Requests;

use App\Services\PartsService;
use Illuminate\Foundation\Http\FormRequest;

class ApiJobRequest extends FormRequest
{
    /**
     * @var PartsService
     */
    private $partsService;

    /**
     * @param PartsService $partsService
     */
    public function __construct(PartsService $partsService)
    {
        $this->partsService = $partsService;
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
        return [
            'memos' => 'filled|array',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() : void
    {
        //パーツから作成の場合はセッションから取得
        if ($this->has('generate')) {
            $memos = $this->partsService->getMemoValues(auth()->id(), 'id');
            if (empty($memos) === false) {
                $this->merge(['memos' => $memos]);
            }
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
}
