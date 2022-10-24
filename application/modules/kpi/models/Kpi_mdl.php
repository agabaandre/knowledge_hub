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
    public function kpi_data($data = FALSE)
    {
        $kpidatas = $this->db->get('data')->result();
        foreach ($kpidatas as $kpidata) :
            $this->attach_related($kpidata);

        endforeach;
        return $kpidatas;
    }
    public function attach_related($kpisdata)
    {

        $kpisdata->author    = $this->find_kpi($kpisdata->kpi_id);
        $kpisdata->subject_area = $this->find_subject($kpisdata->kpi_id);
        $kpisdata->file_type = $this->find_country($kpisdata->country_id);
        return $kpisdata;
    }
    public function find_kpi($id)
    {
        return $this->db->get('kpi')->row();
    }
    public function find_country($id)
    {
        return $this->db->get('country')->row();
    }
    public function find_subject($id)
    {
        return $this->db->get('subject_areas')->row();
    }
}
