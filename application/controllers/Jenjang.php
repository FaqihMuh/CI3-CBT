<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Jenjang extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		} else if (!$this->ion_auth->is_admin()) {
			show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->form_validation->set_error_delimiters('', '');
	}

	public function output_json($data, $encode = true)
	{
		if ($encode) $data = json_encode($data);
		$this->output->set_content_type('application/json')->set_output($data);
	}

	public function index()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Jenjang',
			'subjudul' => 'Data Jenjang'
		];
		$this->load->view('_templates/dashboard/_header', $data);
		$this->load->view('master/jenjang/data');
		$this->load->view('_templates/dashboard/_footer');
	}

	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Tambah Jenjang',
			'subjudul'	=> 'Tambah Data Jenjang',
			'banyak'	=> $this->input->post('banyak', true)
		];
		$this->load->view('_templates/dashboard/_header', $data);
		$this->load->view('master/jenjang/add');
		$this->load->view('_templates/dashboard/_footer');
	}

	public function data()
	{
		$this->output_json($this->master->getDataJenjang(), false);
	}

	public function edit()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			redirect('jenjang');
		} else {
			$jenjang = $this->master->getJenjangById($chk);
			$data = [
				'user' 		=> $this->ion_auth->user()->row(),
				'judul'		=> 'Edit Jenjang',
				'subjudul'	=> 'Edit Data Jenjang',
				'jenjang'	=> $jenjang
			];
			$this->load->view('_templates/dashboard/_header', $data);
			$this->load->view('master/jenjang/edit');
			$this->load->view('_templates/dashboard/_footer');
		}
	}

	public function save()
	{
		$rows = count($this->input->post('nama_jenjang', true));
		$mode = $this->input->post('mode', true);
		for ($i = 1; $i <= $rows; $i++) {
			$nama_jenjang = 'nama_jenjang[' . $i . ']';
			$this->form_validation->set_rules($nama_jenjang, 'Jenjang', 'required');
			$this->form_validation->set_message('required', '{field} Wajib diisi');

			if ($this->form_validation->run() === FALSE) {
				$error[] = [
					$nama_jenjang => form_error($nama_jenjang)
				];
				$status = FALSE;
			} else {
				if ($mode == 'add') {
					$insert[] = [
						'nama_jenjang' => $this->input->post($nama_jenjang, true)
					];
				} else if ($mode == 'edit') {
					$update[] = array(
						'id_jenjang'	=> $this->input->post('id_jenjang[' . $i . ']', true),
						'nama_jenjang' 	=> $this->input->post($nama_jenjang, true)
					);
				}
				$status = TRUE;
			}
		}
		if ($status) {
			if ($mode == 'add') {
				$this->master->create('jenjang', $insert, true);
				$data['insert']	= $insert;
			} else if ($mode == 'edit') {
				$this->master->update('jenjang', $update, 'id_jenjang', null, true);
				$data['update'] = $update;
			}
		} else {
			if (isset($error)) {
				$data['errors'] = $error;
			}
		}
		$data['status'] = $status;
		$this->output_json($data);
	}

	public function delete()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->output_json(['status' => false]);
		} else {
			if ($this->master->delete('jenjang', $chk, 'id_jenjang')) {
				$this->output_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	public function load_jenjang()
	{
		$data = $this->master->getJenjang();
		$this->output_json($data);
	}

	public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Jenjang',
			'subjudul' => 'Import Jenjang'
		];
		if ($import_data != null) $data['import'] = $import_data;

		$this->load->view('_templates/dashboard/_header', $data);
		$this->load->view('master/jenjang/import');
		$this->load->view('_templates/dashboard/_footer');
	}

	public function preview()
	{
		$config['upload_path']		= './uploads/import/';
		$config['allowed_types']	= 'xls|xlsx|csv';
		$config['max_size']			= 2048;
		$config['encrypt_name']		= true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('upload_file')) {
			$error = $this->upload->display_errors();
			echo $error;
			die;
		} else {
			$file = $this->upload->data('full_path');
			$ext = $this->upload->data('file_ext');

			switch ($ext) {
				case '.xlsx':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
					break;
				case '.xls':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
					break;
				case '.csv':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
					break;
				default:
					echo "unknown file ext";
					die;
			}

			$spreadsheet = $reader->load($file);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			$jenjang = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				if ($sheetData[$i][0] != null) {
					$jenjang[] = $sheetData[$i][0];
				}
			}

			unlink($file);

			$this->import($jenjang);
		}
	}
	public function do_import()
	{
		$data = json_decode($this->input->post('jenjang', true));
		$jenjang = [];
		foreach ($data as $j) {
			$jenjang[] = ['nama_jenjang' => $j];
		}

		$save = $this->master->create('jenjang', $jenjang, true);
		if ($save) {
			redirect('jenjang');
		} else {
			redirect('jenjang/import');
		}
	}
}
