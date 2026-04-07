<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    public function get_all() {
        return $this->db->order_by('id', 'ASC')->get('roles')->result();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get('roles')->row();
    }

    public function count_all() {
        return $this->db->count_all('roles');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $this->session->userdata('user_id');
        $this->db->insert('roles', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $this->session->userdata('user_id');
        $this->db->where('id', $id)->update('roles', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id)->delete('roles');
    }

    public function name_exists($name, $exclude_id = NULL) {
        $this->db->where('name', $name);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return $this->db->count_all_results('roles') > 0;
    }

    // -------------------------------------------------------
    // Dynamic profile table creation
    // -------------------------------------------------------

    // Allowed field types admin can pick
    public static function allowed_field_types() {
        return [
            'VARCHAR(255)' => 'Text (short)',
            'TEXT'         => 'Text (long)',
            'INT'          => 'Number (integer)',
            'DECIMAL(10,2)'=> 'Number (decimal)',
            'DATE'         => 'Date',
            'DATETIME'     => 'Date & Time',
            'TINYINT(1)'   => 'Boolean (yes/no)',
        ];
    }

    public function create_profile_table($role_name, $custom_fields = []) {
        $table = $role_name . '_profiles';

        // Drop if somehow exists (fresh create)
        if ($this->db->table_exists($table)) {
            return; // already exists, skip
        }

        // Build SQL
        $sql = "CREATE TABLE `{$table}` (
            `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`     INT(11) UNSIGNED NOT NULL,
            `last_login`  DATETIME DEFAULT NULL,
            `profile_pic` VARCHAR(255) DEFAULT NULL,
            `updated_at`  DATETIME DEFAULT NULL,
            `updated_by`  INT(11) UNSIGNED DEFAULT NULL";

        foreach ($custom_fields as $field) {
            $field_name = $this->_sanitize_field_name($field['name']);
            $field_type = $this->_validate_field_type($field['type']);
            if ($field_name && $field_type) {
                $sql .= ",\n            `{$field_name}` {$field_type} DEFAULT NULL";
            }
        }

        $sql .= ",\n            PRIMARY KEY (`id`),
            UNIQUE KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->db->query($sql);
    }

    public function drop_profile_table($role_name) {
        $table = $role_name . '_profiles';
        if ($this->db->table_exists($table)) {
            $this->db->query("DROP TABLE `{$table}`");
        }
    }

    private function _sanitize_field_name($name) {
        // lowercase, only letters/numbers/underscore, no spaces
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9_]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name); // no double underscores
        $name = trim($name, '_');
        return strlen($name) > 0 ? $name : null;
    }

    private function _validate_field_type($type) {
        $allowed = array_keys(self::allowed_field_types());
        return in_array($type, $allowed) ? $type : null;
    }
    // Get profile table columns for a role (excluding default fields)
public function get_profile_fields($role_name) {
    $table = $role_name . '_profiles';
    if (!$this->db->table_exists($table)) return [];

    $skip = ['id', 'user_id', 'last_login', 'profile_pic', 'updated_at', 'updated_by'];

    $query  = $this->db->query("DESCRIBE `{$table}`");
    $fields = [];
    foreach ($query->result() as $row) {
        if (!in_array($row->Field, $skip)) {
            $fields[] = [
                'name' => $row->Field,
                'type' => $row->Type,
            ];
        }
    }
    return $fields;
}
}