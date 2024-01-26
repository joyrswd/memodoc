<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PostCountRule implements ValidationRule
{
    public const MAX_LENGTH = 280;
    public const MIN_LENGTH = 10;
    public const MAX_URL_LENGTH = 23;
    public const ZENKAKU_PATTERN = '/[^\x01-\x7E\xA1-\xDF]/u';
    public const URL_PATTERN = '/https?:\/\/[^\s]+/iu';

    private int $hasTag;
    private array $tags;

    public function __construct(int $hasTag, array $tags)
    {
        $this->hasTag = $hasTag;
        $this->tags = $tags;
    }

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
        $value = $this->convert(trim($value));
        $length = strlen($value);
        return ($length < self::MIN_LENGTH) ;
    }

    private function invalidMaxPostCheck(string $value, string $tags): bool
    {
        $value = $this->convert($value);
        $length = strlen($value);
        $tags = $this->convert($tags);
        $length += strlen($tags);
        return ($length > self::MAX_LENGTH);
    }

    /**
     * 全角文字を半角スペース2つに変換する
     */
    private function convert(string $value): string
    {
        $hanString = preg_replace(self::ZENKAKU_PATTERN, 'aa', $value);
        $noUlrString = preg_replace_callback(self::URL_PATTERN, function ($matches) {
            return strlen($matches[0]) > self::MAX_URL_LENGTH ? str_repeat('a', self::MAX_URL_LENGTH) : $matches[0];
        }, $hanString);
        $string = str_replace("\r\n", "\n", $noUlrString);
        return $string;
    }
}
