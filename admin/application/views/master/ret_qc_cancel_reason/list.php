<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Section <small></small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Qc Cancel Rason</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Section</h3>
                        <?php if($access['add']==1){?>
                        <a class="btn btn-success pull-right" href="#" data-toggle="modal" data-target="#confirm-add">
                       
                            <i class="fa fa-user-plus"></i> Add
                        </a>
                        <?php }?>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <!-- Alert -->
                        <?php if ($this->session->flashdata('chit_alert')): ?>
                            <?php $message = $this->session->flashdata('chit_alert'); ?>
                            <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                                <?php echo $message['message']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table id="cancel_list" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- The data will be populated via AJAX -->
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
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Modal for adding a new reason -->
<div class="modal fade" id="confirm-add" role="dialog" aria-labelledby="addReasonLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="addReasonLabel">Add Cancel Reason</h4>
            </div>
            <div class="modal-body">
                <div id="error-msg-reason"></div>
                <form id="add-reason-form">
                    <div class="form-group row">
                        <label for="reason_name" class="col-md-4 col-md-offset-1">Reason</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="reason_name" name="reason_name" placeholder="Enter Reason" style="text-transform: capitalize;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reason_status" class="col-md-4 col-md-offset-1">Active</label>
                        <div class="col-md-4">
                            <input type="checkbox" checked="true" class="status" id="reason_status" name="switch" data-on-text="YES" data-off-text="NO"/>
                            <input type="hidden" id="reason_status_value" value="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="#" id="add_reason" class="btn btn-success">Save & Close</a>
                <!-- <a href="#" id="add_new_reason" class="btn btn-success">Save & New</a> -->
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal for editing an existing reason -->
<div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="editReasonLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="editReasonLabel">Edit Cancel Reason</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-10" id='error_message_reason'></div>
                </div>
                <form id="edit-reason-form">
                    <div class="form-group row">
                        <label for="ed_reason_name" class="col-md-4 col-md-offset-1">Reason</label>
                        <div class="col-md-4">
                            <input type="hidden" id="edit-reason-id" value=""/>
                            <input type="text" id="ed_reason_name" class="form-control" placeholder="Enter Reason" style="text-transform: capitalize;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ed_reason_status" class="col-md-4 col-md-offset-1">Active</label>
                        <div class="col-md-4">
                            <input type="checkbox" checked="true" class="status" id="ed_reason_status" name="switch" data-on-text="YES" data-off-text="NO"/>
                            <input type="hidden" id="ed_reason_status_value" value="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="#" id="update_reason" class="btn btn-success">Update</a>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal for deleting a section -->
<div class="modal fade" id="confirm-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Delete Section</h4>
            </div>
            <div class="modal-body">
                <strong>Are you sure you want to delete this Section?</strong>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-danger btn-confirm">Delete</a>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>