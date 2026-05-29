 <!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<section class="content-header">

	  <h1>

	    Tagging

		 <small>Tag Mark</small>

	  </h1>

	  <ol class="breadcrumb">

	    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

	    <li><a href="#">Tagging</a></li>

	    <li class="active">Tag Mark</li>

	  </ol>

	</section>



	<!-- Main content -->

	<section class="content">

	  <div class="row">

	    <div class="col-xs-12">

	       <div class="box box-primary">

		    <div class="box-header with-border">

	          <h3 class="box-title">Tag List</h3>  <span id="total_count" class="badge bg-green"></span>   

	        </div>

	         <div class="box-body">  

	          <div class="row">

			  	<div class="col-md-12">  

	              <div class="box box-default">  

	               <div class="box-body">  

					   <div class="row">

							<div class="col-md-2"> 

								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="form-group tagged">

									<label>Select Branch</label>

									<select id="branch_select" class="form-control branch_filter" style="width:100%;"></select>

									<input type="hidden" id="id_branch"  value=""> 

								</div> 

								<?php }else{?>

									<input type="hidden" id="id_branch"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

								<?php }?> 

							</div>  

							<div class="col-md-2"> 

							<div class="form-group">    

							<label>Select Metal</label> 

							<select id="metal" class="form-control" style="width:100%;"></select>

							</div> 

							</div>

							<div class="col-md-2"> 

							<div class="form-group">    

								<label>Select Category</label> 

								<select id="category" class="form-control" style="width:100%;"></select>

							</div> 

							</div>

							<div class="col-md-2"> 

									<div class="form-group">    

										<label>Select Product</label> 

										<select id="prod_select" class="form-control" style="width:100%;"></select>

									</div> 

							</div>

						


				

						    <div class="col-md-1"> 

									<div class="form-group">    

										<label>Est No</label> 

										<input type="text" class="form-control" id="est_no" placeholder="Enter Est No">

									</div> 

							</div>

							<div class="col-md-2"> 

									<div class="form-group">    

										<label>Filter By</label> 

										<select class="form-control" id="filter_by">

										    <option value="0">Normal Tag</option>

										    <option value="1">Green Tag</option>

										</select>

									</div> 

							</div>

							

							<div class="col-md-1"> 

								<label></label>

								<div class="form-group">

									<button type="button" id="tag_mark_search" class="btn btn-info">Search</button>   

								</div>

							</div>

						</div></br>

					

    					<div class="col-md-offset-1 row">

    					    <?php if($this->session->userdata('profile')==1 || $this->session->userdata('profile')==2 || $this->session->userdata('profile')==3 || $this->session->userdata('profile')==15){?>

                             <div class="col-md-6">

                                 <div class="btn-group" data-toggle="buttons">

        					        <label class="btn btn-success" id="approve">

        					            <input type="radio" name="upd_status_btn" value="1"><i class="icon fa fa-check"></i> Mark Green Tag

        					        </label>

        					        <label class="btn btn-danger" id="reject">

        					            <input type="radio" name="upd_status_btn"  value="0"><i class="icon fa fa-remove"></i> Unmark Green Tag

        					        </label>

        						</div>

                            </div>  

                            <?php }?>

                             <div class="col-md-2">

						 		    <div class="btn-group" data-toggle="buttons">

							 			<button type="button" class="btn btn-success" id="tag_print" >Tag Print</button>

									</div>

						    </div>

                       </div></br>

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

		  

	           <div class="row without_bill_detail">

	               <div class="col-md-12">

	               	<div class="table-responsive">

	                 <table id="tagging_list" class="table table-bordered table-striped text-center">

	                    <thead>

						  <tr> 

						    <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>  

							<th>Metal</th>

							<th>Category</th>

						    <th>Product</th>

						    <th>Tag Date</th>

						    <th style="text-align:right;">Gross Wt(g)</th>

						    <th style="text-align:right;">Net Wt(g)</th>

						    <th>Tag Status</th>

						    <th>Green Mark</th>
						    <th>Marked Date</th>
						    <th>Marked By</th>

						  </tr>

	                    </thead> 

	                     <tbody> 

	                </tbody>

						   

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

      



