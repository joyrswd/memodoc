<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PostCountRule implements ValidationRule
{
    const MAX_LENGTH = 280;
    const MIN_LENGTH = 10;
    const ZENKAKU_PATTERN = '[^\x01-\x7E\xA1-\xDF]';

    private int $hasTag;
    private array $tags;

    /**
     * Create a new rule instance.
     *
     * @param int $hasTag
     * @param array $tags
     */
    public function __construct(int $hasTag, array $tags)
    {
        $this->hasTag = $hasTag;
        $this->tags = $tags;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->invalidMinContentCheck($value)) {
            $fail('半角' . self::MIN_LENGTH . '文字以上入力してください');
        }
        $tags = $this->hasTag ? implode(' ', $this->tags) : '';
        if ($this->invalidMaxPostCheck($value, $tags)) {
            $fail('半角' . self::MAX_LENGTH . '文字以内で入力してください');
        }
    }

    private function invalidMinContentCheck(string $value): bool
    {
        $value = $this->zen2han(trim($value));
        $length = strlen($value);
        return ($length < self::MIN_LENGTH) ;
    }

    private function invalidMaxPostCheck(string $value, string $tags): bool
    {
        $value = $this->zen2han($value);
        $length = strlen($value);
        $tags = $this->zen2Han($tags);
        $length += strlen($tags);
        return ($length > self::MAX_LENGTH);
    }

    /**
     * 全角文字を半角スペース2つに変換する
     */
    private function zen2han(string $value): string
    {
        return preg_replace('/' . self::ZENKAKU_PATTERN . '/u', '  ', $value);
    }
}
