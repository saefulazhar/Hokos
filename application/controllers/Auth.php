<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'middleware/AuthMiddleware.php';

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper(['json_helper', 'jwt_helper']);
        $this->load->config('jwt');
        $this->auth_middleware = new AuthMiddleware();
    }

    public function register() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
    
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        $role = trim($data['role'] ?? '');

        // Validasi input
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            show_json(['message' => 'Semua field harus diisi'], 400);
        }
    
        if (!in_array($role, ['pemilik', 'pencari'])) {
            show_json(['message' => 'Role tidak valid'], 400);
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            show_json(['message' => 'Format email tidak valid'], 400);
        }

        if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            show_json(['message' => 'Nama hanya boleh berisi huruf dan spasi'], 400);
        }
    
        if (strlen($password) < 6) {
            show_json(['message' => 'Password minimal 6 karakter'], 400);
        }

        if ($this->User_model->get_user_by_email($email)) {
            show_json(['message' => 'Email sudah terdaftar'], 409);
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $user_id = $this->User_model->insert_user([
            'name' => $name,
            'email' => $email,
            'password' => $hashed_password,
            'role' => $role
        ]);

        if ($user_id) {
            show_json(['message' => 'Registrasi berhasil', 'user_id' => $user_id], 201);
        }
    
        show_json(['message' => 'Gagal mendaftar'], 500);
    }

    public function login() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        $device_info = trim($data['device_info'] ?? 'Unknown Device');

        if (empty($email) || empty($password)) {
            show_json(['message' => 'Email dan password harus diisi'], 400);
        }

        $user = $this->User_model->get_user_by_email($email);
        if (!$user || !password_verify($password, $user->password)) {
            show_json(['message' => 'Email atau password salah'], 401);
        }

        // Generate token
        $access_token = generate_jwt([
            'user_id' => $user->id,
            'role' => $user->role
        ], $this->config->item('jwt_expire_time'));
        
        $refresh_token = generate_jwt([
            'user_id' => $user->id,
            'role' => $user->role
        ], $this->config->item('jwt_refresh_expire_time'));

        // Simpan refresh token di database
        $this->User_model->save_refresh_token(
            $user->id,
            $refresh_token,
            date('Y-m-d H:i:s', time() + $this->config->item('jwt_refresh_expire_time')),
            $device_info
        );

        unset($user->password); // Hapus password dari respons

        show_json([
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'user' => $user
        ], 200);
    }

    public function refresh_token() {
        $headers = $this->input->get_request_header('Authorization');
        if (!$headers) {
            show_json(['message' => 'Refresh token diperlukan'], 400);
        }

        $refresh_token = str_replace('Bearer ', '', $headers);
        $decoded = decode_jwt($refresh_token);

        if (!$decoded || !isset($decoded->user_id)) {
            show_json(['message' => 'Refresh token tidak valid'], 401);
        }

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

        // Buat token baru
        $access_token = generate_jwt(['user_id' => $user_id], $this->config->item('jwt_expire_time'));
        $new_refresh_token = generate_jwt(['user_id' => $user_id], $this->config->item('jwt_refresh_expire_time'));

        // Update token
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

    public function logout() {
        $this->auth_middleware->check_auth();
        $user_id = $this->auth_middleware->user->id;

        $headers = $this->input->get_request_header('Authorization');
        if (!$headers) {
            show_json(['message' => 'Refresh token diperlukan'], 400);
        }

        $refresh_token = str_replace('Bearer ', '', $headers);
        if ($this->User_model->delete_refresh_token($user_id, $refresh_token)) {
            show_json(['message' => 'Logout berhasil'], 200);
        }

        show_json(['message' => 'Gagal logout, coba lagi'], 500);
    }

    public function get_profile() {
        $this->auth_middleware->check_auth();
        $user_id = $this->auth_middleware->user->id;
    
        $user = $this->User_model->get_user_by_id($user_id);
        if (!$user) {
            show_json(['message' => 'User tidak ditemukan'], 404);
        }

        unset($user->password);
        show_json(['user' => $user], 200);
    }

    public function register_page(){
        $this->load->view('register_view');
    }

    public function login_page(){
        $this->load->view('login_view');
    }
}
