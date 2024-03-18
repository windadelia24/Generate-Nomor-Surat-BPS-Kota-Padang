<?php

class Dashboard extends CI_Controller{

    public function index()
    {
        $data['total_permintaan'] = $this->model_surat->total_permintaan();
        $data['permintaan_per_bulan'] = $this->model_surat->get_permintaan_per_bulan();
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('dashboard', $data);
        $this->load->view('templates/footer');
    }
}