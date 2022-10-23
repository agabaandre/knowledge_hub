<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_mdl extends CI_Model
{


    public function __Construct()
    {

        parent::__Construct();
    }
    public function create_kpi()
    {

        $data = $this->db->get('kpi')->result();

        return $data;
    }
}
