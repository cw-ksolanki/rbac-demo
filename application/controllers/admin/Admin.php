<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('auth_check');
        $this->auth_check->require_role('admin');
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation']);
    }

    public function dashboard() {
        $this->load->model('User_model');
        $this->load->model('Role_model');

        $data['page_title']    = 'Dashboard';
        $data['total_users']   = $this->User_model->count_all();
        $data['active_users']  = $this->User_model->count_by_status('active');
        $data['total_drivers'] = $this->User_model->count_by_role('driver');
        $data['total_roles']   = $this->Role_model->count_all();
        $data['recent_users']  = $this->User_model->get_recent(5);

        $this->load->view('admin/dashboard', $data); 
    }

    public function profile() {
    $this->load->model('User_model');
    $user_id = $this->session->userdata('user_id');

    $data['page_title'] = 'My Profile';
    $data['user']       = $this->User_model->get_by_id($user_id);
    $data['profile']    = $this->User_model->get_admin_profile($user_id);

    $this->load->view('admin/profile', $data);
}

public function update_profile() {
    $this->load->model('User_model');
    $user_id = $this->session->userdata('user_id');

    $type = $this->input->post('type'); // 'info' or 'password'

    if ($type === 'info') {
        $name  = trim($this->input->post('name', TRUE));
        $phone = trim($this->input->post('phone', TRUE));

        $errors = [];
        if (empty($name)) $errors[] = 'Name is required.';

        if (empty($errors)) {
            $this->User_model->update_basic($user_id, [
                'name'  => $name,
                'phone' => $phone,
            ]);
            // Update session name
            $this->session->set_userdata('user_name', $name);
            $this->session->set_flashdata('success', 'Profile updated successfully.');
        } else {
            $this->session->set_flashdata('error', implode(' ', $errors));
        }

    } elseif ($type === 'password') {
        $current     = $this->input->post('current_password');
        $new_pass    = $this->input->post('new_password');
        $confirm     = $this->input->post('confirm_password');

        $errors = [];
        $user   = $this->User_model->get_by_id($user_id);

        if (!password_verify($current, $user->password))  $errors[] = 'Current password is incorrect.';
        if (strlen($new_pass) < 6)                         $errors[] = 'New password must be at least 6 characters.';
        if ($new_pass !== $confirm)                        $errors[] = 'Passwords do not match.';

        if (empty($errors)) {
            $this->User_model->update_basic($user_id, [
                'password' => password_hash($new_pass, PASSWORD_DEFAULT),
            ]);
            $this->session->set_flashdata('success', 'Password changed successfully.');
        } else {
            $this->session->set_flashdata('error', implode(' ', $errors));
        }
    }

    redirect('admin/profile');
}
    // -------------------------------------------------------
// ROLES
// -------------------------------------------------------

public function roles() {
    $this->load->model('Role_model');
    $data['page_title'] = 'Roles';
    $data['roles']      = $this->Role_model->get_all();
    $this->load->view('admin/roles/index', $data);
}

public function create_role() {
    $this->load->model('Role_model');

    if ($this->input->post()) {
        $name         = strtolower(trim($this->input->post('name', TRUE)));
        $display_name = trim($this->input->post('display_name', TRUE));
        $description  = trim($this->input->post('description', TRUE));
        $field_names  = $this->input->post('field_name');  // array
        $field_types  = $this->input->post('field_type');  // array

        // Validate
        $errors = [];
        if (empty($name))         $errors[] = 'Role name is required.';
        if (empty($display_name)) $errors[] = 'Display name is required.';
        if (!preg_match('/^[a-z_]+$/', $name)) $errors[] = 'Role name can only contain lowercase letters and underscores.';
        if ($this->Role_model->name_exists($name)) $errors[] = 'Role name already exists.';

        // Validate custom fields
        $custom_fields = [];
        if (!empty($field_names)) {
            foreach ($field_names as $i => $fname) {
                $fname = trim($fname);
                $ftype = isset($field_types[$i]) ? trim($field_types[$i]) : '';
                if (!empty($fname) && !empty($ftype)) {
                    $custom_fields[] = ['name' => $fname, 'type' => $ftype];
                }
            }
        }

        if (empty($errors)) {
            $role_id = $this->Role_model->create([
                'name'         => $name,
                'display_name' => $display_name,
                'description'  => $description,
            ]);

            // Create the profile table
            $this->Role_model->create_profile_table($name, $custom_fields);

            $this->session->set_flashdata('success', 'Role "' . $display_name . '" created successfully.');
            redirect('admin/roles');
        }

        $data['errors']       = $errors;
        $data['field_types']  = Role_model::allowed_field_types();
        $data['page_title']   = 'Create Role';
        $this->load->view('admin/roles/create', $data);
        return;
    }

    $data['page_title']  = 'Create Role';
    $data['field_types'] = Role_model::allowed_field_types();
    $this->load->view('admin/roles/create', $data);
}

public function edit_role($id) {
    $this->load->model('Role_model');
    $role = $this->Role_model->get_by_id($id);

    if (!$role) {
        $this->session->set_flashdata('error', 'Role not found.');
        redirect('admin/roles');
    }

    // Protect built-in roles from name change
    $protected = ['admin', 'driver', 'user'];

    if ($this->input->post()) {
        $display_name = trim($this->input->post('display_name', TRUE));
        $description  = trim($this->input->post('description', TRUE));

        $errors = [];
        if (empty($display_name)) $errors[] = 'Display name is required.';

        if (empty($errors)) {
            $update = ['display_name' => $display_name, 'description' => $description];

            // Only allow name edit if not a protected role
            if (!in_array($role->name, $protected)) {
                $new_name = strtolower(trim($this->input->post('name', TRUE)));
                if (empty($new_name)) $errors[] = 'Role name is required.';
                elseif (!preg_match('/^[a-z_]+$/', $new_name)) $errors[] = 'Role name: lowercase letters and underscores only.';
                elseif ($this->Role_model->name_exists($new_name, $id)) $errors[] = 'Role name already exists.';
                else $update['name'] = $new_name;
            }

            if (empty($errors)) {
                $this->Role_model->update($id, $update);
                $this->session->set_flashdata('success', 'Role updated successfully.');
                redirect('admin/roles');
            }
        }

        $data['errors'] = $errors;
    }

    $data['page_title'] = 'Edit Role';
    $data['role']       = $role;
    $data['protected']  = $protected;
    $this->load->view('admin/roles/edit', $data);
}

public function delete_role($id) {
    $this->load->model('Role_model');
    $role = $this->Role_model->get_by_id($id);

    $protected = ['admin', 'driver', 'user'];

    if (!$role) {
        $this->session->set_flashdata('error', 'Role not found.');
    } elseif (in_array($role->name, $protected)) {
        $this->session->set_flashdata('error', 'Cannot delete built-in role "' . $role->name . '".');
    } else {
        $this->Role_model->drop_profile_table($role->name);
        $this->Role_model->delete($id);
        $this->session->set_flashdata('success', 'Role "' . $role->display_name . '" deleted.');
    }

    redirect('admin/roles');
}

}