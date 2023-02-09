<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscription extends CI_Controller {
    public function index() {
        $this->load->model('msubscription');

        $this->session->unset_userdata('error_message');
        $this->session->unset_userdata('success_message');

        $email = $this->input->post('email');
        $result = $this->msubscription->subscribe_email($email);
        if ($result) {
            $this->session->set_flashdata('success_message', 'Subscribed successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Email already subscribed');
        }

        redirect('/');
    }
}