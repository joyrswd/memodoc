<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemoRequest extends FormRequest
{
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
            'memo.add.content' => 'required|min:5',
            'tags' => 'nullable|array',
            'tags.*' => 'string|min:2|max:20|regex:/^[^ -\\/:-@\[-~]+$/',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'memo.add.content' => __('validation.attributes.memo.content'),
            'memo.add.tags' => __('validation.attributes.tag'),
            'tags.*' => __('validation.attributes.tag'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() : void
    {
        $tags = $this->input('memo.add.tags');
        if ($tags) {
            $tagsArray = explode(' ', str_replace('　', ' ', $tags));
            $this->merge(['tags' => array_values(array_filter($tagsArray))]);
        }
    }
}
