<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kos_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Ambil semua data kos berdasarkan owner_id (pemilik)
    public function get_kos_by_owner($owner_id) {
        return $this->db->get_where('kos', ['owner_id' => $owner_id])->result();
    }

    // Ambil satu data kos berdasarkan id
    public function get_kos_by_id($id, $owner_id) {
        return $this->db->get_where('kos', ['id' => $id, 'owner_id' => $owner_id])->row();
    }

    // Tambah data kos baru
    public function insert_kos($data) {
        return $this->db->insert('kos', $data);
    }

    // Update data kos
    public function update_kos($id, $owner_id, $data) {
        $this->db->where(['id' => $id, 'owner_id' => $owner_id]);
        return $this->db->update('kos', $data);
    }

    // Hapus data kos
    public function delete_kos($id, $owner_id) {
        return $this->db->delete('kos', ['id' => $id, 'owner_id' => $owner_id]);
    }
}
