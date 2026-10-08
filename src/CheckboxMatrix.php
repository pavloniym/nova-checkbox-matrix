<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Rows × columns checkbox matrix. The value is `{rowKey: [columnKey, ...]}`.
 */
class CheckboxMatrix extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'checkbox-matrix';

    /**
     * The matrix needs the full form width, so the label is rendered above it by default.
     *
     * @var bool
     */
    public $stacked = true;

    /**
     * @var bool
     */
    public $fullWidth = true;

    /**
     * @var array<int, MatrixRow>
     */
    protected array $rows = [];

    /**
     * @var array<string, string>
     */
    protected array $columns = [];

    protected bool $searchable = false;

    /**
     * @param iterable<MatrixRow> $rows
     * @return $this
     */
    public function rows(iterable $rows): static
    {
        $this->rows = [];

        foreach ($rows as $row) {
            $this->rows[] = $row;
        }

        return $this;
    }

    /**
     * @param array<string, string> $columns Column key => label, in display order
     * @return $this
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param bool $searchable
     * @return $this
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * @param mixed $raw
     * @return array<array-key, list<string>> Numeric row keys become ints (PHP array semantics)
     */
    public function normalizeValue(mixed $raw): array
    {
        return MatrixValueNormalizer::normalize(
            raw: $raw,
            rowKeys: array_map(static fn(MatrixRow $row): string => $row->key(), $this->rows),
            columnKeys: array_map('strval', array_keys($this->columns)),
        );
    }

    /**
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, string $requestAttribute, object $model, string $attribute): void
    {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = $this->normalizeValue($request->input($requestAttribute));
        }
    }

    /**
     * @param mixed $resource
     * @param string $attribute
     * @return array<array-key, list<string>> Numeric row keys become ints (PHP array semantics)
     */
    protected function resolveAttribute($resource, string $attribute): array
    {
        return $this->normalizeValue(parent::resolveAttribute($resource, $attribute));
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'rows' => array_map(static fn(MatrixRow $row): array => $row->jsonSerialize(), $this->rows),
            'columns' => array_map(
                static fn(string|int $key, string $label): array => ['key' => (string) $key, 'label' => $label],
                array_keys($this->columns),
                array_values($this->columns),
            ),
            'searchable' => $this->searchable,
        ]);
    }
}
