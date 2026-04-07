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

    // -------------------------------------------------------
    // COUNTS (for dashboard)
    // -------------------------------------------------------

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

    // -------------------------------------------------------
    // WRITE
    // -------------------------------------------------------

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
    public function update_profile($user_id, $role_name, $data) {
    $table = $role_name . '_profiles';
    if (!$this->db->table_exists($table)) return;

    $data['updated_at'] = date('Y-m-d H:i:s');
    $data['updated_by'] = $this->session->userdata('user_id');

    $exists = $this->db->where('user_id', $user_id)->count_all_results($table);
    if ($exists) {
        $this->db->where('user_id', $user_id)->update($table, $data);
    } else {
        $data['user_id'] = $user_id;
        $this->db->insert($table, $data);
    }
}
public function create_user() {
    $this->load->model('User_model');
    $this->load->model('Role_model');

    if ($this->input->post()) {
        $name     = trim($this->input->post('name', TRUE));
        $email    = trim($this->input->post('email', TRUE));
        $phone    = trim($this->input->post('phone', TRUE));
        $password = $this->input->post('password');
        $confirm  = $this->input->post('confirm_password');
        $role_id  = (int)$this->input->post('role_id');
        $status   = $this->input->post('status');

        $errors = [];
        if (empty($name))                                                    $errors[] = 'Name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'Valid email is required.';
        if ($this->User_model->email_exists($email))                         $errors[] = 'Email already exists.';
        if (empty($password))                                                $errors[] = 'Password is required.';
        if (strlen($password) < 6)                                           $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm)                                          $errors[] = 'Passwords do not match.';
        if (empty($role_id))                                                 $errors[] = 'Role is required.';
        if (!in_array($status, ['active', 'inactive', 'banned']))            $errors[] = 'Invalid status.';

        if (empty($errors)) {
            $role = $this->Role_model->get_by_id($role_id);

            // Create user
            $user_id = $this->User_model->create([
                'name'        => $name,
                'email'       => $email,
                'phone'       => $phone,
                'password'    => password_hash($password, PASSWORD_DEFAULT),
                'role_id'     => $role_id,
                'status'      => $status,
                'assigned_by' => $this->session->userdata('user_id'),
                'assigned_at' => date('Y-m-d H:i:s'),
            ]);

            // Handle profile fields
            $profile_fields = $this->input->post('profile');
            $profile_data   = [];

            if (!empty($profile_fields) && is_array($profile_fields)) {
                $allowed = $this->Role_model->get_profile_fields($role->name);
                $allowed_names = array_column($allowed, 'name');
                foreach ($profile_fields as $key => $val) {
                    if (in_array($key, $allowed_names)) {
                        $profile_data[$key] = trim($val);
                    }
                }
            }

            $this->User_model->update_profile($user_id, $role->name, $profile_data);

            $this->session->set_flashdata('success', 'User "' . $name . '" created successfully.');
            redirect('admin/users');
        }

        $data['errors']     = $errors;
        $data['roles']      = $this->Role_model->get_all();
        $data['page_title'] = 'Create User';
        $this->load->view('admin/users/create', $data);
        return;
    }

    $data['page_title'] = 'Create User';
    $data['roles']      = $this->Role_model->get_all();
    $this->load->view('admin/users/create', $data);
}
public function edit_user($id) {
    $this->load->model('User_model');
    $this->load->model('Role_model');

    $user = $this->User_model->get_by_id($id);
    if (!$user) {
        $this->session->set_flashdata('error', 'User not found.');
        redirect('admin/users');
    }

    $current_admin_id = $this->session->userdata('user_id');
    if ($user->role_name === 'admin' && $user->id != $current_admin_id) {
        $this->session->set_flashdata('error', 'You cannot edit other admin accounts.');
        redirect('admin/users');
    }

    if ($this->input->post()) {
        $name    = trim($this->input->post('name', TRUE));
        $phone   = trim($this->input->post('phone', TRUE));
        $role_id = (int)$this->input->post('role_id');
        $status  = $this->input->post('status');

        $errors = [];
        if (empty($name))    $errors[] = 'Name is required.';
        if (empty($role_id)) $errors[] = 'Role is required.';
        if (!in_array($status, ['active', 'inactive', 'banned'])) $errors[] = 'Invalid status.';

        if (empty($errors)) {
            $new_role     = $this->Role_model->get_by_id($role_id);
            $role_changed = ($role_id != $user->role_id);

            $this->User_model->update_basic($id, [
                'name'    => $name,
                'phone'   => $phone,
                'role_id' => $role_id,
                'status'  => $status,
            ]);

            // Handle profile fields
            $profile_fields = $this->input->post('profile');
            $profile_data   = [];

            if (!empty($profile_fields) && is_array($profile_fields)) {
                $allowed       = $this->Role_model->get_profile_fields($new_role->name);
                $allowed_names = array_column($allowed, 'name');
                foreach ($profile_fields as $key => $val) {
                    if (in_array($key, $allowed_names)) {
                        $profile_data[$key] = trim($val);
                    }
                }
            }

            $this->User_model->update_profile($id, $new_role->name, $profile_data);

            $this->session->set_flashdata('success', 'User updated successfully.');
            redirect('admin/users');
        }

        $data['errors']     = $errors;
        $data['user']       = $user;
        $data['roles']      = $this->Role_model->get_all();
        $data['page_title'] = 'Edit User';
        $this->load->view('admin/users/edit', $data);
        return;
    }

    // Load existing profile data
    $data['profile']    = $this->User_model->get_profile($user->id, $user->role_name);
    $data['role_fields']= $this->Role_model->get_profile_fields($user->role_name);
    $data['page_title'] = 'Edit User';
    $data['user']       = $user;
    $data['roles']      = $this->Role_model->get_all();
    $this->load->view('admin/users/edit', $data);
}
}