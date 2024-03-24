<?php

namespace App\Http\Helpers;

class JsonFormat
{
    public static function success(string $message = 'OK', array $data = [], int $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "status" => "success",
            "message" => $message,
            "data" => $data,
        ], $code);
    }

    public static function error(string $message, array $data = [], int $code = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "status" => "failed",
            "message" => $message,
            "data" => $data,
        ], $code);
    }
}
