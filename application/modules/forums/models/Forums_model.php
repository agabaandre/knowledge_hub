<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Forums_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->table                = "forums";
		$this->forum_tags_table     = "forum_tags";
		$this->forum_comments_table = "forum_comments";
		$this->users_table 			= "user";
	}

	public function count($filters=[])
	{
		$this->applyFilters($filters);
		return $this->db->count_all($this->table);
	}

	public function get($filters=[],$limit=null,$start=null)
	{
		if($limit)
		$this->db->limit($limit,$start);

		$this->applyFilters($filters);

		$rows = $this->db->get($this->table)->result();

		foreach($rows as $row):
			$row->tags = $this->get_tags($row->id);
			$row->user     = $this->find_user($row->created_by);
		endforeach;

		return $rows;
	}

	public function applyFilters($filters=[]){

		foreach($filters as $key=>$value){

			if($key=="not_id"):
			 $this->db->where("id !=$value");
			else:
				$this->db->where($key,$value);
			endif;
		}

	}

	public function find($id)
	{
		$row = $this->db->where('id', $id)->get($this->table)->row();
		
		if($row):
			$row->tags     = $this->get_tags($row->id);
			$row->comments = $this->get_comments($row->id);
			$row->user     = $this->find_user($row->created_by);
		endif;

		return $row;
	}

	public function find_user($id)
	{
		return $this->db->where('id', $id)->get($this->users_table)->row();
	}

	public function get_tags($forum_id)
	{
		$tags = $this->db->where('forum_id', $forum_id)->get($this->forum_tags_table)->result();
		return $tags;
	}

	public function comment_replies($comment_id)
	{
		$replies = $this->db->where('parent_id', $comment_id)->get($this->forum_comments_table)->result();
		$replies = ($replies)?$replies:[];

		foreach($replies as $reply){
			$reply->user = $this->find_user($reply->created_by);
		}

		return $replies;
	}

	public function get_comments($forum_id)
	{
		
		$this->db->where('parent_id is NULL');
		$comments = $this->db->where('forum_id', $forum_id)->get($this->forum_comments_table)->result();

		foreach($comments as $comment){
			$comment->replies = $this->comment_replies($comment->id);
			$comment->user     = $this->find_user($comment->created_by);
		}
		
		return $comments;
	}

	//Save and returned inserted/updated row
	public function save($data, $update = false)
	{

		//if id is supplied, find record to update
		if ($update ||  (int) $data['id'] > 0) {
			$this->db->where('id', $data['id']);
			$this->db->update($this->table, $data);
		} else {
			$this->db->insert($this->table, $data);
		}

		$row_id = ($update) ? $data['id'] : $this->db->insert_id();

		//return inserted row
		return $this->find($row_id);
	}

	public function update($data)
	{

		$this->db->insert($this->table, $data);
		$row_id =  $this->db->insert_id();

		//return inserted row
		return $this->find($row_id);
	}

	public function delete($id)
	{
		//return inserted row
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}
}
