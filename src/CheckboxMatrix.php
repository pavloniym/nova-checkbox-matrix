<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Rows × columns checkbox matrix. The submitted value is a {@see MatrixValue}.
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
     * @var list<MatrixRow>
     */
    protected array $rows = [];

    /**
     * @var list<MatrixColumn>
     */
    protected array $columns = [];

    protected MatrixOptions $options;

    /**
     * @param string $name
     * @param string|callable|null $attribute
     * @param callable|null $resolveCallback
     */
    public function __construct($name, $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->options = MatrixOptions::make();
    }

    /**
     * @param iterable<MatrixRow> $rows Rows in display order
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
     * @param iterable<MatrixColumn> $columns Columns in display order
     * @return $this
     */
    public function columns(iterable $columns): static
    {
        $this->columns = [];

        foreach ($columns as $column) {
            $this->columns[] = $column;
        }

        return $this;
    }

    /**
     * @param MatrixOptions $options
     * @return $this
     */
    public function options(MatrixOptions $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Normalizes an untrusted value against the declared rows and columns.
     *
     * @param mixed $raw Array, JSON string or {@see MatrixValue}
     * @return MatrixValue
     */
    public function normalizeValue(mixed $raw): MatrixValue
    {
        if ($raw instanceof MatrixValue) {
            $raw = $raw->toArray();
        }

        return MatrixValue::fromArray(MatrixValueNormalizer::normalize(
            raw: $raw,
            rowKeys: array_map(static fn(MatrixRow $row): string => $row->key(), $this->rows),
            columnKeys: array_map(static fn(MatrixColumn $column): string => $column->key(), $this->columns),
        ));
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
     * @return MatrixValue
     */
    protected function resolveAttribute($resource, string $attribute): MatrixValue
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
            'columns' => array_map(static fn(MatrixColumn $column): array => $column->jsonSerialize(), $this->columns),
            'options' => $this->options->jsonSerialize(),
        ]);
    }
}
