<?php

declare(strict_types=1);

namespace Akse1a\Seo\ValueObject;

use Akse1a\Seo\Exception\InvalidArgumentException;

/**
 * Value Object for URL
 */
final class Url
{
    private readonly string $value;

    public function __construct(string $url)
    {
        $this->validate($url);
        $this->value = $url;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $url): void
    {
        if (empty(trim($url))) {
            throw new InvalidArgumentException('URL cannot be empty');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf('Invalid URL format: %s', $url));
        }
    }
}

