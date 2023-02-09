<!-- Col -->
<div class="row">
    <div class="col-md-5 col-lg-4 col-xl-3 col-xs-12 col-md-pull-2">
        <div class="card box-widget widget-user">
            <div class="widget-user-header testbgpattern1"></div>
            <div class="widget-user-image">
                <img alt="User Avatar" class="rounded-circle" src="<?php echo base_url() ?>assets/images/user.jpg">
            </div>
            <div class="card-body text-center">
                <div class="item-user pro-user">
                    <h4 class="pro-user-username tx-15 pt-2 mt-4 mb-1">
                        <?php echo $this->session->userdata()->name; ?>
                    </h4>
                    <h6 class="pro-user-desc tx-13 mb-3 font-weight-normal text-muted">
                        <?php echo $this->session->userdata()->group_name; ?>
                    </h6>
                </div>
            </div>

        </div>
        <div class="card">
            <div class="card-header pb-0 border-bottom">
                <div class="card-title pb-1">Edit Password</div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Old Password</label>
                    <input type="password" class="form-control" value="password">
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="new_password" class="form-control" value="password">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="new_password" class="form-control" value="password">
                </div>
            </div>
            <div class="card-footer text-right"><a href="#" class="btn btn-success">Update</a> <a href="#"
                    class="btn btn-danger">Cancel</a></div>
        </div>
    </div>

    <!-- Col -->
    <div class="col-md-7 col-lg-8 col-xl-9">
        <div class="card">
            <div class="card-body">
                <div class="mb-4 main-content-label">Personal Information</div>
                <form class="form-horizontal">
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Language</label>
                            </div>
                            <div class="col-md-9">
                                <?php echo translate(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 main-content-label">Name</div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">User Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="username" placeholder=""
                                    value="<?php echo $this->session->userdata()->username; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control" placeholder="Name"
                                    value="<?php echo $this->session->userdata()->name; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Email"
                                    value="<?php echo $this->session->userdata()->email; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                            </div>
                            <div class="col-md-9">
                                <p>
                                    <?php echo $this->session->userdata()->group_name; ?>
                                </p>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success waves-effect waves-light">Update Profile</button>
            </div>
        </div>
    </div>
    <!-- /Col -->
</div>
<!-- /row -->