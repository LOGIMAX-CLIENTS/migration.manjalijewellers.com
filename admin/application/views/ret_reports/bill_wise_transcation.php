<style>

        .stickyBlk {

		    margin: 0 auto;

		    top: 0;

		     max-width: 1200px

		    z-index: 999;

		    background: #fff;

		}

		/* Right-align numeric data columns */
		#bill_list td:nth-child(n+18):nth-child(-n+22),
		#bill_list td:nth-child(n+24):nth-child(-n+28),
		#bill_list td:nth-child(n+30):nth-child(-n+53) {
		    text-align: right;
		}

    </style>



  <!-- Content Wrapper. Contains page content -->



      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Sales Report</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Sales Report</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Bill Wise Transcation List</h3>  <span id="total_count" class="badge bg-green"></span>  

                 

                </div>

                 <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-12">  

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

								

								<div class="col-md-2"> 

									<div class="form-group tagged">

										<label>Select Area</label>

										<select id="select_village" class="form-control"></select>

									</div> 

								</div> 

								

							 

								

								<?php }else{?>

								<div class="col-md-2"> 

									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">

        		                    <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

								</div>

								<?php }?> 

								

									<div class="col-md-2"> 

									<div class="form-group tagged">

									<label>Select Floor</label>

										<select id="floor_sel" class="form-control" style="width:100%;"></select>

									</div> 

								</div>

								

								<div class="col-md-2"> 

    		                     <div class="form-group tagged">

    		                       <label>Select Counter</label>

    									<select id="counter_sel" class="form-control" style="width:100%;"></select>

    		                     </div> 

        		                </div>



								<div class="col-md-2"> 

    		                     <div class="form-group tagged">

    		                       <label>Select Metal</label>

    									<select id="metal" class="form-control" style="width:100%;"multiple></select>

    		                     </div> 

        		                </div>

							



								<div class="col-md-2"> 

    		                     <div class="form-group tagged">

    		                       <label>Select Category</label>

    									<select id="category" class="form-control" style="width:100%;"multiple></select>

    		                     </div> 

        		                </div>

								

							

							</div>

							<div class="col-md-2"> 

    		                     <div class="form-group tagged">

    		                       <label>Select Type</label>

    									<select id="bill_wise_type" class="form-control" style="width:100%;">

										<option value="0"> All</option>

										<option value="1"> Sales</option>

										<option value="2"> Sales&Purchase</option>

										<option value="3"> Sales&Return</option>

										<option value="4"> Purchase</option>

										<option value="5"> Order Advance</option>

										<option value="6"> Advance</option>

										<option value="7"> Sales Return</option>

										<option value="8"> Credit Bill Payment</option>

										<option value="9"> Order Delivery</option>

										<option value="10"> Chit Proclose</option>

										<option value="11"> Repair Order </option>



									</select>

    		                     </div> 

        		                </div>

							<div class="row">

							<div class="col-md-2"> 



							

									 <div class="form-group">

            		                    <div class="input-group">

            		                        <br>

            		                       <button class="btn btn-default btn_date_range" id="rpt_date_picker">

            							    

            		                        <i class="fa fa-calendar"></i> Date range picker

            		                        <i class="fa fa-caret-down"></i>

            		                      </button>

										  <span  style="display:none;" id="rpt_from_date"></span>

            							<span  style="display:none;" id="rpt_to_date"></span>

										  <span id="reportrange"></span>

            		                    </div>

            		                 </div><!-- /.form group -->


								</div>

								<!--<div class="col-md-2"> 

									<label>Select Village</label>

									<select id="village_select" class="form-control" style="width:100%;"></select>

								</div>

								<div class="col-md-2"> 

									<label>Select Customer</label>

									<select id="cus_select" class="form-control" style="width:100%;"></select>

								</div>-->





								<div class="col-md-2"> 

    		                     <div class="form-group">

    		                       <label>Select Section</label>

    									<select id="section_select" class="form-control" style="width:100%;"></select>

    		                     </div> 

        		                </div>



								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="bill_wise_search" class="btn btn-info">Search</button>   

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

						  <h3 class="box-title">Sales Details</h3>

						  <div class="box-tools pull-right">

							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

						  </div>

						</div>

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive">

									  <table id="bill_list" class="table table-bordered table-striped text-center">

										 <thead>

            							  <tr  style="text-transform:uppercase;">

										    <th width="1%">S.No</th>

            							    <th width="1%">Bill No</th>

            							    <th width="1%">Bill Date</th>

											<th width="1%">Bill Type</th>

            							    <th width="1%">Customer</th>

											<th width="1%">Ref Customer</th>

											<th width="1%">Address</th>

            							    <th width="1%">Mobile</th>

											<th width="1%">Pan No</th>

											<th width="1%">Gst No</th>

											<th width="1%">Emp name</th>

            							    <th width="1%">Village</th>

											<th width="1%">Supplier</th>

            							    <th width="1%">Product</th>

											<th width="1%">Design</th>

											<th width="1%">Sub Design</th>

											<th width="1%">Section</th>

											<th width="1%">Counter</th>

											<th width="1%">Gross wt</th>

											<th width="1%">Less wt</th>

											<th width="1%">Net wt</th>

											<th width="1%">Dia wt</th>

											<th width="1%">Stone wt</th>

											<th width="1%">MC Type</th>

											<th width="1%">MC value</th>

											<th width="1%">Wastage</th>

											<th width="1%">Rate</th>

											<th width="1%">Stone Amt</th>

											<th width="1%">Dia Amt</th>

            							    <th width="1%">Tag No</th>

            							    <th width="1%">Piece</th>

            							    <th width="1%">sal wt</th>

            							    <th width="1%">disc</th>

            							    <th width="1%">Taxable Amt</th>

            							    <th width="1%">CGST</th>

            							    <th width="1%">GST</th>

            							    <th width="1%">IGST</th>

            							    <th width="1%">Sales Amt</th>

            							    <th width="1%">Pur Wt</th>

            							    <th width="1%">Pur Amt</th>

            							    <th width="1%">Ret Amt</th>

            							    <th width="1%">due</th>

            							    <th width="1%">Advance</th>

            							    <th width="1%">Handling Charges</th>

            							    <th width="1%">Net Amt</th>

            							    <th width="1%">cash</th>

            							    <th width="1%">card</th>

            							    <th width="1%">chq</th>

            							    <th width="1%">Net banking</th>

            							    <th width="1%">adv</th>

            							    <th width="1%">chit adj</th>

            							    <th width="1%">Rate Benefit</th>

            							    <th width="1%">Gift voucher</th>

               							    <th width="1%">total</th>

											<th width="1%">Narration</th>

											<th width="1%">Status</th>



            							  </tr>

		                            </thead> 

		                    <tbody></tbody>

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