<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('auth_check');
        $this->auth_check->require_login();

        // Block admins and drivers from user panel
        $role = $this->session->userdata('role_name');
        if (in_array($role, ['admin', 'driver'])) {
            redirect($role . '/dashboard');
        }

        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation']);
        $this->load->model('User_model');
        $this->load->model('Role_model');
    }

    public function dashboard() {
        $user_id = $this->session->userdata('user_id');
        $role    = $this->session->userdata('role_name');

        $data['page_title'] = 'Dashboard';
        $data['user']       = $this->User_model->get_by_id($user_id);
        $data['profile']    = $this->User_model->get_profile($user_id, $role);
        $this->load->view('user/dashboard', $data);
    }

    public function profile() {
        $user_id = $this->session->userdata('user_id');
        $role    = $this->session->userdata('role_name');

        $data['page_title']   = 'My Profile';
        $data['user']         = $this->User_model->get_by_id($user_id);
        $data['profile']      = $this->User_model->get_profile($user_id, $role);
        $data['role_fields']  = $this->Role_model->get_profile_fields($role);
        $this->load->view('user/profile', $data);
    }

    public function update_profile() {
        $user_id = $this->session->userdata('user_id');
        $role    = $this->session->userdata('role_name');
        $type    = $this->input->post('type');

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

                // Update profile fields
                $profile_fields = $this->input->post('profile');
                if (!empty($profile_fields) && is_array($profile_fields)) {
                    $allowed       = $this->Role_model->get_profile_fields($role);
                    $allowed_names = array_column($allowed, 'name');
                    $profile_data  = [];
                    foreach ($profile_fields as $key => $val) {
                        if (in_array($key, $allowed_names)) {
                            $profile_data[$key] = trim($val);
                        }
                    }
                    if (!empty($profile_data)) {
                        $this->User_model->update_profile($user_id, $role, $profile_data);
                    }
                }

                $this->session->set_userdata('user_name', $name);
                $this->session->set_flashdata('success', 'Profile updated successfully.');
            } else {
                $this->session->set_flashdata('error', implode(' ', $errors));
            }

        } elseif ($type === 'password') {
            $current  = $this->input->post('current_password');
            $new_pass = $this->input->post('new_password');
            $confirm  = $this->input->post('confirm_password');

            $errors = [];
            $user   = $this->User_model->get_by_id($user_id);

            if (!password_verify($current, $user->password)) $errors[] = 'Current password is incorrect.';
            if (strlen($new_pass) < 6)                        $errors[] = 'New password must be at least 6 characters.';
            if ($new_pass !== $confirm)                       $errors[] = 'Passwords do not match.';

            if (empty($errors)) {
                $this->User_model->update_basic($user_id, [
                    'password' => password_hash($new_pass, PASSWORD_DEFAULT),
                ]);
                $this->session->set_flashdata('success', 'Password changed successfully.');
            } else {
                $this->session->set_flashdata('error', implode(' ', $errors));
            }
        }

        redirect('user/profile');
    }
}