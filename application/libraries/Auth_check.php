<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_check {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    // Call this in any controller __construct to require login
    public function require_login() {
        if (!$this->CI->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    // Require a specific role — pass single string or array of roles
    public function require_role($roles) {
        $this->require_login();
        $current_role = $this->CI->session->userdata('role_name');
        if (is_array($roles)) {
            if (!in_array($current_role, $roles)) {
                show_error('Access Denied', 403);
            }
        } else {
            if ($current_role !== $roles) {
                show_error('Access Denied', 403);
            }
        }
    }

    public function get_user() {
        return [
            'id'                => $this->CI->session->userdata('user_id'),
            'name'              => $this->CI->session->userdata('user_name'),
            'email'             => $this->CI->session->userdata('user_email'),
            'role_id'           => $this->CI->session->userdata('role_id'),
            'role_name'         => $this->CI->session->userdata('role_name'),
            'role_display_name' => $this->CI->session->userdata('role_display_name'),
        ];
    }
}