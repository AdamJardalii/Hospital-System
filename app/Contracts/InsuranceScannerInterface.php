<?php

namespace App\Contracts;

interface InsuranceScannerInterface
{
    /**
     * Scan an image and return structured data.
     * * @param string $imagePath
     * @return array
     */
    public function scan(string $imagePath): array;
}