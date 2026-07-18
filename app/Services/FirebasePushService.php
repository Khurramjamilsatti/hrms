<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends push notifications through Firebase Cloud Messaging (HTTP v1 API)
 * using the service account file at storage/app/firebase.json.
 */
class FirebasePushService
{
    protected const TOKEN_CACHE_KEY = 'fcm_oauth_access_token';

    protected ?array $credentials = null;

    public function isConfigured(): bool
    {
        return $this->credentials() !== null;
    }

    /**
     * Send a push notification to every registered device of the given users.
     *
     * @param array<int> $userIds
     * @param array<string, mixed> $data extra payload (values are stringified for FCM)
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (empty($userIds) || !$this->isConfigured()) {
            return;
        }

        $tokens = DeviceToken::whereIn('user_id', $userIds)->get();
        foreach ($tokens as $deviceToken) {
            $this->sendToToken($deviceToken, $title, $body, $data);
        }
    }

    public function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        $this->sendToUsers([$userId], $title, $body, $data);
    }

    protected function sendToToken(DeviceToken $deviceToken, string $title, string $body, array $data): void
    {
        $credentials = $this->credentials();
        $accessToken = $this->accessToken();
        if (!$credentials || !$accessToken) {
            return;
        }

        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        $message = [
            'message' => [
                'token' => $deviceToken->token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData,
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'hrms_default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->post(
                    "https://fcm.googleapis.com/v1/projects/{$credentials['project_id']}/messages:send",
                    $message
                );

            if ($response->successful()) {
                $deviceToken->forceFill(['last_used_at' => now()])->saveQuietly();
                return;
            }

            $errorStatus = data_get($response->json(), 'error.details.0.errorCode')
                ?? data_get($response->json(), 'error.status');

            // Remove tokens Firebase reports as dead
            if (in_array($errorStatus, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'], true) || $response->status() === 404) {
                $deviceToken->delete();
                return;
            }

            Log::warning('FCM push failed', [
                'status' => $response->status(),
                'error' => $response->json('error.message'),
                'user_id' => $deviceToken->user_id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('FCM push exception: ' . $e->getMessage(), ['user_id' => $deviceToken->user_id]);
        }
    }

    protected function credentials(): ?array
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $path = storage_path('app/firebase.json');
        if (!is_file($path)) {
            return null;
        }

        $json = json_decode((string) file_get_contents($path), true);
        if (!is_array($json) || empty($json['project_id']) || empty($json['private_key']) || empty($json['client_email'])) {
            Log::warning('firebase.json is present but missing required keys');
            return null;
        }

        return $this->credentials = $json;
    }

    /**
     * Exchange a signed service-account JWT for an OAuth2 access token (cached).
     */
    protected function accessToken(): ?string
    {
        $cached = Cache::get(self::TOKEN_CACHE_KEY);
        if ($cached) {
            return $cached;
        }

        $credentials = $this->credentials();
        if (!$credentials) {
            return null;
        }

        try {
            $now = time();
            $tokenUri = $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token';

            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $this->base64UrlEncode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $tokenUri,
                'iat' => $now,
                'exp' => $now + 3600,
            ]));

            $signature = '';
            if (!openssl_sign("{$header}.{$claims}", $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
                Log::warning('FCM: failed to sign service-account JWT');
                return null;
            }

            $jwt = "{$header}.{$claims}." . $this->base64UrlEncode($signature);

            $response = Http::asForm()->timeout(10)->post($tokenUri, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (!$response->successful()) {
                Log::warning('FCM: token exchange failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $accessToken = $response->json('access_token');
            $expiresIn = (int) ($response->json('expires_in') ?? 3600);

            if ($accessToken) {
                Cache::put(self::TOKEN_CACHE_KEY, $accessToken, max(60, $expiresIn - 300));
            }

            return $accessToken;
        } catch (\Throwable $e) {
            Log::warning('FCM: token exchange exception: ' . $e->getMessage());
            return null;
        }
    }

    protected function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
