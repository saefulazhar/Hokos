<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    public function get_user_by_id($user_id) {
        return $this->db->get_where('users', ['id' => $user_id])->row();
    }

    public function get_user_by_email($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function insert_user($data) {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function save_refresh_token($user_id, $token, $expires_at, $device_info) {
        $existing = $this->db->get_where('users_token', [
            'user_id' => $user_id,
            'device_info' => $device_info
        ])->row();

        if ($existing) {
            $this->db->where('id', $existing->id)->update('users_token', [
                'token' => $token,
                'expires_at' => $expires_at
            ]);
        } else {
            $this->db->insert('users_token', [
                'user_id' => $user_id,
                'token' => $token,
                'expires_at' => $expires_at,
                'device_info' => $device_info
            ]);
        }
    }
}
