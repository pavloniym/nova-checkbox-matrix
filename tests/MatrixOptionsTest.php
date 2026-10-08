<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix\Tests;

use Pavloniym\NovaCheckboxMatrix\MatrixOptions;
use PHPUnit\Framework\TestCase;

final class MatrixOptionsTest extends TestCase
{
    public function test_defaults(): void
    {
        $this->assertSame(
            ['searchable' => false, 'avatars' => true, 'subtitles' => true, 'captions' => true, 'counters' => true],
            MatrixOptions::make()->jsonSerialize(),
        );
    }

    public function test_toggles(): void
    {
        $options = MatrixOptions::make()
            ->searchable()
            ->avatars(false)
            ->subtitles(false)
            ->captions(false)
            ->counters(false);

        $this->assertSame(
            ['searchable' => true, 'avatars' => false, 'subtitles' => false, 'captions' => false, 'counters' => false],
            $options->jsonSerialize(),
        );
    }
}
