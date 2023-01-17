<?php
defined('BASEPATH') or exit('No direct script access allowed');
#[AllowDynamicProperties]
class Publications_model extends CI_Model
{


	public function __construct()
	{

		parent::__Construct();

		$this->table = "publication";
		$this->filetypes_table = "file_type";
		$this->comments_table = "publication_comments";
	}

	public function get($filter = [], $limit = null, $start = 0)
	{

		if ($limit) {
			$this->db->limit($limit, $start);
		}

		$this->applyFilter($filter);

		if(isset($filter['is_featured'])){
		$recommendation   = get_cookie('recommendation');

		if(!empty($recommendation)){

		  $recommended = unserialize(get_cookie('recommendation'));

		  $this->db->or_where_in('sub_thematic_area_id', $recommended);
		}
	  }

	  if(isset($filter['favourites'])){ //get favourites
		$this->db->where_in('id', $filter['favourites']);
	  }

		$publications = $this->db->get($this->table)->result();

		foreach ($publications as $pub) {
			$pub = $this->attach_related($pub);
		}

		return $publications;
	}

	public function count($filter)
	{
		$this->applyFilter($filter);
		return count($this->db->get($this->table)->result());
	}

	public function applyFilter($filter)
	{

		if (!empty($filter)) {

			foreach ($filter as $key => $value) {

				if ($key == "search_key") :
					$this->db->like('title', $value);
					$this->db->or_like('description', $value);
					$this->db->or_like('publication', $value);
				else :

					if ($key !== "favourites")
					 $this->db->like($key, $value);
				endif;
			}
		}
	}

	public function get_by_subtheme($sub_theme_id)
	{

		$this->db->where('sub_thematic_area_id', $sub_theme_id);
		$publications = $this->db->get($this->table)->result();

		foreach ($publications as $pub) {
			$pub = $this->attach_related($pub);
		}

		return $publications;
	}

	public function find($id)
	{

		$publication = $this->db->where('id', $id)->get($this->table)->row();

		if ($publication) :
			$publication = $this->attach_related($publication);
			//increment visits
			$this->save(['visits' => (intval($publication->visits) + 1), 'id' => $id]);
		endif;
		return $publication;
	}

	public function attach_related($publication)
	{

		$publication->author      = $this->get_author($publication);
		$publication->sub_theme   = $this->subthemesmodel->find($publication->sub_thematic_area_id);
		$publication->theme       = $publication->sub_theme->theme;
		$publication->file_type   = $this->get_filetype($publication->file_type_id);
		$publication->attachments = $this->get_attachments($publication->id);
		$publication->has_attachments = (count($publication->attachments) > 0) ? true : false;
		$publication->comments     = $this->get_comments($publication->id);
		$publication->has_comments = (count($publication->comments) > 0) ? true : false;
		$publication->tags         = $this->get_tags($publication->id);
		$publication->is_favourite = in_array($publication->id,$this->user_favourites());

		return $publication;
	}

	public function get_attachments($publication_id)
	{
		$this->db->where('publication_id', $publication_id);
		return $this->db->get('publication_attachments')->result();
	}

	public function get_author($publication)
	{

		$this->db->where('id', $publication->id);
		return $this->db->get('author')->row();
	}

	public function get_filetype($type_id)
	{

		$this->db->where('id', $type_id);
		return $this->db->get($this->filetypes_table)->row();
	}

	//Save and returned inserted/updated row
	public function save($data, $update = false)
	{

		//if id is supplied, find record to update
		if ($update ||  (int) $data['id'] > 0) {
			$this->db->where('id', $data['id']);
			$saved = $this->db->update($this->table, $data);
		} else {
			$saved = $this->db->insert($this->table, $data);
		}

		$row_id = ($update) ? $data['id'] : $this->db->insert_id();

		//return inserted row
		if ($saved) {
			$mess = " Saved Sucessfully";
		} else {
			$mess = "Failed to Save";
		}
		if (empty($mess)) {
			$mess = "Update Successful";
		}

		return $mess;
	}

	public function update($data)
	{

		// $this->db->insert($this->table, $data);
		// $row_id =  $this->db->insert_id();

		// //return inserted row
		// return $this->find($row_id);

		// Update record with id
		$this->db->where('id', $data['id']);
		$this->db->update($this->table, $data);

		//return inserted row
		return true;
	}

	public function delete($id)
	{

		//return inserted row
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}

	public function save_comment($data)
	{
		return $this->db->insert($this->comments_table, $data);
	}

	public function get_comments($publication_id)
	{
		$this->load->model('auth/auth_mdl');

		$this->db->where('publication_id', $publication_id);
		$comments = $this->db->get($this->comments_table)->result();

		foreach ($comments as $comment) :
			$comment->user = $this->auth_mdl->find_user($comment->user_id);
		endforeach;

		return $comments;
	}

	public function get_tags($publication_id = null)
	{

		if ($publication_id) :
			$this->db->where('publication_id', $publication_id);
			$this->db->join('publication_tags', 'tags.id=publication_tags.publication_id');
			$results = $this->db->get('tags')->result();
		else :
			$results = $this->db->get('tags')->result();
		endif;

		return $results;
	}

	public function get_types()
	{

		return $this->db->get('file_type')->result();
	}

	public function count_favourites(){
		return (!is_guest())?count($this->db->where('user_id',user_session()->id)->get('favourites')->result()):0;
	}

	public function user_favourites(){
		
		
		if(is_guest())
		return [];

		$favs = $this->db->where('user_id',user_session()->id)->get('favourites')->result();
		$favourites = [];

		foreach($favs as $fav){
			array_push($favourites,$fav->publication_id);
		}

		return $favourites;
	}

	public function get_favourites($start=null,$limit=null)
	{
		
		if(is_guest())
		return [];

		$filter['favourites'] = $this->user_favourites();

		return (count($filter['favourites'] )>0)?$this->get($filter,$start,$limit):[];
	}

	public function save_favourite($id){


		$row = ['publication_id'=>$id, 'user_id'=>user_session()->id];

		return $this->db->insert('favourites',$row);
	}

	public function delete_favourite($id){

		$this->db->where('publication_id',$id);
		$this->db->where('user_id',user_session()->id);

		return $this->db->delete('favourites');
	}

}
