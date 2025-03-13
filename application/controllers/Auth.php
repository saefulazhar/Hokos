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
}
