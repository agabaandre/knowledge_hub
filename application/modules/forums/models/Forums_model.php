<?php
defined('BASEPATH') or exit('No direct script access allowed');
#[AllowDynamicProperties]
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

	public function count($filters = [])
	{
		$this->applyFilters($filters);
		return $this->db->count_all($this->table);
	}

	public function get($filters = [], $limit = null, $start = null)
	{
		if ($limit)
			$this->db->limit($limit, $start);

		$this->applyFilters($filters);

		$rows = $this->db->get($this->table)->result();

		foreach ($rows as $row) :
			$row->tags = $this->get_tags($row->id);
			$row->user     = $this->find_user($row->created_by);
		endforeach;

		return $rows;
	}

	public function applyFilters($filters = [])
	{

		foreach ($filters as $key => $value) {

			if ($key == "not_id") :
				$this->db->where("id !=$value");
			else :
				$this->db->where($key, $value);
			endif;
		}
	}

	public function find($id)
	{
		$row = $this->db->where('id', $id)->get($this->table)->row();

		if ($row) :
			$row->tags     = $this->get_tags($row->id);
			$row->comments = $this->get_comments($row->id);
			$row->user     = $this->find_user($row->created_by);
		endif;

		return $row;
	}

	public function find_user($id)
	{
		return $this->db->where('user_id', $id)->get($this->users_table)->row();
	}

	public function get_tags($forum_id)
	{
		$tags = $this->db->where('forum_id', $forum_id)->get($this->forum_tags_table)->result();
		return $tags;
	}

	public function comment_replies($comment_id)
	{
		$replies = $this->db->where('parent_id', $comment_id)->get($this->forum_comments_table)->result();
		$replies = ($replies) ? $replies : [];

		foreach ($replies as $reply) {
			$reply->user = $this->find_user($reply->created_by);
		}

		return $replies;
	}

	public function get_comments($forum_id, $status = null)
	{

		if ($status) {
			$this->db->where('status', $status);
		}

		//Get comments
		$comments = $this->db->where('forum_id', $forum_id)->get($this->forum_comments_table)->result();

		//Loop through comments and get replies
		foreach ($comments as $comment) {
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

	public function get_forums_with_pending_comments()
	{
		$this->db->select('forums.*');
		$this->db->from('forums');
		$this->db->join('forum_comments', 'forums.id = forum_comments.forum_id', 'inner');
		$this->db->where('forum_comments.status', 'pending');
		$this->db->where('forum_comments.parent_id', null);
		$this->db->group_by('forums.id');
		$query = $this->db->get();
		$results = $query->result();

		foreach ($results as $forum) {
			$this->db->select('forum_comments.*, user.name as user_name, user.user_id, user.email as user_email');
			$this->db->from('forum_comments');
			$this->db->join('user', 'forum_comments.created_by = user.user_id', 'left');
			$this->db->where('forum_comments.forum_id', $forum->id);
			$this->db->where('forum_comments.status', 'pending');
			$this->db->where('forum_comments.parent_id', null);
			$comments_query = $this->db->get();
			$comments_result = $comments_query->result();
			foreach ($comments_result as $comment) {
				$comment->created_by = array("name" => $comment->user_name, "id" => $comment->user_id, "email" => $comment->user_email);
				unset($comment->user_name);
				unset($comment->user_id);
				unset($comment->user_email);
			}
			$forum->comments = $comments_result;
		}

		// //Eliminate forums with no comments
		// $results = array_filter($results, function($forum) {
		// 	return count($forum->comments) > 0;
		// });

		return $results;
	}

	public function get_forums_with_pending_comments_count()
	{
		$this->db->select('forums.*');
		$this->db->from('forums');
		$this->db->join('forum_comments', 'forums.id = forum_comments.forum_id', 'left');
		$this->db->where('forum_comments.status', 'pending');
		$this->db->group_by('forums.id');
		$query = $this->db->get();
		$results = $query->result();

		return count($results);
	}

	public function approve_comment($comment_id)
	{
		$this->db->where('id', $comment_id);
		$this->db->update('forum_comments', array('status' => 'approved'));
	}

	public function reject_comment($comment_id)
	{
		$this->db->where('id', $comment_id);
		$this->db->update('forum_comments', array('status' => 'rejected'));
	}

	// Approve forum
	public function approve_forum($forum_id)
	{
		$this->db->where('id', $forum_id);
		$this->db->update('forums', array('status' => 'approved'));
	}

	// Reject forum
	public function reject_forum($forum_id)
	{
		$this->db->where('id', $forum_id);
		$this->db->update('forums', array('status' => 'rejected'));
	}
}
