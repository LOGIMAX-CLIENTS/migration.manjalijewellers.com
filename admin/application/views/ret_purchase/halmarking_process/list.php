<!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Master

            <small>Halmarking Issue/ Receipt Details</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Purchase</a></li>

            <li class="active">Halmarking Issue/ Receipt Details</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Halmarking Issue/ Receipt Details</h3>  <span id="total_count" class="badge bg-green"></span>  

                  <div class="pull-right">

                    <?php if($access['add']==1 ){?>

                  	 <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/halmarking_issue_receipt/add');?>" ><i class="fa fa-plus-circle"></i>ISSUE</a> 

                  	 &nbsp;<a class="btn btn-primary pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/halmarking_issue_receipt/hm_receipt');?>" ><i class="fa fa-plus-circle"></i>RECEIPT</a> 

                     <?php }?>
				  </div>

                </div>

                 <div class="box-body">  

                   

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

			  

                  <div class="table-responsive">

	                 <table id="item_list" class="table table-bordered table-striped text-center">

	                    <thead>

    				          <tr>

    				            <th width="5%;">HM Ref No</th> 

    				            <th width="5%;">Issue Date</th> 

    				            <th width="5%;">Karigar</th> 

    				            <th width="5%;">Pcs</th> 

    				            <th width="5%;">Gwt</th> 

    				            <th width="5%;">Lwt</th> 

    				            <th width="5%;">Nwt</th> 

    				            <th width="5%;">H.M Charge</th> 

    				            <th width="5%;">Status</th> 

    				          </tr>

	                    </thead> 

	                 </table>

                  </div>

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

            

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

        <h4 class="modal-title" id="myModalLabel">Delete Order</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Order?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      