<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix\Tests;

use Pavloniym\NovaCheckboxMatrix\MatrixColumn;
use PHPUnit\Framework\TestCase;

final class MatrixColumnTest extends TestCase
{
    public function test_serializes(): void
    {
        $column = MatrixColumn::make(5, 'Five');

        $this->assertSame('5', $column->key());
        $this->assertSame(['key' => '5', 'label' => 'Five'], $column->jsonSerialize());
    }
}
