  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Monthly on Month Sales Comparision</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Month On Month</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Monthly on Month Sales Comparision Report</h3>  <span id="total_count" class="badge bg-green"></span>  

                 

                </div>

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

                                <div class="col-md-3"> 

                                    <label>Select Year</label>

									<div class="form-group">

										<select name="yearpicker" id="yearpicker" class="form-control"></select>

									</div> 

								</div>

								

								<!-- <div class="col-md-3"> 

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_select" class="form-control branch_filter"></select>

									</div> 

								</div>  -->

								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

									<div class="col-md-3"> 

										<div class="form-group tagged">

											<label class="trans_centre">Select Branch</label>

											<select id="branch_select" class="form-control branch_filter"></select>

										</div> 

									</div> 



									<?php }else{?>

									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

									<?php }?>


								

								<div class="col-md-3"> 

									<div class="form-group tagged">

										<label>Select Village</label>

										<select id="select_village" class="form-control"></select>

									</div> 

								</div> 

                               

								<div class="col-md-2"> 

                                <label></label>

									<div class="form-group">

										<button type="button" id="monthly_sales_comparision_search" class="btn btn-info">Search</button>   

										<input type="hidden" id="cur_year" value="0">

									</div>

								</div>

							</div>

						 </div>

	                   </div> 

	                  </div> 

                   </div> 

                

				   <div class="row">

						<div class="col-xs-12">

						<!-- Alert -->

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

						</div>

				   </div>

			  

                   <div class="row">

	                   <div class="col-md-12">

	                   	<div class="table-responsive">

		                 <table id="sales_comparision" class="table table-bordered table-striped text-center">

		                   <thead>

							  <tr>

							    <th width="5%">Village</th>

							    <th width="5%">Branch</th>

							    <th colspan="3" width="5%">January</th>

							    <th colspan="3" width="5%">Feb</th>

							    <th colspan="3" width="5%">Mar</th>

							    <th colspan="3" width="5%">Apirl</th>

							    <th colspan="3" width="5%">May</th>

							    <th colspan="3" width="5%">June</th>

							    <th colspan="3" width="5%">July</th>

							    <th colspan="3" width="5%">Aug</th>

							    <th colspan="3" width="5%">Sep</th>

							    <th colspan="3" width="5%">Oct</th>

							    <th colspan="3" width="5%">Nov</th>

							    <th colspan="3" width="5%">Dec</th>

							  </tr>

							  <tr>

							      <th colspan="2"></th>

							      <th>Gold in <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							      <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold<br>(Grams)</th>

							      <th>Silver<br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold<br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							      <th>Gold <br>(Grams)</th>

							      <th>Silver <br>(Grams)</th>

							       <th>MRP Items <br>(Rs)</th>

							  </tr>

							 

		                    </thead> 

		                    <tbody></tbody>

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

      



