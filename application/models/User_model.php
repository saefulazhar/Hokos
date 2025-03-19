<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    
    // Ambil user berdasarkan ID
    public function get_user_by_id($user_id) {
        return $this->db->get_where('users', ['id' => (int) $user_id])->row();
    }

    // Ambil user berdasarkan email
    public function get_user_by_email($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    // Insert user baru
    public function insert_user($data) {
        if (!isset($data['role']) || empty($data['email']) || empty($data['password'])) {
            return false; // Pastikan data yang penting tidak kosong
        }
        $this->db->insert('users', $data);
        return $this->db->insert_id(); // Kembalikan ID user yang baru dibuat
    }

    // Update data user
    public function update_user($user_id, $data) {
        $this->db->where('id', (int) $user_id);
        return $this->db->update('users', $data);
    }

    // Simpan atau update refresh token
    public function save_refresh_token($user_id, $token, $expires_at, $device_info) {
        $existing = $this->db->get_where('user_tokens', [
            'user_id' => (int) $user_id,
            'device_info' => $device_info
        ])->row();

        $data = [
            'user_id' => (int) $user_id,
            'token' => $token,
            'expires_at' => $expires_at,
            'device_info' => $device_info
        ];

        if ($existing) {
            $this->db->where('id', $existing->id);
            return $this->db->update('user_tokens', $data);
        } else {
            return $this->db->insert('user_tokens', $data);
        }
    }

    // Hapus refresh token berdasarkan user_id & token
    public function delete_refresh_token($user_id, $refresh_token) {
        $this->db->where([
            'user_id' => (int) $user_id,
            'token' => $refresh_token
        ]);
        return $this->db->delete('user_tokens');
    }

    // Hapus semua token user (misalnya saat logout dari semua perangkat)
    public function delete_all_refresh_tokens($user_id) {
        $this->db->where('user_id', (int) $user_id);
        return $this->db->delete('user_tokens');
    }
}
