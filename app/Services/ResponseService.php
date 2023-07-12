<?php

namespace App\Services;

class ResponseService
{
    public function getOkResponse(string $message, $data = []): array
    {
        return [
            'status' => 'ok',
            'message' => $message,
            'data' => $data,
        ];
    }

    public function getErrorResponse(string $message): array
    {
        return [
            'status' => 'error',
            'message' => $message,
            'data' => [],
        ];
    }
}
