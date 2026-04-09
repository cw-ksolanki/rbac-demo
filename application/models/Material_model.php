<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Material_model extends CI_Model { 
    public function save($data){
        $this->db->insert('materials', $data);
    }
}