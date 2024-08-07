<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <button type="button" onclick="bulk_delete()" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
        <div class="pull-right">
        <button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-sm btn-flat bg-purple"><i class="fa fa-plus"></i> Ujian Baru</button>
            <!-- <a href="<?//=base_url('ujian/add')?>" class="btn bg-purple btn-sm btn-flat"><i class="fa fa-file-text-o"></i> Ujian Baru</a> -->
            <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat bg-maroon"><i class="fa fa-refresh"></i> Reload</button>
        </div>
    </div>
	<?=form_open('ujian/delete', array('id'=>'bulk'))?>
    <div class="table-responsive px-4 pb-3" style="border: 0">
        <table id="ujian" class="w-100 table table-striped table-bordered table-hover">
        <thead>
            <tr>
				<th class="text-center">
					<input type="checkbox" class="select_all">
				</th>
                <th>No.</th>
                <th>Nama Ujian</th>
                <th>Mata Pelajaran</th>
                <th>Jumlah Soal</th>
                <th>Waktu</th>
                <th>Acak Soal</th>
				<th	class="text-center">Token</th>
				<th class="text-center">Aksi</th>
            </tr>        
        </thead>
        <tfoot>
            <tr>
				<th class="text-center">
					<input type="checkbox" class="select_all">
				</th>
                <th>No.</th>
                <th>Nama Ujian</th>
                <th>Mata Pelajaran</th>
                <th>Jumlah Soal</th>
                <th>Waktu</th>
                <th>Acak Soal</th>                
				<th	class="text-center">Token</th>
				<th class="text-center">Aksi</th>
            </tr>
        </tfoot>
        </table>
    </div>
	<?=form_close();?>
</div>
<div class="modal fade" id="myModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Tambah Data</h4>
			</div>
			<?= form_open('ujian/add', array('id', 'tambah')); ?>
			<div class="modal-body">
            <div class="alert bg-white">
                    <label>Guru (Mata Pelajaran)</label>
                    <?php if ($this->ion_auth->in_group('guru')) : ?>
                    <select name="mapel" required="required" id="mapel" class="select2 form-group" style="width:100% !important">
                        <option value="" disabled selected>Pilih Mata Pelajaran</option>
                        <?php foreach ($mapel2 as $d) : ?>
                            <option value="<?=$d->id_guru?>:<?=$d->id_mapel?>"><?=$d->nama_guru?> (<?=$d->nama_mapel?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block" style="color: #dc3545"><?=form_error('mapel_id')?></small>
                    <?php else : ?>
                    
                    <input type="text" readonly="readonly" class="form-control" value="<?=$mapel2->nama_guru; ?> (<?=$mapel2->nama_mapel; ?>)">
                    <?php endif; ?>
                
                </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" name="input">Generate</button>
			</div>
			<?= form_close(); ?>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<script type="text/javascript">
	var id_guru = '<?=$guru->id_guru?>';
</script>

<script src="<?=base_url()?>assets/dist/js/app/ujian/data.js"></script>