<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

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

        if (is_array($data) && ! isset($data['error'])) {
            $response['data'] = $data;
        }

        if ($status < 200 || $status >= 300) {
            if (is_array($data) && isset($data['error'])) {
                Log::error('API Error', ['error' => $data['error']]);
                $response['metadata']['error'] = $data['error'];
                unset($response['data']);
            }

            return response()->json($response, $status);
        }

        return response()->json($response, $status);
    }
}
