<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    // -------------------------------------------------------
    // READ
    // -------------------------------------------------------

    public function get_all($type) {
        $this->db->select('u.*, r.name as role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id', 'left');

        if ($type === 'admin') {
            $this->db->where('u.role_id', 1);
        }else{
            $this->db->where('u.role_id !=',1);
        }

        $this->db->order_by('u.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function count_all(){
        $this->db->select('u.id');
        $this->db->from('users u');
        return $this->db->get()->num_rows();
    }

    public function count_users() {
        $this->db->select('u.id');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id', 'left');
        $this->db->where('u.role_id !=',1);

        return $this->db->get()->num_rows();
    }
    public function count_admins() {
        $this->db->select('u.id');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id', 'left');
        $this->db->where('u.role_id',1);

        return $this->db->get()->num_rows();
    }

    public function count_by_status($status){
        $this->db->select('u.id');
        $this->db->from('users u');
        $this->db->where('u.status',$status);

        return $this->db->get()->num_rows();

    }

      public function count_drivers() {
        $this->db->select('u.id');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id', 'left');
        $this->db->where('u.role_id',3);

        return $this->db->get()->num_rows();
    }


    public function get_by_id($id) {
        $this->db->select('u.*, r.name as role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id', 'left');
        $this->db->where('u.id', $id);
        return $this->db->get()->row();
    }

    public function get_profile($user_id, $role_name) {
        $table = $role_name . '_profiles';
        if ($this->db->table_exists($table)) {
            return $this->db->where('user_id', $user_id)->get($table)->row();
        }
        return null;
    }

    public function get_admin_profile($user_id) {
        return $this->db->where('user_id', $user_id)->get('admin_profiles')->row();
    }

    public function email_exists($email, $exclude_id = NULL) {
        $this->db->where('email', $email);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return $this->db->count_all_results('users') > 0;
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $this->session->userdata('user_id');
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function create_profile($user_id, $role_name) {
        $table = $role_name . '_profiles';
        if ($this->db->table_exists($table)) {
            // Check not already exists
            $exists = $this->db->where('user_id', $user_id)->count_all_results($table);
            if (!$exists) {
                $this->db->insert($table, [
                    'user_id'    => $user_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('user_id'),
                ]);
            }
        }
    }

    public function update_basic($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $this->session->userdata('user_id');
        $data['assigned_at'] = date('Y-m-d H:i:s');
        $data['assigned_by'] = $this->session->userdata('user_id');
        $this->db->where('id', $id)->update('users', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id)->delete('users');
    }
}