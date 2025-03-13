<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'middleware/AuthMiddleware.php';

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->auth_middleware = new AuthMiddleware();
    }

    public function get_profile() {
        $this->auth_middleware->check_auth(); // Middleware untuk validasi token

        $user = $this->User_model->get_user_by_id($this->user->id);
        if (!$user) {
            show_json(['message' => 'User not found'], 404);
        }

        show_json($user);
    }

    public function register() {
        $this->load->helper('json');
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            show_json(['message' => 'Semua field harus diisi'], 400);
        }
        
        if ($this->User_model->get_user_by_email($data['email'])) {
            show_json(['message' => 'Email sudah terdaftar'], 409);
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $user_id = $this->User_model->insert_user($data);
        
        if ($user_id) {
            show_json(['message' => 'Registrasi berhasil', 'user_id' => $user_id], 201);
        }
        
        show_json(['message' => 'Gagal mendaftar'], 500);
    }

    public function login() {
        $this->load->helper('json');
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $device_info = $data['device_info'] ?? 'Unknown Device';
        
        $user = $this->User_model->get_user_by_email($email);
        if (!$user || !password_verify($password, $user->password)) {
            show_json(['message' => 'Email atau password salah'], 401);
        }
        
        $access_token = generate_jwt(['user_id' => $user->id], $this->config->item('jwt_expire_time'));
        $refresh_token = generate_jwt(['user_id' => $user->id], $this->config->item('jwt_refresh_expire_time'));
        
        $this->User_model->save_refresh_token(
            $user->id,
            $refresh_token,
            date('Y-m-d H:i:s', time() + $this->config->item('jwt_refresh_expire_time')),
            $device_info
        );
        
        setcookie("access_token", $access_token, [
            'expires' => time() + $this->config->item('jwt_expire_time'),
            'path' => '/',
            'httponly' => true,
            'secure' => false, // Ubah ke `true` jika menggunakan HTTPS
            'samesite' => 'Strict'
        ]);
        
        show_json([
            'access_token' => $access_token,
            'refresh_token' => $refresh_token
        ], 200);
    }

    public function refresh_token() {
        $this->load->helper(['jwt', 'json']);
    
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $refresh_token = $data['refresh_token'] ?? null;
    
        if (!$refresh_token) {
            show_json(['message' => 'Refresh token diperlukan'], 400);
        }
    
        // Decode refresh token
        $decoded = decode_jwt($refresh_token);
        if (!empty($decoded->error)) {
            show_json(['message' => 'Refresh token tidak valid'], 401);
        }
    
        // Cek apakah refresh token ada di database
        $user_id = $decoded->user_id;
        $user = $this->User_model->get_user_by_id($user_id);
        if (!$user) {
            show_json(['message' => 'User tidak ditemukan'], 404);
        }
    
        $token_data = $this->db->get_where('user_tokens', [
            'user_id' => $user_id,
            'token' => $refresh_token
        ])->row();
    
        if (!$token_data || strtotime($token_data->expires_at) < time()) {
            show_json(['message' => 'Refresh token expired atau tidak valid'], 401);
        }
    
        // Buat access token baru
        $access_token = generate_jwt(['user_id' => $user_id], $this->config->item('jwt_expire_time'));
    
        // Buat refresh token baru
        $new_refresh_token = generate_jwt(['user_id' => $user_id], $this->config->item('jwt_refresh_expire_time'));
    
        // Perbarui refresh token di database
        $this->User_model->save_refresh_token(
            $user_id,
            $new_refresh_token,
            date('Y-m-d H:i:s', time() + $this->config->item('jwt_refresh_expire_time')),
            $token_data->device_info
        );
    
        show_json([
            'access_token' => $access_token,
            'refresh_token' => $new_refresh_token
        ], 200);
    }
    
    
}
