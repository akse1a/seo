# SEO Library

[![Latest Version](https://img.shields.io/packagist/v/akse1a/seo)](https://packagist.org/packages/akse1a/seo)
[![PHP Version](https://img.shields.io/packagist/php-v/akse1a/seo)](https://packagist.org/packages/akse1a/seo)

A PHP library for working with SEO meta tags, Open Graph, Schema.org markup, and sitemap.xml generation.

## Installation

Install via Composer:

```bash
composer require akse1a/seo
```

Or add to your `composer.json`:

```json
{
    "require": {
        "akse1a/seo": "^1.0"
    }
}
```

## Usage

### Basic Usage

```php
<?php

use Akse1a\Seo\Seo;

$seo = new Seo();

// Set basic meta tags
$seo->setTitle('Page Title')
    ->setDescription('Page description')
    ->setKeywords(['keyword', 'word', 'more']);

// Open Graph
$seo->setImage('https://example.com/image.jpg')
    ->setUrl('https://example.com/page')
    ->setType(\Akse1a\Seo\Enum\OpenGraphType::ARTICLE);

// Output HTML
echo $seo->render();
```

### Adding Custom Meta Tags

```php
$seo->addMetaTag('author', 'Author Name')
    ->addMetaTag('robots', 'index, follow');
```

### Adding Open Graph Tags

```php
$seo->addOpenGraph('og:site_name', 'Site Name')
    ->addOpenGraph('og:locale', 'en_US');
```

### Schema.org Markup

```php
$seo->addSchema([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => 'Article Headline',
    'author' => [
        '@type' => 'Person',
        'name' => 'Author Name'
    ]
]);
```

### Generating sitemap.xml

#### Basic Example

```php
<?php

use Akse1a\Seo\Sitemap;
use Akse1a\Seo\Enum\ChangeFrequency;
use Akse1a\Seo\Exception\RuntimeException;

$sitemap = new Sitemap();

// Add URLs
$sitemap->addUrl('https://example.com/')
    ->addUrl(
        'https://example.com/page',
        '2024-01-15',  // lastmod
        'weekly',      // changefreq
        0.8            // priority
    )
    ->addUrlWithNow('https://example.com/new-page', ChangeFrequency::DAILY, 1.0);

// Generate XML string
$xml = $sitemap->generate();
echo $xml;

// Save to file sitemap.xml
try {
    $sitemap->save('sitemap.xml');
    echo "Sitemap successfully saved to sitemap.xml\n";
} catch (RuntimeException $e) {
    echo "Error saving: " . $e->getMessage() . "\n";
}
```

#### Full Example with Error Handling

```php
<?php

use Akse1a\Seo\Sitemap;
use Akse1a\Seo\Enum\ChangeFrequency;
use Akse1a\Seo\Exception\InvalidArgumentException;
use Akse1a\Seo\Exception\RuntimeException;

try {
    $sitemap = new Sitemap();
    
    // Add URLs with different parameters
    $sitemap->addUrl('https://example.com/')
        ->addUrl(
            'https://example.com/about',
            new \DateTime('2024-01-15'),
            ChangeFrequency::MONTHLY,
            0.8
        )
        ->addUrl(
            'https://example.com/blog',
            '2024-01-20',
            ChangeFrequency::WEEKLY,
            0.9
        )
        ->addUrlWithNow('https://example.com/contact', ChangeFrequency::YEARLY, 0.7);
    
    // Generate XML
    $xml = $sitemap->generate();
    
    // Save to file
    $filename = __DIR__ . '/sitemap.xml';
    $sitemap->save($filename);
    
    // Check if file was created
    if (file_exists($filename)) {
        echo sprintf("Sitemap successfully created: %s (%d bytes)\n", $filename, filesize($filename));
    }
    
} catch (InvalidArgumentException $e) {
    echo "Validation error: " . $e->getMessage() . "\n";
} catch (RuntimeException $e) {
    echo "Runtime error: " . $e->getMessage() . "\n";
}
```

#### Output Sitemap Directly to Browser

```php
<?php

use Akse1a\Seo\Sitemap;

$sitemap = new Sitemap();
$sitemap->addUrl('https://example.com/')
    ->addUrl('https://example.com/page');

// Output with correct HTTP headers
$sitemap->output();
exit;
```

#### Parameters for addUrl()

- `$loc` (required) - Page URL (automatically validated)
- `$lastmod` (optional) - Last modification date (format: Y-m-d or DateTimeInterface object)
- `$changefreq` (optional) - Change frequency: string or `ChangeFrequency` enum
- `$priority` (optional) - Priority from 0.0 to 1.0 (automatically validated)

#### Error Handling

The library throws exceptions for invalid data:

```php
use Akse1a\Seo\Exception\InvalidArgumentException;
use Akse1a\Seo\Sitemap;

try {
    $sitemap = new Sitemap();
    $sitemap->addUrl('invalid-url'); // Will throw InvalidArgumentException
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}
```

#### Example with Multiple Pages

```php
$sitemap = new Sitemap();

$pages = [
    ['url' => 'https://example.com/', 'priority' => 1.0, 'changefreq' => 'daily'],
    ['url' => 'https://example.com/about', 'priority' => 0.8, 'changefreq' => 'monthly'],
    ['url' => 'https://example.com/contact', 'priority' => 0.7, 'changefreq' => 'monthly'],
];

foreach ($pages as $page) {
    $sitemap->addUrlWithNow($page['url'], $page['changefreq'], $page['priority']);
}

$sitemap->save('sitemap.xml');
```

## Development

### Requirements

- PHP 8.0 or higher
- Composer

### Install Dependencies for Development

```bash
composer install
```

### Run Examples

```bash
# Run sitemap generation example
php example_sitemap.php
```

## License

MIT

## Author

akse1a
