<?php

declare(strict_types=1);

namespace Kopling\Contact;

use Illuminate\Support\Str;
use Kopling\Core\Settings\Settings;

final class Config
{
    public const SUBJECTS = 'subjects';

    public const NOTIFY_ADDRESS = 'notify-address';

    public const RETENTION_DAYS = 'retention-days';

    public const COOLDOWN_MINUTES = 'cooldown-minutes';

    public const PER_HOUR = 'per-hour';

    public const MAX_LINKS = 'max-links';

    public const DEFAULT_SUBJECTS = "question = A question\nother = Something else";

    public const DEFAULT_RETENTION_DAYS = 7;

    public const DEFAULT_COOLDOWN_MINUTES = 60;

    public const DEFAULT_PER_HOUR = 5;

    public const DEFAULT_MAX_LINKS = 3;

    /**
     * @return array<string, string>
     */
    public static function subjects(): array
    {
        $subjects = [];

        foreach (preg_split('/\R/', (string) self::get(self::SUBJECTS, self::DEFAULT_SUBJECTS)) as $line) {
            [$key, $label] = str_contains($line, '=')
                ? array_map('trim', explode('=', $line, 2))
                : [trim($line), trim($line)];

            $key = Str::slug($key);

            if ($key !== '' && $label !== '') {
                $subjects[$key] = $label;
            }
        }

        return $subjects ?: ['other' => 'Other'];
    }

    public static function subjectLabel(string $key): string
    {
        return self::subjects()[$key] ?? $key;
    }

    public static function notifyAddress(): ?string
    {
        $address = trim((string) self::get(self::NOTIFY_ADDRESS, ''));

        return filter_var($address, FILTER_VALIDATE_EMAIL) ? $address : null;
    }

    public static function retentionDays(): int
    {
        return max(1, (int) self::get(self::RETENTION_DAYS, self::DEFAULT_RETENTION_DAYS));
    }

    public static function cooldownMinutes(): int
    {
        return max(0, (int) self::get(self::COOLDOWN_MINUTES, self::DEFAULT_COOLDOWN_MINUTES));
    }

    public static function perHour(): int
    {
        return max(1, (int) self::get(self::PER_HOUR, self::DEFAULT_PER_HOUR));
    }

    public static function maxLinks(): int
    {
        return max(0, (int) self::get(self::MAX_LINKS, self::DEFAULT_MAX_LINKS));
    }

    private static function get(string $id, mixed $default): mixed
    {
        $value = Settings::get(Extension::ID.'::'.$id);

        return $value === null || $value === '' ? $default : $value;
    }
}
