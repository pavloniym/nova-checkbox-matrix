<?php

declare(strict_types=1);

namespace Pavloniym\NovaCheckboxMatrix;

use JsonSerializable;

/**
 * Display options of the checkbox matrix.
 */
final class MatrixOptions implements JsonSerializable
{
    private bool $searchable = false;

    private bool $avatars = true;

    private bool $subtitles = true;

    private bool $captions = true;

    private bool $counters = true;

    /**
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Show the search input above the matrix.
     *
     * @param bool $searchable
     * @return $this
     */
    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * Show row avatars (or initials when a row has no avatar).
     *
     * @param bool $avatars
     * @return $this
     */
    public function avatars(bool $avatars = true): self
    {
        $this->avatars = $avatars;

        return $this;
    }

    /**
     * Show the row subtitle line.
     *
     * @param bool $subtitles
     * @return $this
     */
    public function subtitles(bool $subtitles = true): self
    {
        $this->subtitles = $subtitles;

        return $this;
    }

    /**
     * Show the row caption line.
     *
     * @param bool $captions
     * @return $this
     */
    public function captions(bool $captions = true): self
    {
        $this->captions = $captions;

        return $this;
    }

    /**
     * Show the number of checked rows in each column header.
     *
     * @param bool $counters
     * @return $this
     */
    public function counters(bool $counters = true): self
    {
        $this->counters = $counters;

        return $this;
    }

    /**
     * @return array{searchable: bool, avatars: bool, subtitles: bool, captions: bool, counters: bool}
     */
    public function jsonSerialize(): array
    {
        return [
            'searchable' => $this->searchable,
            'avatars' => $this->avatars,
            'subtitles' => $this->subtitles,
            'captions' => $this->captions,
            'counters' => $this->counters,
        ];
    }
}
