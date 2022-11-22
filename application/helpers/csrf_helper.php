<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
*Developer:Agaba Andrew 2022

*/
if (!function_exists('csrfinput_helper')) {
    function csrfinput()
    {
        $ci = &get_instance();
        $csrf = array(
            'name' => $ci->security->get_csrf_token_name(),
            'hash' => $ci->security->get_csrf_hash()
        );
        return $csrf;
    }
}
