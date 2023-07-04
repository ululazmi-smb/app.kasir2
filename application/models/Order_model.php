<?php

class Order_model extends CI_Model
{
    public function CreateCode(){
        $this->db->select('RIGHT(pesanan.kode_pesanan,5) as kode_pesanan', FALSE);
        $this->db->order_by('kode_pesanan','DESC');    
        $this->db->limit(1);    
        $query = $this->db->get('pesanan');
            if($query->num_rows() <> 0){      
                $data = $query->row();
                $kode = intval($data->kode_pesanan) + 1; 
            }
            else{      
                $kode = 1;  
            }
        $batas = str_pad($kode, 5, "0", STR_PAD_LEFT);    
        $kodetampil = "BR".$batas;
        return $kodetampil; 
    }
}