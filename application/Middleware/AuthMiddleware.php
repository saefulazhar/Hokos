<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthMiddleware {
    public function check_auth() {
        $CI =& get_instance();
        $headers = $CI->input->request_headers();

        if (!isset($headers['Authorization'])) {
            show_json(['message' => 'Token tidak ditemukan'], 401);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded = decode_jwt($token);

        if (!$decoded || !isset($decoded->user_id)) {
            show_json(['message' => 'Token tidak valid'], 401);
        }

        $CI->user_id = $decoded->user_id;
    }
}
