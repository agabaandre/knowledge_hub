<?php
class PrivacyPolicyModel extends CI_Model{


    public  function get()
    {
        return file_get_contents(APPPATH . 'modules/privacypolicy/privacy_policy.md');
    }
    
}