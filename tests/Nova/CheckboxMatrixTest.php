<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix\Tests\Nova;

use Illuminate\Support\Fluent;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Pavloniym\NovaCheckboxMatrix\CheckboxMatrix;
use Pavloniym\NovaCheckboxMatrix\MatrixColumn;
use Pavloniym\NovaCheckboxMatrix\MatrixRow;
use Pavloniym\NovaCheckboxMatrix\MatrixSelection;
use Pavloniym\NovaCheckboxMatrix\MatrixValue;
use PHPUnit\Framework\TestCase;

/**
 * Runs only where laravel/nova is installed (it is a paid package and is absent in CI).
 */
final class CheckboxMatrixTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Field::class)) {
            $this->markTestSkipped('laravel/nova is not installed');
        }
    }

    public function test_fill_produces_normalized_matrix_value(): void
    {
        $model = new Fluent();
        $request = NovaRequest::create('/', 'POST', ['members' => '{"a":["x","evil"],"intruder":["x"]}']);

        $this->field()->fillInto($request, $model, 'members');

        $this->assertInstanceOf(MatrixValue::class, $model->get('members'));
        $this->assertEquals([new MatrixSelection('a', ['x'])], $model->get('members')->selections());
    }

    public function test_fill_with_empty_object_produces_empty_value(): void
    {
        $model = new Fluent();

        $this->field()->fillInto(NovaRequest::create('/', 'POST', ['members' => '{}']), $model, 'members');

        $this->assertTrue($model->get('members')->isEmpty());
    }

    public function test_fill_without_attribute_leaves_model_untouched(): void
    {
        $model = new Fluent();

        $this->field()->fillInto(NovaRequest::create('/', 'POST'), $model, 'members');

        $this->assertNull($model->get('members'));
    }

    public function test_normalize_accepts_matrix_value(): void
    {
        $value = $this->field()->normalizeValue(MatrixValue::fromArray(['a' => ['x'], 'b' => ['y']]));

        $this->assertSame(['a' => ['x']], $value->toArray());
    }

    public function test_defaults_to_stacked_full_width(): void
    {
        $this->assertTrue($this->field()->stacked);
        $this->assertTrue($this->field()->fullWidth);
    }

    private function field(): CheckboxMatrix
    {
        return CheckboxMatrix::make('Members', 'members')
            ->rows([MatrixRow::make('a', 'A')])
            ->columns([MatrixColumn::make('x', 'X')]);
    }
}
