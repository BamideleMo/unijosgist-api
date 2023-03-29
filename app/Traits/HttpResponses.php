<?php

namespace App\Traits;

trait HttpResponses {
    protected function success($data, $message = null, $code = 200){
        return response()->json([
            'status' => "Successful",
            'message' => $message,
            'data' => $data,
        ],$code);
    }
    protected function error($data, string $message = null, $code){
        return response()->json([
            'status' => "Error",
            'message' => $message,
            'data' => $data,
        ],$code);
    }
}