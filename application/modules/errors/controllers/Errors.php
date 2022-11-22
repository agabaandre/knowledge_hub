<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Errors extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function error404()
    {
        // Create your custom controller

        // Display page
        $this->load->view('errors/error404');
    }
}
