<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ContactChallengeService
{
    public function issue(): array
    {
        $a = random_int(2, 12);
        $b = random_int(1, 9);
        $answer = $a + $b;
        $token = Str::random(40);

        Cache::put($this->cacheKey($token), [
            'answer' => $answer,
            'issued_at' => now()->timestamp,
            'verified_square' => false,
        ], now()->addMinutes(10));

        return [
            'token' => $token,
            'question' => "What is {$a} + {$b}?",
            'expires_in' => 600,
        ];
    }

    public function markSquareVerified(string $token): bool
    {
        $payload = Cache::get($this->cacheKey($token));
        if (!$payload) {
            return false;
        }

        // Require a short delay so bots can't instant-submit
        $elapsed = now()->timestamp - (int) ($payload['issued_at'] ?? 0);
        if ($elapsed < 1) {
            return false;
        }

        $payload['verified_square'] = true;
        $payload['square_at'] = now()->timestamp;
        Cache::put($this->cacheKey($token), $payload, now()->addMinutes(10));

        return true;
    }

    public function validate(string $token, $answer, bool $squareChecked): array
    {
        $payload = Cache::pull($this->cacheKey($token));

        if (!$payload) {
            return ['ok' => false, 'message' => 'Security challenge expired. Please refresh and try again.'];
        }

        if (!$squareChecked || empty($payload['verified_square'])) {
            return ['ok' => false, 'message' => 'Please complete the “I’m human” checkbox.'];
        }

        $elapsed = now()->timestamp - (int) ($payload['issued_at'] ?? 0);
        if ($elapsed < 2) {
            return ['ok' => false, 'message' => 'Submitted too quickly. Please try again.'];
        }

        if ((int) $answer !== (int) $payload['answer']) {
            return ['ok' => false, 'message' => 'Incorrect security answer. Please try again.'];
        }

        return ['ok' => true];
    }

    private function cacheKey(string $token): string
    {
        return 'contact_challenge:' . $token;
    }
}
