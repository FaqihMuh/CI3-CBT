<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JenjangMapel extends CI_Controller {

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
			'judul'	=> 'Jenjang Mata Pelajaran',
			'subjudul'=> 'Data Jenjang Mata Pelajaran'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/jenjangmapel/data');
		$this->load->view('_templates/dashboard/_footer.php');
    }

    public function data()
    {
        $this->output_json($this->master->getJenjangMapel(), false);
	}

	public function getJenjangId($id)
	{
		$this->output_json($this->master->getAllJenjang($id));		
	}
	public function getKelasId($id)
	{
		$this->output_json($this->master->getKelasByJenjang($id));		
	}
	
	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Tambah Jenjang Mata Pelajaran',
			'subjudul'	=> 'Tambah Data Jenjang Mata Pelajaran',
			'mapel'	=> $this->master->getMapel()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/jenjangmapel/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$data = [
			'user' 			=> $this->ion_auth->user()->row(),
			'judul'			=> 'Edit Jenjang Mata Pelajaran',
			'subjudul'		=> 'Edit Data Jenjang Mata Pelajaran',
			'mapel'		=> $this->master->getMapelById($id, true),
			'id_mapel'		=> $id,
			'all_jenjang'	=> $this->master->getAllJenjang(),
			'jenjang'		=> $this->master->getJenjangByIdMapel($id),
			'all_kelas'	=> $this->master->getKelasByJenjang($id),
			'kelas'		=> $this->master->getKelasByIdMapel($id)
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/jenjangmapel/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function save()
	{
		$method = $this->input->post('method', true);
		$this->form_validation->set_rules('mapel_id', 'Mata Kuliah', 'required');
		$this->form_validation->set_rules('jenjang_id', 'Jenjang', 'required');
		$this->form_validation->set_rules('kelas_id', 'Kelas', 'required');
	
		if($this->form_validation->run() == FALSE){
			$data = [
				'status'	=> false,
				'errors'	=> [
					'mapel_id' => form_error('mapel_id'),
					'jenjang_id' => form_error('jenjang_id'),
					'kelas_id' => form_error('kelas_id'),
				]
			];
			$this->output_json($data);
		}else{
			$mapel_id 	= $this->input->post('mapel_id', true);
			$jenjang_id = $this->input->post('jenjang_id', true);
			$kelas_id = $this->input->post('kelas_id', true);
				$input[] = [
					'mapel_id' 	=> $mapel_id,
					'jenjang_id'  	=> $jenjang_id,
					'kelas_id'  	=> $kelas_id
				];
			
			if($method==='add'){
				$action = $this->master->create('jenjang_mapel', $input, true);
			}else if($method==='edit'){
				$id = $this->input->post('mapel_id', true);
				$this->master->delete('jenjang_mapel', $id, 'mapel_id');
				$action = $this->master->create('jenjang_mapel', $input, true);
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
            if($this->master->delete('jenjang_mapel', $chk, 'mapel_id')){
                $this->output_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
	}
}