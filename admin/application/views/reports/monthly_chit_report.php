<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Monthly Chit Report
            <small><span class="badge bg-green" id="monthly_report_count"></span></small>
          </h1><span id="total" class="badge bg-green"></span>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Monthly Chit Report</li>
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
                    <div class="box-header with-border"> </div><!-- /.box-header --> 
                    <!-- Main box body starts -->       
                    <div class="box-body">
                            <!-- Alert block starts -->
                            <?php 
                                if($this->session->flashdata('chit_alert'))
                                {
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
                                       <!--Month picker starts-->
                                       <div class="col-md-2">
                                            <div class="form-group" >
                                                <label>Select Month</label>									
                                                <select id="month_select" class="form-control" style="width:100%; "></select>
                                                <input id="id_month"  name="id_month" type="hidden" value="" />
                                            </div>
                                        </div>
                                       <!--Month picker ends-->
                                       <!--Year input starts-->
                                       <div class="col-md-2">
                                            <div class="form-group" >
                                                <label>Enter Year</label>									
                                                
                                                <input id="id_year"  name="id_year" maxlength="4" minlength="4" onkeypress="return /^[0-9]$/i.test(event.key)" type="text" value="" />
                                            </div>
                                        </div>
                                       <!--year input ends-->
                                        <!--branch select dropdown starts-->
                                        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?> 			
                                            <div class="col-md-2" id="branch_div">
                                                <div class="form-group" >
                                                    <label>Select Branch </label>
                                                    <select id="branch_select" class="form-control" style="width:100%;" ></select>
                                                    <input id="id_branch" name="scheme[id_branch]"  type="hidden" value=""/>
                                                </div>
                                            </div>
                                        <?php }else{?>
                                            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
                                            <input type="hidden" id="login_branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
                                        <?php }?>
                                        <!--branch select dropdown ends-->
                                
                                        <!--scheme select dropdown starts-->
                                        <div class="col-md-2">
                                            <div class="form-group" >
                                                <label>Scheme Name</label>									
                                                <select id="scheme_select" class="form-control" style="width:100%; "></select>
                                                <input id="id_schemes"  name="id_scheme" type="hidden" value="" />
                                            </div>
                                        </div>
                                        <!--scheme select dropdown ends-->
                                        
                                       
                           
                           
                                            <br/>
                                        <div class="col-md-2"> 
                                                
                                                <div class="form-group">
                                                    <button type="button" id="search_monthly_list" class="btn btn-info">Search</button>   
                                                </div>
                                        </div>
                                </div>
                            </div>
                        
                        </br>
                        <!--summary block starts-->
                        <!--<div class="box box-info stock_details collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Member Report Summary <span class="summary_description"></span></h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body collapse" style="display: none;">
                                    <div class="row" style="background: #ecf0f5;">
                                            <div class="col-md-6" style="text-align:center;font-weight: bold;"><span id="member_summary" ></span></div>
                                            <div class="col-md-6" style="text-align:center;font-weight: bold;"><span id="join_summary" ></span></div>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-success btn-sm" id="print_member_summary" style="margin-top:-20px;display:none;"><i class="fa fa-print"></i> Print</button>
                                            </div>
                                    </div>
                                    <div class="row">
                                        <span id="member_summary_details"></span>
                                    </div>
                                </div>

                        </div>
                        <div id="membersummary"></div>-->
                        <!--summary block ends-->


                        <!--Table row starts-->
                        <div class="row">
                            <!--Table box block starts-->
                            <div class="box-body">
                                <!--Table row starts-->
                                <div class="table-responsive" id="monthly_chit_report_table_div">
                                        <table id="monthly_chit_report_table" class="table table-bordered table-striped text-center">
                                            
                                            <thead>
                                                
                                                 <tr  style="text-transform:uppercase;">
                                                    <th>S.No</th>
                                                    <th>Date</th>
                                                    <th>Cash</th>
                                                    <th>Card</th>
                                                    <th>Cheque</th>
                                                    <th>Net Banking</th>
                                                    <th>UPI</th>
                                                    <th>Wallet</th>
                                                    <th>Total Amount</th>
                                                   
                                                </tr>
                                            </thead> 
                                            <tbody> 
                                        
                                            </tbody>
                                            <tfoot>
                                            <tr style="font-weight:bold;">
                                                    <td></td>
                                                    <td>Total </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                   
                                                </tr>
                                            </tfoot>
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

