<?php

namespace App\Services\Scanners;

use App\Contracts\InsuranceScannerInterface;
use Illuminate\Support\Facades\Process;
use Exception;

class PaddleOCRScanner implements InsuranceScannerInterface
{
    private $pythonScriptPath;
    private $scriptPath;
    public function __construct()
    {
        $this->pythonScriptPath = config('services.paddle.python_path');
        $this->scriptPath = config('services.paddle.script_path');
    }
    public function scan(string $path): array
    {
        try {
            $result = Process::run([
                $this->pythonScriptPath,
                $this->scriptPath,
                $path
            ]);

            if ($result->failed()) {
                throw new Exception("PaddleOCR Error: " . $result->errorOutput());
            }

            $output = json_decode($result->output(), true);

            return [
                'raw_text' => $output['raw_text'] ?? '',
                'success' => $output['success'] ?? false,
            ];

        } catch (Exception $e) {
            return [
                'raw_text' => '',
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}