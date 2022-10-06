<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Permissions extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->model('permissions_mdl', 'perms_mdl');
    $this->module = "permissions";
  }
  public function index()
  {
    $data['module'] = $this->module;
    $data['title'] = $data['uptitle'] = "User Groups and Permissions";
    render('groups', $data);
  }


  //permissions management

  public function getUserGroups()
  {
    $groups = $this->perms_mdl->getUserGroups();
    return $groups;
  }

  public function addPermissions()
  {
    $data['view'] = "add_permissions";
    $data['title'] = "Add Permission";
    $data['module'] = "auth";
    echo Modules::run('templates/main', $data);
  }
  public function getPermissions()
  {
    $perms = $this->perms_mdl->getPermissions();
    return $perms;
  }
  public function groupPermissions($group = FALSE)
  {
    $fperms = array();
    $perms = $this->perms_mdl->groupPermissions($group);
    foreach ($perms as $perm) {
      $perm['id'];
      array_push($fperms, $perm['id']);
    }
    return $fperms;
  }
  public function getGroupPerms($groupId = FALSE)
  {
    $perms = $this->perms_mdl->getGroupPerms($groupId);
    return $perms;
    //print_r($perms);
  }
  public function savePermissions()
  {
    $data = $this->input->post();
    $post_d = $this->auth_mdl->savePermissions($data);
    if ($post_d) {
      $msg = "PermissionassignPermissions is Saved successfully";
      Modules::run('utility/setFlash', $msg);
      redirect('permissions');
    }
  }
  public function assignPermissions()
  {
    $this->session->set_flashdata('group', $this->input->post('group'));
    if (!empty($this->input->post('assign'))) {
      $data = $this->input->post();
      $groupId = $data['group'];
      $permissions = $data['permissions'];
      $insert_data = array();
      foreach ($permissions as $perm) {
        $row = array("group_id" => $groupId, "permission_id" => $perm);
        array_push($insert_data, $row);
      }
      $post_d = $this->auth_mdl->assignPermissions($groupId, $insert_data);
      if ($post_d) {
        $msg = "Assignments have been Saved successfully";
        Modules::run('utility/setFlash', $msg);
      }
    }
    redirect('permissions');
  }
}
