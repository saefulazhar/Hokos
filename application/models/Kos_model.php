<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kos_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Load database
    }

    // Ambil semua kos dari database
    public function get_all_kos() {
        return $this->db->get('kos')->result_array(); 
    }
}
