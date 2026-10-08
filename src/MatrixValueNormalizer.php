<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

/**
 * Turns an untrusted matrix value into `{rowKey: [columnKey, ...]}` limited to the declared rows and columns.
 */
final class MatrixValueNormalizer
{
    /**
     * @param mixed $raw Array or JSON string coming from the request or the model
     * @param array<int, string|int> $rowKeys Declared row keys
     * @param array<int, string> $columnKeys Declared column keys, in display order
     * @return array<string, list<string>>
     */
    public static function normalize(mixed $raw, array $rowKeys, array $columnKeys): array
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (!is_array($raw)) {
            return [];
        }

        $allowedRows = array_flip(array_map(static fn(string|int $key): string => (string) $key, $rowKeys));
        $columnKeys = array_values(array_map(static fn(string|int $key): string => (string) $key, $columnKeys));

        $normalized = [];

        foreach ($raw as $rowKey => $cells) {
            $rowKey = (string) $rowKey;

            if (!isset($allowedRows[$rowKey]) || !is_array($cells)) {
                continue;
            }

            $cells = array_filter($cells, static fn(mixed $cell): bool => is_string($cell));
            $columns = array_values(array_filter($columnKeys, static fn(string $column): bool => in_array($column, $cells, true)));

            if ($columns !== []) {
                $normalized[$rowKey] = $columns;
            }
        }

        return $normalized;
    }
}
