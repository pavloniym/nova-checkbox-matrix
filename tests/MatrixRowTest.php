<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix\Tests;

use Pavloniym\NovaCheckboxMatrix\MatrixRow;
use PHPUnit\Framework\TestCase;

final class MatrixRowTest extends TestCase
{
    public function test_serializes(): void
    {
        $row = MatrixRow::make(7, 'Ann')->subtitle('@ann')->caption('translator')->avatar('https://x.test/a.jpg');

        $this->assertSame('7', $row->key());
        $this->assertSame('Ann', $row->label());
        $this->assertSame(
            ['key' => '7', 'label' => 'Ann', 'subtitle' => '@ann', 'caption' => 'translator', 'avatar' => 'https://x.test/a.jpg'],
            $row->jsonSerialize(),
        );
    }

    public function test_defaults_to_nulls(): void
    {
        $this->assertSame(
            ['key' => 'a', 'label' => 'Ann', 'subtitle' => null, 'caption' => null, 'avatar' => null],
            MatrixRow::make('a', 'Ann')->jsonSerialize(),
        );
    }

    public function test_rejects_non_http_avatar(): void
    {
        $this->assertNull(MatrixRow::make('a', 'Ann')->avatar('javascript:alert(1)')->jsonSerialize()['avatar']);
        $this->assertNull(MatrixRow::make('a', 'Ann')->avatar('data:image/png;base64,AAAA')->jsonSerialize()['avatar']);
        $this->assertNull(MatrixRow::make('a', 'Ann')->avatar('//x.test/a.jpg')->jsonSerialize()['avatar']);
    }

    public function test_allows_http_and_https(): void
    {
        $this->assertSame('http://x.test/a.jpg', MatrixRow::make('a', 'Ann')->avatar('http://x.test/a.jpg')->jsonSerialize()['avatar']);
        $this->assertSame('HTTPS://x.test/a.jpg', MatrixRow::make('a', 'Ann')->avatar('HTTPS://x.test/a.jpg')->jsonSerialize()['avatar']);
    }

    public function test_empty_subtitle_becomes_null(): void
    {
        $this->assertNull(MatrixRow::make('a', 'Ann')->subtitle('')->jsonSerialize()['subtitle']);
        $this->assertNull(MatrixRow::make('a', 'Ann')->subtitle(null)->jsonSerialize()['subtitle']);
    }

    public function test_empty_caption_becomes_null(): void
    {
        $this->assertNull(MatrixRow::make('a', 'Ann')->caption('')->jsonSerialize()['caption']);
        $this->assertNull(MatrixRow::make('a', 'Ann')->caption(null)->jsonSerialize()['caption']);
    }
}
