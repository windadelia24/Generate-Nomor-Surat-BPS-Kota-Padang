<?php

class Model_pegawai extends CI_Model{
    public function get_nama(){
        return $this->db->get('tb_pegawai');
    }
}