<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemoRequest extends FormRequest
{
    /**
     * @var array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    private $_rules = [
        'memo.index' => [
            'memo_content' => 'nullable|min:2|max:100',
            'memo_from' => 'nullable|date|before_or_equal:today|exclude_if:memo_to,null|before_or_equal:memo_to',
            'memo_to' => 'nullable|date|before_or_equal:today|exclude_if:memo_from,null|after_or_equal:memo_from',
        ],
        'memo.store' => [
            'memo_content' => 'required|min:5',
        ],
        'memo.update' => [],
    ];
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
        return array_merge($this->_rules[$this->route()->getName()], [
            'tags' => 'nullable|array',
            'tags.*' => 'string|min:2|max:20|regex:/^[^!-\\/:-@[-`{-~]+$/',
        ]);
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tags.*' => __('validation.attributes.tag'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() : void
    {
        $tags = $this->input('memo_tags');
        if ($tags) {
            $tagsArray = explode(' ', str_replace('　', ' ', $tags));
            $this->merge(['tags' => array_values(array_filter($tagsArray))]);
        }
    }
}
