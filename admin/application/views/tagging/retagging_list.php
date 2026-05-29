  <!-- Content Wrapper. Contains page content -->

  <style>

  	.custom-label{

		font-weight: 400;

	}

  </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Stock Process

          </h1>

        </section>

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               <div class="box box-primary">

                   

                   <div class="box-header with-border">

                      <div class="pull-right">

                      <?php if($access['add']==1){?>
                      	<a class="btn btn-success" id="add_tagging" href="<?php echo base_url('index.php/admin_ret_tagging/retagging/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
                        <?php }?>
    				  </div>

                   </div>

                

                   

                 <div class="box-body">  

                  <div class="table-responsive">

	                 <table id="process_list" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th width="5%">#</th>

                          <th width="5%">Lot No</th>

	                        <th width="10%">Date</th>

                          <th width="10%">Branch</th>

                          <th width="5%">Type</th>

	                        <th width="5%">Process</th>

                          <th width="5%">Karigar</th>

                          <th width="5%">Category</th>
                          
	                        <th width="5%">Purity</th>

                          <th width="5%">Product</th>

                          <th width="5%">Design</th>

                          <th width="5%">Sub Design</th>

                          <th width="5%">G.wt</th>

                          <th width="5%">L.wt</th>

                          <th width="5%">N.wt</th>


                          <th width="5%">Employee</th>

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

