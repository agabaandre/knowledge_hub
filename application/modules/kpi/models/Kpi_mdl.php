<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_mdl extends CI_Model
{


    public function __Construct()
    {

        parent::__Construct();
    }
    public function create_kpi($insert)
    {

        $data = $this->db->get('kpi')->result();
        if (!empty($insert)) {
            $query = $this->db->insert('kpi', $insert);
            if ($query) {
                $data['message'] = "Succesful";
            } else {
                $data['message'] = "Failed";
            }
        }


        return $data;
    }
    public function kpi_data($insert = FALSE)
    {
        if (!empty($insert)) :
            $date = $insert['period'];
            unset($insert['period']);


            $insert['period'] = date("Y", strtotime($date));
            $insert['entry_id'] = $insert['country_id'] . '-' . $insert['period'] . '-' . $insert['kpi_id'];
        endif;
        $kpidatas = $this->db->get('data')->result();
        if (!empty($insert)) {
            $query = $this->db->insert('data', $insert);
            if ($query) {
                $kpidatas['message'] = "Saved";
            } else {
                $kpidatas['message'] = "Failed";
            }
        }
        foreach ($kpidatas as $kpidata) :
            $this->attach_related_data($kpidata);

        endforeach;
        return $kpidatas;
    }
    public function attach_related_data($kpisdata)
    {

        @$kpisdata->kpi    = $this->find_kpi(@$kpisdata->kpi_id);
        @$kpisdata->subject_area = $this->find_subject(@$kpisdata->kpi_id);
        @$kpisdata->country = $this->find_country(@$kpisdata->country_id);
        return $kpisdata;
    }
    public function find_kpi($id)
    {
        $this->db->where("id", $id);
        return $this->db->get('kpi')->row();
    }
    public function find_country($id)
    {
        $this->db->where("id", $id);
        return $this->db->get('country')->row();
    }

    public function find_subject($id)
    {
        $this->db->where("id", $id);
        return $this->db->get('subject_areas')->row();
    }

    public function get_subjects()
    {
        return $this->db->get('subject_areas')->result();
    }
}
