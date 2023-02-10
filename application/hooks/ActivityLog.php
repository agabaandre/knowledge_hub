<?php 
class ActivityLog
{
    public function logActivity()
    {
        $CI = & CI_Controller::get_instance();
        $CI->load->library('user_agent');

        // Log the user activity
        $data = array(
            'user_id' => $CI->session->userdata('user')['id'],
            'controller' => $CI->router->fetch_class(),
            'method' => $CI->router->fetch_method(),
            'ip_address' => $CI->input->ip_address(),
            'user_agent' => $CI->agent->agent_string(),
            'timestamp' => date('Y-m-d H:i:s')
        );
        $CI->db->insert('user_activity', $data);
    }
}