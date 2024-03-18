<?php

class Model_surat extends CI_Model{
    public function tampil_data($limit, $offset)
    {
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get('tb_suratbos', $limit, $offset);
    }

    public function get_data()
    {
        return $this->db->get('tb_suratbos');
    }

    public function countData(){
        return $this->db->get('tb_suratbos')->num_rows();
    }

    public function total_permintaan() {
        return $this->db->count_all_results('tb_suratbos');
    }

    public function get_permintaan_per_bulan() {
        $this->db->select("DATE_FORMAT(tanggal, '%m') as bulan, COUNT(*) as total_permintaan");
        $this->db->where("YEAR(tanggal)", 2024);
        $this->db->group_by("DATE_FORMAT(tanggal, '%m')");
        return $this->db->get('tb_suratbos')->result_array();
    }

    public function tambah_permintaan($data,$table){
        $this->db->insert($table,$data);
    }

    public function cek_nomor_surat_valid($nomor_surat) {
        $this->db->select('nomor_surat');
        $this->db->where('nomor_surat', $nomor_surat);
        $this->db->order_by('id_surat', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tb_suratbos');
        if ($query->num_rows() > 0) {
            return false;
        }
        return true;
    }

    public function get_nosurat($tanggal) {
        // Cek apakah ada nomor surat untuk tanggal yang diberikan
        $this->db->select('nomor_surat');
        
        // Jika tanggal yang diinputkan sama dengan tanggal terakhir di database
        $this->db->where('tanggal', date("Y-m-d", strtotime($tanggal)));
        
        $this->db->order_by('id_surat', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tb_suratbos');
    
        if ($query->num_rows() > 0) {
            return $query->row()->nomor_surat;
        }
    
        // Jika tidak ada nomor surat untuk tanggal yang diinputkan,
        // ambil nomor surat terakhir sebelum tanggal yang diinputkan
        $this->db->select('nomor_surat');
        $this->db->where('tanggal <', date("Y-m-d", strtotime($tanggal)));
        $this->db->order_by('tanggal', 'DESC');
        $this->db->order_by('id_surat', 'DESC');
        $this->db->limit(1);
        $query_previous_date = $this->db->get('tb_suratbos');
    
        if ($query_previous_date->num_rows() > 0) {
            return $query_previous_date->row()->nomor_surat;
        }
        
        return null; // Kembalikan null jika tidak ada data
    }        
    
    public function edit_surat($where,$table){
        return $this->db->get_where($table,$where);
    }

    public function update_data($where,$data,$table) {
        $this->db->where($where);
        $this->db->update($table,$data);
    }

    public function hapus_data($where,$table){
        $this->db->where($where);
        $this->db->delete($table);
    }

    public function get_keyword_limit($keyword, $limit, $offset) {
        $this->db->select('tb_suratbos.*, tb_pegawai.nama_pegawai');
        $this->db->from('tb_suratbos');
        $this->db->join('tb_pegawai', 'tb_pegawai.NIP = tb_suratbos.NIP'); 
        $this->db->like('tb_suratbos.kegiatan', $keyword); 
        $this->db->or_like('tb_pegawai.nama_pegawai', $keyword); 
        $this->db->or_like('tb_suratbos.tanggal', $keyword);
        $this->db->or_like('tb_suratbos.nomor_surat', $keyword);
        $this->db->limit($limit, $offset); // Batasan pagination
        return $this->db->get()->result();
    }
         
}