<?php

declare(strict_types=1);

namespace Akse1a\Seo\Enum;

/**
 * Enum for change frequency in sitemap
 */
enum ChangeFrequency: string
{
    case ALWAYS = 'always';
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
    case NEVER = 'never';

    /**
     * Check if string is a valid change frequency
     */
    public static function isValid(string $value): bool
    {
        return self::tryFrom(strtolower($value)) !== null;
    }

    /**
     * Get enum from string or null
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtolower($value));
    }
}

