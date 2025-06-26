<?php

namespace App\Helpers;

class JSend
{
    public array $data = [];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->data = [];
    }

    public static function success(array $data)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public static function error(string $message, ?int $status_code = null, ?array $data = null)
    {
        $_data = [];
        $_data['status'] = 'error';
        $_data['message'] = $message;
        $_data ? $_data['data'] = $data : null;

        return response()->json($_data, $status_code);
    }

    public static function fail(array $data, $status_code = 404)
    {
        return response()->json([
            'status' => 'fail',
            'data' => $data,
        ], $status_code);
    }
}
