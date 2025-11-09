<?php

declare(strict_types=1);

namespace Akse1a\Seo\Enum;

/**
 * Enum for Open Graph types
 */
enum OpenGraphType: string
{
    case WEBSITE = 'website';
    case ARTICLE = 'article';
    case BOOK = 'book';
    case PROFILE = 'profile';
    case MUSIC = 'music.song';
    case VIDEO = 'video.movie';
}

