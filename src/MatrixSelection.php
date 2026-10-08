<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

/**
 * Checked columns of a single row.
 */
final readonly class MatrixSelection
{
    /**
     * @param string $rowKey
     * @param list<string> $columnKeys
     */
    public function __construct(
        public string $rowKey,
        public array  $columnKeys,
    ) {
    }
}
