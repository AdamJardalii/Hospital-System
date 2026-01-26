<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use App\Contracts\InsuranceScannerInterface;
use App\Services\ScannerManager;

class OCRScannerController extends Controller
{
    public function __construct(private ScannerManager $scanner) {}

    public function scanInsurance(Request $request)
    {
        $request->validate(['card_image' => 'required|image|max:5120']);

        try {
            $driverName = $request->query('driver_name');
            $result = $this->scanner->driver($driverName)->scan($request->file('card_image')->path());

            if (!$result['success']) {
                return response()->json(['error' => $result], 422);
            }

            return response()->json($result);
        }
        catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => "The scanner '{$driverName}' is not supported. Please use google, paddle, or tesseract."
            ], 400);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}