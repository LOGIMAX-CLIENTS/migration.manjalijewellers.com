<!-- Jothika 07-11-2025 -->
<style>
	
	.overlay {
		background-color: rgba(243, 243, 243, 0.5); 
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		display: flex;              
		justify-content: center;    
		align-items: center;        
		z-index: 9999;              
	}

	.overlay i {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		font-size: 40px;
		color: #333;
	}

</style>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Scheme Wise Mode Wise Report
      <span class="badge bg-green" style="font-size: 14px;margin-left: 130px;color:#000;">Amount in : <b class="" id="currency_symbol"></b>
        &nbsp; | &nbsp; Weight in : <b>Gram</b>
      </span>

    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Reports</a></li>
      <li class="active">Scheme Wise Mode Wise Report</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content report_sel">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">

            <!-- <div class="col-xs-4">
                   <h3 class="box-title">Payment Mode And Group wise Chit Collection Report</h3>  
                   </div> -->

            <?php if ($this->session->userdata('branch_settings') == 1) { ?>
                <?php if($this->session->userdata('login_branch') == 1) { ?>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Paid Branch </label>
                      <select id="branch_select" class="form-control" multiple></select>
                      <input id="id_branch" name="scheme[id_branch]" type="hidden" value="" />
                    </div>
                  </div>
                <?php } else { ?>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Paid Branch </label>
                    <select id="branch_select" class="form-control"></select>
                    <input id="id_branch" name="scheme[id_branch]" type="hidden" value="" />
                  </div>
                <?php } ?>
            <?php } ?>

            <div class="col-md-2">
              <span id="mode_wise_daterange" style="font-weight:bold;"></span>

              <div class="form-group">

                <button class="btn btn-default btn_date_range" id="payment_group_modewise_date">
                  <span style="display:none;" id="rpts_payments1"></span>
                  <span style="display:none;" id="rpts_payments2"></span>
                  <i class="fa fa-calendar"></i> PaymentDate
                  <i class="fa fa-caret-down"></i>
                </button>
              </div>

            </div>

            <div class="col-md-2 " style = "margin-top:15px;">
              <label for=""></label>
              <label for=""></label>
              <label for=""></label>
              <button type="button" class="btn btn-primary " id="search_mode_list" value="">Search</button>
            </div>

            <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
            <input type="hidden" id="login_branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">

            <!-- /.box-header -->
          </div>
          <div class="box-body">
            <!-- Alert -->
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
            
            <!--  esakki 26-09     -->
            <!-- <table id="payment_mode_group_wise_list" class="table table-bordered table-striped dataTable text-center grid" > -->
            <table id="payment_mode_group_wise_list" class="table table-bordered table-striped" style="width: 100% !important;">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Source</th>
                  <th>Cash</th>
                  <th>Card</th>
                  <th>Net Banking</th>
                  <th>Upi</th>
                  <th>Cheque</th>
                  <th>Wallet</th>
                  <th>Total</th>
                </tr>


              </thead>
              <tbody></tbody>

            </table>
          </div><!-- /.box-body -->
        </div><!-- /.box -->

        <div class="overlay" style="display:block">

          <i class="fa fa-refresh fa-spin"></i>

        </div>

      </div><!-- /.col -->
    </div><!-- /.row -->
  </section><!-- /.content -->
</div><!-- /.content-wrapper -->