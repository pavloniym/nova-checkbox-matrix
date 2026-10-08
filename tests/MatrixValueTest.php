<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix\Tests;

use Pavloniym\NovaCheckboxMatrix\MatrixSelection;
use Pavloniym\NovaCheckboxMatrix\MatrixValue;
use PHPUnit\Framework\TestCase;

final class MatrixValueTest extends TestCase
{
    public function test_selections_from_array(): void
    {
        $value = MatrixValue::fromArray(['a' => ['x', 'y'], 7 => ['z']]);

        $this->assertEquals(
            [new MatrixSelection('a', ['x', 'y']), new MatrixSelection('7', ['z'])],
            $value->selections(),
        );
        $this->assertCount(2, $value);
    }

    public function test_row_keys_are_strings(): void
    {
        $this->assertSame('7', MatrixValue::fromArray([7 => ['z']])->selections()[0]->rowKey);
    }

    public function test_from_array_skips_empty_and_invalid_rows(): void
    {
        $value = MatrixValue::fromArray(['a' => [], 'b' => 'x', 'c' => ['x', 1, null, 'x']]);

        $this->assertEquals([new MatrixSelection('c', ['x'])], $value->selections());
    }

    public function test_for_row(): void
    {
        $value = MatrixValue::fromArray(['a' => ['x'], 7 => ['y']]);

        $this->assertSame(['x'], $value->forRow('a')?->columnKeys);
        $this->assertSame(['y'], $value->forRow(7)?->columnKeys);
        $this->assertNull($value->forRow('missing'));
    }

    public function test_is_empty(): void
    {
        $this->assertTrue(MatrixValue::empty()->isEmpty());
        $this->assertFalse(MatrixValue::fromArray(['a' => ['x']])->isEmpty());
    }

    public function test_iterates_selections(): void
    {
        $keys = [];

        foreach (MatrixValue::fromArray(['a' => ['x'], 'b' => ['y']]) as $selection) {
            $keys[] = $selection->rowKey;
        }

        $this->assertSame(['a', 'b'], $keys);
    }

    public function test_to_array(): void
    {
        $this->assertSame(['a' => ['x'], '7' => ['y']], MatrixValue::fromArray(['a' => ['x'], 7 => ['y']])->toArray());
    }

    public function test_json_is_always_an_object(): void
    {
        $this->assertSame('{}', json_encode(MatrixValue::empty()));
        $this->assertSame('{"0":["x"]}', json_encode(MatrixValue::fromArray([0 => ['x']])));
        $this->assertSame('{"a":["x"]}', json_encode(MatrixValue::fromArray(['a' => ['x']])));
    }
}
