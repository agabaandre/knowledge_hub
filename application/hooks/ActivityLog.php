<?php 
class ActivityLog
{
    private $previous_controller = '';

    public function logActivity()
    {
        $CI = & CI_Controller::get_instance();
        $CI->load->library('user_agent');

        // Get the current controller and method
        $current_controller = $CI->router->fetch_class();
        $current_method = $CI->input->server('REQUEST_METHOD');

        // Use previous controller if current controller is empty
        if (empty($current_controller)) {
            $current_controller = $this->previous_controller;
        } else {
            $this->previous_controller = $current_controller;
        }

        // Log the user activity
        if (!empty($current_controller) && !empty($current_method)) {

            // Log the user activity
            $data = array(
                'user_id' => $CI->session->has_userdata('user') ? $CI->session->userdata('user')['id'] : null,
                'controller' => $CI->router->fetch_class() . '/'. $CI->router->fetch_method(),
                'method' => $CI->input->server('REQUEST_METHOD'),
                'ip_address' => $CI->input->ip_address(),
                'user_agent' => $CI->agent->agent_string(),
                'timestamp' => date('Y-m-d H:i:s'),
                'url' => current_url()
            );
            $CI->db->insert('user_activity', $data);
        }
    }
}