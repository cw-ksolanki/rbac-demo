<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    public function get_all() {
        return $this->db->order_by('id', 'ASC')->get('roles')->result();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get('roles')->row();
    }

    public function count_all() {
        return $this->db->count_all('roles');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $this->session->userdata('user_id');
        $this->db->insert('roles', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $this->session->userdata('user_id');
        $this->db->where('id', $id)->update('roles', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id)->delete('roles');
    }

    public function name_exists($name, $exclude_id = NULL) {
        $this->db->where('name', $name);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return $this->db->count_all_results('roles') > 0;
    }

}