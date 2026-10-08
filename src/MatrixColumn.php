<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

use JsonSerializable;

/**
 * A single column of the checkbox matrix.
 */
final class MatrixColumn implements JsonSerializable
{
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
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return array{key: string, label: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
        ];
    }
}
