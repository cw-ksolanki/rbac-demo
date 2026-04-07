<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function get_user_by_email($email) {
        $this->db->select('u.*, r.name as role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id', 'left');
        $this->db->where('u.email', $email);
        $this->db->where('u.status !=', 'banned');
        $query = $this->db->get();
        return $query->row();
    }

    public function update_last_login($user_id) {
            $this->db->where('id', $user_id);
            $this->db->update('users', [
                'last_login' => date('Y-m-d H:i:s')
            ]);
    }
}