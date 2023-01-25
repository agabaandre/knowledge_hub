<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Privacy_policy extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = "privacy_policy";
        $this->title = "Privacy Policy";

        $this->load->model('privacy_policymodel');
    }

    public function index() {
        $data['module'] = $this->module;
        $data['title'] = $this->title;
        $data['page'] = "Privacy Policy";
        $data['privacy_policy'] = $this->privacy_policymodel->get();
        render('privacy_policy', $data);
    }

    public function read() {
        $data['module'] = $this->module;
        $data['title'] = "PRIVACY POLICY";
        $data['policy'] = $this->privacy_policymodel->get();
        render_site('policy', $data,true);
    }

    public function save() {
        $is_error = false;
        $privacy_policy = $this->input->post("privacy_policy");
        $resp = file_put_contents(APPPATH . 'modules/privacypolicy/privacy_policy.md', $privacy_policy);
        if ($resp) {
            $msg = "success";
        } else {
            $msg = "false";
        }

        // Set json header
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        echo json_encode($msg);
    }
}