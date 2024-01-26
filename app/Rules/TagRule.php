<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TagRule implements ValidationRule
{
    const MIN_LENGTH = 1;
    const MAX_LENGTH = 20;
    const TAG_PATTERN = '/[\p{P}\p{Z}]/u';//半角記号、全角記号、半角スペース、全角スペース

    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail("{$attribute}は必須です");
        }
        if ($this->key === 'memo.index' && mb_strlen($value) < self::MIN_LENGTH) {
            $fail("{$attribute}は" . self::MIN_LENGTH . "文字以上入力してください");
        }
        if (mb_strlen($value) > self::MAX_LENGTH) {
            $fail("{$attribute}は" . self::MAX_LENGTH . "文字以内で入力してください");
        }
        if (preg_match(self::TAG_PATTERN, $value)) {
            $fail("{$attribute}に不正な文字列が含まれています");
        }
    }

}