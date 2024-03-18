<div class="container-fluid">
    <h3><i class="fas fa-edit"></i>EDIT DATA SURAT BOS</h3>

    <?php foreach($surat as $srt) : ?>
        <form method="post" action="<?php echo base_url(). 'surat_bos/update' ?>">
            <div class="for-group">
                <label>Uraian Kegiatan</label>
                <input type="hidden" name="id_surat" class="form-control" value="<?php echo $srt->id_surat ?>">
                <input type="text" name="kegiatan" class="form-control" value="<?php echo $srt->kegiatan ?>">
            </div>
            <div class="for-group">
                <label>Nama Pembuat Form Permintaan</label>
                <select name="nama_pegawai" class="form-control selectize" id="select_box">
                    <?php foreach($pegawai as $pgw): ?>
                        <?php if($pgw->NIP == $srt->NIP): ?>
                            <option value="<?php echo $pgw->NIP ?>" selected><?php echo $pgw->nama_pegawai ?></option>
                        <?php else: ?>
                            <option value="<?php echo $pgw->NIP ?>"><?php echo $pgw->nama_pegawai ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>   
                </select>
            </div>
            <div class="for-group">
                <label>Tanggal (mm/dd/yyyy)</label>
                <input type="date" name="tanggal" class="form-control" value="<?php echo $srt->tanggal ?>">
            </div>
            <div class="for-group">
                <label>Nomor Surat</label>
                <input type="text" name="nomor_surat" class="form-control" value="<?php echo $srt->nomor_surat ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm mt-3">Save</button>
        </form>

    <?php endforeach; ?>
        
</div>