<?php

namespace App\Interfaces;

use Generator;

interface ParserInterface
{
    /**
     * @param string $filePath
     * @return Generator<int, array<string, mixed>>
     */
    public function parse(string $filePath): Generator;
}
