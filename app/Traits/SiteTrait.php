<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\JsonException;

trait SiteTrait
{
    protected function jsonResponse($status, $message, $data = null)
    {
        $response = [
            'metadata' => [
                'status' => $status,
                'message' => $message,
            ],
        ];

        if (is_array($data) && !isset($data['error'])) {
            $response['data'] = $data;
        }

        if ($status !== Response::HTTP_OK) {
            if (is_array($data) && isset($data['error'])) {
                Log::error('API Error', ['error' => $data['error']]);
            }

            return response()->json($response, $status);
        }

        return response()->json($response, $status);
    }
}
