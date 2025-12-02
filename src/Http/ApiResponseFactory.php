<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ApiResponseFactory
{
    public function __construct(private string $apiVersion = 'v1')
    {
    }

    public function ok(mixed $data, int $status = 200): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $data,
            'meta' => $this->meta(),
        ], $status);
    }

    public function error(array|string $error, int $status = 400): JsonResponse
    {
        $err = is_array($error) ? $error : ['message' => (string) $error];

        return $this->json([
            'success' => false,
            'error' => $err,
            'meta' => $this->meta(),
        ], $status);
    }

    private function meta(): array
    {
        return [
            'timestamp' => gmdate(DATE_ATOM),
            'version' => $this->apiVersion,
        ];
    }

    private function json(array $payload, int $status): JsonResponse
    {
        $response = new JsonResponse($payload, $status);
        $response->headers->set('Content-Type', 'application/json');
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $response;
    }
}
