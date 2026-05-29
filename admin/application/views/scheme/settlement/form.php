<div class="content-wrapper">

<!-- Content Header (Page header) -->

<section class="content-header">

  <h1>

    Weight Settlement

    <small>To fix the weight to the paid amount</small>

  </h1>

  <ol class="breadcrumb">

    <li><a href="#"><i class="fa fa-dashboard"></i> Manage Scheme</a></li>

    <li><a href="#">Weight Settlement</a></li>    

  </ol>

</section>



<!-- Main content -->

<section class="content">



   <!-- Default box -->

	<div class="box">

		<div class="box-header with-border">

		  <h3 class="box-title">Weight Settlement</h3>

	<!--  <div class="box-tools pull-right">

		    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

		    <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

		  </div>-->

		</div>

		

		<div class="box-body">

		<div class="row">

			<div class="col-sm-10 col-sm-offset-1">

			<div id="chit_alert"></div>

			 

			</div>

		</div> 

		    <div class="row">

		    	<div class="col-sm-12">

	           		<div class="table-responsive">

	           			<table id="scheme_list" class="table table-stripped table-bordered text-center">

	           				<thead>

	           					<tr>

	           						<th><input type="checkbox" id="select_all" /> ID</th>

	           						<th>Scheme Name</th>

	           						<th>Code</th>

	           						<th>Accounts</th>	           						

	           						<th>Payments</th>
	           							           						
	           						<th>Settlement Type</th>	           						
	           						
	           						<th>Adjust By</th>	           						
	           						
	           						<th>Rate</th>          						

	           					</tr>

	           				</thead>	           				

	           			</table>

	           		</div>

	            </div>    

		    </div>   

		    <br/>
		    
		    				 <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                <div class="box-header">

                  <h3 class="box-title">Settlement List</h3>      

                  <div class="pull-right">

                  	 

                  </div>       

                </div><!-- /.box-header -->

                <div class="box-body">

                   <div class="table-responsive">

	                 <table id="settlement_list" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th>ID</th>

	                        <th>Date</th>

	                        <th>Customer</th>

	                        <th>Scheme A/c No</th>

	                        <th>Mobile</th>    

	                        <th>Amount (<?php echo $this->session->userdata('currency_symbol');?>)</th>                        

	                      </tr>

	                    </thead> 

	                 </table>

                  </div>

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

					 
				<div class="box-body">

                	<br/>      

					 <div class="row col-xs-12">

					   <div class="box box-default"><br/>

						  <div class="col-xs-offset-5">

							<button type="button" id="btn_settlement" class="btn btn-primary">Save</button> 

							<button type="button" class="btn btn-default btn-cancel">Cancel</button>

							

						  </div> <br/>

						</div>

					  </div>
				
				</div><!-- / box body -->
	
	<div class="overlay" style="display:none">

	  <i class="fa fa-refresh fa-spin"></i>

	</div>  

    <div class="box-footer">

            

    </div><!-- -->

</section><!-- /.content -->

</div><!-- /.content-wrapper -->