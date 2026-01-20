<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class InsuranceOcrService
{
    private const CONFIDENCE_THRESHOLD = 60.0;

    /**
     * Process insurance card image using OCR
     */
    public function processInsuranceCard(string $imagePath): array
    {
        try {
            // Extract text using Tesseract
            $rawText = $this->extractTextWithTesseract($imagePath);
            
            // Parse insurance information
            $parsedData = $this->parseInsuranceData($rawText);
            
            // Validate and return
            return [
                'success' => true,
                'data' => $parsedData,
                'raw_text' => $rawText,
                'confidence_score' => $parsedData['confidence_score'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('OCR Processing Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Extract text using Tesseract OCR
     */
    private function extractTextWithTesseract(string $imagePath): string
    {
        $ocr = new TesseractOCR($imagePath);
        $ocr->lang('eng');
        $ocr->psm(6); // Assume uniform block of text
        
        return $ocr->run();
    }

    /**
     * Parse insurance data from raw OCR text
     */
    private function parseInsuranceData(string $text): array
    {
        $data = [
            'policy_number' => null,
            'provider_name' => null,
            'group_number' => null,
            'expiry_date' => null,
            'effective_date' => null,
            'confidence_score' => 0,
        ];

        $lines = explode("\n", $text);
        $matchCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Policy Number patterns
            if (preg_match('/(?:policy|member|id)[\s#:]*([A-Z0-9]{8,20})/i', $line, $matches)) {
                $data['policy_number'] = $matches[1];
                $matchCount++;
            }
            
            // Group Number patterns
            if (preg_match('/(?:group|grp)[\s#:]*([A-Z0-9]{4,15})/i', $line, $matches)) {
                $data['group_number'] = $matches[1];
                $matchCount++;
            }
            
            // Provider Name (common insurance companies)
            $providers = ['Aetna', 'UnitedHealthcare', 'Blue Cross', 'Cigna', 'Humana', 'Kaiser'];
            foreach ($providers as $provider) {
                if (stripos($line, $provider) !== false) {
                    $data['provider_name'] = $provider;
                    $matchCount++;
                    break;
                }
            }
            
            // Expiry Date patterns
            if (preg_match('/(?:exp|expir|valid\s*thru?)[\s:]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $line, $matches)) {
                $data['expiry_date'] = $this->normalizeDate($matches[1]);
                $matchCount++;
            }
            
            // Effective Date patterns
            if (preg_match('/(?:effective|eff)[\s:]*(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $line, $matches)) {
                $data['effective_date'] = $this->normalizeDate($matches[1]);
                $matchCount++;
            }
        }

        // Calculate confidence based on fields found
        $data['confidence_score'] = min(100, ($matchCount / 5) * 100);

        return $data;
    }

    /**
     * Normalize date format to Y-m-d
     */
    private function normalizeDate(string $date): ?string
    {
        try {
            $date = str_replace('-', '/', $date);
            $timestamp = strtotime($date);
            
            if ($timestamp === false) {
                return null;
            }
            
            return date('Y-m-d', $timestamp);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Alternative: Process with Google Vision API (Free Tier)
     */
    public function processWithGoogleVision(string $imagePath): array
    {
        $apiKey = config('services.google.vision_api_key');
        
        if (!$apiKey) {
            throw new \Exception('Google Vision API key not configured');
        }

        $imageContent = base64_encode(file_get_contents($imagePath));

        $response = Http::post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
            'requests' => [
                [
                    'image' => ['content' => $imageContent],
                    'features' => [['type' => 'TEXT_DETECTION']],
                ],
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Google Vision API request failed');
        }

        $result = $response->json();
        $text = $result['responses'][0]['textAnnotations'][0]['description'] ?? '';

        return [
            'success' => true,
            'data' => $this->parseInsuranceData($text),
            'raw_text' => $text,
        ];
    }

    /**
     * Alternative: Process with Hugging Face Inference API
     */
    public function processWithHuggingFace(string $imagePath): array
    {
        $apiKey = config('services.huggingface.api_key');
        
        if (!$apiKey) {
            throw new \Exception('Hugging Face API key not configured');
        }

        $imageContent = file_get_contents($imagePath);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
        ])->attach('file', $imageContent, 'image.jpg')
            ->post('https://api-inference.huggingface.co/models/microsoft/trocr-base-printed');

        if (!$response->successful()) {
            throw new \Exception('Hugging Face API request failed');
        }

        $result = $response->json();
        $text = $result[0]['generated_text'] ?? '';

        return [
            'success' => true,
            'data' => $this->parseInsuranceData($text),
            'raw_text' => $text,
        ];
    }
}