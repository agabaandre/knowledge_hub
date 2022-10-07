   <?php

    $groups = Modules::run('permissions/getUserGroups');
    $permissions = Modules::run('permissions/getPermissions');
    $gpermissions = Modules::run('permissions/groupPermissions', $this->session->flashdata('group'));
    $this->load->view('add_perm_modal');
    ?>
   <style>
     .modal {
       clear: both;
       position: fixed;
       margin-left: 100px;
       margin-top: 120px;
       z-index: 10040;
       overflow-x: auto;
       overflow-y: auto;
     }
   </style>
   <div class="card">
     <div class="dashtwo-order-area " style="padding-top: 10px; min-height: 35em">
       <div class="container-fluid">
         <div class="row">
           <div class="row col-lg-12">
             <div class="col-md-6 pull-left">
               <div class="panel panel-default">
                 <div class="panel-heading">
                   <h5 class="panel-title">Manage User Groups and Permissions <h5>

                 </div>
                 <?php //print_r($gpermissions); 
                  ?>
                 <div class="panel-body">

                   <a href="#newgrp" class="btn btn-danger btn-sm" data-toggle="modal">Create a group</a>

                   <a href="#permsModal" class="btn btn-danger btn-sm pull-right" data-toggle="modal">Add Permission</a>

                   <?php echo $this->session->flashdata("msg"); ?>
                   <hr>
                   <?php echo form_open_multipart(base_url('permissions/assignPermissions'), array('id' => 'permissions', 'class' => 'permissions')); ?>

                   <div class="form-group">

                     <?php $selgroup = $this->session->flashdata('group'); ?>
                     <label>Select User Group to view or re-assign permissions</label>
                     <div class="input-group">

                       <select id="changeugroup" class="form-control" name="group" style="min-width:300px; text-transform:capitalize;" onchange="this.form.submit()">
                         <?php


                          foreach ($groups as $group) {  ?>

                           <option value="<?php echo $group->id; ?>" <?php if ($group->id == $selgroup) {
                                                                        echo "selected";
                                                                      } ?>><?php echo $group->group_name; ?></option>

                         <?php } ?>
                       </select>
                     </div>

                   </div>

                   <button type="submit" class="btn btn-danger">Save Group Permissions</button>
                   <br>
                   <table border="0" class="table">
                     <tr style="background: #dee2e6; color:#17a2b8; border-radius:4px;">
                       <td><?php echo "<p style='color:;'>Turn on/off Permission Assignment</p>"; ?></td>
                       <td><input style="display: block; " name="assign" value="assign" type="checkbox" class="btn btn-primary"></td>

                     </tr>
                     <tr style="background: #dee2e6; color:#17a2b8; border-radius:4px;">
                       <td><?php echo "<p style='color:17a2b8;'>Grant All Permissions</p>"; ?></td>
                       <td><input style="display: block;" id="checkAll" type="checkbox" class="btn btn-primary"></td>
                     </tr>

                     <hr>
                     <?php foreach ($permissions as $perm) : ?>

                       <tr>
                         <td><?php echo $perm->definition; ?></td>
                         <td><input style="display: block; " name="permissions[]" value="<?php echo $perm->id; ?>" type="checkbox" <?php if (in_array($perm->id, $gpermissions)) echo "checked"; ?>></td>

                       </tr>

                     <?php endforeach; ?>


                   </table>



                   <div class="form-group">

                     <div class="input-group">

                       <input type="submit" class="btn btn-sm btn-primary" value="Save Group">

                     </div>

                   </div>


                   </form>

                 </div>
               </div>
             </div>

             <div class="col-md-6 pull-right">
               <div class="panel panel-default">
                 <div class="panel-heading">
                   <h5 class="panel-title">Groups Permissions<h5>

                 </div>
                 <div class="panel-body">

                   <table class="table">

                     <?php

                      foreach ($groups as $group) {
                      ?>

                       <tr>
                         <td><?php echo ucwords($group->group_name); ?></td>
                         <td>
                           <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal<?php echo $group->id; ?>">Permissions</button>
                         </td>
                       </tr>

                       <!-- Modal -->
                       <div id="myModal<?php echo $group->id; ?>" class="modal fade" role="dialog">
                         <div class="modal-dialog modal-sm">

                           <div class="modal-content">
                             <div class="modal-header">

                               <h4 class="modal-title">Permissions for <?php echo ucwords($group->group_name); ?></h4>
                               <button type="button" class="close" data-dismiss="modal">&times;</button>
                             </div>
                             <div class="modal-body" style="padding-left:3em;">
                               <?php

                                $group_perms = Modules::run('permissions/getGroupPerms', $group->id);

                                //print_r($group_perms);

                                foreach ($group_perms as $perm) {
                                  echo "<li>" . ucwords($perm->name) . "</li>";
                                }

                                if (count($group_perms) < 1) {

                                  echo "<h3 class='text-danger text-center'> No permissions assigned</h3>";
                                }

                                ?>

                             </div>
                             <div class="modal-footer">
                               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                             </div>
                           </div>

                         </div>
                       </div>


                     <?php } ?>

                   </table>
                 </div>
               </div>
             </div>
           </div>
         </div>
       </div>
     </div>
     <div>

       <!-- new grp Modal -->
       <div id="newgrp" class="modal fade" role="dialog">
         <div class="modal-dialog">
           <?php echo form_open_multipart(base_url('permissions/addGroup'), array('id' => 'addgroup', 'class' => 'addgroup')); ?>
           <!-- Modal content-->
           <div class="modal-content">
             <div class="modal-header">

               <h4 class="modal-title">Add group</h4>
               <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body" style="padding-left:3em;">

               <div class="form-group">
                 <input type="text" placeholder="Group Name" name="group_name" class="form-control">
               </div>

             </div>


           </div>
           </form>
         </div>
       </div>


     </div>


     <script>
       $("#checkAll").click(function() {
         $('input:checkbox').not(this).prop('checked', this.checked);
       });
     </script>

     <script type="text/javascript">
       $('#group_form').submit(function(e) {

         e.preventDefault();

         var data = $(this).serialize();
         var url = '<?php echo base_url(); ?>permissions/groupAllow'


         $.ajax({
           url: url,
           method: "post",
           data: data,
           success: function(res) {

             if (res == "OK") {

               $('.suc').html("<center><font color='green'>Group Permissions configured</font></center>");
             } else {

               $('.suc').html("<center><font color='red'>Error Occured, Failed</font></center>");
             }



           } //success

         }); // ajax



       }); //form submit
     </script>