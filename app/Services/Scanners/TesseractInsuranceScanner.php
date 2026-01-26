<?php

namespace App\Services\Scanners;

use Illuminate\Support\Facades\Http;
use Exception;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Throwable;

use App\Contracts\{InsuranceScannerInterface,RawTextAIScannerInterface};

class TesseractInsuranceScanner implements InsuranceScannerInterface
{


    public function __construct(private RawTextAIScannerInterface $groqScanner){}
       public function scan(string $imagePath): array{
            try {
                $rawText = $this->getRawText($imagePath);
                if (!$rawText) {
                    return ['success' => false, 'error' => 'OCR.space failed to read the image.'];
                }
                $data = $this->groqScanner->scanRawText($rawText);

                return ['success' => true,'data' => $rawText, 'parsed_data' => $data];
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
    }

    private function getRawText($imagePath):?string{
        try {
            return (new TesseractOCR($imagePath))
                ->lang('eng')
                ->psm(6)
                ->oem(1)
                ->run();
        } catch (Throwable $e) {
            \Log::error("Tesseract OCR failed: ".$e->getMessage());
            return null;
        }

    }
}