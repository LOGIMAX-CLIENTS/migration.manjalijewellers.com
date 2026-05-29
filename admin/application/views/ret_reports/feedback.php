<div class="content-wrapper">
    <section class="content-header">
        <h1>Customer Feedback</h1>
    </section>

    <section class="content">
        <input type="hidden" id="fb_login_branch" value="<?php echo isset($login_branch) ? $login_branch : ''; ?>" />

        <div class="row">
            <div class="col-xs-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button class="btn btn-default btn_date_range" id="feedback_date">
                                        <span style="display:none;" id="fb_date1"></span>
                                        <span style="display:none;" id="fb_date2"></span>
                                        <i class="fa fa-calendar"></i> Date range picker
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" id="fb_branch_filter">
                                        <?php if(isset($login_branch) && $login_branch > 0) {
                                            // Single-branch login — show only logged-in branch
                                            foreach($branch_list as $b) {
                                                if($b['id_branch'] == $login_branch) {
                                                    echo '<option value="'.$b['id_branch'].'" selected>'.$b['name'].'</option>';
                                                    break;
                                                }
                                            }
                                        } else {
                                            // All-branch login — show all
                                        ?>
                                            <option value="">All Branches</option>
                                            <?php if(isset($branch_list)) foreach($branch_list as $b) { ?>
                                                <option value="<?=$b['id_branch']?>"><?=$b['name']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" id="fb_employee_filter">
                                        <option value="">All Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" id="fb_source_filter">
                                        <option value="">All Sources</option>
                                        <option value="Social Media">Social Media</option>
                                        <option value="Bill Board">Bill Board</option>
                                        <option value="Reference">Reference</option>
                                        <option value="Tele Calling">Tele Calling</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" id="fb_rating_type">
                                        <option value="">Rating Type</option>
                                        <option value="rating_ambiance">Ambiance</option>
                                        <option value="rating_collection">Collection</option>
                                        <option value="rating_staff">Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <select class="form-control" id="fb_rating_value">
                                        <option value="">Stars</option>
                                        <option value="5">5 ★</option>
                                        <option value="4">4 ★</option>
                                        <option value="3">3 ★</option>
                                        <option value="2">2 ★</option>
                                        <option value="1">1 ★</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div id="chit_alert1" style="width: 92%;margin-left: 3%;"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered feedback_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Branch</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>DOB</th>
                                        <th>DOA</th>
                                        <th>Address</th>
                                        <th>Pincode</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Source</th>
                                        <th>Occasion</th>
                                        <th>Staff</th>
                                        <th>Staff Rating</th>
                                        <th>Ambiance</th>
                                        <th>Collection</th>
                                        <th>Suggestions</th>
                                        <?php foreach($feedback_header as $fb){ ?>
                                            <th><?php echo $fb['short_code']; ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->


    </section>
    <script type="text/javascript">
        var base_url = "<?php echo base_url(); ?>";
    </script>
    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jQuery-2.1.4.min.js"></script>


</div>