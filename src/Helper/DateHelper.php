<?php

declare(strict_types=1);

namespace Akse1a\Seo\Helper;

use DateTime;
use DateTimeInterface;

/**
 * Helper for working with dates
 */
final class DateHelper
{
    /**
     * Convert date to sitemap format (Y-m-d)
     *
     * @param string|DateTimeInterface|null $date
     * @return string|null
     * @throws \Akse1a\Seo\Exception\InvalidArgumentException
     */
    public static function toSitemapFormat(string|DateTimeInterface|null $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        try {
            $dateTime = new DateTime($date);
            return $dateTime->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Akse1a\Seo\Exception\InvalidArgumentException(
                sprintf('Invalid date format: %s. Expected Y-m-d or DateTime object', $date),
                0,
                $e
            );
        }
    }
}

