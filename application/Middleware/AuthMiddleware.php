<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class AuthMiddleware {
    public function check_auth() {
    $this->CI->load->helper('jwt');
    $headers = $this->CI->input->get_request_header('Authorization');
    
    if (!$headers) {
        show_json(['message' => 'Unauthorized'], 401);
    }

    $token = str_replace('Bearer ', '', $headers);
    $decoded = decode_jwt($token);
    
    if (!empty($decoded->error)) {
        show_json(['message' => 'Invalid Token'], 401);
    }

    $this->CI->user = $this->CI->User_model->get_user_by_id($decoded->user_id);
    
    if (!$this->CI->user) {
        show_json(['message' => 'User not found'], 404);
    }

    // Cek apakah token hampir expired (misal < 5 menit)
    $time_left = $decoded->exp - time();
    if ($time_left < 300) { // 300 detik = 5 menit
        show_json([
            'message' => 'Access token hampir expired',
            'need_refresh' => true
        ], 401);
    }
}

}
