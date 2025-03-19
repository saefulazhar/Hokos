<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '../vendor/autoload.php';

use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware {
    private $CI;
    public $user; // Simpan user yang berhasil login

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->helper(['jwt_helper', 'json_helper']);
        $this->CI->load->model('User_model');
    }

    public function check_auth() {
        $headers = $this->CI->input->get_request_header('Authorization');

        if (!$headers) {
            log_message('error', 'JWT Error: Token tidak ditemukan');
            show_json(['status' => false, 'message' => 'Unauthorized: Token tidak ditemukan'], 401);
        }

        $token = str_replace('Bearer ', '', $headers);

        try {
            $decoded = decode_jwt($token);
        } catch (ExpiredException $e) {
            log_message('error', 'JWT Expired: ' . $e->getMessage());
            show_json(['status' => false, 'message' => 'Unauthorized: Token sudah expired'], 401);
        } catch (SignatureInvalidException $e) {
            log_message('error', 'JWT Signature Invalid: ' . $e->getMessage());
            show_json(['status' => false, 'message' => 'Unauthorized: Token tidak valid'], 401);
        } catch (Exception $e) {
            log_message('error', 'JWT Error: ' . $e->getMessage());
            show_json(['status' => false, 'message' => 'Unauthorized: Token tidak valid'], 401);
        }

        if (!$decoded || !isset($decoded->user_id)) {
            log_message('error', 'JWT Error: Token tidak memiliki user_id');
            show_json(['status' => false, 'message' => 'Unauthorized: Token tidak valid'], 401);
        }

        // Periksa 'aud' untuk memastikan token dibuat untuk client
        if (isset($decoded->aud) && $decoded->aud !== 'client') {
            log_message('error', 'JWT Error: Token bukan untuk client');
            show_json(['status' => false, 'message' => 'Unauthorized: Token tidak valid untuk aplikasi ini'], 403);
        }

        // Ambil data user berdasarkan ID dari token
        $this->user = $this->CI->User_model->get_user_by_id($decoded->user_id);
        if (!$this->user) {
            log_message('error', 'JWT Error: User ID ' . $decoded->user_id . ' tidak ditemukan');
            show_json(['status' => false, 'message' => 'Unauthorized: User tidak ditemukan'], 404);
        }

        // Cek apakah token hampir expired (misal < 5 menit)
        $time_left = $decoded->exp - time();
        if ($time_left < 300) {
            log_message('info', 'JWT Warning: Token hampir expired (kurang dari 5 menit)');
            $this->user->token_near_expiry = true;
        } else {
            $this->user->token_near_expiry = false;
        }

        // Validasi role 
        if (!in_array($this->user->role, ['admin', 'pemilik', 'pencari'])) {
            log_message('error', 'JWT Error: Role ' . $this->user->role . ' tidak diizinkan');
            show_json(['status' => false, 'message' => 'Akses ditolak'], 403);
        }
    }
}
