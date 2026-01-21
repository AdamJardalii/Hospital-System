<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use App\Contracts\InsuranceScannerInterface;

class OCRScannerController extends Controller
{
    private InsuranceScannerInterface $scanner;

    public function __construct(InsuranceScannerInterface $scanner) {
        $this->scanner = $scanner;
    }

    public function scanInsurance(Request $request)
    {
        $request->validate(['card_image' => 'required|image|max:5120']);

        try {
            $result = $this->scanner->scan($request->file('card_image')->path());

            if (!$result['success']) {
                return response()->json(['error' => $result], 422);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}