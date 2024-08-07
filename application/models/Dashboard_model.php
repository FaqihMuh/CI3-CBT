<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function total($table)
    {
        $query = $this->db->get($table)->num_rows();
        return $query;
    }

    public function get_where($table, $pk, $id, $join = null, $order = null)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($pk, $id);

        if($join !== null){
            foreach($join as $table => $field){
                $this->db->join($table, $field);
            }
        }
        
        if($order !== null){
            foreach($order as $field => $sort){
                $this->db->order_by($field, $sort);
            }
        }

        $query = $this->db->get();
        return $query;
    }
    // public function getJenjangByIdKelas($id)
    // {
    //     $this->db->select('jenjang.id_jenjang,jenjang.nama_jenjang');
    //     $this->db->from('jenjang');
    //     $this->db->join('kelas_jenjang', 'jenjang.id_jenjang=kelas_jenjang.jenjang_id');
    //     $this->db->where('kelas_jenjang.kelas_id', $id);
    //     $query = $this->db->get();
    //     return $query;
    // }
    public function getKelasJenjang()
    {
        $this->db->select('kelas_jenjang.id, kelas.nama_kelas, jenjang.id_jenjang, jenjang.nama_jenjang');
        $this->db->from('kelas_jenjang');
        $this->db->join('kelas', 'kelas_jenjang.kelas_id=kelas.id_kelas');
        $this->db->join('jenjang', 'kelas_jenjang.jenjang_id=jenjang.id_jenjang');
        $this->db->group_by('jenjang.nama_jenjang');
        $query = $this->db->get();
        return $query;
    }
}