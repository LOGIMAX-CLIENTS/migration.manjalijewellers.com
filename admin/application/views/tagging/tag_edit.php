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

              <h3 class="box-title">Tagging Edit</h3>

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

					            <?php if($this->session->userdata('branch_settings')==1){?>

						 		<div class="col-md-2">

						 			<div class="form-group">

						 				<label><a data-toggle="tooltip" title="Branch" style="width:100%;">Select Branch</a><span class="error">*</span></label>

							 			<select id="branch_select" class="form-control" required></select>

									</div>

						 		</div>

						 		<?php }?>

						 		

						 		<div class="col-md-2">

    					 			<div class="form-group">

    					 				<label><a  data-toggle="tooltip" title="Enter Product">Select Product</a></label>

    						 			<select id="prod_select" class="form-control" style="width:100%;"></select>

    						 			<input type="hidden" id="id_product" name="">

    								</div>

							 	</div>

							 	

							 	<div class="col-md-2">

    					 			<div class="form-group">

    					 				<label><a  data-toggle="tooltip" title="Enter Product">Select Design</a></label>

    						 			<select id="des_select_filter" class="form-control" style="width:100%;"></select>

    						 			<input type="hidden" id="id_design" name="">

    								</div>

							 	</div>

							 	

							 	<div class="col-md-2">

    					 			<div class="form-group">

    					 				<label><a  data-toggle="tooltip" title="Enter Product">Select Design</a></label>

    						 			<select id="sub_des_filter" class="form-control" style="width:100%;"></select>

    						 			<input type="hidden" id="id_design" name="">

    								</div>

							 	</div>

						 		

					            <div class="col-md-2">

    					 			<div class="form-group">

    					 				<label><a  data-toggle="tooltip" title="Enter Tag No">Tag No </a></label>

    						 			<input type="text" class="form-control" id="tag_code"  placeholder="Enter Tag Code">

    								</div>

							 	</div>

							 	

							 	 <div class="col-md-2">

    					 			<div class="form-group">

    					 				<label><a  data-toggle="tooltip" title="Enter Tag No">Est No </a></label>

    						 			<input type="number" class="form-control" id="est_no" placeholder="Enter Est No">

    								</div>

							 	</div>

							 	

							 	

						 	    <div class="col-md-2">

    					 			<div class="form-group">

    					 				<label><a  data-toggle="tooltip" title="Enter Lot">Select Lot</a></label>

    						 			<select id="tag_edit_lot" class="form-control" style="width:100%;"></select>

    						 			<input type="hidden" id="tag_lot_id" name="">

    								</div>

							 	</div>

							 	<div class="col-sm-2">

							 	    <label><a  data-toggle="tooltip" title="Get Tag Details">Get Tag Details</a></label>

        							<button class="btn btn-warning" id="tag_edit_filter" >Apply Filter</button>

        						</div>



				 	</div> 	

				 

				 	<div class="row">

				 	        <div class="col-md-2">

							 			<div class="form-group">

								 			<select id="des_select" class="form-control"></select>

								 			<input type="hidden" id="id_product" name="">

										</div>

							 </div>

							 <div class="col-md-2">

							 			<div class="form-group">

								 			<select id="sub_des_select" class="form-control" style="width:100%;"></select>

										</div>

							 </div>

							 <div class="col-md-2">

							 			<div class="form-group">

								 			<select id="select_size" class="form-control" style="width:100%;"></select>

								 			<input type="hidden" id="id_size" name="">

										</div>

							 </div>

							 

							 <div class="col-md-2">

                                	<div class="form-group">

                                		<input type="text" class="form-control" id="old_tag_id"  placeholder="Old Tag" value="">

                            		<input type="hidden" id="old_tag_id" value="">

                                	</div>

                            </div>	

							 	

                            <div class="col-sm-2">

							<?php if($access['edit']==1){?>

                                <button class="btn btn-success" id="update_tag_edit" >Update</button>
								
								<?php }?>
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

							<th width="5%">Lot No</th>

							<th width="5%">Product Name</th>

							<th width="5%">Design Name</th>

							<th width="5%">Sub Design</th>

	                        <th width="5%">Gross Wgt</th>

	                        <th width="10%">Net Wgt</th>

	                        <th width="10%">Size</th>

	                        <th width="10%">Old Tag</th>

	                      </tr>

	                    </thead> 

	                    <tbody></tbody>

	                 </table>

                  </div>		

	

			   <p class="help-block"> </p> 

	            </div> 

	            <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>



	             <!-- /form -->

	          </div>

             </section>

     

</div>

  

