<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends CI_Model
{
    public function __construct()
    {
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function create($table, $data, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->insert($table, $data);
        } else {
            $insert = $this->db->insert_batch($table, $data);
        }
        return $insert;
    }

    public function createv2($table, $data, $pk, $id = null, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->insert($table, $data, array($pk => $id));
        } else {
            $insert = $this->db->insert_batch($table, $data, $pk);
        }
        return $insert;
    }

    public function update($table, $data, $pk, $id = null, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->update($table, $data, array($pk => $id));
        } else {
            $insert = $this->db->update_batch($table, $data, $pk);
        }
        return $insert;
    }

    public function delete($table, $data, $pk)
    {
        $this->db->where_in($pk, $data);
        return $this->db->delete($table);
    }

    /**
     * Data Kelas
     */

    public function getDataKelas()
    {
        $this->datatables->select('id_kelas, nama_kelas, id_jenjang, nama_jenjang');
        $this->datatables->from('kelas');
        $this->datatables->join('jenjang', 'jenjang_id=id_jenjang');
        $this->datatables->add_column('bulk_select', '<div class="text-center"><input type="checkbox" class="check" name="checked[]" value="$1"/></div>', 'id_kelas, nama_kelas, id_jenjang, nama_jenjang');
        return $this->datatables->generate();
    }

    public function getKelasById($id)
    {
        $this->db->where_in('id_kelas', $id);
        $this->db->order_by('nama_kelas');
        $query = $this->db->get('kelas')->result();
        return $query;
    }
    // public function getJenjangMapelById($id)
    // {
    //     return $this->db->get_where('jenjang_mapel', ['id' => $id])->row();
    // }

    /**
     * Data Jenjang
     */

    public function getDataJenjang()
    {
        $this->datatables->select('id_jenjang, nama_jenjang');
        $this->datatables->from('jenjang');
        $this->datatables->add_column('bulk_select', '<div class="text-center"><input type="checkbox" class="check" name="checked[]" value="$1"/></div>', 'id_jenjang, nama_jenjang');
        return $this->datatables->generate();
    }

    public function getJenjangById($id)
    {
        $this->db->where_in('id_jenjang', $id);
        $this->db->order_by('nama_jenjang');
        $query = $this->db->get('jenjang')->result();
        return $query;
    }

    /**
     * Data Siswa
     */

    public function getDataSiswa()
    {
        $this->datatables->select('a.id_siswa, a.nama, a.nis, a.email, b.nama_kelas, c.nama_jenjang');
        $this->datatables->select('(SELECT COUNT(id) FROM users WHERE username = a.nis) AS ada');
        $this->datatables->from('siswa a');
        $this->datatables->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->datatables->join('jenjang c', 'b.jenjang_id=c.id_jenjang');
        return $this->datatables->generate();
    }

    public function getSiswaById($id)
    {
        $this->db->select('*');
        $this->db->from('siswa');
        $this->db->join('kelas', 'kelas_id=id_kelas');
        $this->db->join('jenjang', 'jenjang_id=id_jenjang');
        $this->db->where(['id_siswa' => $id]);
        return $this->db->get()->row();
    }
    public function getJenjangMapelById($id)
    {
        $this->db->select('jenjang_mapel.jenjang_id,jenjang_mapel.kelas_id,kelas.nama_kelas,jenjang.nama_jenjang');
        $this->db->from('jenjang_mapel');
        $this->db->join('kelas', 'jenjang_mapel.kelas_id=kelas.id_kelas');
        $this->db->join('jenjang', 'jenjang_mapel.jenjang_id=jenjang.id_jenjang');
        $this->db->where(['jenjang_mapel.id' =>$id]);
        return $this->db->get()->row();
    }

    public function getJenjang()
    {
        $this->db->select('id_jenjang, nama_jenjang');
        $this->db->from('kelas');
        $this->db->join('jenjang', 'jenjang_id=id_jenjang');
        $this->db->order_by('nama_jenjang', 'ASC');
        $this->db->group_by('id_jenjang');
        $query = $this->db->get();
        return $query->result();
    }

    public function getAllJenjang($id = null)
    {
        if ($id === null) {
            $this->db->order_by('nama_jenjang', 'ASC');
            return $this->db->get('jenjang')->result();
        } else {
            $this->db->select('jenjang_id');
            $this->db->from('jenjang_mapel');
            $this->db->where('mapel_id', $id);
            $jenjang = $this->db->get()->result();
            $id_jenjang = [];
            foreach ($jenjang as $j) {
                $id_jenjang[] = $j->jenjang_id;
            }
            if ($id_jenjang === []) {
                $id_jenjang = null;
            }
            
            $this->db->select('*');
            $this->db->from('jenjang');
            $this->db->where_not_in('id_jenjang', $id_jenjang);
            $mapel = $this->db->get()->result();
            return $mapel;
        }
    }

    public function getKelasByJenjang($id)
    {
        $query = $this->db->get_where('kelas', array('jenjang_id'=>$id));
        return $query->result();
    }

    public function getKelasByJenjangv2($id)
    {
        $this->db->select('kelas.id_kelas');
        $this->db->from('kelas_jenjang');
        $this->db->join('kelas', 'kelas_jenjang.kelas_id=kelas.id_kelas');
        $this->db->where('kelas_jenjang.jenjang_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }

    

    /**
     * Data Guru
     */

    public function getDataGuru()
    {
        $this->datatables->select('guru.id_guru,guru.nip, guru.nama_guru, guru.email, guru.telepon, (SELECT COUNT(id) FROM users WHERE users.username = guru.email OR users.email = guru.email) AS ada');
        $this->datatables->from('guru');
        return $this->datatables->generate();
    }
    // public function getDataGuru()
    // {
    //     $this->datatables->select('a.id_guru,a.nip, a.nama_guru, a.email, a.mapel_id, b.nama_mapel, (SELECT COUNT(id) FROM users WHERE username = a.nip OR email = a.email) AS ada');
    //     $this->datatables->from('guru a');
    //     $this->datatables->join('mapel b', 'a.mapel_id=b.id_mapel');
    //     return $this->datatables->generate();
    // }

    public function getGuruById($id)
    {
        $query = $this->db->get_where('guru', array('id_guru'=>$id));
        return $query->row();
    }
    public function getJenjangByIdv2($id)
    {
        $query = $this->db->get_where('jenjang', array('id_jenjang'=>$id));
        return $query->row();
    }

    /**
     * Data Mapel
     */

    public function getDataMapel()
    {
        $this->datatables->select('id_mapel, nama_mapel');
        $this->datatables->from('mapel');
        return $this->datatables->generate();
    }

    public function getDataMapelById($id)
    {
        $this->datatables->select('id_mapel, nama_mapel');
        $this->datatables->from('mapel');
        $this->datatables->where('created_by', $id);
        return $this->datatables->generate();
    }

    public function getAllMapel()
    {
        return $this->db->get('mapel')->result();
    }

    public function getMapelById($id, $single = false)
    {
        if ($single === false) {
            $this->db->where_in('id_mapel', $id);
            $this->db->order_by('nama_mapel');
            $query = $this->db->get('mapel')->result();
        } else {
            $query = $this->db->get_where('mapel', array('id_mapel'=>$id))->row();
        }
        return $query;
    }

    /**
     * Data Kelas Guru
     */

    public function getKelasGuru()
    {
        $this->datatables->select('kelas_guru.id, guru.id_guru, guru.nip, guru.nama_guru, GROUP_CONCAT(kelas.nama_kelas) as kelas');
        $this->datatables->from('kelas_guru');
        $this->datatables->join('kelas', 'kelas_id=id_kelas');
        $this->datatables->join('guru', 'guru_id=id_guru');
        $this->datatables->group_by('guru.nama_guru');
        return $this->datatables->generate();
    }
    public function getKelasJenjang()
    {
        $this->datatables->select('kelas_jenjang.id, jenjang.id_jenjang, jenjang.nama_jenjang, GROUP_CONCAT(kelas.nama_kelas) as kelas');
        $this->datatables->from('kelas_jenjang');
        $this->datatables->join('kelas', 'kelas_jenjang.kelas_id=kelas.id_kelas');
        $this->datatables->join('jenjang', 'kelas_jenjang.jenjang_id=jenjang.id_jenjang');
        $this->datatables->group_by('jenjang.nama_jenjang');
        return $this->datatables->generate();
    }

    public function getAllGuru($id = null)
    {
        $this->db->select('guru_id');
        $this->db->from('kelas_guru');
        if ($id !== null) {
            $this->db->where_not_in('guru_id', [$id]);
        }
        $guru = $this->db->get()->result();
        $id_guru = [];
        foreach ($guru as $d) {
            $id_guru[] = $d->guru_id;
        }
        if ($id_guru === []) {
            $id_guru = null;
        }

        $this->db->select('id_guru, nip, nama_guru');
        $this->db->from('guru');
        $this->db->where_not_in('id_guru', $id_guru);
        return $this->db->get()->result();
    }

    
    public function getAllKelas()
    {
        $this->db->select('id_kelas, nama_kelas, nama_jenjang');
        $this->db->from('kelas');
        $this->db->join('jenjang', 'jenjang_id=id_jenjang');
        $this->db->order_by('nama_kelas');
        return $this->db->get()->result();
    }

    public function getAllKelasByJenjang($id)
    {
        $this->db->select('kelas.id_kelas, kelas.nama_kelas, jenjang.nama_jenjang');
        $this->db->from('jenjang_guru');
        $this->db->join('jenjang', 'jenjang_guru.jenjang_id=jenjang.id_jenjang');
        $this->db->join('kelas', 'jenjang_guru.jenjang_id=kelas.jenjang_id');
        $this->db->where('jenjang_guru.guru_id', $id);
        return $this->db->get()->result();
    }
    
    
    
    public function getKelasByGuru($id)
    {
        $this->db->select('kelas.id_kelas');
        $this->db->from('kelas_guru');
        $this->db->join('kelas', 'kelas_guru.kelas_id=kelas.id_kelas');
        $this->db->where('guru_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }
    /**
     * Data Jenjang Mapel
     */

    public function getJenjangMapel()
    {
        $this->datatables->select('jenjang_mapel.id, mapel.id_mapel, mapel.nama_mapel, jenjang.id_jenjang,jenjang.nama_jenjang,kelas.nama_kelas');
        $this->datatables->from('jenjang_mapel');
        $this->datatables->join('mapel', 'mapel_id=id_mapel');
        $this->datatables->join('jenjang', 'jenjang_id=id_jenjang');
        $this->datatables->join('kelas', 'kelas_id=id_kelas');
        //$this->datatables->group_by('mapel.nama_mapel');
        return $this->datatables->generate();
    }
    // public function getJenjangMapel()
    // {
    //     $this->datatables->select('jenjang_mapel.id, mapel.id_mapel, mapel.nama_mapel, jenjang.id_jenjang, GROUP_CONCAT(jenjang.nama_jenjang) as nama_jenjang');
    //     $this->datatables->from('jenjang_mapel');
    //     $this->datatables->join('mapel', 'mapel_id=id_mapel');
    //     $this->datatables->join('jenjang', 'jenjang_id=id_jenjang');
    //     $this->datatables->group_by('mapel.nama_mapel');
    //     return $this->datatables->generate();
    // }
    public function getJenjangGuru()
    {
        $this->datatables->select('jenjang_guru.id, guru.id_guru, guru.nama_guru, jenjang.id_jenjang, GROUP_CONCAT(jenjang.nama_jenjang) as nama_jenjang');
        $this->datatables->from('jenjang_guru');
        $this->datatables->join('guru', 'jenjang_guru.guru_id=guru.id_guru');
        $this->datatables->join('jenjang', 'jenjang_guru.jenjang_id=jenjang.id_jenjang');
        $this->datatables->group_by('guru.nama_guru');
        return $this->datatables->generate();
    }

    public function getMapel($id = null)
    {
        $this->db->select('mapel_id');
        $this->db->from('jenjang_mapel');
        if ($id !== null) {
            $this->db->where_not_in('mapel_id', [$id]);
        }
        $mapel = $this->db->get()->result();
        $id_mapel = [];
        foreach ($mapel as $d) {
            $id_mapel[] = $d->mapel_id;
        }
        if ($id_mapel === []) {
            $id_mapel = null;
        }

        $this->db->select('id_mapel, nama_mapel');
        $this->db->from('mapel');
        $this->db->where_not_in('id_mapel', $id_mapel);
        return $this->db->get()->result();
    }
    
    public function getGuru($id = null)
    {
        $this->db->select('guru_id');
        $this->db->from('jenjang_guru');
        if ($id !== null) {
            $this->db->where_not_in('guru_id', [$id]);
        }
        $guru = $this->db->get()->result();
        $id_guru = [];
        foreach ($guru as $d) {
            $id_guru[] = $d->guru_id;
        }
        if ($id_guru === []) {
            $id_guru = null;
        }

        $this->db->select('id_guru, nama_guru');
        $this->db->from('guru');
        $this->db->where_not_in('id_guru', $id_guru);
        return $this->db->get()->result();
    }

    public function getJenjangByIdMapel($id)
    {
        $this->db->select('jenjang.id_jenjang, jenjang.nama_jenjang');
        $this->db->from('jenjang_mapel');
        $this->db->join('jenjang', 'jenjang_mapel.jenjang_id=jenjang.id_jenjang');
        $this->db->where('mapel_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }
    public function getKelasByIdMapel($id)
    {
        $this->db->select('kelas.id_kelas');
        $this->db->from('jenjang_mapel');
        $this->db->join('kelas', 'jenjang_mapel.kelas_id=kelas.id_kelas');
        $this->db->where('mapel_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }
    public function getJenjangMapelByIdMapel($id)
    {
        $this->db->select('kelas.id_kelas, kelas.nama_kelas,jenjang.id_jenjang, jenjang.nama_jenjang');
        $this->db->from('jenjang_mapel');
        $this->db->join('kelas', 'jenjang_mapel.kelas_id=kelas.id_kelas');
        $this->db->join('jenjang', 'jenjang_mapel.jenjang_id=jenjang.id_jenjang');
        $this->db->where('mapel_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }
    public function getJenjangByIdGuru($id)
    {
        $this->db->select('jenjang.id_jenjang');
        $this->db->from('jenjang_guru');
        $this->db->join('jenjang', 'jenjang_guru.jenjang_id=jenjang.id_jenjang');
        $this->db->where('guru_id', $id);
        $query = $this->db->get()->result();
        return $query;
    }

    public function getAllMapelByGuru($id)
    {
        $this->datatables->select('a.id_mapel, a.nama_mapel');
        $this->datatables->from('mapel a');
        $this->datatables->join('jenjang_mapel b', 'a.id_mapel=b.mapel_id');
        $this->datatables->join('jenjang_guru c', 'b.jenjang_id=c.jenjang_id');
        $this->datatables->join('guru d','c.guru_id = d.id_guru');
        $this->datatables->join('jenjang e','c.jenjang_id = e.id_jenjang');
        $this->datatables->where('d.nama_guru', $id);
        return $this->datatables->generate();
    }
    

//     function add_ajax_kel($id_guru,$id_kel=NULL){
//         $this->db->select('nama_kelas');
//         $this->db->from('kelas');
//         $this->db->join('kelas_jenjang', 'kelas.id_kelas=kelas_jenjang.kelas_id');
//         $this->db->join('jenjang_guru', 'kelas_jenjang.jenjang_id=jenjang_guru.jenjang_id');
//         $this->db->where('guru_id', $id_guru);
//         $query = $this->db->get()->result();
//         return $query;
//    }
}
