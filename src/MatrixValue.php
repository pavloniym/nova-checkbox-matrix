<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use stdClass;
use Traversable;

/**
 * Submitted value of the checkbox matrix: one selection per row that has at least one checked column.
 *
 * @implements IteratorAggregate<int, MatrixSelection>
 */
final readonly class MatrixValue implements Countable, IteratorAggregate, JsonSerializable
{
    /**
     * @param list<MatrixSelection> $selections
     */
    private function __construct(
        private array $selections,
    ) {
    }

    /**
     * @return self
     */
    public static function empty(): self
    {
        return new self(selections: []);
    }

    /**
     * Builds the value from `{rowKey: [columnKey, ...]}`. Rows without string column keys are skipped.
     * It does not validate keys against a field — the field normalizes request input before building the value.
     *
     * @param array<array-key, mixed> $value
     * @return self
     */
    public static function fromArray(array $value): self
    {
        $selections = [];

        foreach ($value as $rowKey => $columnKeys) {
            if (!is_array($columnKeys)) {
                continue;
            }

            $columnKeys = array_values(array_unique(array_filter($columnKeys, static fn(mixed $key): bool => is_string($key))));

            if ($columnKeys !== []) {
                $selections[] = new MatrixSelection(rowKey: (string) $rowKey, columnKeys: $columnKeys);
            }
        }

        return new self(selections: $selections);
    }

    /**
     * @return list<MatrixSelection>
     */
    public function selections(): array
    {
        return $this->selections;
    }

    /**
     * @param string|int $rowKey
     * @return MatrixSelection|null
     */
    public function forRow(string|int $rowKey): ?MatrixSelection
    {
        foreach ($this->selections as $selection) {
            if ($selection->rowKey === (string) $rowKey) {
                return $selection;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->selections === [];
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->selections);
    }

    /**
     * @return Traversable<int, MatrixSelection>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->selections);
    }

    /**
     * Numeric row keys become ints (PHP array semantics).
     *
     * @return array<array-key, list<string>>
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->selections as $selection) {
            $result[$selection->rowKey] = $selection->columnKeys;
        }

        return $result;
    }

    /**
     * Always serialized as a JSON object, even when empty or keyed by "0", "1", ...
     *
     * @return stdClass
     */
    public function jsonSerialize(): stdClass
    {
        return (object) $this->toArray();
    }
}
