<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            Stock Age Master
            <small><?php echo $process_type; ?> Age Range</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li><a href="<?php echo base_url('index.php/admin_stock_age_master'); ?>">Stock Age Master</a></li>
            <li class="active"><?php echo $process_type; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-calendar"></i> 
                            <?php echo $process_type; ?> Age Range
                        </h3>
                    </div>

                    <form id="age_master_form" method="post" action="<?php echo base_url('index.php/admin_stock_age_master/save'); ?>">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('success')) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
                                </div>
                            <?php } ?>
                            <?php if ($this->session->flashdata('error')) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                                </div>
                            <?php } ?>
                            <input type="hidden" name="id" value="<?php echo $record['id'] ?? ''; ?>">

                            <!-- Age From -->
                            <div class="form-group">
                                <label for="age_from">Age From <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="age_from" name="age_from" 
                                       value="<?php echo $record['age_from'] ?? ''; ?>" min="0" required>
                                <small class="form-text text-muted">Enter age in days (e.g., 0 for start)</small>
                            </div>

                            <!-- Age To -->
                            <div class="form-group">
                                <label for="age_to">Age To <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="age_to" name="age_to" 
                                       value="<?php echo $record['age_to'] ?? ''; ?>" min="0" required>
                                <small class="form-text text-muted">Enter age in days (must be greater than Age From)</small>
                            </div>

                            <!-- Value/Label -->
                            <div class="form-group">
                                <label for="value">Display Value <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="value" name="value" 
                                       value="<?php echo $record['value'] ?? ''; ?>" 
                                       placeholder="e.g., Very Old, Old, Medium, New" required>
                                <small class="form-text text-muted">Label or description for this age range</small>
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label>Status</label>
                                <div class="btn-group" data-toggle="buttons" style="width: 100%;">
                                    <label class="btn btn-default btn-sm <?php echo ($record['status'] == 1) ? 'active' : ''; ?>" style="width: 50%;">
                                        <input type="radio" name="status" value="1" 
                                            <?php echo ($record['status'] == 1) ? 'checked' : ''; ?>>
                                        Active
                                    </label>
                                    <label class="btn btn-default btn-sm <?php echo ($record['status'] == 0) ? 'active' : ''; ?>" style="width: 50%;">
                                        <input type="radio" name="status" value="0" 
                                            <?php echo ($record['status'] == 0) ? 'checked' : ''; ?>>
                                        Inactive
                                    </label>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <a href="<?php echo base_url('index.php/admin_stock_age_master'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>
                    </form>
                </div><!-- /.box -->
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('age_master_form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var age_from = parseInt(document.getElementById('age_from').value);
            var age_to = parseInt(document.getElementById('age_to').value);
            if (age_to <= age_from) {
                e.preventDefault();
                alert('Age To must be greater than Age From');
                return false;
            }
        });
    }
});
</script>



