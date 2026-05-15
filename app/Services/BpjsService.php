<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BpjsService
{
    protected $consId;
    protected $consSecret;
    protected $userKey;
    protected $baseUrl;
    protected $serviceName;

    public function __construct($serviceName = 'vclaim')
    {
        $this->consId = config('bpjs.cons_id', env('JKN_CONS_ID', ''));
        $this->consSecret = config('bpjs.cons_secret', env('JKN_CONS_SECRET', ''));
        $this->serviceName = $serviceName;

        $userKeyMap = [
            'vclaim' => env('JKN_VCLAIM_USER_KEY', ''),
            'antrean' => env('JKN_ANTREAN_USER_KEY', ''),
            'aplicares' => env('JKN_APLICARES_USER_KEY', env('JKN_VCLAIM_USER_KEY', '')),
            'icare' => env('JKN_ICARE_USER_KEY', env('JKN_VCLAIM_USER_KEY', '')),
            'apotek' => env('JKN_APOTEK_USER_KEY', env('JKN_VCLAIM_USER_KEY', '')),
            'pcare' => env('JKN_PCARE_USER_KEY', ''),
        ];
        $this->userKey = $userKeyMap[$serviceName] ?? '';

        $baseUrlMap = [
            'vclaim' => env('JKN_VCLAIM_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest'),
            'antrean' => env('JKN_ANTREAN_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs'),
            'aplicares' => env('JKN_APLICARES_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/aplicares'),
            'icare' => env('JKN_ICARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/icare'),
            'apotek' => env('JKN_APOTEK_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/apotek'),
            'pcare' => env('JKN_PCARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/pcare'),
        ];
        $this->baseUrl = $baseUrlMap[$serviceName] ?? $baseUrlMap['vclaim'];
    }

    protected function timestamp()
    {
        return now()->format('Y-m-d\\TH:i:sO');
    }

    protected function signature()
    {
        $data = $this->consId . '&' . $this->timestamp();
        return hash_hmac('sha256', $data, $this->consSecret, true);
    }

    protected function encodedSignature()
    {
        return base64_encode($this->signature());
    }

    protected function key()
    {
        $key = $this->consId . $this->consSecret . $this->timestamp();
        return base64_encode(hash_hmac('sha256', $key, $this->userKey, true));
    }

    protected function encrypt($data)
    {
        $key = $this->key();
        $method = 'AES-256-CBC';
        $iv = substr(hex2bin(hash('sha256', $this->consId)), 0, 16);
        $encrypted = openssl_encrypt($data, $method, base64_decode($key), OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted);
    }

    protected function decrypt($data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $key = $this->key();
        $method = 'AES-256-CBC';
        $iv = substr(hex2bin(hash('sha256', $this->consId)), 0, 16);
        $decrypted = openssl_decrypt(base64_decode($data), $method, base64_decode($key), OPENSSL_RAW_DATA, $iv);
        $result = json_decode($decrypted, true);
        return $result ?? $decrypted;
    }

    protected function response($statusCode, $data)
    {
        if ($statusCode >= 200 && $statusCode < 300 && isset($data['response'])) {
            $responseRaw = $data['response'];
            if (is_string($responseRaw)) {
                $data['response'] = $this->decrypt($responseRaw);
            }
        }
        return response()->json($data, $statusCode);
    }

    public function get($endpoint, $skipDecrypt = false)
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $timestamp = $this->timestamp();
        $signature = $this->encodedSignature();

        try {
            $http = Http::withHeaders([
                'X-cons-id' => $this->consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'user_key' => $this->userKey,
            ]);

            $response = $http->get($url);

            $statusCode = $response->status();
            $body = $response->json() ?? [];

            if ($statusCode >= 200 && $statusCode < 300 && isset($body['response']) && !$skipDecrypt) {
                $metaData = $body['metaData'] ?? [];
                if (isset($metaData['code']) && $metaData['code'] === '200') {
                    $responseRaw = $body['response'];
                    if (is_string($responseRaw)) {
                        $body['response'] = $this->decrypt($responseRaw);
                    }
                }
            }

            return response()->json($body, $statusCode);
        } catch (\Exception $e) {
            Log::error('BPJS GET Error: ' . $e->getMessage(), ['url' => $url]);
            return response()->json([
                'metaData' => ['code' => '500', 'message' => $e->getMessage()],
            ], 500);
        }
    }

    public function post($endpoint, $data = [], $skipEncrypt = false)
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $timestamp = $this->timestamp();
        $signature = $this->encodedSignature();

        try {
            $http = Http::withHeaders([
                'X-cons-id' => $this->consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'user_key' => $this->userKey,
                'Content-Type' => 'application/json',
            ]);

            $payload = $data;
            if (!$skipEncrypt && !empty($data)) {
                $payload = ['request' => $this->encrypt(json_encode($data))];
            }

            $response = $http->post($url, $payload);
            $statusCode = $response->status();
            $body = $response->json() ?? [];

            if ($statusCode >= 200 && $statusCode < 300 && isset($body['response'])) {
                $metaData = $body['metaData'] ?? [];
                if (isset($metaData['code']) && $metaData['code'] === '200') {
                    $responseRaw = $body['response'];
                    if (is_string($responseRaw)) {
                        $body['response'] = $this->decrypt($responseRaw);
                    }
                }
            }

            return response()->json($body, $statusCode);
        } catch (\Exception $e) {
            Log::error('BPJS POST Error: ' . $e->getMessage(), ['url' => $url]);
            return response()->json([
                'metaData' => ['code' => '500', 'message' => $e->getMessage()],
            ], 500);
        }
    }

    public function put($endpoint, $data = [], $skipEncrypt = false)
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $timestamp = $this->timestamp();
        $signature = $this->encodedSignature();

        try {
            $http = Http::withHeaders([
                'X-cons-id' => $this->consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'user_key' => $this->userKey,
                'Content-Type' => 'application/json',
            ]);

            $payload = $data;
            if (!$skipEncrypt && !empty($data)) {
                $payload = ['request' => $this->encrypt(json_encode($data))];
            }

            $response = $http->put($url, $payload);
            $statusCode = $response->status();
            $body = $response->json() ?? [];

            if ($statusCode >= 200 && $statusCode < 300 && isset($body['response'])) {
                $metaData = $body['metaData'] ?? [];
                if (isset($metaData['code']) && $metaData['code'] === '200') {
                    $responseRaw = $body['response'];
                    if (is_string($responseRaw)) {
                        $body['response'] = $this->decrypt($responseRaw);
                    }
                }
            }

            return response()->json($body, $statusCode);
        } catch (\Exception $e) {
            Log::error('BPJS PUT Error: ' . $e->getMessage(), ['url' => $url]);
            return response()->json([
                'metaData' => ['code' => '500', 'message' => $e->getMessage()],
            ], 500);
        }
    }

    public function delete($endpoint, $data = [], $skipEncrypt = false)
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $timestamp = $this->timestamp();
        $signature = $this->encodedSignature();

        try {
            $http = Http::withHeaders([
                'X-cons-id' => $this->consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'user_key' => $this->userKey,
                'Content-Type' => 'application/json',
            ]);

            $payload = $data;
            if (!$skipEncrypt && !empty($data)) {
                $payload = ['request' => $this->encrypt(json_encode($data))];
            }

            $response = $http->delete($url, $payload);
            $statusCode = $response->status();
            $body = $response->json() ?? [];

            if ($statusCode >= 200 && $statusCode < 300 && isset($body['response'])) {
                $metaData = $body['metaData'] ?? [];
                if (isset($metaData['code']) && $metaData['code'] === '200') {
                    $responseRaw = $body['response'];
                    if (is_string($responseRaw)) {
                        $body['response'] = $this->decrypt($responseRaw);
                    }
                }
            }

            return response()->json($body, $statusCode);
        } catch (\Exception $e) {
            Log::error('BPJS DELETE Error: ' . $e->getMessage(), ['url' => $url]);
            return response()->json([
                'metaData' => ['code' => '500', 'message' => $e->getMessage()],
            ], 500);
        }
    }
}
