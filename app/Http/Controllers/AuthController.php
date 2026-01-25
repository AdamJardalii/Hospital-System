<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\{LoginRequest,RegisterRequest};
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->authenticate(
                $request->email, 
                $request->password
            );

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token'   => $data['token'],
                'user'    => $data['user']
            ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error("Auth Service Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login service unavailable.'
            ], 500);
        }
    }

    public function signup(RegisterRequest $request): JsonResponse{
        try {
            $data = $this->authService->register($request->validated());

            return response()->json([
                'success' => true,
                'message' => '• Registration successful!',
            ], 201);

            } 
            catch (\RuntimeException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);

            } 
            catch (\Exception $e) {
                Log::error("Signup Error: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => '• A system error occurred.'
                ], 500);
            }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->revokeAccess($request->user());
            return response()->json(['success' => true, 'message' => 'Logged out']);
        } catch (Exception $e) {
            return response()->json(['success' => false,'message' => 'Logout failed'], 500);
        }
    }
}