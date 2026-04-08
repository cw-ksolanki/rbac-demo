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

        $this->load->view('admin/dashboard', $data); 
    }

    public function profile() {
    $this->load->model('User_model');
    $user_id = $this->session->userdata('user_id');

    $data['page_title'] = 'My Profile';
    $data['user']       = $this->User_model->get_by_id($user_id);

    $this->load->view('admin/profile', $data);
}

public function all_admins(){
    $this->load->model('User_model');

    $type = 'admin';

    $total = $this->User_model->count_admins();
    $users = $this->User_model->get_all($type);    
    
    $data['page_title']   = 'Users';
    $data['users']        = $users;
    $data['total']        = $total;

    $this->load->view('admin/admins/index.php',$data);
}

public function update_profile() {
    $this->load->model('User_model');
    $user_id = $this->session->userdata('user_id');

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
        $description  = trim($this->input->post('description', TRUE));
       
        $errors = [];
        if (empty($name))         $errors[] = 'Role name is required.';
        if (!preg_match('/^[a-z_]+$/', $name)) $errors[] = 'Role name can only contain lowercase letters and underscores.';
        if ($this->Role_model->name_exists($name)) $errors[] = 'Role name already exists.';

        if (empty($errors)) {
            $role_id = $this->Role_model->create([
                'name'         => $name,
                'description'  => $description,
            ]);

            $this->session->set_flashdata('success', 'Role "' . $name . '" created successfully.');
            redirect('admin/roles');
        }

        $data['errors']       = $errors;
        $data['page_title']   = 'Create Role';
        $this->load->view('admin/roles/create', $data);
        return;
    }

    $data['page_title']  = 'Create Role';
    $this->load->view('admin/roles/create', $data);
}

public function edit_role($id) {
    $this->load->model('Role_model');
    $role = $this->Role_model->get_by_id($id);

    if (!$role) {
        $this->session->set_flashdata('error', 'Role not found.');
        redirect('admin/roles');
    }


    if ($this->input->post()) {
        $description  = trim($this->input->post('description', TRUE));

        $errors = [];

        if (empty($errors)) {
            $update = ['description' => $description];

                $new_name = strtolower(trim($this->input->post('name', TRUE)));
                if (empty($new_name)) $errors[] = 'Role name is required.';
                elseif (!preg_match('/^[a-z_]+$/', $new_name)) $errors[] = 'Role name: lowercase letters and underscores only.';
                elseif ($this->Role_model->name_exists($new_name, $id)) $errors[] = 'Role name already exists.';
                else $update['name'] = $new_name;

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
    $this->load->view('admin/roles/edit', $data);
}

public function delete_role($id) {
    $this->load->model('Role_model');
    $role = $this->Role_model->get_by_id($id);

    if (!$role) {
        $this->session->set_flashdata('error', 'Role not found.');
    } else {
        $this->Role_model->delete($id);
        $this->session->set_flashdata('success', 'Role "' . $role->display_name . '" deleted.');
    }

    redirect('admin/roles');
}

// -------------------------------------------------------
// USERS
// -------------------------------------------------------

public function users() {
    $this->load->model('User_model');
    $this->load->model('Role_model');

    $type = 'users';

    $total = $this->User_model->count_users();
    $users = $this->User_model->get_all($type);

    $data['page_title']   = 'Users';
    $data['users']        = $users;
    $data['total']        = $total;

    $this->load->view('admin/users/index', $data);
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

        $vehivle_no = $this->input->post('vehivle_no');
        $vehivle_type = $this->input->post('vehivle_type');
        $licence_no = $this->input->post('licence_no');
        $company = $this->input->post('company');
        $company_website = $this->input->post('company_website');

        $errors = [];
        if (empty($name))                                                 $errors[] = 'Name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))  $errors[] = 'Valid email is required.';
        if ($this->User_model->email_exists($email))                      $errors[] = 'Email already exists.';
        if (empty($password))                                             $errors[] = 'Password is required.';
        if (strlen($password) < 6)                                        $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm)                                       $errors[] = 'Passwords do not match.';
        if (empty($role_id))                                              $errors[] = 'Role is required.';
        if (!in_array($status, ['active','inactive','banned']))            $errors[] = 'Invalid status.';

        if (empty($errors)) {
            $role = $this->Role_model->get_by_id($role_id);

            $user_id = $this->User_model->create([
                'name'        => $name,
                'email'       => $email,
                'phone'       => $phone,
                'password'    => password_hash($password, PASSWORD_DEFAULT),
                'role_id'     => $role_id,
                'status'      => $status,
                'assigned_by' => $this->session->userdata('user_id'),
                'assigned_at' => date('Y-m-d H:i:s'),
                'vehicle_type'    => $vehivle_type,
                'licence_no'   => $licence_no,
                'vehicle_no' => $vehivle_no,
                'company'    => $company,
                'company_website'   => $company_website,
            ]);


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
        $vehivle_no = $this->input->post('vehivle_no');
        $vehivle_type = $this->input->post('vehivle_type');
        $licence_no = $this->input->post('licence_no');
        $company = $this->input->post('company');
        $company_website = $this->input->post('company_website');

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

            if($role_id === 3){
                $this->User_model->update_basic($id, [
                'vehicle_type'    => $vehivle_type,
                'licence_no'   => $licence_no,
                'vehicle_no' => $vehivle_no,
            ]);
            }
            if($role_id === 2){
                $this->User_model->update_basic($id, [
                'company'    => $company,
                'company_website'   => $company_website,
            ]);
            }
            if($role_id !== 1 && $role_id !==2 && $role_id !== 3){
                $this->User_model->update_basic($id, [
                'company'    => $company,
                'company_website'   => $company_website,
                'vehicle_type'    => $vehivle_type,
                'licence_no'   => $licence_no,
                'vehicle_no' => $vehivle_no,
            ]);
            }

            $this->session->set_flashdata('success', 'User updated successfully.');
            redirect('admin/users');
        }

        // On error — reload with existing profile data so AJAX fields re-populate
        $data['errors']      = $errors;
        $data['user']        = $user;
        $data['roles']       = $this->Role_model->get_all();
        $data['page_title']  = 'Edit User';
        $this->load->view('admin/users/edit', $data);
        return;
    }

    // GET — load existing profile data for pre-filling AJAX fields
    $data['page_title']  = 'Edit User';
    $data['user']        = $user;
    $data['roles']       = $this->Role_model->get_all();
    $this->load->view('admin/users/edit', $data);
}

public function view_user($id) {
    $this->load->model('User_model');

    $user = $this->User_model->get_by_id($id);
    if (!$user) {
        $this->session->set_flashdata('error', 'User not found.');
        redirect('admin/users');
    }

    $data['page_title'] = 'View User — ' . $user->name;
    $data['user']       = $user;
    $this->load->view('admin/users/view', $data);
}

public function delete_user($id) {
    $this->load->model('User_model');

    $user             = $this->User_model->get_by_id($id);
    $current_admin_id = $this->session->userdata('user_id');

    if (!$user) {
        $this->session->set_flashdata('error', 'User not found.');
    } elseif ($user->id == $current_admin_id) {
        $this->session->set_flashdata('error', 'You cannot delete your own account.');
    } elseif ($user->role_name === 'admin') {
        $this->session->set_flashdata('error', 'Cannot delete other admin accounts.');
    } else {
        $this->User_model->delete($id);
        $this->session->set_flashdata('success', 'User "' . $user->name . '" deleted.');
    }

    redirect('admin/users');
}

}