<?php
defined('BASEPATH') or exit('No direct script access allowed');

#[AllowDynamicProperties]
class Account_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->users_table = "user";
	}

	public function insert_user($data)
	{

		return $this->db->insert($this->users_table, $data);
	}
}
