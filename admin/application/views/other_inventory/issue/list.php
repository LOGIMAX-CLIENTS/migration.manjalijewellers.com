  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Packaging Item Issue

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Packaging Item Issue List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                <div class="box-header with-border">

                  <h3 class="box-title">Issue List</h3></span>      
                  <?php if($access['add']==1){?>
                           <a class="btn btn-success pull-right" id="add_issue_details" href="#"data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                           <?php }?>
                </div><!-- /.box-header -->

                <div class="box-body">

                <!-- Alert -->

                <?php 

                	if($this->session->flashdata('chit_alert'))

                	 {

                		$message = $this->session->flashdata('chit_alert');

                    ?>

                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

	                    <?php echo $message['message']; ?>

	                  </div>

	                  

	            <?php } ?>

				  <div class="row">

					<div class="col-sm-10 col-sm-offset-1">

					<div id="chit_alert"></div>

					</div>

				  </div>

				  

				  <div class="row">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

						        

						        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

						        <div  class="col-md-4">

						           <label>Branch</label>

						            <select class="form-control branch_filter" style="width:100%;"></select>

						        </div>

						        <?php }else{?>

						            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

						        <?php }?>

						        <div class="col-md-3"> 

									<div class="form-group">

                                            <div class="input-group">

                                                <br>

                                                <button class="btn btn-default btn_date_range" id="date_range_picker">

                                                    <i class="fa fa-calendar"></i> Date range picker

                                                    <i class="fa fa-caret-down"></i>

                                                </button>

                                                <span style="display:none;" id="from_date"></span>

                                                <span style="display:none;" id="to_date"></span>

                                            </div>

                                        </div><!-- /.form group --> 

								</div>

								<div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="search_issue_item" class="btn btn-info">Search</button>   

									</div>

								</div>

							</div>

						 </div>

	                   </div> 

	                  </div> 

                   </div>

				

                  <div class="table-responsive">

                  <table id="issue_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr style="text-transform:uppercase;">

                        <th>ID</th>

                        <th>Branch</th>

					    <th>Item Name</th>

					    <th>Issue Date</th>

                        <th>Bill No</th>

                        <th>Customer</th>

                        <th>No of Pieces</th>

                        <th>Approx Amount</th>

                        <th>Given By</th>

                        <th>Remarks</th>

                      </tr>

                 	</thead>

                 	<tfoot><tr style="font-weight:bold;"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>

                  </table>

                  </div> 

                  

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      



<!-- modal -->      

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete item</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this item?</strong>

      </div>

      <div class="modal-footer">

       	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->  

<!-- modal -->    





<!-- modal -->      

<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Issue Details </h4>

      </div>

      <div class="modal-body">

          <form id="inventory_issue">

			 <div class="row">

			 	<div class="form-group">

                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Branch<span class="error">*</span></label>

                   <div class="col-md-5">

                       	<?php if($this->session->userdata('id_branch') == "") { ?>

                   	            <select class="form-control"  id="branch_select" name="issue[id_branch]"  style="width:100%;"></select>

                   	            <input type="hidden" id="id_branch" />

                   	    <?php }else{?>

                   	    <label><?php echo $this->ret_other_inventory_model->get_currentBranchName($this->session->userdata('id_branch')); ?> </label>

                   	    <input type="hidden" id="id_branch" name="issue[id_branch]"  value="<?php echo $this->session->userdata('id_branch');?>" />

                   	    <?php }?>

            	     <p class="help-block"></p>

                   </div>

                </div>

			 </div>

			 

			 <div class="row">

			 	<div class="form-group">

                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Item<span class="error">*</span></label>

                   <div class="col-md-5">

                   	 <select class="form-control" name="issue[id_other_item]" id="select_item" style="width:100%;"></select>

            	     <p class="help-block"></p>

                   </div>

                </div>

			 </div>

			 

			 <div class="row">

			 	<div class="form-group">

                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Bill No</label>

                   <div class="col-md-5">

                      <select class="form-control" name="issue[bill_id]" id="select_bill_no" style="width:100%;"></select>

            	     <p class="help-block"></p>

                   </div>

                </div>

			 </div>

			 

			 <div class="row">

			 	<div class="form-group">

                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">No of Piece<span class="error">*</span></label>

                   <div class="col-md-5">

                      <input type="number" name="issue[total_pcs]" class="form-control" id="issue_total_pcs">

            	      <p class="help-block"></p>

                   </div>

                </div>

			 </div>

			  

			<div class="row">   

                <div class="form-group">

                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Description<span class="error">*</span></label>

                   <div class="col-md-5">

                   	 <textarea  class="form-control" name="issue[remarks]" id="remarks" name="remarks" rows="5" cols="100"> </textarea>

            	  <p class="help-block"></p>

                   	

                   </div>

                </div>

			 </div>

		</form>

      </div>

      <div class="modal-footer">

      	<a href="#" id="item_issue" class="btn btn-warning" >Save & Close</a>

        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->

