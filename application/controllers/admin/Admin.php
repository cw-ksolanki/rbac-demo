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

// -------------------------------------------------------
// USERS
// -------------------------------------------------------

public function users() {
    $this->load->model('User_model');
    $this->load->model('Role_model');
    $this->load->library('pagination');

    $per_page = 10;
    $page     = (int)($this->input->get('page') ?? 1);
    if ($page < 1) $page = 1;
    $offset   = ($page - 1) * $per_page;

    $filters = [
        'search'  => $this->input->get('search', TRUE),
        'role_id' => $this->input->get('role_id', TRUE),
        'status'  => $this->input->get('status', TRUE),
    ];

    $total = $this->User_model->count_filtered($filters);
    $users = $this->User_model->get_all($filters, $per_page, $offset);

    $config['base_url']                 = site_url('admin/users') . '?' . http_build_query(array_filter($filters)) . '&';
    $config['total_rows']               = $total;
    $config['per_page']                 = $per_page;
    $config['cur_page']                 = $page;
    $config['use_page_numbers']         = TRUE;
    $config['uri_segment']              = 'page';
    $config['query_string_segment']     = 'page';
    $config['full_tag_open']            = '<ul class="pagination pagination-sm mb-0">';
    $config['full_tag_close']           = '</ul>';
    $config['num_tag_open']             = '<li class="page-item">';
    $config['num_tag_close']            = '</li>';
    $config['cur_tag_open']             = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close']            = '</a></li>';
    $config['next_tag_open']            = '<li class="page-item">';
    $config['next_tag_close']           = '</li>';
    $config['prev_tag_open']            = '<li class="page-item">';
    $config['prev_tag_close']           = '</li>';
    $config['first_tag_open']           = '<li class="page-item">';
    $config['first_tag_close']          = '</li>';
    $config['last_tag_open']            = '<li class="page-item">';
    $config['last_tag_close']           = '</li>';
    $config['attributes']               = ['class' => 'page-link'];
    $this->pagination->initialize($config);

    $data['page_title']   = 'Users';
    $data['users']        = $users;
    $data['roles']        = $this->Role_model->get_all();
    $data['filters']      = $filters;
    $data['total']        = $total;
    $data['per_page']     = $per_page;
    $data['current_page'] = $page;
    $data['pagination']   = $this->pagination->create_links();
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
            ]);

            // Handle profile fields submitted via AJAX-loaded form
            $profile_fields = $this->input->post('profile');
            $profile_data   = [];
            if (!empty($profile_fields) && is_array($profile_fields)) {
                $allowed       = $this->Role_model->get_profile_fields($role->name);
                $allowed_names = array_column($allowed, 'name');
                foreach ($profile_fields as $key => $val) {
                    if (in_array($key, $allowed_names)) {
                        $profile_data[$key] = trim($val);
                    }
                }
            }

            // Always create profile row (with or without extra fields)
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

            // Handle profile fields submitted via AJAX-loaded form
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

            // Save profile — update_profile handles insert vs update automatically
            $this->User_model->update_profile($id, $new_role->name, $profile_data);

            $this->session->set_flashdata('success', 'User updated successfully.');
            redirect('admin/users');
        }

        // On error — reload with existing profile data so AJAX fields re-populate
        $data['errors']      = $errors;
        $data['user']        = $user;
        $data['profile']     = $this->User_model->get_profile($user->id, $user->role_name);
        $data['role_fields'] = $this->Role_model->get_profile_fields($user->role_name);
        $data['roles']       = $this->Role_model->get_all();
        $data['page_title']  = 'Edit User';
        $this->load->view('admin/users/edit', $data);
        return;
    }

    // GET — load existing profile data for pre-filling AJAX fields
    $data['page_title']  = 'Edit User';
    $data['user']        = $user;
    $data['profile']     = $this->User_model->get_profile($user->id, $user->role_name);
    $data['role_fields'] = $this->Role_model->get_profile_fields($user->role_name);
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
    $data['profile']    = $this->User_model->get_profile($user->id, $user->role_name);
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

public function ajax_role_fields($role_id) {
    if (!$this->input->is_ajax_request()) show_404();

    $this->load->model('Role_model');
    $role = $this->Role_model->get_by_id($role_id);

    if (!$role) {
        echo json_encode(['fields' => [], 'table' => '']);
        return;
    }

    $fields = $this->Role_model->get_profile_fields($role->name);
    echo json_encode([
        'fields'    => $fields,
        'role_name' => $role->name,
        'table'     => $role->name . '_profiles',
    ]);
}
}