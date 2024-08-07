<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KelasGuru extends CI_Controller {

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

	public function add_ajax_kel($id_guru){
        $db = db_connect();
        $this->db->select('nama_kelas');
        $this->db->from('kelas');
        $this->db->join('kelas_jenjang', 'kelas.id_kelas=kelas_jenjang.kelas_id');
        $this->db->join('jenjang_guru', 'kelas_jenjang.jenjang_id=jenjang_guru.jenjang_id');
        $this->db->where('guru_id', $id_guru);
        $query = $this->db->get()->result();
        $data = "<option value=''>- Select Kelas -</option>";
        foreach ($query->getResult() as $value) {
            if($id_kel==$value->id){$s='selected';}else{$s='';}
            $data .= "<option value='".$value->id."' ".$s.">".$value->nama."</option>";
        }
        $this->output->$data;
    }
}