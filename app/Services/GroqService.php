<?php

namespace App\Services;

use App\Contracts\RawTextAIScannerInterface;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class GroqService implements RawTextAIScannerInterface
{
    private string $apiKey;
    private $apiUrl;

    public function __construct(){
        $this->apiKey = config('services.groq.key');
        $this->apiUrl = config('services.groq.url');
    }
    public function scanRawText(string $rawText): array
    {
        $data = $this->executeRequest($this->getExtractionPrompt($rawText));
        if ($data !== null) {
            return [
                'success' => true,
                'data' => $data
            ];
        }
        return ['success' => false, 'error' => 'AI extraction failed.'];
    }

    private function getExtractionPrompt(string $ocrText): string
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

    private function executeRequest(string $prompt){
        try {
            $response = Http::withToken($this->apiKey)->post($this->apiUrl, [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.1
            ]);

            if ($response->successful()) {
                $data = json_decode($response->json('choices.0.message.content'), true);
                return $data;
            }
            return null;
        } 
        catch (Exception $e) {
            Log::error("Groq API Error: " . $e->getMessage());
            return null;
        }
    }
}