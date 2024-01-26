<?php

namespace App\Enums;

enum ApiJobStatus: string
{
    case Started = 'Started';
    case Waiting = 'Waiting';
    case Processing = 'Processing';
    case Processed = 'Processed';
    case Success = 'Success';
    case Error = 'Error';
    case Aborted = 'Aborted';

    public static function getAll(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getUpcoming(): array
    {
        return [self::Waiting->value, self::Processing->value];
    }

    public static function isDeletable($status): bool
    {
        return in_array($status, [self::Started->value, self::Waiting->value, self::Error->value, self::Aborted->value]);
    }

    public static function isRegeneratable($status): bool
    {
        return in_array($status, [self::Aborted->value]);
    }

}
