  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Gift Master <span id="total_banks" class="badge bg-green"></span> 

            

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Master</a></li>

            <li class="active">Gift List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <div class="row">

                    

                    <div class="col-md-2">



                      <div class="form-group">   

                        <span id="gift_date_range" style="font-weight:bold;"></span>                  

                        <button class="btn btn-default btn_date_range" id="gift-dt-btn">

                          <span  style="display:none;" id="gift_list1"></span>

                          <span  style="display:none;" id="gift_list2"></span>

                          <i class="fa fa-calendar"></i> Date range picker

                          <i class="fa fa-caret-down"></i>

                        </button>  

                      </div>

	                  </div> 



                    <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?> 			

											<div class="col-md-2">

													<div class="form-group" >

													<label>Select Branch </label>

													<select id="branch_select" class="form-control"  style="width:180px; margin-left: 15px !important;" ></select>

													<input id="id_branch" name="scheme[id_branch]"  type="hidden" value=""/>

												</div>

											</div>

										<?php }else{?>

											<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

										<?php }?>



                    <div class="col-md-2">

												<div class="form-group" >

													<label>Gift Name</label>									

													<select id="gift_name_select" class="form-control" style="width:180px; margin-left: 15px !important;"></select>

													<input id="id_gift_name_select"  name="id_gift_select" type="hidden" value="" />

												</div>

										</div>



                      <br/>

                    <div class="col-md-2">

                      <div class="form-group">  
                      <?php if($access['add']==1){?>
                        <a href="#" class="btn btn-success pull-right" data-href="index.php/settings/gift/add" id="new_gift_add" data-toggle="modal" data-target="#gift_form" onclick="set_gift_form('')"><i class="fa fa-plus" ></i> Add</a>
                        <?php }?>
                      </div>

                    </div>

                  </div>

                </div><!-- /.box-header -->

                <div class="box-body">

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

                  <div class="table-responsive">

                  <table id="gift_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>ID</th>

                        <th>Gift</th>

                        <th>Branch</th>

                        <th>Total Stock</th>

                        <th>Out Stock</th>

                        <th>Balance Stock</th>

                        <th>Status</th>                                           

                        <th>Action</th>

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

        <h4 class="modal-title" id="myModalLabel">Delete Gift</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this gift?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      





<div class="modal fade" id="gift_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog" style="width: 700px;">

    <div class="modal-content">

    <div class="modal-header" style= "border:0px;">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel" href="#"><span id="gift_form_title" style="font-weight:bold;"></span></h4>

      </div>

      <div class="modal-body">

            

              <div class="box-header with-border">

              <!-- <h3 class="box-title">Gift</h3> -->

			  <input type="hidden" id="id_gift_modal" name="gift[id_gift]" value="">

			   <input type="hidden" id="form_type" value="<?php echo ( $gift['id_gift']!=NULL?'Edit' :'ADD'); ?>">

            </div>

            <div class="box-body">

                    <div class="row">

                    <div class="form-group">

                    <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Gift Name <span class="error">*</span></label>

                        <div class="col-md-4">

                            <input type="text" class="form-control"  name="gift[gift_name]" id="gift_name_modal" value="" placeholder="Enter Gift name" required="true" autofocus>

                            <p class="help-block"></p>

                        </div>

                    </div>

                    </div>

					

					<div class="row">

                    <div class="form-group">

                    <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Type<span class="error">*</span></label>

                        <div class="col-md-4">

                            <input type="radio" name="gift[gift_type]" class="gift_type" id="gift_coin" value="1">Coins

							<input type="radio" name="gift[gift_type]" class="gift_type" id="gift_bar" value="2">Bar

							<input type="radio" name="gift[gift_type]" class="gift_type" id="gift_jewel" value="3">Jewel

							<input type="radio" name="gift[gift_type]" class="gift_type" id="gift_other" value="4">Others

                            <p class="help-block"></p>

                        </div>

                    </div>

                    </div>

					

					<div class="row">

                    <div class="form-group">

                    <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Metal</label>

                        <div class="col-md-8">

                            <select id="metal_select" name="gift[metal]"></select>

                            <p class="help-block"></p>

                        </div>

                    </div>

                    </div>

					

					<div class="row">

                    <div class="form-group">

                    <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Net Weight</label>

                        <div class="col-md-4">

                            <input type="number" class="form-control"  name="gift[weight]" id="gift_weight" value="" placeholder="Enter Gift weight" required="true">

                            <p class="help-block"> (in grms) </p>

                        </div>

                    </div>

                    </div>

					

		            <div class="row">

                    <div class="form-group">

                      <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Quantity<span class="error">*</span></label>

                          <div class="col-md-4">

                              <input type="number" class="form-control"  name="gift[gift_qty]" id="gift_qty" value="" placeholder="Enter Gift quantity">

                              <p class="help-block"></p>

                          </div>

                    </div>

                  </div>

                    <input type="hidden" id="gift_branch_settings" value="<?php echo $this->session->userdata('branch_settings'); ?>" />

                    <input type="hidden" id="gift_id_branch" value="<?php echo $this->session->userdata('id_branch'); ?>" />

                    <input type="hidden" id="gift_branch_wise" value="<?php echo $this->session->userdata('branchWiseLogin'); ?>" />

                    <input type="hidden" id="branchwise_cus_reg" value="<?php echo $this->session->userdata('is_branchwise_cus_reg'); ?>" />

                    <?php if(($this->session->userdata('branch_settings')==1)  && ($this->session->userdata('id_branch')=='')&&($this->session->userdata('is_branchwise_cus_reg')!=1) ){?>

                        <div class="row">

                          <div class="form-group">

                                <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Branch<span class="error">*</  span></label>

                                <div class="col-md-8">

                                    <select id="branch_form_select" name="gift[branch]"></select>

                                    <p class="help-block"></p>

                                </div>

                            </div>

                        </div>

                      <?php } ?>

                      

                      <div class="row">

                        <div class="form-group">

                             <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Status<span class="error">*</span></label>

                              <div class="col-md-4">

                                  <input type="checkbox" id="active" data-on-text="YES"

                                   data-off-text="NO" name="gift[status]" />

                                  <p class="help-block"></p>

                              </div>

                        </div>

                      </div>

                      

                    <br/>      

                    <div class="row col-xs-12">

                        <div class="box box-default"><br/>

                        <div class="col-xs-offset-5">

                        <button type="submit" class="btn btn-primary" id="gift_modal_submit">Save</button>

                        <button type="button" class="btn btn-default" id="gift_cancel">Cancel</button>

                        </div> <br/>

                        </div>

                     </div>      

              

             

            </div><!-- /.box-body -->

           <!-- <div class="box-footer">

             

            </div>--><!-- /.box-footer-->

          

         

      </div>

     <!--<div class="modal-footer">

      <a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>-->

    </div>

  </div>

 

</div>









<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Gift</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this gift?</strong>

      </div>

      <div class="modal-footer">

      <a href="" class="btn btn-danger btn-confirm">Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>