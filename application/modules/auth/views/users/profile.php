<!-- Col -->
<div class="row">
    <div class="col-md-5 col-lg-4 col-xl-3 col-xs-12 col-md-pull-2">
        <div class="card box-widget widget-user">
            <div class="widget-user-header testbgpattern1"></div>
            <div class="widget-user-image">
                <img alt="User Avatar" class="rounded-circle" src="<?php echo base_url()?>assets/images/user.jpg">
            </div>
            <div class="card-body text-center">
                <div class="item-user pro-user">
                    <h4 class="pro-user-username tx-15 pt-2 mt-4 mb-1"><?php echo $this->session->userdata('user')->name; ?></h4>
                    <h6 class="pro-user-desc tx-13 mb-3 font-weight-normal text-muted"><?php echo $this->session->userdata('user')->group_name; ?></h6>
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
                    <input type="password" class="form-control" value="password">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" value="password">
                </div>
            </div>
            <div class="card-footer text-right"><a href="#" class="btn btn-primary">Update</a> <a href="#"
                    class="btn btn-danger">Cancle</a></div>
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
                                <select class="form-control select2">
                                    <option>Us English</option>
                                    <option>Arabic</option>
                                    <option>Korean</option>
                                </select>
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
                                <input type="text" class="form-control" name="username" placeholder="" value="<?php echo $this->session->userdata('user')->username; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo $this->session->userdata('user')->name; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Email" value="<?php echo $this->session->userdata('user')->email; ?>">
                            </div>
                        </div>
                    </div>
                  
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Designation" value="Web Designer">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 main-content-label">Contact Info</div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Email<i>(required)</i></label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Email" value="info@Admix.in">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Website</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Website" value="@spruko.w">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Phone</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="phone number" value="+245 354 654">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Address</label>
                            </div>
                            <div class="col-md-9">
                                <textarea class="form-control" name="example-textarea-input" rows="2"
                                    placeholder="Address">San Francisco, CA</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 main-content-label">Social Info</div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Twitter</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="twitter"
                                    value="twitter.com/spruko.me">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Facebook</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="facebook"
                                    value="https://www.facebook.com/Admix">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Google+</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="google" value="spruko.com">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Linked in</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="linkedin"
                                    value="linkedin.com/in/spruko">
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Github</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="github" value="github.com/sprukos">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 main-content-label">About Yourself</div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Biographical Info</label>
                            </div>
                            <div class="col-md-9">
                                <textarea class="form-control" name="example-textarea-input" rows="4"
                                    placeholder="">pleasure rationally encounter but because pursue consequences that are extremely painful.occur in which toil and pain can procure him some great pleasure..</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 main-content-label">Email Preferences</div>
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Verified User</label>
                            </div>
                            <div class="col-md-9">
                                <div class="custom-controls-stacked">
                                    <label class="ckbox mg-b-10"><input checked="" type="checkbox"><span> Accept to
                                            receive post or page notification emails</span></label>
                                    <label class="ckbox"><input checked="" type="checkbox"><span> Accept to receive
                                            email sent to multiple recipients</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Profile</button>
            </div>
        </div>
    </div>
    <!-- /Col -->
</div>
<!-- /row -->