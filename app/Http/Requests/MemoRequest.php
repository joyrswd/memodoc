<?php

namespace App\Http\Requests;

use App\Rules\PostCountRule;
use App\Rules\TagRule;
use App\Services\MemoService;
use Illuminate\Foundation\Http\FormRequest;

class MemoRequest extends FormRequest
{
    /**
     * @var array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    private $_rules = [
        'memo.index' => [
            'memo_content' => 'nullable|min:2|max:100',
            'memo_from' => 'nullable|date|before_or_equal:today|exclude_without:memo_to|before_or_equal:memo_to',
            'memo_to' => 'nullable|date|before_or_equal:today|exclude_without:memo_from|after_or_equal:memo_from',
        ],
        'memo.store' => [
            'memo_content' => ['required', 'string'],
            'tags' => 'present_if:has_tag,1|array',
        ],
        'memo.update' => [
            'tags' => 'present_if:has_tag,1|array',
        ],
    ];

    private MemoService $memoService;

    public function __construct(MemoService $memoService)
    {
        $this->memoService = $memoService;
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
        $tags = $this->input('tags');
        is_array($tags) || $tags = [];
        $postRule = new PostCountRule((int)$this->input('has_tag', 0), $tags);
        $key = $this->route()->getName();
        $this->_rules['memo.store']['memo_content'][] = $postRule;
        $this->_rules['memo.update']['memo_content'][] = $postRule;
        return array_merge($this->_rules[$key], [
            'tags.*' => ['distinct', new TagRule($key)],
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
        if($this->isMethod('GET') === false && $this->input('has_tag') != 1){
            $this->merge(['tags' => []]);
        }
        if ($this->route()->getName() === 'memo.update') {
            $memoId = $this->route()->parameter('memo');
            $this->merge(['memo_content' => $this->memoService->getMemo(auth()->id(), $memoId)['content']]);
        }
    }
}
