<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;

use App\Models\User;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function authenticate(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$this->userRepository->verifyPassword($user, $password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        $token = $this->generateAccessToken($user);

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function register(array $data): array
    {
        try {
            $user = $this->userRepository->create($data);

            return ['user'  => $user];
        } catch (\Exception $e) {
            throw new \RuntimeException("• Could not complete registration.");
        }
    }

    public function revokeAccess($user): void
    {
        $user->currentAccessToken()->delete();
    }

    protected function generateAccessToken(User $user): string{
        return $user->createToken('access_token')->plainTextToken;
    }
}