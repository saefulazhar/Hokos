<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function get_profile() {
        $this->load->helper('jwt');

        $headers = $this->input->get_request_header('Authorization');
        if (!$headers) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers);
        $decoded = decode_jwt($token);

        // Perbaikan: Cek apakah ada error
        if (!empty($decoded->error)) {
            http_response_code(401);
            echo json_encode(['message' => $decoded->error]);
            exit;
        }

        $user = $this->User_model->get_user_by_id($decoded->user_id);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
            exit;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($user));
    }

    public function register() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Semua field harus diisi']);
            exit;
        }

        $existing_user = $this->User_model->get_user_by_email($data['email']);
        if ($existing_user) {
            http_response_code(409);
            echo json_encode(['message' => 'Email sudah terdaftar']);
            exit;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user_id = $this->User_model->insert_user($data);

        if ($user_id) {
            http_response_code(201);
            echo json_encode(['message' => 'Registrasi berhasil', 'user_id' => $user_id]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Gagal mendaftar']);
        }
        exit;
    }

    public function login() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $device_info = $data['device_info'] ?? 'Unknown Device';

        $user = $this->User_model->get_user_by_email($email);

        if (!$user || !password_verify($password, $user->password)) {
            http_response_code(401);
            echo json_encode(['message' => 'Email atau password salah']);
            exit;
        }

        $access_token = generate_jwt(['user_id' => $user->id], $this->config->item('jwt_expire_time'));
        $refresh_token = generate_jwt(['user_id' => $user->id], $this->config->item('jwt_refresh_expire_time'));

        $this->User_model->save_refresh_token(
            $user->id,
            $refresh_token,
            date('Y-m-d H:i:s', time() + $this->config->item('jwt_refresh_expire_time')),
            $device_info
        );

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'access_token' => $access_token,
            'refresh_token' => $refresh_token
        ]);
        exit;
    }
}
