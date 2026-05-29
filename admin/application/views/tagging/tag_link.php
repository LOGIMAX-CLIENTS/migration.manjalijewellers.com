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

            <small>Order Link</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Tagging</a></li>

            <li class="active">Order Link</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content product">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Order Link</h3>

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

						 				<label><a data-toggle="tooltip" title="Branch" >Select Branch</a><span class="error">*</span></label>

							 			<select id="branch_select" class="form-control" required style="width:100%;"></select>

									</div>

						 		</div>

						 		<?php }?>

						 		<div class="col-md-2">

							 			<div class="form-group">

							 				<label><a  data-toggle="tooltip" title="Enter Lot">Search Order</a></label>

								 			<select id="select_order" class="form-control" required style="width:100%;"></select>

										</div>

							 	</div>

							 	

							 	<div class="col-md-2">

							 	    <div class="form-group" > 

							 	         <label><a data-toggle="tooltip" title="Financial Year" >Select Fiancial Year</a><span class="error">*</span></label>

							 			 <select class="form-control" id="order_fin_year_select" style="width:100px;">

    				 			            <?php 

    				 			            foreach($tagging['financial_year'] as $fin_year)

    				 			            {?>

    				 			                <option value=<?php echo $fin_year['fin_year_code'];?> <?php echo ($fin_year['fin_status']==1 ?'selected' :'')  ?> ><?php echo $fin_year['fin_year_name'];?></option>

    				 			            <?php }

    				 			            ?>

    				 			        </select>

									</div>

							 	</div>

							 	

				 	</div> 	

				 	<p class="help-block"></p>			 

				</div>	<!--/ Col --> 

			</div>	 <!--/ row -->

			<div class="table-responsive">

	                 <table id="order_details" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th width="5%"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>     

	                        <th width="5%">Order No</th>

							<th width="5%">Product</th>

							<th width="5%">Design</th>

							<th width="5%">Sub Design</th>

	                        <th width="10%">Order Weight</th>

	                        <th width="10%">Tag No</th>

							<th width="10%">Old Tag No</th>

	                        <th width="5%">Delete</th>

	                      </tr>

	                    </thead> 

	                    <tbody></tbody>

	                 </table>

                  </div>		

	

			   <p class="help-block"> </p> 

			     <div class="row">

				   <div class="box box-default"><br/>

					  <div class="col-xs-offset-5">

						<button type="submit"  class="btn btn-primary" id="tag_link_submit">Save All</button> 

						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

					  </div> <br/>

					</div>

				  </div> 

	            </div> 

	            <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>



	             <!-- /form -->

	          </div>

             </section>

     

</div>

  

