<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'middleware/AuthMiddleware.php';


class Pemilik extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Kos_model');
        $this->authMiddleware = new AuthMiddleware();
        $this->authMiddleware->check_auth(); // Middleware untuk autentikasi
    }

    public function home() {
        $data['title'] = "Dashboard Pemilik";
        $data['kos_list'] = $this->Kos_model->get_kos_by_owner($this->authMiddleware->user->id);
        $this->load->view('pemilik/home_view', $data);
    }

    public function tambah_kos() {
        $data = [
            'owner_id'   => $this->authMiddleware->user->id,
            'name'       => $this->input->post('name'),
            'address'    => $this->input->post('address'),
            'latitude'   => $this->input->post('latitude'),
            'longitude'  => $this->input->post('longitude'),
            'price'      => $this->input->post('price'),
            'description'=> $this->input->post('description'),
            'status'     => 'tersedia'
        ];

        $this->Kos_model->insert_kos($data);
        redirect('pemilik/home');
    }
    public function edit_kos($id) {
        $data['kos'] = $this->Kos_model->get_kos_by_id($id, $this->authMiddleware->user->id);
        if (!$data['kos']) show_404();
        $this->load->view('pemilik/edit_kos', $data);
    }

    public function update_kos($id) {
        $data = [
            'name'        => $this->input->post('name'),
            'address'     => $this->input->post('address'),
            'latitude'    => $this->input->post('latitude'),
            'longitude'   => $this->input->post('longitude'),
            'price'       => $this->input->post('price'),
            'description' => $this->input->post('description'),
            'status'      => $this->input->post('status')
        ];

        $this->Kos_model->update_kos($id, $this->authMiddleware->user->id, $data);
        redirect('pemilik/home');
    }

    public function hapus_kos($id) {
        $this->Kos_model->delete_kos($id, $this->authMiddleware->user->id);
        redirect('pemilik/home');
    }
}
