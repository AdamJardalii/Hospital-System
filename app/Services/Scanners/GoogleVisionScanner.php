<?php

namespace App\Services\Scanners;

use App\Contracts\InsuranceScannerInterface;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Exception;

class GoogleVisionScanner implements InsuranceScannerInterface
{
    private ImageAnnotatorClient $client;

    public function __construct()
    {
        $this->client = new ImageAnnotatorClient([
            'credentials' => config('services.google.credentials_path'),
        ]);
    }

    public function scan(string $path): array
    {
        try {
            $imageContent = file_get_contents($path);

            $response = $this->client->textDetection($imageContent);
            $texts = $response->getTextAnnotations();

            $this->client->close();

            if (empty($texts)) {
                return [
                    'success' => false,
                    'raw_text' => '',
                    'error' => 'No text detected',
                ];
            }

            return [
                'success' => true,
                'raw_text' => $texts[0]->getDescription(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'raw_text' => '',
                'error' => $e->getMessage(),
            ];
        }
    }
}
