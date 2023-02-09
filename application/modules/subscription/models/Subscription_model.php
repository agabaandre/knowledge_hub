<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class Subscription_model extends CI_Model {
    public function subscribe_email($email) {
        $this->db->where('email', $email);
        $result = $this->db->get('subscribers');

        if ($result->num_rows() > 0) {
            return false;
        } else {
            $data = array(
                'email' => $email,
                'status' => 1, // 1 = ACTIVE, 2 = INACTIVE
                'created_at' => date('Y-m-d H:i:s')
            );
            return $this->db->insert('subscribers', $data);
        }
    }
}