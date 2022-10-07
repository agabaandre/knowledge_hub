<?php

//region
if (!function_exists('access_level')) {
    function access_level1($userid)
    {
        $ci = &get_instance();
        $ci->db->join("regions", "regions.id=user_access_level1.access_id");
        $ci->db->where("user_id", "$userid");
        $query = $ci->db->get('user_access_level1')->result();
        foreach ($query as $q) :
            echo $q->region_name . '<br><hr>';
        endforeach;
    }
    //country
    function access_level2($userid)
    {
        $ci = &get_instance();
        $ci->db->where("user_id", "$userid");
        $ci->db->join("country", "country.id=user_access_level2.access_id");
        $query = $ci->db->get('user_access_level2')->result();
        foreach ($query as $q) :
            echo $q->name . '<br><hr>';
        endforeach;
    }
    function access_level3($userid)
    {
        $ci = &get_instance();
        $ci->db->where("user_id", "$userid");
        $query = $ci->db->get('user_access_level3')->result();
        foreach ($query as $q) :
            echo $q->access_id . '<br><hr>';
        endforeach;
    }
    function access_level4($userid)
    {
        $ci = &get_instance();
        $ci->db->where("user_id", "$userid");
        $query = $ci->db->get('user_access_level4')->rresult();
        foreach ($query as $q) :
            echo $q->access_id . '<br><hr>';
        endforeach;
    }
}
