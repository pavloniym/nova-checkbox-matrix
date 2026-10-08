<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix\Tests;

use Pavloniym\NovaCheckboxMatrix\MatrixValueNormalizer;
use PHPUnit\Framework\TestCase;

final class MatrixValueNormalizerTest extends TestCase
{
    private const array ROWS = ['a', 'b', '123'];
    private const array COLUMNS = ['x', 'y', 'z'];

    public function test_accepts_array(): void
    {
        $this->assertSame(['a' => ['x', 'y']], $this->normalize(['a' => ['y', 'x']]));
    }

    public function test_accepts_json_string(): void
    {
        $this->assertSame(['a' => ['x']], $this->normalize('{"a":["x"]}'));
    }

    public function test_drops_unknown_rows(): void
    {
        $this->assertSame(['a' => ['x']], $this->normalize(['zzz' => ['x'], 'a' => ['x']]));
    }

    public function test_drops_unknown_columns(): void
    {
        $this->assertSame(['a' => ['x']], $this->normalize(['a' => ['x', 'evil']]));
    }

    public function test_dedupes_columns(): void
    {
        $this->assertSame(['a' => ['x']], $this->normalize(['a' => ['x', 'x']]));
    }

    public function test_drops_empty_rows(): void
    {
        $this->assertSame(['b' => ['z']], $this->normalize(['a' => [], 'b' => ['z']]));
    }

    public function test_keeps_row_order_from_input(): void
    {
        $this->assertSame(['b' => ['x'], 'a' => ['x']], $this->normalize(['b' => ['x'], 'a' => ['x']]));
    }

    public function test_numeric_row_keys(): void
    {
        $this->assertSame(['123' => ['x']], $this->normalize([123 => ['x']]));
    }

    public function test_numeric_row_keys_json(): void
    {
        $this->assertSame(['123' => ['x']], $this->normalize('{"123":["x"]}'));
    }

    public function test_int_declared_row_keys(): void
    {
        $this->assertSame(['5' => ['x']], MatrixValueNormalizer::normalize(['5' => ['x']], [5], self::COLUMNS));
    }

    public function test_ignores_non_string_cells(): void
    {
        $this->assertSame(['a' => ['x']], $this->normalize(['a' => ['x', ['y'], 1, null]]));
    }

    public function test_row_value_not_list(): void
    {
        $this->assertSame([], $this->normalize(['a' => 'x']));
    }

    public function test_garbage_input(): void
    {
        $this->assertSame([], $this->normalize('not json'));
        $this->assertSame([], $this->normalize(42));
        $this->assertSame([], $this->normalize(null));
        $this->assertSame([], $this->normalize('"a"'));
    }

    private function normalize(mixed $raw): array
    {
        return MatrixValueNormalizer::normalize($raw, self::ROWS, self::COLUMNS);
    }
}
