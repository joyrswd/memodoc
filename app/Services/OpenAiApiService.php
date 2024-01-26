<?php

namespace App\Services;

use App\Interfaces\AiApiServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiApiService implements AiApiServiceInterface
{
    public const KEY = 'openai';
    private array $config;
    private array $keys = ['endpoint', 'secret', 'model', 'prompt', 'timeout', 'daily_limit'];

    public function __construct(array $config)
    {
        if ($this->validateConfig($config) === false) {
            throw new \Exception('configの値が不正です');
        }
        $this->config = $config;
    }

    /**
     * キーを返す
     */
    public function getKey(): string
    {
        return self::KEY;
    }

    /**
     * 1日のリクエスト回数の上限を返す
     */
    public function getDailyLimit(): int
    {
        return $this->config['daily_limit'];
    }

    /**
     * ChatGPTのAPIを使って複数のメモから文書を生成する
     */
    public function sendRequest(array $parts): array
    {
        $data = [
            'model' => $this->config['model'],
            'messages' => $this->makeMessages($this->config['prompt'], $parts),
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config['secret']
            ])->timeout($this->config['timeout'])->post($this->config['endpoint'], $data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
        return empty($response) ? [] : $response->json();
    }

    /**
     * ChatGPTのAPIからエラーが返ってきたかどうかを判定する
     */
    public function isError(array $response): bool
    {
        return empty($response) || isset($response['error']);
    }

    /**
     * ChatGPTのAPIから返ってきたレスポンスからタイトルを取得する
     */
    public function getTitle(array $response): string
    {
        $text = $this->getText($response);
        //1行目を取得する
        return explode("\n", $text)[0];
    }

    /**
     * ChatGPTのAPIから返ってきたレスポンスから本文を取得する
     */
    public function getContent(array $response): string
    {
        $text = $this->getText($response);
        // 2行目以降を取得する
        $lines = array_slice(explode("\n", $text), 1);
        return trim(implode("\n", $lines));
    }

    /**
     * ChatGPTのAPIから返ってきたレスポンスからテキストを取得する
     */
    private function getText(array $response): string
    {
        return $response['choices'][0]['message']['content'];
    }

    /**
     * configにキーがすべて存在するかどうかを判定する
     */
    private function validateConfig(array $config): bool
    {
        foreach ($this->keys as $key) {
            if (!isset($config[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * ChatGPTのAPIに送るメッセージを生成する
     */
    private function makeMessages(string $prompt, array $parts): array
    {
        if (empty($parts)) {
            throw new \Exception('有効な$partsが無いため処理を中断しました。');
        }
        // $partsの数量を$promptに含める
        $content = str_replace('{count}', count($parts), $prompt);
        $messages = [
            [
                "role" => "system",
                "content" => $content,
            ],
        ];
        foreach ($parts as $content) {
            $messages[] = [
                "role" => "user",
                "content" => $content
            ];
        }
        return $messages;
    }
}
