<?php

namespace App\Contracts;

interface RawTextAIScannerInterface
{
    /**
     * Scan an image and return structured data.
     * * @param string $rawText
     * @return array
     */
    public function scanRawText(string $rawText): array;
}