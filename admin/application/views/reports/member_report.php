<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Member Report
            <!-- <small><span class="badge bg-green" id="member_account_count"></span></small> -->
            <span class="badge bg-green" style="font-size: 14px;margin-left: 230px;color:#000;">Amount in : <b class="" id="currency_symbol"></b>
            </span>
        </h1><span id="total" class="badge bg-green"></span>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Member Report</li>
        </ol>
    </section>

    <!-- Main content starts -->
    <section class="content report_sel">
        <!-- MAIN row starts-->
        <div class="row">
            <!-- MAIN col starts-->
            <div class="col-xs-12">
                <!--main box starts-->
                <div class="box box-primary">
                    <!-- /.box-header -->
                    <!-- Main box body starts -->
                    <div class="box-body">
                        <!-- Alert block starts -->
                        <?php
                        if ($this->session->flashdata('chit_alert')) {
                            $message = $this->session->flashdata('chit_alert');
                        ?>
                            <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                                <?php echo $message['message']; ?>
                            </div>

                        <?php } ?>
                        <!-- Alert block ends -->

                        <!--filter row starts-->
                        <div class="row">
                            <!--filter column starts-->
                            <div class="col-md-12">
                                <!--date range picker starts-->
                                <div class="col-md-2" id="member_date_div" style="display:none;">
                                            <div class="form-group">
                                                    <div class="input-group">
                                                        <span style="font-weight:bold" id="member_date_range"></span>
                                                        <button class="btn btn-default btn_date_range" id="rpt_payment_date" >
                                                        
                                                                <span  style="display:none;" id="rpts_payments1"></span>
                                                                <span  style="display:none;" id="rpts_payments2"></span>
                                                                <i class="fa fa-calendar"></i> Start Date
                                                                <i class="fa fa-caret-down"></i>
                                                        </button>
                                                    </div>
                                            </div>
                                        </div> 
                                <!--date range picker ends-->
                                <!--branch select dropdown starts-->
                                <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
                                    <div class="col-md-2" id="branch_div">
                                        <div class="form-group">
                                            <label>Joined Branch </label>
                                            <select id="branch_select" class="form-control" style="width:100%;"></select>
                                            <input id="id_branch" name="scheme[id_branch]" type="hidden" value="" />
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                                    <input type="hidden" id="login_branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">
                                <?php } ?>
                                <!--branch select dropdown ends-->

                                <!--scheme select dropdown starts-->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Scheme</label>
                                        <select id="scheme_select" class="form-control" style="width:100%; "></select>
                                        <input id="id_schemes" name="id_scheme" type="hidden" value="" />
                                    </div>
                                </div>
                                <!--scheme select dropdown ends-->

                                <!--Area select dropdown starts-->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Area</label>
                                        <select id="area_select" class="form-control" style="width:100%; "></select>
                                        <input id="id_village" name="id_village" type="hidden" value="" />
                                    </div>
                                </div>
                                <!--Area select dropdown ends-->

                                <!--Joined Through select dropdown starts-->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Source</label>
                                        <select id="joined_through_select" class="form-control" style="width:100%; "></select>
                                        <input id="id_mode" name="id_mode" type="hidden" value="" />
                                    </div>
                                </div>
                                <!--Joined Through select dropdown ends-->


                                <!--Referred Employee select dropdown starts-->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Referred Employee</label>
                                        <select id="employee_select" class="form-control" style="width:100%; "></select>
                                        <input id="id_employee" name="id_employee" type="hidden" value="" />
                                    </div>
                                </div>
                                <!--Joined Through select dropdown ends-->


                            </div>
                            <!--filter column ends-->
                        </div>
                        <!--filter row ends-->

                        <!--search row starts-->
                        <div class="row">
                            <div class="col-md-12">
                                <!--Account Type select dropdown starts-->
                                <div class="col-md-2" id="report_type_div">
                                    <div class="form-group">
                                        <label>Report Type</label>
                                        <select id="member_report_type_select" class="form-control" style="width:200px; "></select>
                                        <input id="id_memberreporttype" name="id_memberreporttype" type="hidden" value="" />
                                    </div>
                                </div>
                                <div class="col-md-2" id="account_type_div" style="display:none;">
                                    <div class="form-group">
                                        <label>Account Type</label>
                                        <select id="account_type_select" class="form-control" style="width:200px; "></select>
                                        <input id="id_accounttype" name="id_accounttype" type="hidden" value="" />
                                    </div>
                                </div>
                                <!--Account Type select dropdown ends-->
                                <br>
                                <div class="col-md-2 pull-right">

                                    <div class="form-group">
                                        <button type="button" id="search_member_list" class="btn btn-info pull-right">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--search row ends-->
                        <!-- </br> -->
                        <!--summary block starts-->
                        <div class="box box-info stock_details collapsed-box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Member Report Summary <span class="summary_description"></span></h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="box-body collapse" style="display: none;">
                                <div class="row" style="background: #ecf0f5;">
                                    <div class="col-md-6" style="text-align:center;font-weight: bold;"><span id="member_summary"></span></div>
                                    <div class="col-md-6" style="text-align:center;font-weight: bold;"><span id="join_summary"></span></div>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-success btn-sm" id="print_member_summary" style="margin-top:-20px;display:none;"><i class="fa fa-print"></i> Print</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <span id="member_summary_details"></span>
                                </div>
                            </div>

                        </div>
                        <div id="membersummary"></div>
                        <!--summary block ends-->


                        <!--Table row starts-->
                        <div class="row">
                            <!--Table box block starts-->
                            <div class="box-body">
                                <!--Table row starts-->
                                <div class="table-responsive" id="out_standing_table_div">
                                    <table id="member_report_table" class="table table-bordered table-striped text-center">

                                        <thead>

                                            <tr>
                                                <th>S.No</th>
                                                <th>Scheme Code</th>
                                                <th>Account Number</th>
                                                <th>Account Name</th>
                                                <th>Mobile</th>
                                                <th>Customer Name</th>
                                                <th>Customer Reg On</th>
                                                <th>Joined On</th>
                                                <th>First Payment Amount</th>
                                                <th>Member Address</th>
                                                <!--<th>Area</th>-->
                                                <!--<th>City</th>-->
                                                <th>Source</th>
                                                <!-- <th>Cost Center</th> -->
                                                <th>Joined Branch</th>
                                                <th>Employee Created</th>
                                                <th>Referred Employee</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                <!--Table row ends-->
                            </div>
                            <!--Table box block starts-->
                        </div>
                        <!--Table row ends-->

                    </div>
                    <!--main box body ends -->

                    <!--overlay starts -->
                    <div class="overlay" style="display:block">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!--overlay ends -->
                </div>
                <!--main box ends-->
            </div>
            <!-- MAIN col ends-->
        </div>
        <!-- MAIN row ends-->
    </section>
    <!-- Main content ends -->
</div>
<!-- /.content-wrapper ends -->