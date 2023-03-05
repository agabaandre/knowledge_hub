<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mailing_list extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mailing_list_mdl');
        $this->module = 'mailing_list';
        $this->title = 'Mailing List';

    }

    public function index()
    {
        $data['module'] = $this->module;
        $data['title'] = $this->title;
        $data['mailing_list'] = $this->mailing_list_mdl->getSubscribers();
        render('mailing_list', $data);
    }
}