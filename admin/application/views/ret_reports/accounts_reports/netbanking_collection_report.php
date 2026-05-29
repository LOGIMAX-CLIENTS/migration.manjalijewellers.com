<style>
    @media print {



        html,

        body {

            height: 100vh;

            width: 100vh;

            margin: 0 !important;

            padding: 0 !important;

            overflow: hidden;

        }

    }
</style>

<!-- Content Wrapper. Contains page content -->



<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h1>

            Accounts Reports

            <small>Net Banking Collection Report</small>

        </h1>



        <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Net Banking Collection Report</li>

        </ol>

    </section>



    <!-- Main content -->

    <section class="content">

        <div class="row">

            <div class="col-xs-12">



                <div class="box box-primary">

                    <div class="box-header with-border">

                        <div class="box box-default">

                            <div class="box-body">

                                <div class="row">

                                    <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>

                                        <div class="col-md-2">

                                            <div class="form-group tagged">

                                                <select id="branch_select" class="form-control" style="width:100%;" multiple></select>

                                            </div>

                                        </div>

                                    <?php } else { ?>

                                        <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">

                                        <input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">

                                    <?php } ?>


                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <div class="input-group">

                                                <button class="btn btn-default btn_date_range" id="nb_coll_rpt">

                                                    <i class="fa fa-calendar"></i> Date range picker

                                                    <i class="fa fa-caret-down"></i>

                                                </button>

                                                <span style="display:none;" id="nb_coll_rpt1"></span>

                                                <span style="display:none;" id="nb_coll_rpt2"></span>

                                            </div>

                                        </div><!-- /.form group -->

                                    </div>

                                    <div class="col-md-2">

                                        <select id="nb_source_by" class="form-control" style="width:100%;">

                                            <option value="0" selected>All</option>

                                            <option value="1">Salse</option>

                                            <option value="2">Chit</option>

                                        </select>

                                    </div>
                                    <div class="col-md-2 sch_block" style="display: none">

                                        <div class="form-group">

                                            <select id="chit_name" class="form-control" style="width:100%;" multiple></select>

                                        </div>

                                    </div>

                                    <div class="col-md-2">

                                        <select id="nb_group_by" class="form-control" style="width:100%;">

                                            <option value="0" selected>All</option>

                                            <option value="1">RTGS</option>

                                            <option value="2">IMPS</option>

                                            <option value="3">UPI</option>

                                            <option value="4">NEFT</option>

                                        </select>

                                    </div>


                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <select id="device_name" class="form-control" style="width:100%;" multiple></select>

                                        </div>

                                    </div>

                                </div>


                                <div class="row">

                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <select id="bank" class="form-control" style="width:100%;" multiple></select>

                                        </div>

                                    </div>



                                    <div class="col-md-2 pull-left">

                                        <div class="form-group">

                                            <button type="button" id="nb_collection_search" class="btn btn-info">Search</button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="box-body">

                        <div class="row">



                        </div>



                        <div class="row" id="card_collection_report">

                            <div class="col-md-12">

                                <div class="table-responsive">

                                    <table id="nb_collection_list" class="table table-bordered table-striped text-center">

                                        <thead>

                                            <tr>

                                                <th>Bill No</th>

                                                <th>Code</th>

                                                <th>Bill Date</th>

                                                <th>Payment Date</th>

                                                <th>Branch</th>

                                                <th>Bank</th>

                                                <th>Type</th>
                                                
                                                <th>Device</th>

                                                <th>Amount</th>
                                                
                                                <th>Ref No</th>
                                                
                                                <th>Payment Type</th>
                                                
                                                <th>Mobile</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                        </tbody>



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