<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('auth_check');
        $this->auth_check->require_login();

        $role = $this->session->userdata('role_name');
        if ($role === 'admin') {
            redirect('admin/dashboard');
        }

        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation']);
        $this->load->model('User_model');
    }

    public function dashboard() {
        $user_id = $this->session->userdata('user_id');

        $data['page_title'] = 'Dashboard';
        $data['user']       = $this->User_model->get_by_id($user_id);
        $this->load->view('user/dashboard', $data);
    }

    public function profile() {
        $user_id = $this->session->userdata('user_id');

        $data['page_title'] = 'My Profile';
        $data['user']       = $this->User_model->get_by_id($user_id);
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
                $data = [
                    'name'  => $name,
                    'phone' => $phone,
                ];

                if ($role === 'user') {
                    $data['company']         = trim($this->input->post('company', TRUE));
                    $data['company_website'] = trim($this->input->post('company_website', TRUE));
                } elseif ($role === 'driver') {
                    $data['vehicle_type'] = trim($this->input->post('vehicle_type', TRUE));
                    $data['vehicle_no']   = trim($this->input->post('vehicle_no', TRUE));
                    $data['licence_no']   = trim($this->input->post('licence_no', TRUE));
                } else{
                    $data['company']         = trim($this->input->post('company', TRUE));
                    $data['company_website'] = trim($this->input->post('company_website', TRUE));
                    $data['vehicle_type'] = trim($this->input->post('vehicle_type', TRUE));
                    $data['vehicle_no']   = trim($this->input->post('vehicle_no', TRUE));
                    $data['licence_no']   = trim($this->input->post('licence_no', TRUE));
                }

                $this->User_model->update_basic($user_id, $data);
                $this->session->set_userdata('user_name', $name);
                $this->session->set_flashdata('success', 'Profile updated successfully.');
            } else {
                $this->session->set_flashdata('error', implode(' ', $errors));
            }

        }

        redirect('user/profile');
    }
}