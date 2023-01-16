<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PrivacyPolicy extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = "privacypolicy";
        $this->title = "Privacy Policy";

        $this->load->model('privacypolicymodel');
    }

    public function index() {
        $data['module'] = $this->module;
        $data['title'] = $this->title;
        $data['page'] = "Privacy Policy";
        $data['privacy_policy'] = $this->privacypolicymodel->get();
        render('privacy_policy', $data);
    }

    public function read() {
        $data['module'] = $this->module;
        $data['title'] = "PRIVACY POLICY";
        $data['policy'] = $this->privacypolicymodel->get();
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