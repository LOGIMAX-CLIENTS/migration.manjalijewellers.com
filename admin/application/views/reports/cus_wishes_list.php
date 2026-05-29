  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Birthday/Wedding Day Report
        <span class="badge bg-green" style="font-size: 14px;margin-left: 130px;color:#000;">
          Weight in : <b>Gram</b>
        </span>
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Reports</a></li>
        <li class="active">Birthday/Wedding Day Report </li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content report_sel">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <!-- <h3 class="box-title">Customer List</h3>      -->
              <!-- altered by RK - 16/12/2022-->
              <!-- <br> -->
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <span id="celeb_report_date_range" style="font-weight:bold;"></span><br />
                    <button class="btn btn-default btn_date_range" id="gift-dt-btn">
                      <span style="display:none;" id="gift_list1"></span>
                      <span style="display:none;" id="gift_list2"></span>
                      <i class="fa fa-calendar"></i> Birthday/Wedding Date
                      <i class="fa fa-caret-down"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->
            <div class="box-body">

              <!--<div class="row">
			  	<div class="col-md-offset-2 col-md-8">  
	              <div class="box box-default">  
	               <div class="box-body">  
					   <div class="row">
							<div class="col-md-offset-2 col-md-2"> 
								<label></label>
								<div class="form-group">
									<button type="button" id="send_cus_wish" class="btn btn-info">Send Wish</button>   
								</div>
							</div>
						</div>
					 </div>
	               </div> 
	              </div> 
	           </div> -->

              <div class="table-responsive">
                <table class="table table-bordered table-striped text-center grid" id="customer_list">
                  <thead>
                    <tr>
                      <!--<th><label class="checkbox-inline"><input type="checkbox" id="select_all_cus" name="select_all" value="all"/>All</label></th>-->
                      <th>Customer ID</th>
                      <th>Customer</th>
                      <th>Mobile</th>
                      <th>Area</th>
                      <th>D.O.B</th>
                      <th>D.O.W</th>
                      <th>Gold</th>
                      <th>Silver</th>
                      <th>Active Account(s)</th>
                      <th>Closed Account(s)</th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>

            </div><!-- /.box-body -->
            <div class="overlay" style="display:none;">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div><!-- /.box -->
        </div><!-- /.col -->
      </div><!-- /.row -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->