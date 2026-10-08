<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

use JsonSerializable;

/**
 * A single row of the checkbox matrix.
 */
final class MatrixRow implements JsonSerializable
{
    private ?string $subtitle = null;

    private ?string $caption = null;

    private ?string $avatar = null;

    private function __construct(
        private readonly string $key,
        private readonly string $label,
    ) {
    }

    /**
     * @param string|int $key
     * @param string $label
     * @return self
     */
    public static function make(string|int $key, string $label): self
    {
        return new self(key: (string) $key, label: $label);
    }

    /**
     * @param string|null $subtitle
     * @return $this
     */
    public function subtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle === '' ? null : $subtitle;

        return $this;
    }

    /**
     * Third line under the subtitle.
     *
     * @param string|null $caption
     * @return $this
     */
    public function caption(?string $caption): self
    {
        $this->caption = $caption === '' ? null : $caption;

        return $this;
    }

    /**
     * Only absolute http(s) URLs are accepted, anything else is dropped.
     *
     * @param string|null $url
     * @return $this
     */
    public function avatar(?string $url): self
    {
        $scheme = $url !== null ? strtolower((string) parse_url($url, PHP_URL_SCHEME)) : null;

        $this->avatar = in_array($scheme, ['http', 'https'], true) ? $url : null;

        return $this;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @return array{key: string, label: string, subtitle: string|null, caption: string|null, avatar: string|null}
     */
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'subtitle' => $this->subtitle,
            'caption' => $this->caption,
            'avatar' => $this->avatar,
        ];
    }
}
