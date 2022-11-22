 <div class="row">
     <div class="col-sm-12 col-md-12">
         <div class="panel panel-bd lobidrag">
             <div class="panel-heading">
                 <div class="panel-title">
                     <h4><?php echo (!empty($title) ? $title : null) ?></h4>
                 </div>
             </div>
             <div class="panel-body">
                 <div class="row">
                     <div class="col-sm-12">
                         <div class="card">
                             <!-- Add Subject -->
                             <button type="button" class="btn btn-success" data-toggle="modal" data-target="#staticBackdrop" style="margin-bottom:3px;"><i class="fa fa-plus">
                                 </i>Add Subject
                             </button>
                             <div class="card-content">
                                 <table id="subject" class="table table-responsive table-striped table-bordered">
                                     <thead>
                                         <tr>
                                             <th>#</th>
                                             <th>Subject Area</th>
                                             <th>Display Index</th>
                                             <th>Module</th>
                                             <th>Icon Name(Font Awesome)</th>
                                             <th>Description</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         <?php
                                            $i = 1;
                                            $elements = Modules::run('Kpi/subjectData');
                                            foreach ($elements as $element) : ?>
                                             <tr class="table-row tbrow content strow">
                                                 <td><?php echo $i ?></td>
                                                 <td><?php echo $element->name; ?></td>
                                                 <td><?php echo  $element->display_index; ?></td>
                                                 <td><?php echo  $element->module; ?></td>
                                                 <td><?php echo  $element->icon; ?></td>
                                                 <td><?php echo $element->sub_description; ?></td>
                                             </tr>
                                         <?php
                                                $i++;
                                            endforeach;
                                            if (count($elements) == 0) {
                                                echo "<tr><td colspan='8'><center><h3 class='text-warning'>Please Add Indicators</h3></center></td></tr>";
                                            }
                                            ?>
                                         </tr>
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <?php $this->load->view('includes/add_subject'); ?>
 <script>
     $(document).ready(function() {
         $('#subject').DataTable({
             dom: 'Bfrtip',
             buttons: [
                 'copyHtml5',
                 'excelHtml5',
                 'csvHtml5',
                 'pdfHtml5'
             ]
         });
     });
 </script>