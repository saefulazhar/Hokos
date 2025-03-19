<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('json_helper'); // Load helper JSON
        $this->load->model('User_model'); // Load model User
        
        // Load middleware
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        $this->auth = new AuthMiddleware();
        $this->auth->check_auth(); // Validasi token JWT
    }

    public function index() {
        // Data user sudah tersedia di $this->user dari middleware
        show_json(['status' => true, 'user' => $this->user]);
    }

    public function home_page(){
        $this->load->view('home_view');
    }
}
