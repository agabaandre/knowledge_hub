<div class="row">
    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h3 class="card-title float-left">Mailing List</h3>
        </div>
        <div class="card-body">
            <table id="mailing_list_table" class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach($mailing_list as $record):?>

                    <tr>
                        <td width="5%"><?php echo $i++; ?> <i class="fa email fa-2x text-muted"></i></td>
                        <td><?php echo $record->email; ?></td>
                        <td><?php echo $record->status; ?></td>
                        <td><?php echo $record->created_at; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>