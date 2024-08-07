<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>ujian/master" class="btn btn-sm btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="alert bg-purple">
                    <h4>Guru <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><b><?=$guru->nama_guru?></b></p>
                    <p><b><?=$ujian->nama_mapel?></b></p>
                </div>
                <div class="alert bg-purple">
                    <label>Guru (Mata Pelajaran)</label>
                    <?php if ($this->ion_auth->in_group('guru')) : ?>
                    <select name="mapel" id="mapel" class="select2 form-group bg-white" style="color:purple">
                        <option value="" disabled selected>Pilih Mata Pelajaran</option>
                        <?php
                        $sj = $ujian->nama_mapel;
                        foreach ($mapel2 as $m) : ?>
                            <option class ="bg-purple" <?=$m->id_mapel==$sj ? "selected" : "" ?> value="<?=$m->id_guru?>:<?=$m->id_mapel?>"><?=$m->nama_guru?> (<?=$m->nama_mapel?>)</option>
                        <?php endforeach; ?>
                    </select>


                    <small class="help-block" style="color: #dc3545"><?=form_error('mapel_id')?></small>
                    <?php else : ?>
                    
                    <input type="text" readonly="readonly" class="form-control" value="<?=$mapel2->nama_guru; ?> (<?=$mapel2->nama_mapel; ?>)">
                    <?php endif; ?>
                
                </div>
            </div>
            <div class="col-sm-4">
                <?=form_open('ujian/save', array('id'=>'formujian'), array('method'=>'edit','guru_id'=>$guru->id_guru, 'mapel_id'=>$ujian->mapel_id, 'id_ujian'=>$ujian->id_ujian))?>
                <div class="form-group">
                    <label for="nama_ujian">Nama Ujian</label>
                    <input value="<?=$ujian->nama_ujian?>" autofocus="autofocus" onfocus="this.select()" placeholder="Nama Ujian" type="text" class="form-control" name="nama_ujian">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jumlah_soal">Jumlah Soal</label>
                    <input value="<?=$ujian->jumlah_soal?>" placeholder="Jumlah Soal" type="number" class="form-control" name="jumlah_soal">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_mulai">Tanggal Mulai</label>
                    <input id="tgl_mulai" name="tgl_mulai" type="text" class="datetimepicker form-control" placeholder="Tanggal Mulai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_selesai">Tanggal Selesai</label>
                    <input id="tgl_selesai" name="tgl_selesai" type="text" class="datetimepicker form-control" placeholder="Tanggal Selesai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="waktu">Waktu</label>
                    <input value="<?=$ujian->waktu?>" placeholder="menit" type="number" class="form-control" name="waktu">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jenis">Acak Soal</label>
                    <select name="jenis" class="form-control">
                        <option value="" disabled selected>--- Pilih ---</option>
                        <option <?=$ujian->jenis==="acak"?"selected":"";?> value="acak">Acak Soal</option>
                        <option <?=$ujian->jenis==="urut"?"selected":"";?> value="urut">Urut Soal</option>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-default btn-flat">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button id="submit" type="submit" class="btn btn-flat bg-purple"><i class="fa fa-save"></i> Simpan</button>
                </div>
                <?=form_close()?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var tgl_mulai = '<?=$ujian->tgl_mulai?>';
    var terlambat = '<?=$ujian->terlambat?>';
</script>

<script src="<?=base_url()?>assets/dist/js/app/ujian/edit.js"></script>
<script>
    $("#mapel").change(function(){
         var y = $("#mapel").val()[2];
         var z = $("#mapel").val()[0];
         alert(y);
         $.ajax({
            dataType: 'json',
                delay: 250,
                url: "<?php echo site_url('ujian/updateMapelUjian');?>/"+z+"/"+y,
                data: function(params) {
                    return {
                        id: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(obj) {
                            return [{
                                id: obj.id_mapel,
                                text: obj.nama_mapel,
                            },
                        ];
                        })
                    };
                }
        });
});
</script>