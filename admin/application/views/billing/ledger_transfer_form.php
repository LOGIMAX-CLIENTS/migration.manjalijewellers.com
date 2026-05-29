<div class="content-wrapper">
    <style>
        .select2-container--default .select2-selection--single {
            border-radius: 0;
            border-color: #d2d6de;
            width: 100%;
            height: 34px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }
    </style>

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Ledger Adjustment
            <small>Add Transfer</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('index.php/admin_ret_billing/bank_ledger_transfer/list'); ?>">Ledger Transfer List</a></li>
            <li class="active">Add Transfer</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box box-primary"> <!-- Added box-primary for blue top border -->
            <div class="box-header with-border">
                <h3 class="box-title">Ledger Adjustment - Add</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            
            <form action="#" method="post" id="ledger_transfer_form" class="form-horizontal">
                <div class="box-body">
                    
                    <!-- Transfer Mode Toggle Button Group -->
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <div class="btn-group" role="group" aria-label="Transfer Mode">
                                <button type="button" class="btn btn-primary active btn_mode" id="btn_mode_transfer" value="1">
                                    <i class="fa fa-exchange"></i> Ledger Transfer
                                </button>
                                <button type="button" class="btn btn-default btn_mode" id="btn_mode_manual" value="2">
                                    <i class="fa fa-edit"></i> Manual Credit/Debit
                                </button>
                            </div>
                            <input type="hidden" name="transfer_type" id="transfer_type" value="1">
                        </div>
                    </div>
                    
                    <br>

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_ledger" class="col-sm-4 control-label">From Ledger <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                        <select class="form-control select2" id="from_ledger" name="from_ledger" style="width: 100%;" required>
                                            <option value="">Select Ledger</option>
                                        </select>
                                    <p class="help-block"></p>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="bal_amount" class="col-sm-4 control-label">Current Balance</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                        <input type="text" class="form-control" id="bal_amount" name="bal_amount" readonly style="background-color: #f9f9f9; font-weight: bold;">
                                    </div>
                                    <p class="help-block"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group" id="to_ledger_div">
                                <label for="to_ledger" class="col-sm-4 control-label">To Ledger <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                        <select class="form-control select2" id="to_ledger" name="to_ledger" style="width: 100%;">
                                            <option value="">Select Ledger</option>
                                        </select>
                                    <p class="help-block"></p>
                                </div>
                            </div>

                             <!-- Transaction Type (Visible for Manual) -->
                             <div class="form-group" id="transaction_type_div" style="display:none;">
                                <label for="transaction_type" class="col-sm-4 control-label">Type <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tags"></i></span>
                                        <select class="form-control" id="transaction_type" name="transaction_type" style="width: 100%;">
                                            <option value="">Select Type</option>
                                            <option value="1">Credit (+)</option>
                                            <option value="2">Debit (-)</option>
                                        </select>
                                    </div>
                                    <p class="help-block"></p>
                                </div>
                            </div>

                             <div class="form-group">
                                <label for="amount" class="col-sm-4 control-label">Amount <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-inr"></i></span>
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" placeholder="0.00" disabled required>
                                    </div>
                                    <p class="help-block"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="narration" class="col-sm-4 control-label">Narration <span class="text-danger">*</span></label>
                                <div class="col-sm-8"> <!-- Spans mostly full width -->
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-file-text-o"></i></span>
                                        <textarea class="form-control" id="narration" name="narration" rows="3" placeholder="Enter details..." required></textarea>
                                    </div>
                                    <p class="help-block"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /.box-body -->
                
                <div class="box-footer text-center">
                    <button type="submit" id="submit_transfer" class="btn btn-primary btn-flat"><i class="fa fa-send"></i> Submit</button>
                    <a href="<?php echo base_url('index.php/admin_ret_billing/bank_ledger_transfer/list'); ?>" class="btn btn-default btn-flat">Cancel</a>
                </div><!-- /.box-footer-->
            </form>
            
        </div><!-- /.box -->

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Hidden input for base URL used by external JS -->
<input type="hidden" id="ledger_transfer_base_url" value="<?php echo base_url(); ?>">

