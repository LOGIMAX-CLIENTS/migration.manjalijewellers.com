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

								

                                <div class="col-md-3"> 

									<div class="form-group tagged">

										<label>Select Zone</label>

										<select id="select_zone" class="form-control"></select>

									</div> 

								</div> 



								<div class="col-md-3"> 

									<div class="form-group tagged">

										<label>Select Area</label>

										<select id="select_village" class="form-control"></select>

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

									<label></label>

									<div class="form-group">

									 <?php if($access['add']==1){?>

										<button type="button" id="set_vip_cus" class="btn btn-success">Mark as VIP</button>   
										<?php }?>
									</div>

								</div>

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="customer_search" class="btn btn-info">Search</button>   

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

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive">

								   <table id="telecalling_list" class="table table-bordered table-striped text-center">

                               <thead>

                                   <tr>

                                       <th rowspan="2" scope="col"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>  

                                       <th rowspan="2" scope="col">Customer Name</label></th>  

                                       <th rowspan="2" scope="col">Mobile</th>

                                        <th rowspan="2" scope="col">VIP</th>

                                       <th rowspan="2" scope="col">Village</th>

									   <th width="15%" rowspan="2" scope="col">Zone</th>

                                       <th rowspan="2" scope="col">Branch</th>

                                       <th rowspan="2" scope="col">No.of Estimation</th>

                                       <th rowspan="2" scope="col">No.of Bills</th>

                                       <th width="15%" rowspan="2" scope="col">Last Bill Date</th>

                                       <th colspan="3" scope="col">Purchase</th>

                                       <th rowspan="2">Total Chits</th>

                                       <th rowspan="2">Active Chits</th>

                                       <th rowspan="2">InActive Chits</th>

                                       <th rowspan="2">Closed Chits</th>

                                       <th rowspan="2">Whatsapp</th>

                                       <th rowspan="2">Feedback</th>

                                   </tr>

                                   <tr>

                                       <th scope="col">Gold<br>(Grams)</th>

                                       <th scope="col">Silver<br>(Grams)</th>

                                       <th scope="col">MRP Items<br>(Rs)</th>

                                       

                                   </tr>

                               </thead>

                         

                           </table>

								  </div>

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



	  <div class="modal fade" id="modal-feedback" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

          <h4 class="modal-title" id="myModalLabel">Enter Feedback</h4>

        </div>

      <div class="modal-body">

	  <div class="row" >

					<div class="col-md-offset-1 col-md-10" id='error'></div>

            </div>

              <div class="row">

              <form id="feedback_form">

                <div class="form-group" id='feedback_content'>

                  

                </div>

                </form>

              </div>  

            </div>

      <div class="modal-footer">

      	<a href="#" id="add_feedback" class="btn btn-success" data-dismiss="modal" >Save</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>