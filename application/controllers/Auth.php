<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
    }

    // Redirect if already logged in
    public function index() {
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role($this->session->userdata('role_name'));
        }
        $this->load->view('auth/login');
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role($this->session->userdata('role_name'));
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/login', ['error' => validation_errors()]);
            return;
        }

        $email    = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->Auth_model->get_user_by_email($email);

        if (!$user) {
            $this->load->view('auth/login', ['error' => 'Invalid email', 'err_type' => 'email']);
            return;
        }

        if ($user->status === 'inactive') {
            $this->load->view('auth/login', ['error' => 'Your account is inactive. Contact admin.', 'err_type' => 'inactive']);
            return;
        }

        if (!password_verify($password, $user->password)) {
            $this->load->view('auth/login', ['error' => 'Invalid password.', 'err_type' => 'password']);
            return;
        }

        // Set session
        $session_data = [
            'logged_in'        => TRUE,
            'user_id'          => $user->id,
            'user_name'        => $user->name,
            'user_email'       => $user->email,
            'role_id'          => $user->role_id,
            'role_name'        => $user->role_name,
        ];
        $this->session->set_userdata($session_data);

        // Update last login in profile table
        $this->Auth_model->update_last_login($user->id);

        $this->_redirect_by_role($user->role_name);
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }

    private function _redirect_by_role($role_name) {
        switch ($role_name) {
            case 'admin':
                redirect('admin/dashboard');
                break;
            case 'driver':
                redirect('driver/dashboard');
                break;
            default:
                redirect('user/dashboard');
                break;
        }
    }
}