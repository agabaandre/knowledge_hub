<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_mdl extends CI_Model
{


    public function __Construct()
    {

        parent::__Construct();
    }
    public function getData()
    {
        $data['publications'] = $this->count_publications();
        $data['authors'] = $this->count_authors();
        $data['org_authors'] = $this->organisation_Authors();
        $data['kpis'] = $this->count_kpi();
        $data['active_pubs'] = $this->active_publications();
        $data['subject_areas'] = $this->count_subjects();
        $data['thematic_areas'] = $this->thematic_areas();
        $data['subthemes'] = $this->subtheme_areas();
        $data['publication_areas'] = $this->publication_areas();
        $data['rccs'] = $this->rccs();
        $data['countries'] = $this->countries();

        return $data;
    }

    public function count_publications()
    {

        $db = $this->db->get('publication');
        return  $db->num_rows();
    }
    public function active_publications()
    {

        $this->db->where("is_active", "Active");
        $db = $this->db->get('publication');
        return  $db->num_rows();
    }
    public function count_Authors()
    {

        $db = $this->db->get('author');
        return  $db->num_rows();
    }
    public function organisation_Authors()
    {

        $this->db->where("is_organsiation", "Yes");
        $db = $this->db->get('author');
        return  $db->num_rows();
    }
    public function count_kpi()
    {

        $db = $this->db->get('kpi');
        return  $db->num_rows();
    }
    public function count_subjects()
    {

        $db = $this->db->get('subject_areas');
        return  $db->num_rows();
    }
    public function thematic_areas()
    {

        $db = $this->db->get('thematic_area');
        return  $db->num_rows();
    }
    public function subtheme_areas()
    {

        $db = $this->db->get('sub_thematic_area');
        return  $db->num_rows();
    }
    public function publication_areas()
    {

        $db = $this->db->get('geographical_coverage');
        return  $db->num_rows();
    }
    public function countries()
    {

        $db = $this->db->get('country');
        return  $db->num_rows();
    }
    public function rccs()
    {

        $db = $this->db->get('regions');
        return  $db->num_rows();
    }
}
