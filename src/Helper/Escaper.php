<?php

declare(strict_types=1);

namespace Akse1a\Seo\Helper;

/**
 * Helper for HTML/XML escaping
 */
final class Escaper
{
    /**
     * Escape for HTML
     */
    public static function html(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape for XML
     */
    public static function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
    }
}

