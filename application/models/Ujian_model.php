<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian_model extends CI_Model {
    
    public function getDataUjian($id)
    {
        $this->datatables->select('a.id_ujian, a.token, a.nama_ujian, b.nama_mapel, a.jumlah_soal, CONCAT(a.tgl_mulai, " <br/> (", a.waktu, " Menit)") as waktu, a.jenis');
        $this->datatables->from('m_ujian a');
        $this->datatables->join('mapel b', 'a.mapel_id = b.id_mapel');
        if($id!==null){
            $this->datatables->where('guru_id', $id);
        }
        return $this->datatables->generate();
    }
    
    // public function getListUjian($id, $kelas)
    // {
    //     $this->datatables->select("a.id_ujian, e.nama_guru, d.nama_kelas, a.nama_ujian, b.nama_mapel, a.jumlah_soal, CONCAT(a.tgl_mulai, ' <br/> (', a.waktu, ' Menit)') as waktu,  (SELECT COUNT(id) FROM h_ujian h WHERE h.siswa_id = {$id} AND h.ujian_id = a.id_ujian) AS ada");
    //     $this->datatables->from('m_ujian a');
    //     $this->datatables->join('mapel b', 'a.mapel_id = b.id_mapel');
    //     $this->datatables->join('kelas_guru c', "a.guru_id = c.guru_id");
    //     $this->datatables->join('kelas d', 'c.kelas_id = d.id_kelas');
    //     $this->datatables->join('guru e', 'a.guru_id = e.id_guru');
    //     $this->datatables->where('d.id_kelas', $kelas);
    //     return $this->datatables->generate();
    // }

    public function getListUjian($id, $kelas)
    {
        $this->datatables->select("a.id_ujian, e.nama_guru, d.nama_kelas, a.nama_ujian, b.nama_mapel, a.jumlah_soal, CONCAT(a.tgl_mulai, ' <br/> (', a.waktu, ' Menit)') as waktu,  (SELECT COUNT(id) FROM h_ujian h WHERE h.siswa_id = {$id} AND h.ujian_id = a.id_ujian) AS ada");
        $this->datatables->from('m_ujian a');
        $this->datatables->join('mapel b', 'a.mapel_id = b.id_mapel');
        $this->datatables->join('kelas_guru c', "a.guru_id = c.guru_id");
        $this->datatables->join('kelas d', 'c.kelas_id = d.id_kelas');
        $this->datatables->join('guru e', 'a.guru_id = e.id_guru');
        $this->datatables->join('jenjang_mapel f', 'b.id_mapel = f.mapel_id');
        $this->datatables->join('siswa g', 'd.id_kelas = g.kelas_id');
        $this->datatables->where('g.kelas_id', $kelas);
        return $this->datatables->generate();
    }

    public function getUjianById($id)
    {
        $this->db->select('*');
        $this->db->from('m_ujian a');
        $this->db->join('guru b', 'a.guru_id=b.id_guru');
        $this->db->join('mapel c', 'a.mapel_id=c.id_mapel');
        $this->db->where('id_ujian', $id);
        return $this->db->get()->row();
    }
    public function getUjianByIdv2($id)
    {
        $this->db->select('a.id_ujian,a.guru_id,a.mapel_id,a.jumlah_soal,a.waktu,a.jenis,a.tgl_mulai,a.terlambat,a.token,b.nama_guru,c.nama_mapel');
        $this->db->from('m_ujian a');
        $this->db->join('guru b', 'a.guru_id=b.id_guru');
        $this->db->join('mapel c', 'a.mapel_id=c.id_mapel');
        $this->db->where('id_ujian', $id);
        return $this->db->get()->row();
    }

    public function getIdGuru($username)
    {
        $this->db->select('id_guru, nama_guru')->from('guru')->where('nama_guru', $username);
        return $this->db->get()->row();
    }

    public function getJumlahSoal($guru)
    {
        $this->db->select('COUNT(id_soal) as jml_soal');
        $this->db->from('tb_soal');
        $this->db->where('guru_id', $guru);
        return $this->db->get()->row();
    }

    public function getIdSiswa($nis)
    {
        $this->db->select('*');
        $this->db->from('siswa a');
        $this->db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->db->join('jenjang c', 'b.jenjang_id=c.id_jenjang');
        $this->db->where('a.nis', $nis);
        return $this->db->get()->row();
    }

    public function HslUjian($id, $mhs)
    {
        $this->db->select('*, UNIX_TIMESTAMP(tgl_selesai) as waktu_habis');
        $this->db->from('h_ujian');
        $this->db->where('ujian_id', $id);
        $this->db->where('siswa_id', $mhs);
        return $this->db->get();
    }

    public function getSoal($id)
    {
        $ujian = $this->getUjianByIdv2($id);
        $order = $ujian->jenis==="acak" ? 'rand()' : 'id_soal';

        $this->db->select('id_soal, soal, file, tipe_file, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban');
        $this->db->from('tb_soal');
        $this->db->where('guru_id', $ujian->guru_id);
        $this->db->where('mapel_id', $ujian->mapel_id);
        $this->db->order_by($order);
        $this->db->limit($ujian->jumlah_soal);
        return $this->db->get()->result();
    }

    public function ambilSoal($pc_urut_soal1, $pc_urut_soal_arr)
    {
        $this->db->select("*, {$pc_urut_soal1} AS jawaban");
        $this->db->from('tb_soal');
        $this->db->where('id_soal', $pc_urut_soal_arr);
        return $this->db->get()->row();
    }

    public function getJawaban($id_tes)
    {
        $this->db->select('list_jawaban');
        $this->db->from('h_ujian');
        $this->db->where('id', $id_tes);
        return $this->db->get()->row()->list_jawaban;
    }

    public function getHasilUjian($nip = null)
    {
        $this->datatables->select('b.id_ujian, b.nama_ujian, b.jumlah_soal, CONCAT(b.waktu, " Menit") as waktu, b.tgl_mulai');
        $this->datatables->select('c.nama_mapel, d.nama_guru');
        $this->datatables->from('h_ujian a');
        $this->datatables->join('m_ujian b', 'a.ujian_id = b.id_ujian');
        $this->datatables->join('mapel c', 'b.mapel_id = c.id_mapel');
        $this->datatables->join('guru d', 'b.guru_id = d.id_guru');
        $this->datatables->group_by('b.id_ujian');
        if($nip !== null){
            $this->datatables->where('d.nip', $nip);
        }
        return $this->datatables->generate();
    }

    public function HslUjianById($id, $dt=false)
    {
        if($dt===false){
            $db = "db";
            $get = "get";
        }else{
            $db = "datatables";
            $get = "generate";
        }
        
        $this->$db->select('d.id, a.nama, b.nama_kelas, c.nama_jenjang, d.jml_benar, d.nilai');
        $this->$db->from('siswa a');
        $this->$db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->$db->join('jenjang c', 'b.jenjang_id=c.id_jenjang');
        $this->$db->join('h_ujian d', 'a.id_siswa=d.siswa_id');
        $this->$db->where(['d.ujian_id' => $id]);
        return $this->$db->$get();
    }

    public function bandingNilai($id)
    {
        $this->db->select_min('nilai', 'min_nilai');
        $this->db->select_max('nilai', 'max_nilai');
        $this->db->select_avg('FORMAT(FLOOR(nilai),0)', 'avg_nilai');
        $this->db->where('ujian_id', $id);
        return $this->db->get('h_ujian')->row();
    }

    public function getMapelByGuruv2($id)
    {
        $this->db->select('a.mapel_id, b.nama_mapel, a.id_guru, a.nama_guru');
        $this->db->from('guru a');
        $this->db->join('mapel b','a.mapel_id = b.id_mapel');
        $this->db->where(['a.nama_guru' => $id]);
        return $this->db->get();
    }
    public function getMapelByGuruv3($id)
    {
        $this->db->select('a.id_mapel, a.nama_mapel, d.id_guru, d.nama_guru');
        $this->db->from('mapel a');
        $this->db->join('jenjang_mapel b', 'a.id_mapel=b.mapel_id');
        $this->db->join('jenjang_guru c', 'b.jenjang_id=c.jenjang_id');
        $this->db->join('guru d','c.guru_id = d.id_guru');
        $this->db->where(['d.nama_guru' => $id]);
        return $this->db->get()->result();
    }
    public function getUpdateMapelToUjian($id,$data)
    {
        $this->db->set('mapel_id', $data, FALSE);
        $this->db->where('guru_id', $id);
        $this->db->update('m_ujian'); 
    }

}