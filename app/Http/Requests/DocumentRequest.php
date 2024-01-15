<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    /**
     * @var array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    private $_rules = [
        'doc.index' => [
            'doc_title' => 'nullable|min:2|max:100',
            'doc_content' => 'nullable|min:2|max:100',
            'doc_from' => 'nullable|date|before_or_equal:today|exclude_without:doc_to|before_or_equal:doc_to',
            'doc_to' => 'nullable|date|before_or_equal:today|exclude_without:doc_from|after_or_equal:doc_from',
        ],
        'doc.update' => [
            'doc_title' => 'required|max:255',
            'doc_content' => 'required|min:5',
        ],
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
        return $this->_rules[$this->route()->getName()];
    }

}
