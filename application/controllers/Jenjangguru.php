<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JenjangGuru extends CI_Controller {

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
			'judul'	=> 'Jenjang Guru',
			'subjudul'=> 'Data Jenjang Guru'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/jenjangguru/data');
		$this->load->view('_templates/dashboard/_footer.php');
    }

    public function data()
    {
        $this->output_json($this->master->getJenjangGuru(), false);
	}

	public function getJenjangId($id)
	{
		$this->output_json($this->master->getAllJenjang($id));		
	}
	
	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Tambah Jenjang Guru',
			'subjudul'	=> 'Tambah Data Jenjang Guru',
			'guru'	=> $this->master->getGuru()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/jenjangguru/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$data = [
			'user' 			=> $this->ion_auth->user()->row(),
			'judul'			=> 'Edit Jenjang Guru',
			'subjudul'		=> 'Edit Data Jenjang Guru',
			'guru'		=> $this->master->getGuruById($id, true),
			'id_guru'		=> $id,
			'all_jenjang'	=> $this->master->getAllJenjang(),
			'jenjang'		=> $this->master->getJenjangByIdGuru($id)
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/jenjangguru/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function save()
	{
		$method = $this->input->post('method', true);
		$this->form_validation->set_rules('guru_id', 'Mata Kuliah', 'required');
		$this->form_validation->set_rules('jenjang_id[]', 'Jenjang', 'required');
	
		if($this->form_validation->run() == FALSE){
			$data = [
				'status'	=> false,
				'errors'	=> [
					'guru_id' => form_error('guru_id'),
					'jenjang_id[]' => form_error('jenjang_id[]'),
				]
			];
			$this->output_json($data);
		}else{
			$guru_id 	= $this->input->post('guru_id', true);
			$jenjang_id = $this->input->post('jenjang_id', true);
			$input = [];
			foreach ($jenjang_id as $key => $val) {
				$input[] = [
					'guru_id' 	=> $guru_id,
					'jenjang_id'  	=> $val
				];
			}
			if($method==='add'){
				$action = $this->master->create('jenjang_guru', $input, true);
			}else if($method==='edit'){
				$id = $this->input->post('guru_id', true);
				$this->master->delete('jenjang_guru', $id, 'guru_id');
				$action = $this->master->create('jenjang_guru', $input, true);
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
            if($this->master->delete('jenjang_guru', $chk, 'guru_id')){
                $this->output_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
	}
}