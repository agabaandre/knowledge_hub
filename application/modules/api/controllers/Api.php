<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Api extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Api_Model', 'api_mdl');
    }
    public function index_get()
    {
        echo "AFRICA CDC KNOWLEGE HUB API " . date('Y-m-d');
    }
    public function auth()
    {
    }
}
