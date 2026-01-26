<?php

namespace App\Services\Scanners;

use App\Contracts\InsuranceScannerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiScannerService implements InsuranceScannerInterface
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct(string $model = 'gemini-2.0-flash-lite')
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = $model;
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/";
    }

    /**
     * Dynamic extraction using OCR text.
     */
    public function scanText(string $ocrText): array
    {
        $prompt = $this->getExtractionPrompt($ocrText);

        return $this->callGemini($prompt);
    }

    /**
     * Dynamic extraction directly from an Image (Vision).
     */
    public function scan(string $imagePath): array
    {
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => "Extract insurance details into JSON: first_name, last_name, expiry_date, policy_number, nssf (boolean),class,company, and other (an array of strings containing any other relevant notes found on the card)."],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => ['response_mime_type' => 'application/json']
        ];

        return $this->executeRequest($payload);
    }

    protected function callGemini(string $prompt): array
    {
        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'temperature' => 0.1, // Low temperature for higher accuracy
            ]
        ];

        return $this->executeRequest($payload);
    }

    protected function executeRequest(array $payload): array
    {
        $url = "{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($response->successful()) {
            $textResult = $response->json('candidates.0.content.parts.0.text');
            $decoded = json_decode($textResult, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'error' => 'AI returned invalid JSON format.'];
            }
            return [
                'success' => true, 
                'data' => $decoded
            ];
        }

        Log::error("Gemini API Error", [
            'status' => $response->status(),
            'error' => $response->body()
        ]);

        return ['success' => false, 'error' => 'Extraction failed'];
    }

    protected function getExtractionPrompt(string $ocrText): string
    {
        return "Analyze this OCR text from a medical insurance card. 
                Extract the following fields into a JSON object:
                - first_name
                - last_name
                - expiry_date (use YYYY-MM-DD format)
                - policy_number
                - nssf (set to true if the text indicates National Social Security Fund or CNSS, else false)
                - Company (insurance provider name)
                - class (type of insurance plan, e.g., HMO, PPO, etc.)
                - other: array (a list of strings for any extra info like blood type, card type, or plan level)

                Ensure the JSON is properly formatted.

                OCR TEXT:
                {$ocrText}";
    }
}