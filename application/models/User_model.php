<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function count_all() {
        return $this->db->count_all('users');
    }

    public function count_by_status($status) {
        return $this->db->where('status', $status)->count_all_results('users');
    }

    public function count_by_role($role_name) {
        $this->db->join('roles r', 'r.id = users.role_id', 'left');
        $this->db->where('r.name', $role_name);
        return $this->db->count_all_results('users');
    }

    public function get_recent($limit = 5) {
        $this->db->select('u.*, r.display_name as role_display_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id', 'left');
        $this->db->order_by('u.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
    public function get_by_id($id) {
    return $this->db->where('id', $id)->get('users')->row();
}

public function get_admin_profile($user_id) {
    return $this->db->where('user_id', $user_id)->get('admin_profiles')->row();
}

public function update_basic($id, $data) {
    $data['updated_at'] = date('Y-m-d H:i:s');
    $data['updated_by'] = $this->session->userdata('user_id');
    $this->db->where('id', $id)->update('users', $data);
}
}