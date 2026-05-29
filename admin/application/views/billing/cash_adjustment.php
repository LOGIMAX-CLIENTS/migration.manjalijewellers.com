<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cash Adjustment
            <small>Entry</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Billing</a></li>
            <li class="active">Cash Adjustment</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Entry Form -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">New Cash Adjustment</h3>
                    </div>
                    <div class="box-body">
                        <form id="cash_adjustment_form">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Branch <span class="text-red">*</span></label>
                                        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){ ?>
                                        <select id="adj_branch" class="form-control select2" style="width:100%;" required>
                                            <option value="">Select Branch</option>
                                        </select>
                                        <?php } else { ?>
                                        <input type="text" class="form-control" value="<?php echo $this->session->userdata('branch_name'); ?>" readonly>
                                        <input type="hidden" id="adj_branch" value="<?php echo $this->session->userdata('id_branch'); ?>">
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Adj Amount <span class="text-red">*</span></label>
                                        <input type="number" id="adj_amount" class="form-control" placeholder="Enter Amount" min="0.01" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Type <span class="text-red">*</span></label>
                                        <select id="adj_type" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <option value="1">Credit</option>
                                            <option value="2">Debit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Narration <span class="text-red">*</span></label>
                                        <input type="text" id="adj_narration" class="form-control" placeholder="Enter Narration" maxlength="500" required>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br>
                                        <button type="button" id="btn_save_cash_adj" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entry List -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <div class="col-md-2">
                            <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){ ?>
                            <div class="form-group">
                                <select id="filter_branch" class="form-control select2" style="width:100%;">
                                    <option value="">All Branches</option>
                                </select>
                            </div>
                            <?php } else { ?>
                            <input type="hidden" id="filter_branch" value="<?php echo $this->session->userdata('id_branch'); ?>">
                            <input type="hidden" id="filter_branch_name" value="<?php echo $this->session->userdata('branch_name'); ?>">
                            <?php } ?>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <button class="btn btn-default btn_date_range" id="adj_date_picker">
                                        <i class="fa fa-calendar"></i> Date range picker
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    <span style="display:none;" id="adj_from_date"></span>
                                    <span style="display:none;" id="adj_to_date"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <button type="button" id="btn_filter_adj" class="btn btn-info">Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="cash_adj_list" class="table table-bordered table-striped text-center" style="width:100%;">
                                <thead>
                                    <tr>
                                        <td>ID</td>
                                        <td>Date</td>
                                        <td>Branch</td>
                                        <td>Amount</td>
                                        <td>Type</td>
                                        <td>Narration</td>
                                        <td>Created By</td>
                                        <td>Status</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr style="font-weight:bold; background-color:#f5f5f5;">
                                        <td colspan="3" style="text-align:right;">Grand Total :</td>
                                        <td id="adj_grand_total" style="text-align:right;">0.00</td>
                                        <td colspan="5"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
