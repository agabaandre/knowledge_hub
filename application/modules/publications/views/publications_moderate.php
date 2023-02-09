
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5>Publications Moderation</h5>
                         <hr>
                    </div>
                    <div class="card-tools text-right">

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">


                            <!-- if no publications -->
                            <?php if (empty($publications)) : ?>
                                <div class="card text-center" style="margin: 0 auto;">
                                    <div class="card-body">
                                        <i class="fas fa-exclamation-triangle fa-5x greyed-out-icon mb-3" style="color: #BCBCBC"></i>
                                        <h4 class="card-title">No Forums requiring Moderation</h4>
                                        <p class="card-text">Sorry, there are currently no publications available.</p>
                                    </div>
                                </div>
                            <?php else : ?>
                                <?php foreach ($publications as $publication) : ?>
                                    <h4><?php echo $publication->title; ?></h4>
                                    <p><?php echo substr($publication->description, 0, 150); ?>...</p>
                                    <hr />
                                    <table class="table table-striped condensed">
                                        <thead>
                                            <tr>
                                                <th>Comment</th>
                                                <th>Created By</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($publication->comments as $comment) : ?>
                                                <tr>
                                                    <td><?php echo $comment->comment; ?></td>
                                                    <td><?php echo $comment->created_by['name']; ?></td>
                                                    <td><?php echo $comment->created_at; ?></td>
                                                    <td>
                                                        <a href="<?php echo base_url('publications/approve_comment/' . $comment->id); ?>" class="btn btn-success btn-sm" id="approve_comment">Approve</a>
                                                        <a href="<?php echo base_url('publications/reject_comment/' . $comment->id); ?>" class="btn btn-danger btn-sm" id="reject_comment">Reject</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '#approve_comment', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            type: "get",
            url: url,
            data: {
                comment_id: $(this).data('id')
            },
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Comment approved ',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            }
        });
    });

    $(document).on('click', '#reject_comment', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            type: "get",
            url: url,
            data: {
                comment_id: $(this).data('id')
            },
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Comment declined',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    });
</script>