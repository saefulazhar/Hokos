<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'middleware/AuthMiddleware.php';


class Pencari extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->authMiddleware = new AuthMiddleware();
        $this->authMiddleware->check_auth(); // Middleware untuk autentikasi
    }

    public function home() {
        if ($this->authMiddleware->user->role !== 'pencari') {
            show_404(); // Blokir akses jika bukan pemilik
        }

        $data['title'] = "Dashboard Pencari";
        $this->load->view('pencari/home_view', $data);
        
    }
}
