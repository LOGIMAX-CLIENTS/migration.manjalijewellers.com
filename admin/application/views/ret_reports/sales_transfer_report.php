<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <style>
        @media print {

            html,
            body {
                height: auto;
                width: 190vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
            }
        }
    </style>
    <section class="content-header">
        <h1>
            Reports
            <small>Sales Transfer</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Retail Reports</a></li>
            <li class="active">Sales Transfer Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="col-md-2">
                            <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                                <div class="form-group">
                                    <select id="branch_select" class="form-control branch_filter" style="width:100%;" multiple></select>
                                </div>
                            <?php } else { ?>
                                <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                                <input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">
                            <?php } ?>
                        </div>



                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <button class="btn btn-default btn_date_range" id="rpt_date_picker">

                                        <i class="fa fa-calendar"></i> Date range picker
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    <span style="display:none;" id="rpt_from_date"></span>
                                    <span style="display:none;" id="rpt_to_date"></span>
                                </div>
                            </div><!-- /.form group -->
                        </div>
                        <!-- <div class="col-md-2"> 
            									<select id="category" class="form-control" style="width:100%;"></select>
            								</div> -->





                        <div class="col-md-1">
                            <div class="form-group">
                                <button type="button" id="sales_transfer_search" class="btn btn-info">Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="sales_transfer_list" class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th style="width:15%">Bill No</th>
                                                <th style="width:5%">BillDate</th>
                                                <th style="width:5%">From Branch</th>
                                                <th style="width:5%">To Branch</th>
                                                <th style="width:5%">Pcs</th>
                                                <th style="width:5%">Gwt(Grams)</th>
                                                <th style="width:5%">NWT(Grams)</th>
                                                <th style="width:5%">DIA WT(CT)</th>
                                                <th style="width:5%">Taxable Amount(Rs)</th>
                                                <th style="width:5%">SGST</th>
                                                <th style="width:5%">CGST</th>
                                                <th style="width:5%">IGST</th>
                                                <th style="width:5%">GST</th>
                                                <th style="width:5%">Round Off</th>
                                                <th style="width:5%">Total Amount(Rs)</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>


                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->