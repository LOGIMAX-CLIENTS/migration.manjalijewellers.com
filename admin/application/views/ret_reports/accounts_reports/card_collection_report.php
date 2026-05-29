<style>

@media print {



    html,

    body {

        height: auto;

        width: 100vh;

        margin: 0 !important;

        padding: 0 !important;

        overflow: hidden;

    }
    .alignRight{
        text-align: right;
    }

}

</style>

<!-- Content Wrapper. Contains page content -->



<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h1>

            Card Collection Report

        </h1>

        <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Card Collection Report</li>

        </ol>

    </section>



    <!-- Main content -->

    <section class="content">

        <div class="row">

            <div class="col-xs-12">



                <div class="box box-primary">

                    <div class="box-header with-border">

                        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

                                            <div class="col-md-2">

                                                <div class="form-group tagged">

                                                    <select id="branch_select" class="form-control"

                                                        style="width:100%;" multiple></select>

                                                </div>

                                            </div>

                                            <?php }else{?>

                                            <input type="hidden" id="branch_filter"

                                                value="<?php echo $this->session->userdata('id_branch') ?>">

                                            <input type="hidden" id="branch_name"

                                                value="<?php echo $this->session->userdata('branch_name') ?>">

                                            <?php }?>



                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <div class="input-group">

                                                        <button class="btn btn-default btn_date_range"

                                                            id="rpt_payment_date">

                                                            <i class="fa fa-calendar"></i> Date range picker

                                                            <i class="fa fa-caret-down"></i>

                                                        </button>

														<span style="display:none;" id="rpt_payments1"></span>

                                                            <span style="display:none;" id="rpt_payments2"></span>

                                                    </div>

                                                </div><!-- /.form group -->

                                            </div>

                                            <div class="col-md-2">

                                                <select id="source_by" class="form-control" style="width:100%;">

                                                    <option value="0" selected>All</option>

                                                    <option value="1">Purchase</option>

                                                    <option value="2">Chit</option>

                                                </select>

                                            </div>

                                            <div class="col-md-2 devices"> 

            									<select id="device" class="form-control" style="width:100%;"></select>

            								</div>
                                            <div class="col-md-2 sch_code" style="display: none"> 

            									<select id="chit_name" class="form-control" style="width:100%;" multiple></select>

            								</div>

                								

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <button type="button" id="card_collection_search"

                                                        class="btn btn-info">Search</button>

                                                </div>

                                            </div>



                    </div>

                    <div class="box-body">



                        <div class="row" id="card_collection_report">

                            <div class="col-md-12">

                                <div class="table-responsive">

                                    <table id="card_collection_list"

                                        class="table table-bordered table-striped text-center">

                                        <thead>

                                            <tr>

                                                <th>Bill No</th>

                                                <th>Code</th>

                                                <th>Bill Date</th>

                                                <th>Branch</th>

                                                <th>Device</th>

                                                <th>Amount</th>

                                                <th>Approval no</th>

                                                <th>Card</th>

                                                <th>Card No</th>

                                                <th>Customer</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                        </tbody>

                                        <tfoot>

    											<tr>

    											    <th style="text-align:left">Total : </th>

    											    <th style="text-align:right"></th>

    												<th style="text-align:right"></th>

    												<th style="text-align:right"></th>

    												<th style="text-align:right"></th>

    												<th style="text-align:right"></th>

    												<th style="text-align:right"></th>

    												<th style="text-align:right"></th>

    												<th style="text-align:right"></th>

    												<th style="text-align:right"></th>

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