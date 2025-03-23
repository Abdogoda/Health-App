<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function response(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        $statusMessage = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
        ];

        $resData = [
            'status' => $statusMessage[$status],
        ];

        if ($data)
            $resData['data'] = $data;
        if ($message)
            $resData['message'] = $message;

        return response()->json($resData, $status);
    }
}
