<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mailing_list_mdl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->subscribers_table = 'subscribers';

    }

    public function getSubscribers()
    {
        $query = $this->db->get($this->subscribers_table);
        $subscribers = $query->result();
        return $subscribers;
    }
}