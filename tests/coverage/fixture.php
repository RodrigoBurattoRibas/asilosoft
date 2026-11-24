<?php
final class CoverageFixture
{
    public function choose(bool $yes): string
    {
        if ($yes) {
            return '<tag>'; // covered branch
        }
        return 'unvisited branch';
    }

    public function neverCalled(): string
    {
        return 'unvisited method';
    }
}
