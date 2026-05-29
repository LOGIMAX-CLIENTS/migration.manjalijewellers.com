  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

          Re-Order level report

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Stock In & Out Report</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                

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

				 <div class="box-body">

                        <div class="row">

                            <div class="col-md-offset-2 col-md-8">

                                <div class="box box-default">

                                    <div class="box-body">

                                        <div class="row">

                                            

                                            <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

                    		                  <div class="col-md-3"> 

                    		                     <div class="form-group tagged">

                    		                       <label>Select Branch</label>

                    									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>

                    		                     </div> 

                    		                  </div> 

                    						    <?php }else{?>

                    		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">

                    		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

                    		                  <?php }?>

        		                  

                                              <div class="col-md-2">

                                                <label></label>

                                                <div class="form-group">

                                                    <button type="button" id="reorder_details_search" class="btn btn-info">Search</button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                </div>

				

                  <div class="table-responsive">

                      <table id="reorder_details" class="table table-bordered table-striped text-center">

                        <thead>

                          <tr>

                            <th width="10%;">Branch</th>

                            <th width="10%;">Item</th>

                            <th width="10%;">Min Pcs</th>

                            <th width="10%;">Max Pcs</th>

                            <th width="10%;">Available Pcs</th>

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

