<?php

declare(strict_types=1);

namespace Akse1a\Seo;

use Akse1a\Seo\Enum\OpenGraphType;
use Akse1a\Seo\Exception\InvalidArgumentException;
use Akse1a\Seo\Helper\Escaper;
use Akse1a\Seo\Validator\UrlValidator;

/**
 * Main SEO library class for working with meta tags, Open Graph and Schema.org
 */
final class Seo
{
    private const string TITLE_TAG = 'title';
    private const int MAX_DESCRIPTION_LENGTH = 160;
    private const int MAX_TITLE_LENGTH = 60;

    /** @var array<string, string> */
    private array $metaTags = [];

    /** @var array<string, string> */
    private array $openGraphTags = [];

    /** @var array<int, array<string, mixed>> */
    private array $schemaData = [];

    /**
     * Add meta tag
     *
     * @param string $name Meta tag name
     * @param string $content Meta tag content
     * @return self
     * @throws InvalidArgumentException
     */
    public function addMetaTag(string $name, string $content): self
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Meta tag name cannot be empty');
        }

        $this->metaTags[trim($name)] = trim($content);
        return $this;
    }

    /**
     * Add Open Graph tag
     *
     * @param string $property Open Graph property (e.g., og:title)
     * @param string $content Property value
     * @return self
     * @throws InvalidArgumentException
     */
    public function addOpenGraph(string $property, string $content): self
    {
        if (empty(trim($property))) {
            throw new InvalidArgumentException('Open Graph property cannot be empty');
        }

        if (!str_starts_with($property, 'og:')) {
            throw new InvalidArgumentException(
                sprintf('Open Graph property must start with "og:", got "%s"', $property)
            );
        }

        $this->openGraphTags[trim($property)] = trim($content);
        return $this;
    }

    /**
     * Set page title
     *
     * @param string $title Page title
     * @return self
     * @throws InvalidArgumentException
     */
    public function setTitle(string $title): self
    {
        $title = trim($title);
        
        if (empty($title)) {
            throw new InvalidArgumentException('Title cannot be empty');
        }

        if (mb_strlen($title) > self::MAX_TITLE_LENGTH) {
            $title = mb_substr($title, 0, self::MAX_TITLE_LENGTH - 3) . '...';
        }

        $this->addMetaTag(self::TITLE_TAG, $title);
        $this->addOpenGraph('og:title', $title);
        
        return $this;
    }

    /**
     * Set page description
     *
     * @param string $description Page description
     * @return self
     * @throws InvalidArgumentException
     */
    public function setDescription(string $description): self
    {
        $description = trim($description);
        
        if (empty($description)) {
            throw new InvalidArgumentException('Description cannot be empty');
        }

        if (mb_strlen($description) > self::MAX_DESCRIPTION_LENGTH) {
            $description = mb_substr($description, 0, self::MAX_DESCRIPTION_LENGTH - 3) . '...';
        }

        $this->addMetaTag('description', $description);
        $this->addOpenGraph('og:description', $description);
        
        return $this;
    }

    /**
     * Set keywords
     *
     * @param string|array<int, string> $keywords Keywords
     * @return self
     * @throws InvalidArgumentException
     */
    public function setKeywords(string|array $keywords): self
    {
        if (is_array($keywords)) {
            $keywords = array_filter($keywords, fn($keyword) => !empty(trim((string)$keyword)));
            if (empty($keywords)) {
                throw new InvalidArgumentException('Keywords array cannot be empty');
            }
            $keywords = implode(', ', $keywords);
        }

        $keywords = trim((string)$keywords);
        if (empty($keywords)) {
            throw new InvalidArgumentException('Keywords cannot be empty');
        }

        $this->addMetaTag('keywords', $keywords);
        return $this;
    }

    /**
     * Set image for Open Graph
     *
     * @param string $url Image URL
     * @return self
     * @throws InvalidArgumentException
     */
    public function setImage(string $url): self
    {
        UrlValidator::validate($url);
        $this->addOpenGraph('og:image', $url);
        return $this;
    }

    /**
     * Set page URL
     *
     * @param string $url Page URL
     * @return self
     * @throws InvalidArgumentException
     */
    public function setUrl(string $url): self
    {
        UrlValidator::validate($url);
        $this->addOpenGraph('og:url', $url);
        return $this;
    }

    /**
     * Set content type for Open Graph
     *
     * @param OpenGraphType|string $type Content type
     * @return self
     * @throws InvalidArgumentException
     */
    public function setType(OpenGraphType|string $type = OpenGraphType::WEBSITE): self
    {
        if ($type instanceof OpenGraphType) {
            $typeValue = $type->value;
        } else {
            $typeValue = $type;
            // Check if string is a valid type
            $ogType = OpenGraphType::tryFrom($typeValue);
            if ($ogType === null) {
                throw new InvalidArgumentException(
                    sprintf('Invalid Open Graph type: %s', $typeValue)
                );
            }
        }

        $this->addOpenGraph('og:type', $typeValue);
        return $this;
    }

    /**
     * Add Schema.org markup
     *
     * @param array<string, mixed> $data Schema.org data
     * @return self
     * @throws InvalidArgumentException
     */
    public function addSchema(array $data): self
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Schema data cannot be empty');
        }

        // Basic validation of Schema.org structure
        if (!isset($data['@context']) && !isset($data['@type'])) {
            throw new InvalidArgumentException(
                'Schema.org data must contain at least "@context" or "@type"'
            );
        }

        $this->schemaData[] = $data;
        return $this;
    }

    /**
     * Get HTML for all meta tags
     *
     * @return string
     */
    public function render(): string
    {
        $html = [];

        // Meta tags
        foreach ($this->metaTags as $name => $content) {
            if ($name === self::TITLE_TAG) {
                $html[] = sprintf(
                    '<title>%s</title>',
                    Escaper::html($content)
                );
            } else {
                $html[] = sprintf(
                    '<meta name="%s" content="%s">',
                    Escaper::html($name),
                    Escaper::html($content)
                );
            }
        }

        // Open Graph tags
        foreach ($this->openGraphTags as $property => $content) {
            $html[] = sprintf(
                '<meta property="%s" content="%s">',
                Escaper::html($property),
                Escaper::html($content)
            );
        }

        // Schema.org markup
        if (!empty($this->schemaData)) {
            $json = json_encode(
                $this->schemaData,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
            );
            $html[] = sprintf('<script type="application/ld+json">%s</script>', $json);
        }

        return implode("\n", $html);
    }

    /**
     * Get page title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->metaTags[self::TITLE_TAG] ?? null;
    }

    /**
     * Get page description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->metaTags['description'] ?? null;
    }

    /**
     * Clear all data
     *
     * @return self
     */
    public function clear(): self
    {
        $this->metaTags = [];
        $this->openGraphTags = [];
        $this->schemaData = [];
        return $this;
    }

    /**
     * Check if there is data for rendering
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->metaTags) && empty($this->openGraphTags) && empty($this->schemaData);
    }
}
