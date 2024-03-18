<?php

class Surat_bos extends CI_Controller{
    public function index()
    {
        $this->load->library('pagination');

        // Konfigurasi pagination
        $config['base_url'] = base_url('surat_bos/index');
        $config['total_rows'] = $this->model_surat->countData();
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        // Konfigurasi tampilan pagination
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-end">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $data['start'] = $this->uri->segment(3) ? $this->uri->segment(3) : 0; // Ambil nilai offset dari URI segment 3 atau set ke 0 jika tidak ada
        $data['surat'] = $this->model_surat->tampil_data($config['per_page'], $data['start'])->result();
        $data['pegawai'] = $this->model_pegawai->get_nama()->result();
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('surat_bos', $data);
        $this->load->view('templates/footer');
    }


    function recursive_buat_no_surat($format_penomoran, $urut_surat_terakhir, $urut_anak = 2){
        $nomor_surat_baru = str_replace(['{urut}', '{year}'], [$urut_surat_terakhir. ".$urut_anak", date('Y')], $format_penomoran);
        
        $valid = $this->model_surat->cek_nomor_surat_valid($nomor_surat_baru);
        if(!$valid){
          return $this->recursive_buat_no_surat($format_penomoran, $urut_surat_terakhir, $urut_anak + 1);
        }
        
        return $nomor_surat_baru;
    }

    public function tambah_aksi(){
        $format_penomoran   = "B-00{urut}/13711/KU.300/{year}";
        
        $kegiatan           = $this->input->post('kegiatan');
        $nip                = $this->input->post('nama_pegawai');
        $tanggal            = $this->input->post('tanggal');
        $nosurat_terakhir   = $this->model_surat->get_nosurat($tanggal);
    
        // if $nosurat_terakhir not null
        if ($nosurat_terakhir) {
            //ambil nomor urutnya
            //memecah string berdasarkan tanda '-' dan '/'
            $pecah_nomor = explode('-', $nosurat_terakhir);
            $pecah_nomor = explode('/', $pecah_nomor[1]);
    
            //mengambil bagian pertama hasil pemecahan kedua sebagai nomor // nomor urut surat
            $urut_surat_terakhir = $pecah_nomor[0];
            $urut_surat_terakhir = intval($urut_surat_terakhir);
    
            //coba buat nomor surat baru
            $nomor_surat_baru = str_replace(['{urut}', '{year}'], [$urut_surat_terakhir + 1, date('Y')], $format_penomoran);
    
            //cek di database apakah nomor ini sudah pernah digunakan
            $valid = $this->model_surat->cek_nomor_surat_valid($nomor_surat_baru);
    
            //jika surat sudah digunakan maka panggil fungsi recursive_buat_no_surat
            if(!$valid){
                $nomor_surat_baru = $this->recursive_buat_no_surat($format_penomoran, $urut_surat_terakhir);
            }
        } else {
            $nomor_surat_baru = str_replace(['{urut}', '{year}'], ['1', date('Y')], $format_penomoran);
        }
    
        $data = array(
            'kegiatan'      => $kegiatan,
            'NIP'           => $nip,
            'tanggal'       => $tanggal,
            'nomor_surat'   => $nomor_surat_baru
        );
    
        $this->model_surat->tambah_permintaan($data, 'tb_suratbos');
        $pesan = "Nomor surat adalah <strong>$nomor_surat_baru</strong><button class='btn btn-sm btn-info float-right' onclick='copyToClipboard(\"$nomor_surat_baru\")'><i class='fas fa-copy'></i> Salin</button>";
        $this->session->set_flashdata("success", $pesan);
        redirect('surat_bos/index');
    }    
    
    public function edit($id)
    {
        $where = array('id_surat' =>$id);
        $data['surat'] = $this->model_surat->edit_surat($where, 'tb_suratbos')->result();
        $data['pegawai'] = $this->model_pegawai->get_nama()->result();
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('edit_suratbos', $data);
        $this->load->view('templates/footer');
    }

    public function update(){
        $id             = $this->input->post('id_surat');
        $kegiatan       = $this->input->post('kegiatan');
        $nip            = $this->input->post('nama_pegawai');
        $tanggal        = $this->input->post('tanggal');
        $no_surat       = $this->input->post('nomor_surat');

        $data = array(
            'kegiatan'      => $kegiatan,
            'NIP'           => $nip,
            'tanggal'       => $tanggal,
            'nomor_surat'   => $no_surat
        );

        $where = array(
            'id_surat' => $id
        );

        $this->model_surat->update_data($where, $data, 'tb_suratbos');
        $this->session->set_flashdata("success", "Data berhasil diupdate");
        redirect('surat_bos/index');
    }

    public function hapus ($id)
    {
        $where = array('id_surat' => $id);
        $this->model_surat->hapus_data($where, 'tb_suratbos');
        $this->session->set_flashdata("success", "Data berhasil dihapus");
        redirect('surat_bos/index');
    }

    public function search() {
        $this->load->library('pagination');
        $keyword = $this->input->post('keyword');
    
        // Jika input pencarian kosong, alihkan kembali ke halaman index
        if(empty($keyword)) {
            redirect('surat_bos/index');
        }
        
        // Konfigurasi pagination
        $config['base_url'] = base_url('surat_bos/search');
        $config['total_rows'] = count($this->model_surat->get_keyword_limit($keyword, 0, 0)); // Perhitungan total_rows menggunakan get_keyword_limit
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        
        // Konfigurasi tampilan pagination
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-end">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['attributes'] = array('class' => 'page-link');
        
        $this->pagination->initialize($config);
        
        // Ambil nilai offset dari URI segment 3 atau set ke 0 jika tidak ada
        $data['start'] = $this->uri->segment(3) ? $this->uri->segment(3) : 0;
        
        // Ambil data surat sesuai keyword dan batasan pagination
        $data['surat'] = $this->model_surat->get_keyword_limit($keyword, $config['per_page'], $data['start']);
        $data['pegawai'] = $this->model_pegawai->get_nama()->result();
        
        // Load view dengan data yang telah diperoleh
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('surat_bos', $data);
        $this->load->view('templates/footer');
    }
    
    public function csv(){
        $this->load->helper("file");
        $this->load->dbutil();
    
        // Ambil data dari tabel tb_suratbos dengan join ke tb_pegawai
        $query = $this->db->select('(@row:=@row+1) AS "No.", tb_suratbos.kegiatan AS "Uraian Kegiatan", tb_pegawai.nama_pegawai AS "Nama Pembuat Form Permintaan", DATE_FORMAT(tb_suratbos.tanggal, "%e %M %Y") AS "Tanggal", tb_suratbos.nomor_surat AS "Nomor Surat"')
                  ->from('(SELECT @row := 0) r, tb_suratbos')
                  ->join('tb_pegawai', 'tb_pegawai.NIP = tb_suratbos.NIP')
                  ->order_by('tb_suratbos.tanggal', 'ASC') // Urutkan berdasarkan tanggal secara ascending
                  ->get();
    
        // Konversi hasil query menjadi format CSV
        $data = $this->dbutil->csv_from_result($query, ";", "\r\n", '"');
        
        // Tulis data CSV ke file
        write_file("backup\surat_bos". time(). ".csv", $data );
    
        // Set flashdata untuk memberi pesan sukses
        $this->session->set_flashdata("success", "Data berhasil diexport");
    
        // Redirect kembali ke halaman index
        redirect('surat_bos/index');
    }   
}