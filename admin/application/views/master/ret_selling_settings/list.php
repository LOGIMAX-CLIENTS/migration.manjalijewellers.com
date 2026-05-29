  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           V.A & MC Settings

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Master</a></li>

            <li class="active">Product</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

            <input type="hidden" id="is_va_mc_based_on_branch" name="is_va_mc_based_on_branch"  value="<?php echo set_value('is_va_mc_based_on_branch',$is_va_mc_based_on_branch); ?>" >

               <div class="box box-primary">

			    <div class="box-header with-border">

                  

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

    			   <div class="row">

                        <div class="col-md-2"> 

                            <div class="form-group">

                                <label>Select Product</label>

                                <select id="des_prod_name" class="form-control" style="width:100%;"></select>

                            </div> 

                        </div>

                        <div class="col-md-2"> 

                            <div class="form-group">

                                <label>Select Design</label>

                                <select id="des_des_name" class="form-control" style="width:100%;"></select>

                            </div> 

                        </div>

                        <div class="col-md-2"> 

                            <div class="form-group">

                                <label>Select Sub Design</label>

                                <select id="select_sub_design" class="form-control" style="width:100%;"></select>

                            </div> 

                        </div>



                        <?php if($is_va_mc_based_on_branch == 1  ){ ?>

                            <div class="col-md-2"> 

                                <div class="form-group">

                                <label>Select Branch  </label>

                                        <select id="branch_filter" name="settings[branch][]" class="form-control ret_branch"></select>

                                        <input type="hidden" id="id_branch" name="id_branch"  value="" >

                                </div>

                            </div>

                         <?php } ?>



                        <div class="col-md-2"> 

                            <div class="form-group">

                                <label></label></br>

                                <button type="button" id="search_weight_range" class="btn btn-info">Search</button>   

                            </div> 

                        </div>



                        <div class="col-md-2 pull-right">

                        <div class="form-group">

                                <label></label></br>
                        <?php if($access['add'] ==1){ ?>
                                <a class="btn btn-success pull-right" id="add_product" href="<?php echo base_url('index.php/admin_ret_catalog/wastage_mc_settings/add');?>" ><i class="fa fa-plus-circle"></i> Add</a>  
                        <?php }?>
                            </div> 

                  	       

				             </div>

                        

                  </div>

                                        

                  <div class="table-responsive">

	                
                <table id="item_list" class="table table-bordered table-striped text-center">

                <thead>

                  <tr>

                    <th>ID</th>

                    <th>Product</th>

                    <th>Design</th>

                    <th>Sub Design</th>

                <?php if($is_va_mc_based_on_branch == 1){ ?><th>Branch</th><?php } ?>

                    <th>Type</th>

                    <th>VA Type</th>

                    <th>Min V.A (%)</th>

                    <th>Min V.A (Wt(g))</th>

                    <th>Max V.A (%)</th>

                    <th>Max V.A (Wt(g))</th>

                    <th>MC Type</th>

                    <th>Min MC </th>

                    <th>Max MC </th>

                    <th>Margin MRP</th>

                    <th>Action</th>

                  </tr>

                </thead> 



                </table>

                  </div>

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none;">

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

        <h4 class="modal-title" id="myModalLabel">Delete Settings</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Settings?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      



<div class="modal fade" id="wastage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

   <div class="modal-dialog" style="width: 70%;">

      <div class="modal-content">

         <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

            <h4 class="modal-title">Weight Range</h4>

         </div>

         <div class="modal-body">

            <div class="row">

               <p class="help-block"></p>

               </legend>

               <table id="wcdetail" class="table table-bordered table-striped text-center">

                  <thead>

                     <tr>

                        <th>Design Id</th>

                        <th>From Weight</th>

                        <th>To Weight</th>

                        <th>Wastage Type</th>

                        <th>Min V.A (%)</th>

                        <th>Min V.A (Wt(g))</th>

                        <th>Max V.A (%)</th>

                        <th>Max V.A (Wt(g))</th>

                        <th>Min MC </th>

                        <th>Max MC </th>

                        <th>Action</th>

                     </tr>

                  </thead>

                  <tbody></tbody>

               </table>

            </div>

         </div>

      </div>

   </div>

</div>

