<?php

declare(strict_types=1);

namespace Akse1a\Seo\Validator;

/**
 * URL validator
 */
final class UrlValidator
{
    /**
     * Validate URL
     *
     * @throws \Akse1a\Seo\Exception\InvalidArgumentException
     */
    public static function validate(string $url): void
    {
        if (empty(trim($url))) {
            throw new \Akse1a\Seo\Exception\InvalidArgumentException('URL cannot be empty');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Akse1a\Seo\Exception\InvalidArgumentException(sprintf('Invalid URL format: %s', $url));
        }
    }

    /**
     * Check if URL is valid (without throwing exception)
     */
    public static function isValid(string $url): bool
    {
        return !empty(trim($url)) && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

