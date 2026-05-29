<style>

        .stickyBlk {

		    margin: 0 auto;

		    top: 0;

		     max-width: 1200px

		    z-index: 999;

		    background: #fff;

		}

		

    </style>



  <!-- Content Wrapper. Contains page content -->



      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Customer Details</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Customer Details</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Customer Details</h3>  <span id="total_count" class="badge bg-green"></span>  

                 

                </div>

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-2"> 

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_select" class="form-control branch_filter"></select>

									</div> 

								</div> 

								

            



								<?php }else{?>

								<div class="col-md-3"> 

									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">

        		                    <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

								</div>

								<?php }?> 

								

								<div class="col-md-3"> 

									<div class="form-group">   

									       <label>Date</label>

										   <button class="btn btn-default btn_date_range" id="rpt_customer">	

											<span  style="display:none;" id="rpt_cus1"></span>

											<span  style="display:none;" id="rpt_cus2"></span>

											<i class="fa fa-calendar"></i> Date range picker

											<i class="fa fa-caret-down"></i>

											</button>  

									</div> 

								</div>

							

								<div class="col-md-2"> 

									<label>Vip</label><br>

									<div class="form-group">

										<select  id="vip_fil" style="width: 100px;">
											<option value=""></option>
											<option value="1">Yes</option>
											<option value="0">No</option>
										</select>   

									</div>

								</div>
								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="customer_detail_search" class="btn btn-info">Search</button>   

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

    				   	<div class="box box-info stock_details">

    						<div class="box-header with-border">

    						  <h3 class="box-title">Customer Details</h3>

    						  <div class="box-tools pull-right">

    							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

    						  </div>

    						</div>

                                    <div class="row">

            	                        <div class="col-md-12">

            	                   	        <div class="table-responsive">

                        		                 <table id="customer_details" class="table table-bordered table-striped text-center">

                                                       <thead>

                                                           <tr>

                                                              <th>Id</th>

                                                              <th>Name</th>

                                                              <th>Mobile</th>

                                                              <th>Email</th>

                                                              <th>Address1</th>

                                                              <th>Address2</th>

                                                              <th>Address3</th>

                                                              <th>village</th>

                                                              <th>City</th>

                                                              <th>State</th>

                                                              <th>panno</th>

                                                              <th>Aadhar No</th>

                                                              <th>DOB</th>

                                                              <th>Pincode</th>

                                                              <th>Wedding Date</th>
                                                              
															  <th>Vip Customer</th>
                                                             
															  <th>Vip Marked Emp</th>

                                                     </tr>

                                                       </thead>

                                                       <tbody></tbody>

                                                   </table>

            								  </div>

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



	