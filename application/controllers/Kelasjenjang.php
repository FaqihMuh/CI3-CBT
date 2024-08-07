<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KelasJenjang extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}else if (!$this->ion_auth->is_admin()){
			show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="'.base_url('dashboard').'">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']);// Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->form_validation->set_error_delimiters('','');
	}

	public function output_json($data, $encode = true)
	{
        if($encode) $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function index()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Kelas Jenjang',
			'subjudul'=> 'Data Kelas Jenjang'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/kelasjenjang/data');
		$this->load->view('_templates/dashboard/_footer.php');
    }

    public function data()
    {
        $this->output_json($this->master->getKelasJenjang(), false);
	}
	
	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Tambah Kelas Jenjang',
			'subjudul'	=> 'Tambah Data Kelas Jenjang',
			'jenjang'		=> $this->master->getAllJenjang(),
			'kelas'	    => $this->master->getAllKelas()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/kelasjenjang/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$data = [
			'user' 			=> $this->ion_auth->user()->row(),
			'judul'			=> 'Edit Kelas Jenjang',
			'subjudul'		=> 'Edit Data Kelas Jenjang',
			'jenjang'			=> $this->master->getJenjangByIdv2($id),
			'id_jenjang'		=> $id,
			'all_kelas'	    => $this->master->getAllKelas(),
			'kelas'		    => $this->master->getKelasByJenjangv2($id)
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/kelasjenjang/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function save()
	{
		$method = $this->input->post('method', true);
		$this->form_validation->set_rules('jenjang_id', 'Jenjang', 'required');
		$this->form_validation->set_rules('kelas_id[]', 'Kelas', 'required');
	
		if($this->form_validation->run() == FALSE){
			$data = [
				'status'	=> false,
				'errors'	=> [
					'jenjang_id' => form_error('jenjang_id'),
					'kelas_id[]' => form_error('kelas_id[]'),
				]
			];
			$this->output_json($data);
		}else{
			$jenjang_id = $this->input->post('jenjang_id', true);
			$kelas_id = $this->input->post('kelas_id', true);
			$input = [];
			foreach ($kelas_id as $key => $val) {
				$input[] = [
					'jenjang_id'  => $jenjang_id,
					'kelas_id' => $val
				];
			}
			if($method==='add'){
				$action = $this->master->create('kelas_jenjang', $input, true);
			}else if($method==='edit'){
				$id = $this->input->post('jenjang_id', true);
				$this->master->delete('kelas_jenjang', $id, 'jenjang_id');
				$action = $this->master->create('kelas_jenjang', $input, true);
			}
			$data['status'] = $action ? TRUE : FALSE ;
		}
		$this->output_json($data);
	}

	public function delete()
    {
        $chk = $this->input->post('checked', true);
        if(!$chk){
            $this->output_json(['status'=>false]);
        }else{
            if($this->master->delete('kelas_jenjang', $chk, 'jenjang_id')){
                $this->output_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
	}
}