<?php
namespace App\Services;

use Illuminate\Support\Manager;
use App\Contracts\InsuranceScannerInterface;
use App\Services\Scanners\{GoogleVisionScanner, PaddleOCRScanner, TesseractInsuranceScanner, OcrSpaceScanner};

class ScannerManager extends Manager
{
    /**
     * Define the default driver from config.
     */
    public function getDefaultDriver(): string {
        return config('services.insurance.default', 'ocr_space');
    }

    protected function createGoogleDriver(): InsuranceScannerInterface {
        return $this->container->make(GoogleVisionScanner::class);
    }

    protected function createPaddleDriver(): InsuranceScannerInterface {
        return $this->container->make(PaddleOCRScanner::class);
    }

    protected function createTesseractDriver(): InsuranceScannerInterface {
        return $this->container->make(TesseractInsuranceScanner::class);
    }

    protected function createOcrSpaceDriver(): InsuranceScannerInterface {
        return $this->container->make(OcrSpaceScanner::class);
    }
}