<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo isset($master) && $master ? 'Edit' : 'Add' ?> KYC Master</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li><a href="<?php echo base_url('index.php/kyc/master'); ?>">KYC Master List</a></li>
            <li class="active"><?php echo isset($master) && $master ? 'Edit' : 'Add' ?></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <form role="form" id="kyc_master_form" method="POST" action="<?php echo base_url('index.php/kyc/master_save'); ?>">
                        <div class="box-body">
                            <input type="hidden" name="id_mas_kyc" value="<?php echo isset($master['id_mas_kyc']) ? $master['id_mas_kyc'] : ''; ?>">
                            
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>Document Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="<?php echo isset($master['name']) ? $master['name'] : ''; ?>" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Short Code</label>
                                    <input type="text" class="form-control" name="short_code" value="<?php echo isset($master['short_code']) ? $master['short_code'] : ''; ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Document Type <span class="text-danger">*</span></label>
                                    <select class="form-control" name="doc_type" required>
                                        <option value="">Select Type</option>
                                        <option value="1" <?php if(isset($master['doc_type']) && $master['doc_type'] == 1) echo 'selected'; ?>>Identity Proof</option>
                                        <option value="2" <?php if(isset($master['doc_type']) && $master['doc_type'] == 2) echo 'selected'; ?>>Address Proof</option>
                                        <option value="3" <?php if(isset($master['doc_type']) && $master['doc_type'] == 3) echo 'selected'; ?>>ID & Address proof</option>
                                        <option value="4" <?php if(isset($master['doc_type']) && $master['doc_type'] == 4) echo 'selected'; ?>>Transaction Proof</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label>Is Mandatory?</label>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="is_mandatory" value="1" <?php if(isset($master['is_mandatory']) && $master['is_mandatory'] == 1) echo 'checked'; ?>> Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Is Attachment Req?</label>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="is_attachment_req" value="1" <?php if(isset($master['is_attachment_req']) && $master['is_attachment_req'] == 1) echo 'checked'; ?>> Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Sort Order</label>
                                    <input type="number" class="form-control" name="sort" value="<?php echo isset($master['sort']) ? $master['sort'] : '0'; ?>">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php if(isset($master['status']) && $master['status'] == 1) echo 'selected'; ?>>Active</option>
                                        <option value="0" <?php if(isset($master['status']) && $master['status'] == 0) echo 'selected'; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <h4>KYC Attributes</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="kyc_attributes_table">
                                    <thead>
                                        <tr>
                                            <th>Attribute Name (DB Code) <span class="text-danger">*</span></th>
                                            <th>Label (Display) <span class="text-danger">*</span></th>
                                            <th>Input Type</th>
                                            <th>Max Length</th>
                                            <th>Regex Expression</th>
                                            <th>Mandatory?</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($attributes)): foreach($attributes as $attr): ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="id_kyc_attribute[]" value="<?php echo $attr['id_kyc_attribute']; ?>">
                                                <input type="text" class="form-control" name="attr_name[]" value="<?php echo $attr['attribute']; ?>" required>
                                            </td>
                                            <td><input type="text" class="form-control" name="attr_label[]" value="<?php echo $attr['attr_label']; ?>" required></td>
                                            <td>
                                                <select class="form-control" name="attr_input[]">
                                                    <option value="varchar" <?php if($attr['attr_input'] == 'varchar') echo 'selected'; ?>>Text (varchar)</option>
                                                    <option value="number" <?php if($attr['attr_input'] == 'number') echo 'selected'; ?>>Number</option>
                                                    <option value="file" <?php if($attr['attr_input'] == 'file') echo 'selected'; ?>>File Upload</option>
                                                    <option value="date" <?php if($attr['attr_input'] == 'date') echo 'selected'; ?>>Date</option>
                                                     <option value="image" <?php if($attr['attr_input'] == 'image') echo 'selected'; ?>>Image</option>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control" name="attr_length[]" value="<?php echo $attr['attr_length']; ?>"></td>
                                            <td><input type="text" class="form-control" name="attr_regex[]" value="<?php echo isset($attr['reg_expression']) ? $attr['reg_expression'] : ''; ?>"></td>
                                            <td>
                                                <select class="form-control" name="attr_mandatory[]">
                                                    <option value="1" <?php if($attr['is_mandatory'] == 1) echo 'selected'; ?>>Yes</option>
                                                    <option value="0" <?php if($attr['is_mandatory'] == 0) echo 'selected'; ?>>No</option>
                                                </select>
                                            </td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-attr"><i class="fa fa-times"></i></button></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                        <!-- Default Empty Row -->
                                        <tr>
                                            <td>
                                                <input type="hidden" name="id_kyc_attribute[]" value="0">
                                                <input type="text" class="form-control" name="attr_name[]" placeholder="e.g. pan_number" required>
                                            </td>
                                            <td><input type="text" class="form-control" name="attr_label[]" placeholder="e.g. PAN Number" required></td>
                                            <td>
                                                <select class="form-control" name="attr_input[]">
                                                    <option value="varchar">Text (varchar)</option>
                                                    <option value="number">Number</option>
                                                    <option value="file">File Upload</option>
                                                    <option value="date">Date</option>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control" name="attr_length[]"></td>
                                            <td><input type="text" class="form-control" name="attr_regex[]"></td>
                                            <td>
                                                <select class="form-control" name="attr_mandatory[]">
                                                    <option value="1">Yes</option>
                                                    <option value="0" selected>No</option>
                                                </select>
                                            </td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-attr"><i class="fa fa-times"></i></button></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-right">
                                                <button type="button" id="add_kyc_attr" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> Add Attribute</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="box-footer text-right">
                            <a href="<?php echo base_url('index.php/kyc/master'); ?>" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Template for new row -->
<script type="text/template" id="attr_row_template">
<tr>
    <td>
        <input type="hidden" name="id_kyc_attribute[]" value="0">
        <input type="text" class="form-control" name="attr_name[]" placeholder="e.g. back_img" required>
    </td>
    <td><input type="text" class="form-control" name="attr_label[]" placeholder="e.g. Back Side Image" required></td>
    <td>
        <select class="form-control" name="attr_input[]">
            <option value="varchar">Text (varchar)</option>
            <option value="number">Number</option>
            <option value="file">File Upload</option>
            <option value="date">Date</option>
        </select>
    </td>
    <td><input type="text" class="form-control" name="attr_length[]"></td>
    <td><input type="text" class="form-control" name="attr_regex[]"></td>
    <td>
        <select class="form-control" name="attr_mandatory[]">
            <option value="1">Yes</option>
            <option value="0" selected>No</option>
        </select>
    </td>
    <td><button type="button" class="btn btn-danger btn-sm remove-attr"><i class="fa fa-times"></i></button></td>
</tr>
</script>

<script src="<?php echo base_url('assets/js/kyc.js?v='.time()); ?>"></script>
