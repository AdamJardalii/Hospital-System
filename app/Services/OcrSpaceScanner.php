<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

use App\Contracts\{InsuranceScannerInterface,RawTextAIScannerInterface};

class OcrSpaceScanner implements InsuranceScannerInterface
{
    private string $apiKey;
    private $apiUrl;
    private $groqScanner;

    public function __construct(RawTextAIScannerInterface $groqScanner){
        $this->apiKey = config('services.ocr.key');
        $this->apiUrl = config('services.ocr.url');
        $this->groqScanner = $groqScanner;
    }
       public function scan(string $imagePath): array{
            try {
                $rawText = $this->getRawTextFromOcrSpace($imagePath);

                if (!$rawText) {
                    return ['success' => false, 'error' => 'OCR.space failed to read the image.'];
                }
                $data = $this->groqScanner->scanRawText($rawText);

                return ['success' => true,'data' => $rawText, 'parsed_data' => $data];
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
    }

    private function parseText(string $text): array
    {
        // Simple regex logic (Same as previous step)
        preg_match('/ID[:\s]+([A-Z0-9-]+)/i', $text, $id);
        return [
            'policy_number' => $id[1] ?? 'Unknown',
            'raw_text' => $text
        ];
    }

    private function getRawTextFromOcrSpace(string $imagePath):?string{
        $filename = basename($imagePath) . '.jpg';
        $response = Http::withOptions(['verify' => 'C:/laragon/etc/ssl/cacert.pem'])->asMultipart()->attach('file', fopen($imagePath, 'r'), $filename)->post($this->apiUrl, [
            'apikey' => $this->apiKey,
            'OCREngine' => 2,
            'isOverlayRequired' => 'false',
            'language' => 'eng',
        ]);

        if ($response->successful()) {
            return $response->json('ParsedResults.0.ParsedText');
        }
        return null;
    }
}