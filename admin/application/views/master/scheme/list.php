  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Scheme Details

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Scheme List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Scheme List</h3>    <span id="total_schemes" class="badge bg-green"></span>   
                           <?php if($access['add']==1){?>
                           <a class="btn btn-success pull-right" id="add_scheme" href="<?php echo base_url('index.php/scheme/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
                           <?php }?>
                </div><!-- /.box-header -->

                <div class="box-body">

                <!-- Alert -->

                <?php 

                	if($this->session->flashdata('sch_info'))

                	 {

                		$message = $this->session->flashdata('sch_info');

                ?>

                       <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

	                    <?php echo $message['message']; ?>

	                  </div>

	                  

	            <?php } ?>      

                  <div class="table-responsive">

                  <table id="scheme_list" class="table grid table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Code</th>

                        <th>Type</th>                        

                        <th>Amount (<?php echo $this->session->userdata('currency_symbol')?>)</th>

                        <th>Installments</th>

                        <th>Advance</th>

                        <th>Advance Months</th>

                        <th>Status</th>

                        <th>Action</th>

                      </tr>

                    </thead>

                 <tfoot>

                      <tr>

                        

                      </tr>

                    </tfoot> 

                  </table>

                  </div>

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

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

        <h4 class="modal-title" id="myModalLabel">Delete Scheme</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this scheme?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      

