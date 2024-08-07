<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soal_model extends CI_Model {
    
    public function getDataSoal($id, $guru)
    {
        $this->datatables->select('a.id_soal, a.soal, FROM_UNIXTIME(a.created_on) as created_on, FROM_UNIXTIME(a.updated_on) as updated_on, b.nama_mapel, c.nama_guru');
        $this->datatables->from('tb_soal a');
        $this->datatables->join('mapel b', 'b.id_mapel=a.mapel_id');
        $this->datatables->join('guru c', 'c.id_guru=a.guru_id');
        if ($id!==null && $guru===null) {
            $this->datatables->where('a.mapel_id', $id);            
        }else if($id!==null && $guru!==null){
            $this->datatables->where('a.guru_id', $guru);
        }
        return $this->datatables->generate();
    }

    public function getAllDataSoal()
    {
        $this->datatables->select('a.id_soal, a.soal, FROM_UNIXTIME(a.created_on) as created_on, FROM_UNIXTIME(a.updated_on) as updated_on, b.nama_mapel, c.nama_guru');
        $this->datatables->from('tb_soal a');
        $this->datatables->join('mapel b', 'b.id_mapel=a.mapel_id');
        $this->datatables->join('guru c', 'c.id_guru=a.guru_id');
        return $this->datatables->generate();
    }
    public function getDataSoalByGuru($id)
    {
        $this->datatables->select('a.id_soal, a.soal, FROM_UNIXTIME(a.created_on) as created_on, FROM_UNIXTIME(a.updated_on) as updated_on, b.nama_mapel, c.nama_guru');
        $this->datatables->from('tb_soal a');
        $this->datatables->join('mapel b', 'b.id_mapel=a.mapel_id');
        $this->datatables->join('guru c', 'c.id_guru=a.guru_id');
        $this->datatables->where('a.guru_id', $guru);
        return $this->datatables->generate();
    }


    public function getSoalById($id)
    {
        return $this->db->get_where('tb_soal', ['id_soal' => $id])->row();
    }

    public function getMapelGuru($username)
    {
        $this->db->select('a.id_mapel, a.nama_mapel, b.id_guru, b.nama_guru');
        $this->db->join('mapel a', 'b.nama_guru=a.created_by');
        $this->db->from('guru b')->where('b.nama_guru', $username);
        return $this->db->get()->row();
    }
    public function getMapelByGuru($id)
    {
        $this->db->select('a.id_mapel, a.nama_mapel, d.id_guru, d.nama_guru');
        $this->db->from('mapel a');
        $this->db->join('jenjang_mapel b', 'a.id_mapel=b.mapel_id');
        $this->db->join('jenjang_guru c', 'b.jenjang_id=c.jenjang_id');
        $this->db->join('guru d','c.guru_id = d.id_guru');
        $this->db->where(['d.nama_guru' => $id]);
        return $this->db->get()->result();
    }

    public function getMapelKelasByGuru($id)
    {
        $this->db->select('a.id_mapel, a.nama_mapel, d.id_guru, d.nama_guru');
        $this->db->from('mapel a');
        $this->db->join('jenjang_mapel b', 'a.id_mapel=b.mapel_id');
        $this->db->join('kelas_guru c', 'b.kelas_id=c.kelas_id');
        $this->db->join('guru d','c.guru_id = d.id_guru');
        $this->db->where(['a.id_mapel' => $id]);
        return $this->db->get()->row();
    }
    

    public function getAllMapelByGuru()
    {
        $this->db->select('a.id_mapel, a.nama_mapel, d.id_guru, d.nama_guru');
        $this->db->from('mapel a');
        $this->db->join('jenjang_mapel b', 'a.id_mapel=b.mapel_id');
        $this->db->join('jenjang_guru c', 'b.jenjang_id=c.jenjang_id');
        $this->db->join('guru d','c.guru_id = d.id_guru');
        return $this->db->get()->result();
    }

    public function getAllGuru()
    {
        $this->db->select('*');
        $this->db->from('guru a');
        $this->db->join('mapel b', 'a.mapel_id=b.id_mapel');
        $this->db->join('mapel b', 'a.mapel_id=b.id_mapel');
        return $this->db->get()->result();
    }

    public function getIDGuruByUsername($username)
    {
        $this->db->select('a.id_guru');
        $this->db->from('guru a');
        $this->db->join('user b', 'a.email=b.email');
        $this->db->where(['a.nama_guru' => $id]);
        return $this->db->get()->result();
    }

    
}