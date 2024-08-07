<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		$this->load->model('Dashboard_model', 'dashboard');
		$this->user = $this->ion_auth->user()->row();
	}

	public function admin_box()
	{
		$box = [
			[
				'box' 		=> 'light-blue',
				'total' 	=> $this->dashboard->total('jenjang'),
				'title'		=> 'Jenjang',
				'icon'		=> 'graduation-cap'
			],
			[
				'box' 		=> 'olive',
				'total' 	=> $this->dashboard->total('kelas'),
				'title'		=> 'Kelas',
				'icon'		=> 'building-o'
			],
			[
				'box' 		=> 'yellow-active',
				'total' 	=> $this->dashboard->total('guru'),
				'title'		=> 'Guru',
				'icon'		=> 'user-secret'
			],
			[
				'box' 		=> 'red',
				'total' 	=> $this->dashboard->total('siswa'),
				'title'		=> 'Siswa',
				'icon'		=> 'user'
			],
		];
		$info_box = json_decode(json_encode($box), FALSE);
		return $info_box;
	}

	public function index()
	{
		$user = $this->user;
		$data = [
			'user' 		=> $user,
			'judul'		=> 'Dashboard',
			'subjudul'	=> 'Data Aplikasi',
		];

		if ( $this->ion_auth->is_admin() ) {
			$data['info_box'] = $this->admin_box();
		} elseif ( $this->ion_auth->in_group('guru') ) {
			//$mapel = ['mapel' => 'guru.mapel_id=mapel.id_mapel'];
//			$data['guru'] = $this->dashboard->get_where('guru', 'nama_guru', $user->username, $mapel)->row();
			// yy $data['guru'] = $this->dashboard->get_where('guru', 'nama_guru', $user->username)->row();
			// yy $kelas = ['kelas' => 'kelas_guru.kelas_id=kelas.id_kelas'];
			// yy $data['kelasguru'] = $this->dashboard->get_where('kelas_guru', 'guru_id' , $data['guru']->id_guru, $kelas, ['nama_kelas'=>'ASC'])->result();
			// $data['jenjangguru'] = $this->dashboard->getKelasJenjang()->result();
			//$data['jenjang'] = $this->dashboard->getJenjangByIdKelas($data['kelas']->kelas_id)->row();
			$data['guru'] = $this->dashboard->get_where('guru', 'nama_guru', $user->username)->row();
			$join = [
				'kelas b' 	=> 'a.kelas_id = b.id_kelas',
				'jenjang c'	=> 'b.jenjang_id = c.id_jenjang',
				'guru d'	=> 'a.guru_id = d.id_guru'
			];	
			$data['kelasguru'] = $this->dashboard->get_where('kelas_guru a', 'a.guru_id', $data['guru']->id_guru, $join)->row();
			$data['kelasguru2'] = $this->dashboard->get_where('kelas_guru a', 'a.guru_id', $data['guru']->id_guru, $join)->result();
		}else{
			$join = [
				'kelas b' 	=> 'a.kelas_id = b.id_kelas',
				'jenjang c'	=> 'b.jenjang_id = c.id_jenjang'
			];
			$data['siswa'] = $this->dashboard->get_where('siswa a', 'email', $user->email, $join)->row();
		}

		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('dashboard');
		$this->load->view('_templates/dashboard/_footer.php');
	}
}