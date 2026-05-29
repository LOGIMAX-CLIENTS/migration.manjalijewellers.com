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

        .alignRight {
            text-align: right;
        }

    }
</style>

<!-- Content Wrapper. Contains page content -->



<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h1>

            Petty Cash Report

        </h1>

        <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Petty Cash Report</li>

        </ol>

    </section>



    <!-- Main content -->

    <section class="content">

        <div class="row">

            <div class="col-xs-12">



                <div class="box box-primary">

                    <div class="box-header with-border">

                        <div class="col-md-2" style="margin:20px 0px 0px 20px;">

                            <div class="form-group">

                                <div class="input-group">

                                    <button class="btn btn-default btn_date_range"

                                        id="rpt_payment_date">

                                        <i class="fa fa-calendar"></i> Date range picker

                                        <i class="fa fa-caret-down"></i>

                                    </button>

                                    <span style="display:none;" id="rpt_from_date"></span>

                                    <span style="display:none;" id="rpt_to_date"></span>

                                </div>

                            </div><!-- /.form group -->

                        </div>

                        <div class="col-md-2">

                            <div class="form-group">

                                <label>Select Branch</label>

                                    <select class="form-control" id="branch_select" style="width:100%;"></select>

                                </div>

                            </div>

                        <div class="col-md-2">

                            <div class="form-group">

                                <label>Select Account Head</label>

                                    <select class="form-control" id="account_head_select" style="width:100%;"></select>

                                </div>

                            </div>

                        <!-- <div class="col-md-2">

                           <div class="form-group">

                                <label>Select Ledger</label>

                                    <select class="form-control" id="ledger_select" style="width:100%;"></select>

                                </div>

                            </div> -->

                        <div class="col-md-2"> 

                            <div class="form-group">

									<label>Report Type</label>

									<select id="petty_cash_report_type" class="form-control" style="width:100%;">

									    <option value="1" >Summary</option>

									    <option value="2" selected>Detailed</option>

									</select>

                            </div>

						</div>

                        <div class="col-md-1" style="margin-top:20px;">

                            <div class="form-group">

                                <button type="button" id="petty_cash_search"

                                    class="btn btn-info">Search</button>

                            </div>

                        </div>



                    </div>

                    <div class="box-body">



                        <div class="row" id="petty_cash_report">

                            <div class="col-md-12">

                                <div class="table-responsive summary_report" style="display:none;">

                                    <table id="petty_cash_report_summary_list"

                                        class="table table-bordered table-striped text-center">

                                        <thead>

                                            <tr>

                                                <th style="text-align:left; width:10%;" >Branch</th>

                                                <th style="text-align:center; width:8%;">Date</th>

                                                <th style="text-align:center; width:8%;">Issue No</th>

                                                <th style="text-align:center; width:8%;">Receipt No</th>

                                                <th style="text-align:left; width:8%;">Issue To</th>
                                                
                                                <th style="text-align:left; width:14%;">Employee / Karigar Name</th>
                                               
                                                <th style="text-align:right; width:7%;">Issue Amount</th>

                                                <th style="text-align:right; width:10%;">Receipt Amount</th>

                                                <th style="text-align:right; width:10%;">Expense Amount</th>

                                                <th style="text-align:right; width:7%;">Cash</th>

                                                <th style="text-align:left; width:8%;">Cheque Number</th>

                                                <th style="text-align:right; width:7%;">Cheque</th>

                                                <th style="text-align:left; width:8%;">NB Ref No</th>

                                                <th style="text-align:right; width:7%;">Net Banking</th>                                                                                         

                                                <th style="text-align:left; width:14%;">Narration</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                        </tbody>

                                        <tfoot>

                                            <tr>

                                                <th style="text-align:left;">Total </th>
                                                                  
                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                                <th style="text-align:right;"></th>

                                            </tr>

                                        </tfoot>

                                    </table>
                                
                                </div>  

                                <div class="table-responsive detailed_report">

                                    <table id="petty_cash_report_detailed_list"

                                        class="table table-bordered table-striped text-center">

                                        <thead>

                                            <tr>

                                                <th style="text-align:left; width:15%;">Branch</th>

                                                <th style="text-align:center; width:10%;">Date</th>

                                                <th style="text-align:center; width:10%;">Issue No</th>

                                                <th style="text-align:left; width:45%;">Account Head</th>

                                                <th style="text-align:right; width:20%;">Issue Amount</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                        </tbody>

                                        <tfoot>

                                            <tr>

                                                <th style="text-align:left">Total </th>

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