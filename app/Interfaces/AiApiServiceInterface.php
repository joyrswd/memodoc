<?php
namespace App\Interfaces;

interface AiApiServiceInterface
{
    public function getKey(): string;
    public function getDailyLimit(): int;
    public function sendRequest(array $parts): array;
    public function isError(array $response): bool;
    public function getTitle(array $response): string;
    public function getContent(array $response, ?string $title = null): string;
}
