<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class AuthMiddleware {
    public function check_auth() {
        $CI =& get_instance();
        $headers = array_change_key_case($CI->input->request_headers(), CASE_LOWER);

        if (!isset($headers['authorization'])) {
            show_json(['message' => 'Token tidak ditemukan'], 401);
        }

        $token = str_replace('Bearer ', '', $headers['authorization']);
        $decoded = decode_jwt($token);

        if (!empty($decoded->error)) {
            show_json(['message' => $decoded->error], 401);
        }

        $CI->load->model('User_model');
        $CI->user = $CI->User_model->get_user_by_id($decoded->user_id);

        if (!$CI->user) {
            show_json(['message' => 'User not found'], 404);
        }
    }
}
