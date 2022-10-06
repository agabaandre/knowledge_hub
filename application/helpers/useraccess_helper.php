<?php

//region
if (!function_exists('access_level')) {
    function access_level1($userid)
    {
        $ci = &get_instance();
        $ci->db->where("user_id", "$userid");
        $query = $ci->db->get('user_access_level1')->result();
        foreach ($query as $q) :
            echo $q->access_id . '<br><hr>';
        endforeach;
    }
    //country
    function access_level2($userid)
    {
        $ci = &get_instance();
        $ci->db->where("user_id", "$userid");
        $query = $ci->db->get('user_access_level2')->result();
        foreach ($query as $q) :
            echo $q->access_id . '<br><hr>';
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
        $query = $ci->db->get('user_access_level4')->result();
        foreach ($query as $q) :
            echo $q->access_id . '<br><hr>';
        endforeach;
    }
}
