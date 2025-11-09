<?php

declare(strict_types=1);

namespace Akse1a\Seo;

use Akse1a\Seo\Enum\ChangeFrequency;
use Akse1a\Seo\Exception\InvalidArgumentException;
use Akse1a\Seo\Exception\RuntimeException;
use Akse1a\Seo\Helper\DateHelper;
use Akse1a\Seo\Helper\Escaper;
use Akse1a\Seo\Validator\UrlValidator;
use Akse1a\Seo\ValueObject\Priority;
use DateTimeInterface;

/**
 * Class for generating sitemap.xml according to sitemaps.org standard
 */
final class Sitemap
{
    private const int MAX_URLS = 50000;
    private const string SITEMAP_NAMESPACE = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * @var array<int, array{loc: string, lastmod?: string, changefreq?: string, priority?: string}>
     */
    private array $urls = [];

    /**
     * Add URL to sitemap
     *
     * @param string $loc Page URL
     * @param string|DateTimeInterface|null $lastmod Last modification date
     * @param ChangeFrequency|string|null $changefreq Change frequency
     * @param float|null $priority Priority (0.0 - 1.0)
     * @return self
     * @throws InvalidArgumentException
     */
    public function addUrl(
        string $loc,
        string|DateTimeInterface|null $lastmod = null,
        ChangeFrequency|string|null $changefreq = null,
        ?float $priority = null
    ): self {
        if ($this->count() >= self::MAX_URLS) {
            throw new InvalidArgumentException(
                sprintf('Sitemap cannot contain more than %d URLs', self::MAX_URLS)
            );
        }

        // URL validation
        if (!UrlValidator::isValid($loc)) {
            throw new InvalidArgumentException(sprintf('Invalid URL: %s', $loc));
        }

        $url = ['loc' => $loc];

        // Process lastmod
        if ($lastmod !== null) {
            try {
                $lastmodFormatted = DateHelper::toSitemapFormat($lastmod);
                if ($lastmodFormatted !== null) {
                    $url['lastmod'] = $lastmodFormatted;
                }
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException(
                    sprintf('Invalid lastmod format for URL %s: %s', $loc, $e->getMessage()),
                    0,
                    $e
                );
            }
        }

        // Process changefreq
        if ($changefreq !== null) {
            if ($changefreq instanceof ChangeFrequency) {
                $url['changefreq'] = $changefreq->value;
            } else {
                $freq = ChangeFrequency::tryFromString($changefreq);
                if ($freq === null) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Invalid changefreq: %s. Allowed values: %s',
                            $changefreq,
                            implode(', ', array_column(ChangeFrequency::cases(), 'value'))
                        )
                    );
                }
                $url['changefreq'] = $freq->value;
            }
        }

        // Process priority
        if ($priority !== null) {
            try {
                $priorityObj = new Priority($priority);
                $url['priority'] = $priorityObj->getFormatted();
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException(
                    sprintf('Invalid priority for URL %s: %s', $loc, $e->getMessage()),
                    0,
                    $e
                );
            }
        }

        $this->urls[] = $url;
        return $this;
    }

    /**
     * Add URL with current date as lastmod
     *
     * @param string $loc Page URL
     * @param ChangeFrequency|string|null $changefreq Change frequency
     * @param float|null $priority Priority
     * @return self
     * @throws InvalidArgumentException
     */
    public function addUrlWithNow(
        string $loc,
        ChangeFrequency|string|null $changefreq = null,
        ?float $priority = null
    ): self {
        return $this->addUrl($loc, new \DateTime(), $changefreq, $priority);
    }

    /**
     * Add multiple URLs at once
     *
     * @param array<int, array{loc: string, lastmod?: string|DateTimeInterface, changefreq?: ChangeFrequency|string, priority?: float}> $urls
     * @return self
     * @throws InvalidArgumentException
     */
    public function addUrls(array $urls): self
    {
        foreach ($urls as $urlData) {
            if (!isset($urlData['loc'])) {
                throw new InvalidArgumentException('URL data must contain "loc" key');
            }

            $this->addUrl(
                $urlData['loc'],
                $urlData['lastmod'] ?? null,
                $urlData['changefreq'] ?? null,
                $urlData['priority'] ?? null
            );
        }

        return $this;
    }

    /**
     * Generate XML sitemap
     *
     * @return string
     * @throws RuntimeException
     */
    public function generate(): string
    {
        if (empty($this->urls)) {
            throw new RuntimeException('Cannot generate sitemap: no URLs added');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= sprintf('<urlset xmlns="%s">', self::SITEMAP_NAMESPACE) . "\n";

        foreach ($this->urls as $url) {
            $xml .= "  <url>\n";
            $xml .= sprintf("    <loc>%s</loc>\n", Escaper::xml($url['loc']));

            if (isset($url['lastmod'])) {
                $xml .= sprintf("    <lastmod>%s</lastmod>\n", Escaper::xml($url['lastmod']));
            }

            if (isset($url['changefreq'])) {
                $xml .= sprintf("    <changefreq>%s</changefreq>\n", Escaper::xml($url['changefreq']));
            }

            if (isset($url['priority'])) {
                $xml .= sprintf("    <priority>%s</priority>\n", Escaper::xml($url['priority']));
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Save sitemap to file
     *
     * @param string $filename File path
     * @return bool
     * @throws RuntimeException
     */
    public function save(string $filename): bool
    {
        try {
            $xml = $this->generate();
            $result = file_put_contents($filename, $xml, LOCK_EX);
            
            if ($result === false) {
                throw new RuntimeException(sprintf('Failed to save sitemap to file: %s', $filename));
            }

            return true;
        } catch (\Exception $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            throw new RuntimeException(
                sprintf('Error saving sitemap to file %s: %s', $filename, $e->getMessage()),
                0,
                $e
            );
        }
    }

    /**
     * Output sitemap with correct headers
     *
     * @return void
     * @throws RuntimeException
     */
    public function output(): void
    {
        if (headers_sent()) {
            throw new RuntimeException('Cannot output sitemap: headers already sent');
        }

        header('Content-Type: application/xml; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        
        echo $this->generate();
    }

    /**
     * Clear all URLs
     *
     * @return self
     */
    public function clear(): self
    {
        $this->urls = [];
        return $this;
    }

    /**
     * Get URL count
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->urls);
    }

    /**
     * Check if sitemap is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->urls);
    }

    /**
     * Get all URLs
     *
     * @return array<int, array{loc: string, lastmod?: string, changefreq?: string, priority?: string}>
     */
    public function getUrls(): array
    {
        return $this->urls;
    }
}
