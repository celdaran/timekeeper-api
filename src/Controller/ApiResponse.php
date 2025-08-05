<?php namespace App\Controller;

class ApiResponse
{
    public static function success(array $payload = []) : array {
        return ApiResponse::payload('ok', $payload);
    }

    public static function created(array $payload = []) : array {
        return ApiResponse::payload('created', $payload);
    }

    public static function error(array $payload) : array {
        return ApiResponse::payload('error', $payload);
    }

    private static function payload(string $status, array $payload): array {
        return [
            'status' => $status,
            'payload' => $payload,
        ];
    }
}