      <!-- Content Wrapper. Contains page content -->

    <style>

    	.remove-btn{

			margin-top: -168px;

		    margin-left: -38px;

		    background-color: #e51712 !important;

		    border: none;

		    color: white !important;

		}

    </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

        	Tagging

            <small>Tag</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Tagging</a></li>

            <li class="active">Tag</li>

          </ol>

        </section>

        <!-- Main content -->

        <section class="content product">

          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Duplicate Tagging</h3>

              <div class="box-tools pull-right">

                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

              </div>

            </div>

            <div class="box-body">

             <!-- form container -->

              <div class="row">

				<div class="col-sm-12"> 

					<!-- Lot Details Start Here -->

					<div class="row">				    	

						 

						 		<div class="col-md-2">

						 			<div class="form-group">

						 				<label><a data-toggle="tooltip" title="Branch">Select Branch<span class="error">*</span></a></label>

										<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){ ?>
										<select id="branch_select" class="form-control" required style="width:100%;"></select>

										<input id="id_branch" name="id_branch" type="hidden" />


						 		
								<?php } else { ?>

							<input type="text" class="form-control" readonly value="<?php echo $this->session->userdata('branch_name'); ?>" placeholder="Branch">

							<input id="id_branch" name="id_branch" type="hidden" value="<?php echo $this->session->userdata('id_branch'); ?>">

						<?php } ?>

					</div>

			 		</div>

						 		<div class="col-md-2">

							 			<div class="form-group">

							 				<label><a  data-toggle="tooltip" title="Enter Lot">Select Lot</a></label>

								 			<select id="lot_id" class="form-control" style="width:100%;"></select>

								 			<input type="hidden" id="tag_lot_id" name="">

										</div>

							 	</div>

						 		<div class="col-md-2">

							 			<div class="form-group">

							 				<label><a  data-toggle="tooltip" title="Enter Product">Select Product<span class="error">*</span></a>  </label>

								 			<select id="prod_select" class="form-control" style="width:100%;"></select>

								 			<input type="hidden" id="id_product" name="">

										</div>

							 	</div>

							 	<div class="col-md-2">

							 			<div class="form-group">

							 				<label><a  data-toggle="tooltip" title="Enter Design">Select Design</a>  </label>

								 			<select class="form-control" id="des_select" style="width:100%;"></select>

										</div>

							 	</div>

							 	<div class="col-md-2">

							 			<div class="form-group">

							 				<label><a  data-toggle="tooltip" title="Enter Sub Design">Select Sub Design</a>  </label>

								 			<select class="form-control" id="sub_des_select" style="width:100%;"></select>

										</div>

							 	</div>

						 		<div class="col-md-2">

						 			<div class="form-group">

						 				<label><a data-toggle="tooltip" title="Select Tag No">Select Tag Code</a></label>

							 			<input type="text" class="form-control" id="tag_no" name="tag_no" placeholder="Search Tag Code">

										<input id="tag_id" name="tagging[tag_id]" type="hidden" />

										<div  id="tagAlert" name=""></div>

									</div>

						 		</div>

								 <div class="col-md-2">

									<div class="form-group">

										<label><a data-toggle="tooltip" title="Select Tag No">Select Old Tag Code</a></label>

										<input type="text" class="form-control" id="old_tag_no" name="old_tag_no" placeholder="Search Old Tag Code">

									   <input id="old_tag_id" name="tagging[old_tag_id]" type="hidden" />

									 <div  id="tagAlert" name=""></div>

									</div>

									</div>

						 		<div class="col-md-2">

							 		<div class="form-group">

							 			<label><a  data-toggle="tooltip" title="From Weight">From weight</a>  </label>

							 			<input class="form-control" type="number" name="from_weight" id="from_weight" placeholder="Enter From Weight">

							 		</div>

							 	</div>

							 	<div class="col-md-2">

							 		<label><a  data-toggle="tooltip" title="From Weight">To weight</a>  </label>

							 		<div class="form-group">

							 			<input class="form-control" type="number" name="to_weight" id="to_weight" placeholder="Enter To Weight">

							 		</div>

							 	</div>

						 		<!--<div class="col-md-2">

						 			<div class="form-group">

							 			<button type="button" class="btn btn-success" id="add_tag" style="margin-top:20px;">Add Tag</button>

									</div>

						 		</div>-->

							 	

							 	

				 	</div> 	

				 	<div class="row">

				 		<div class="col-sm-2">

							<button class="btn btn-warning" id="get_duplicate_tag" >Apply Filter</button>

						</div>

				 	</div>		 

				 	<p class="help-block"></p>			 

				</div>	<!--/ Col --> 

			</div>	 <!--/ row -->

			<div class="table-responsive">

	                 <table id="tagging_list" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th width="5%"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>     

	                        <th width="5%">Tag Code</th>

							<th width="5%">Old Tag Code</th>

							<th width="5%">Lot No</th>

							<th width="5%">Product Name</th>

							<th width="5%">Design Name</th>

							<th width="5%">Sub Design Name</th>

	                        <th width="5%" style="text-align:right;">Gross Wgt</th>

	                        <th width="10%" style="text-align:right;">Net Wgt</th>

	                      </tr>

	                    </thead> 

	                    <tbody></tbody>

	                 </table>

                  </div>		

	

			   <p class="help-block"> </p> 

				   <div class="row">

					   <div class="box box-default"><br/>

						  <div class="col-xs-offset-5">

						  	<button type="" class="duplicate_print btn btn-primary" data-prn="0">Submit</button> 

							  <?php if($access['add']==1){?>

							<button type="" class="duplicate_print btn btn-primary" data-prn="1">Generate PRN</button> 
							<?php }?>
						  </div> <br/>

						</div>

					  </div>

	            </div> 

	            <div class="overlay" style="display:none;">

				  <i class="fa fa-refresh fa-spin"></i>

	            </div>

	             <!-- /form -->

	          </div>

             </section>

     </div>

     

<div id="otp_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

      <div class="modal-content">

        <div class="modal-header ">

        

      <button type="button" id="close_model" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

      <h3 id="myModalLabel">Mobile Number Verification</h3>

      </div>

        <div class="modal-body">

          <p>Please enter the code sent to your mobile number</p>

          <div>

          <label style="display:inline; margin:5px" for="otp">Enter Code:</label>

          <input  style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required/>

           <input style="margin-left:1%" type="submit" value="Verify" id="verify_otp" style="background-color:#0079C0"  class="button btn btn-primary" />

          <span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>

        </div>

        <span id="otp_alert"></span>

        <div class="modal-footer">

		       <input type="submit" id="tagResend" value="Resend OTP" class="resendotp" style="display:none;" />

        </div>

        </div>

      </div>

    

    </div>


  </div>

            