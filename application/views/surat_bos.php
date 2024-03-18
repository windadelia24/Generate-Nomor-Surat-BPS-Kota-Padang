<div class="container-fluid">
    <?php if($this->session->flashdata('success')) : ?>
    <div class="alert alert-success">
      <?= $this->session->flashdata('success'); ?>
    </div>
    <?php endif; ?>
    <button class="btn btn-sm btn-primary mb-3" data-toggle="modal" data-target="#tambah_permintaan"><i class="fas fa-plus fa-sm"></i>Tambah</button>
    <a class="btn btn-sm btn-warning mb-3" href=" <?php echo base_url('surat_bos/csv') ?>"><i class="fa fa-file-excel"></i>Export CSV</a>

    <div class="navbar-form navbar-right inline mb-3">
        <form class="d-flex" action="<?php echo base_url('surat_bos/search') ?>" method="post">
            <input type="text" name="keyword" class="form-control mr-3" placeholder="Search">
            <button type="submit" class="btn btn-success">Cari</button>
        </form>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>No.</th>
            <th>Uraian Kegiatan</th>
            <th>Nama Pembuat Form Permintaan</th>
            <th>Tanggal</th>
            <th>Nomor Surat</th>
            <th colspan="2">Aksi</th>
        </tr>

        <?php
        foreach($surat as $srt) : ?>

        <tr>
            <td><?php echo ++$start; ?></td>
            <td><?php echo $srt->kegiatan ?></td>
            <td>
              <?php 
              foreach($pegawai as $pgw){
                if($pgw->NIP == $srt->NIP){
                  echo $pgw->nama_pegawai;
                  break;
                }
              }
              ?>
            </td>
            <td>
              <?php 
              $bulan = array(
                '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April','05' => 'Mei','06' => 'Juni',
                '07' => 'Juli','08' => 'Agustus','09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
              );
              $tanggal = date('d', strtotime($srt->tanggal));
              $bulan_angka = date('m', strtotime($srt->tanggal));
              $tahun = date('Y', strtotime($srt->tanggal));
              $tanggal_formatted = $tanggal . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;
              echo $tanggal_formatted;
              ?></td>
            <td><?php echo $srt->nomor_surat ?></td>
            <td><?php echo anchor('surat_bos/edit/' .$srt->id_surat, '<div class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></div>') ?></td>
            <td><?php echo anchor('surat_bos/hapus/' .$srt->id_surat, '<div class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></div>') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="pagination">
        <?php echo $this->pagination->create_links(); ?>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="tambah_permintaan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Input Permintaan</h1>
      </div>
      <div class="modal-body">
        <form action="<?php echo base_url(). 'surat_bos/tambah_aksi';
        ?>" method="post">
          <div class="form-group">
            <label>Uraian Kegiatan</label>
            <input type="text" name="kegiatan" class="form-control">
            <label for="select_box">Nama Pembuat Form Permintaan</label>
            <select name="nama_pegawai" class="form-control selectize" id="select_box">
              <option value="">Select Name</option>
              <?php foreach($pegawai as $pgw): ?>
                <option value="<?php echo $pgw->NIP ?>"><?php echo $pgw->nama_pegawai ?></option>
              <?php endforeach; ?>   
            </select>
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Load Selectize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap4.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.bootstrap4.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.colVis.min.js"></script>
<script>
    function copyToClipboard(text) {
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        alert("Nomor surat telah disalin ke clipboard: " + text);
    }
</script>