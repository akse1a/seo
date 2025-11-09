<?php

declare(strict_types=1);

namespace Akse1a\Seo\ValueObject;

use Akse1a\Seo\Exception\InvalidArgumentException;

/**
 * Value Object for sitemap priority (0.0 - 1.0)
 */
final class Priority
{
    private const float MIN = 0.0;
    private const float MAX = 1.0;

    private readonly float $value;

    public function __construct(float $priority)
    {
        $this->validate($priority);
        $this->value = $this->normalize($priority);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        return number_format($this->value, 1, '.', '');
    }

    private function validate(float $priority): void
    {
        if ($priority < self::MIN || $priority > self::MAX) {
            throw new InvalidArgumentException(
                sprintf('Priority must be between %.1f and %.1f, got %.1f', self::MIN, self::MAX, $priority)
            );
        }
    }

    private function normalize(float $priority): float
    {
        return max(self::MIN, min(self::MAX, $priority));
    }
}

