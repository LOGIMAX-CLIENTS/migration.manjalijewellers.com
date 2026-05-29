  

  <style>

  	/* CSS for Drill-down */

  	.collapsed {

	    display: none;

	}

	.close {

	    display: none;

	}

	.open {

	    display: block;

	}

	.detail {

	    background:#fdfdfd

	}

	/* .CSS for Drill-down */

  </style>

  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Master

            <small>Branch Transfer</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Master</a></li>

            <li class="active">Branch Transfer</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Branch Transfer Approval List</h3>  <span id="total_count" class="badge bg-green"></span>  

                  <!-- <a class="btn btn-success pull-right" id="add_bnk"  href="<?php echo base_url('index.php/admin_ret_brntransfer/branch_transfer/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->

                 

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

                  	<div class="col-md-offset-1 col-md-10">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

	                   	<div class="row">  

		                  <div class="col-md-1">

			                 <p class="lead">Filter </p>  

		                  </div>

		                  <div class="col-md-3">

			                 <label for="">Approval Type</label>

			                 <input type="hidden" id="form_secret" name="form_secret"  value="<?php echo get_form_secret_key(); ?>"> <!-- From Helper Class.. -->

		                     <div class="form-group"> 

								<input type="radio" name="bt_approval_type" id="appr_type1" value="1" checked> <label class="radio-label" for="appr_type1">Transit Approval</label>  &nbsp;&nbsp;<br/>

								<input type="radio" name="bt_approval_type" id="appr_type2" value="2"> <label class="radio-label" for="appr_type2">Stock Download</label>

				             </div>   

		                  </div>

		                  <?php if($this->session->userdata('branch_settings')==1){?>

		                     <div class="col-md-2 app_frm_brn">

		                        <div class="form-group">

		                          <label>From Branch</label>

		                            <select id="filter_from_brn" class="form-control from_branch"></select> 

		                        </div>   

	                         </div> 

	                       

	                         <div class="col-md-2 app_to_brn">  

		                        <div class="form-group">

		                          <label>To Branch </label>

		                          	<select id="filtr_to_brn" class="form-control filter_to_brn"></select> 

		                          	<!--<?php if($this->session->userdata('id_branch')==0){?>

		                            <select id="filtr_to_brn" class="form-control filter_to_brn"></select> 

		                            <?php }else{?> 

				                  	<div><?php echo $this->session->userdata('branch_name') ?></div>

				                    <input type="hidden" id="filtr_to_brn"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

				                  <?php }?>-->

		                        </div>  

		                    </div>

		                  <?php }?>

		                 

		                     

		                  <div class="col-md-2"> 

		                     <div class="form-group">    

		                   		  <label>Trans Code </label> 

		                   		  <input type="text" class="form-control pull-right" id="bt_trans_code" placeholder="Branch Transfer Code">  

		                     </div> 

		                  </div>

		                </div>

		                <div class="row">  

		                  <div class="col-md-offset-1 col-md-4">

			                 <label for="">Type</label>

		                     <div class="form-group"> 

		                             <?php 

				                     $profile=$this->admin_settings_model->profileDB('get',$this->session->userdata('profile'));

				                     ?>

				                      <?php if($profile['tag_transfer']==1){?>

				                      <input type="radio" name="transfer_item_type" id="type1" value="1" checked> <label for="type1">Tagged</label>  &nbsp;&nbsp;

				                      <?php }?>

				                      

				                      <?php if($profile['non_tag_transfer']==1){?>

				                      <input type="radio" name="transfer_item_type" id="type2" value="2"> <label for="type2">Non Tagged</label>

				                      <?php }?>

				                      

				                      <?php if($profile['purchase_item_transfer']==1){?>

				                      <input type="radio" name="transfer_item_type" id="type3" value="3"> <label for="type3">Purchase Items</label>

				                      <?php }?>

				                      

				                       <?php if($profile['packaging_item_transfer']==1){?>

				                      <input type="radio" name="transfer_item_type" id="type4" value="4"> <label for="type4">Packaging Items</label>

				                      <?php }?>

				                      

				                      <input type="radio" name="transfer_item_type" id="type5" value="5"> <label for="type5" class="radio-label">Repair Order</label>

				                    

				             </div>

		                  </div>		                    

		                  <div class="col-md-2">

		                  	<div class="form-group">

		                       <label>Product</label> 

		                       <input type="text" class="form-control product" id="product" placeholder="Product Name/Code" autocomplete="off">

							   <input type="hidden" class="form-control" id="id_product">

							   <input type="hidden" id="required_otp_approval" value="<?php echo $is_otp_required_for_approval;?>">



		                     </div>

		                  </div>

		                  <div class="col-md-2">

		                  	 <div class="form-group">    

		                   		  <label>Date </label> 

		                   		  <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" autocomplete="off">  

		                     </div>

		                  </div>

		                   

		                  <div class="col-md-3"> 

		                  		<div class="form-group">

		                  		  <label></label> 


								<?php if($profile['allow_other_issue']== 1){ ?>
		                  		  <?php if($this->session->userdata('id_branch')==0){?>

				                  <label class="checkbox-inline"><input type="checkbox" id="isOtherIssue" name="isOtherIssue"/>Other Issue</label>

				                   <?php }?>
				                <?php }?>

				                  <input type="hidden" id="other_issue_branch" value="<?php echo $other_issue_branch;?>">

				                  <input type="hidden" id="head_office_branch" value="<?php echo $head_office_branch;?>">

				                  <input type="hidden" id="branch_trans_dnload" value="<?php echo $branch_transfer_download;?>">

								  <input type="hidden" id="dnload_trans_id" value="">

								  <input type="hidden" id="branch_trans_from_branch" value="">

								  <input type="hidden" id="branch_trans_to_branch" value="">

								  <input type="hidden" id="actual_pcs_dnload" value="">

								  <input type="hidden" id="actual_weights_dnload" value="">

								  <input type ="hidden" id="allow_approval_type" value="<?php echo $profile['allow_bill_type']?>" />

								  <input type="hidden" id="is_eda_appr" value=1>

								  <input type="hidden" id="BT_otp_approval_type" value="<?php echo $profile['BT_otp_approval_type'];?>">

								  <input type="hidden" id="uid" value="<?php echo $this->session->userdata('uid');?>">

								  <input type="hidden" id="appr_trans_id" value="">

								  <input type="hidden" id="approval_id" value="">

								  <input type="hidden" id="apprl_status" value="">

								  <input type="hidden" id="approval_type" value="">

								  <input id="cliIDcode" type="hidden" value="<?php echo $this->config->item('cliIDcode')?>" />



				                  <label></label> 

				                  <button type="button" id="btran_filter" class="btn btn-info btn-flat pull-right">Filter</button>  

				                </div>

		                  </div>

		                 

		                  <div class="col-md-2">

		                     <div class="form-group" style="display: none;">

		                       <label>Lot</label> 

		                       <select class="form-control" id="lotno"></select>

		                     </div>   

		                     <div class="form-group " style="display: none;">

		                       <label>Design</label> 

		                       <input type="text" class="form-control" id="design" placeholder="Design"  autocomplete="off">

							   <input type="hidden" class="form-control" id="id_design">

		                     </div> 

		                  </div>

		                   

	                   </div> 

	                   </div> 

	                  </div> 

                   </div> 

                  </div>   

				

                <div class="row">

                    <div class="col-md-12">

                        <div class='form-group'>

                            <label id="otp_status"></label>

                        </div>

                    </div>

                </div>

                <div class="alert-msg"></div>

			  

                  <div class="table-responsive tagged">

	                 <table id="bt_approval_list" class="table table-bordered table-hover table-striped text-center ">

	                 	<thead>

	                      <tr>

	                        <!--<th  width="10%"><label class="checkbox-inline"><input type="checkbox" id="appr_sel_all_tg" name="appr_sel_all_tg" value="all"/><b>Trans Code</b></label></th>    -->

	                        <th  width="10%"><label class="checkbox-inline"><b>Trans Code</b></label></th>    

                          	<th width="5%"></th>   

	                      	<th width="10%">Lot</th> 

	                      	<th width="10%">Transfer Id</th> 

                          	<th width="10%">From Branch</th>  

                          	<th width="10%">To Branch</th> 

                          	<th width="15%">No. of Products</th>

                          	<!--<th width="10%">Tag No</th>  -->

                          	<!--<th width="15%">Design</th> -->   

                          	<th width="10%">Pcs</th>  

                          	<th width="10%">G.wt</th>  

                          	<th width="10%">N.wt</th>  

                          	<th width="10%">Status</th>

	                      </tr>

	                    </thead>

	                    <thead id="search_head">

				            <tr>

				                <th><input style="width:100px" class="form-control" id="f_transcode" type="text" placeholder="Trans Code" /></th>

				                <th></th>

				                <th><input style="width:100px" class="form-control" id="f_lot" type="text" placeholder="Lot No" /></th> 

				                <th colspan="7"></th>

				            </tr>

				        </thead>

	                    <tbody></tbody>

	                    <tfoot>

	                    	<tr>

	                    		<td colspan="6">Total</td>

	                    		<td><input type="text" class="t_tot_pieces" disabled="true" placeholder="Pieces"/></td>

	                    		<td><input type="text" class="t_tot_gross_wt" disabled="true" placeholder="Gross Weight"/></td>

	                    		<td><input type="text" class="t_tot_net_wt" disabled="true" placeholder="Net Weight"/></td>

	                    		<td></td>

	                    	</tr>

	                    </tfoot>

	                 </table>

	                 

                  </div>

                  

                  <div class="row">

					<div class="col-md-12 container tag_scan_download" style="display:none;">

    					<div class="table-responsive">

    	                 <table id="bt_approval_list_by_scan"  style="display:none;width:80%;margin-left: auto;margin-right: auto;"  class="table table-bordered table-striped text-center ">

    	                    <thead>

    	                      <tr>

    	                        <th>Trans ID</th> 

    							<th>Pcs</th>

    							<th>G.wt</th>

                              	<th>From Branch</th> 

                     			<th>To Branch</th> 

                              	<th>Status</th>

    	                      </tr>

    	                    </thead> 

    						<tbody></tbody>

    	                    

    	                 </table>

                      </div>

				    </div>

				  </div>

				  <div class="form-group tag_scan_download" id="tag_scan_code" style="display:none;    margin-top: 20px;"> 

						<div class="row">

							<div class="col-md-5 ">

								<label for="" class="control-label pull-right">Tag Code</label> 

							</div>

							<div class="col-md-2">

								<input type="text" class="form-control" id="scan_tag_no" placeholder="Tag Code"  autocomplete="off">

							</div>

						</div> 

					</div>

					<div class="scan_summary" style="display:none;">

						<p>

							<span style="margin-right:15%;margin-left: 42%;">

								<span><b>TOTAL : </b></span>

								<span><b>PCS : </b></span>

								<span class="tot_bt_pcs_scan" style="font-bold;">0</span>

								<span><b>WEIGHT : </b></span>

								<span class="tot_bt_gross_wt_scan" style="font-bold;">0.000</span>

							</span>

						</p> 

                    </div>

                    <div class="table-responsive">

                            <table id="bt_dwnload_list" style="display:none" class="table table-bordered table-striped text-center">

                               <thead>

                                 <tr>

                          	<th width="5%">Tag Code</th>   

                          	<th width="10%">Product</th>  

                          	<th width="10%">Pcs</th>  

                          	<th width="10%">G.wt</th>  

                          	<th width="10%">L.wt</th>  

                          	<th width="10%">N.wt</th>  

                                 </tr>

                               </thead>

                               <tbody></tbody>



                            </table>

                         </div>

                         

                  <div class="table-responsive non_tagged"  style="display: none">

	                 <table id="bt_approval_list_nt" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th><label class="checkbox-inline"><input type="checkbox" id="appr_sel_all_nt" name="appr_sel_all_nt" value="all"/>All</label></th> 

                          	<th>From Branch</th>  

                          	<th>To Branch</th> 

                          	<th>Trans Id</th>

                          	<th>Type</th>  

                          	<!-- <th>Section</th>   

                          	<th>Product</th>    -->

                          	<th>Pcs</th>  

                          	<th>G.wt</th>  

                          	<th>N.wt</th>  

                          	<th>Status</th>

	                      </tr>

	                    </thead> 

	                    <!--<tfoot>

	                    	<tr>

	                    		<th colspan="6"><b>Total</b></th>

	                    		<td><input type="text" class="nt_tot_pieces" disabled="true" placeholder="Pieces"/></td>

	                    		<td><input type="text" class="nt_tot_gross_wt" disabled="true" placeholder="Gross Weight"/></td>

	                    		<td><input type="text" class="nt_tot_net_wt" disabled="true" placeholder="Net Weight"/></td>

	                    		<td></td>

	                    	</tr>

	                    </tfoot>-->

	                 </table>

                  </div>

                  

                  <div class="table-responsive old_metal"  style="display: none">

	                 <table id="bt_approval_list_old_metal" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th><label class="checkbox-inline"><input type="checkbox" id="appr_sel_all_nt" name="appr_sel_all_nt" value="all"/>All</label></th> 

                          	<th>From Branch</th>  

                          	<th>To Branch</th> 

                          	<th>Trans Id</th>

                          	<th>Type</th>  

                          	<th>G.wt</th>  

                          	<th>N.wt</th>  

                          	<th>Status</th>

	                      </tr>

	                    </thead> 

	                    <!--<tfoot>

	                    	<tr>

	                    		<th colspan="6"><b>Total</b></th>

	                    		<td><input type="text" class="nt_tot_pieces" disabled="true" placeholder="Pieces"/></td>

	                    		<td><input type="text" class="nt_tot_gross_wt" disabled="true" placeholder="Gross Weight"/></td>

	                    		<td><input type="text" class="nt_tot_net_wt" disabled="true" placeholder="Net Weight"/></td>

	                    		<td></td>

	                    	</tr>

	                    </tfoot>-->

	                 </table>

                  </div>

                  

                  <div class="table-responsive packaging"  style="display: none">

	                 <table id="bt_approval_list_packaging" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th><label class="checkbox-inline"><input type="checkbox" id="appr_sel_all_nt" name="appr_sel_all_nt" value="all"/>All</label></th> 

                          	<th>From Branch</th>  

                          	<th>To Branch</th> 

                          	<th>Trans Id</th>

                          	<th>Type</th>  

                          	<th>Pcs</th>  

	                      </tr>

	                    </thead> 

	                    <!--<tfoot>

	                    	<tr>

	                    		<th colspan="6"><b>Total</b></th>

	                    		<td><input type="text" class="nt_tot_pieces" disabled="true" placeholder="Pieces"/></td>

	                    		<td><input type="text" class="nt_tot_gross_wt" disabled="true" placeholder="Gross Weight"/></td>

	                    		<td><input type="text" class="nt_tot_net_wt" disabled="true" placeholder="Net Weight"/></td>

	                    		<td></td>

	                    	</tr>

	                    </tfoot>-->

	                 </table>

                  </div>

                  

                     <div class="table-responsive orders"  style="display: none">

	                 <table id="bt_approval_list_orders" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th><label class="checkbox-inline"><input type="checkbox" id="appr_sel_all_orders" name="appr_sel_all_orders" value="all"/>All</label></th> 

                          	<th></th>  

                          	<th>From Branch</th>  

                          	<th>To Branch</th> 

                          	<th>Order No</th>

                          	<th>Product</th>   

                          	<th>Pcs</th>  

                          	<th>Order Wt</th>  

                          	<th>Status</th>

	                      </tr>

	                    </thead> 

	                    <!--<tfoot>

	                    	<tr>

	                    		<th colspan="6"><b>Total</b></th>

	                    		<td><input type="text" class="nt_tot_pieces" disabled="true" placeholder="Pieces"/></td>

	                    		<td><input type="text" class="nt_tot_gross_wt" disabled="true" placeholder="Gross Weight"/></td>

	                    		<td><input type="text" class="nt_tot_net_wt" disabled="true" placeholder="Net Weight"/></td>

	                    		<td></td>

	                    	</tr>

	                    </tfoot>-->

	                 </table>

                  </div>

                    

                    <p class="help-block"> </p>  

    			   <div class="row">

                        <div class="col-xs-offset-5">

                            <div class='form-group'>

							<?php if($access['add']==1){?>

                                <button type="button" id="upd_status_btn" name="upd_status_btn" value="2" class="btn btn-success btn-flat"><i class="icon fa fa-check"></i> Approve</button>   
							
								<?php }?>
                            </div>

                        </div>

                    </div>   

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

            

            </div><!-- /.col -->

          </div><!-- /.row -->

           

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

<!--Modal-->

<div class="modal fade" id="otp_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

		<h4 class="modal-title" id="myModalLabel">Verify OTP and Update Status</h4>

	  </div>

      <div class="modal-body"> 

         	<div class="row" > 

         		<div class="col-md-12">

         			<h5>We have sent OTP to autorized mobile number. Kindly verify OTP to proceed further.</h5> 

		    	</div>

		    </div>

		    <p></p>

         	<div class="row otp_block"> 

		    	<div class="col-md-2">

		    		<div class='form-group'>

		                <label for="">OTP</label>

		            </div>

		    	</div> 

		    	<div class="col-md-5">

		    		<div class='form-group'>

			    		<div class='input-group'>

			                <input type="text" id="otp" name="otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required /> 

			                <span class="input-group-btn">

				            	<button type="button" id="verify_otp" class="btn btn-primary btn-flat" disabled >Verify</button>

				            </span>

			            </div> 

		            </div>

		    	</div> 

		    	<div class="col-md-2">

		    		<div class='form-group'>

		               <input type="button" id="resend_otp" class="btn btn-warning btn-flat" value="Resend OTP"/>  

		            </div>

		    	</div>     

			 </div> 

			 <div class="row">

			 	<div class="col-md-12">

			 		<span class="otp_alert"></span>

			 	</div>

			 </div>  

	</div>  

	<div class="modal-footer">

		<button type="button" id="approve" class="btn btn-success btn-flat" disabled>Approve</button>	 

		<button type="button"  class="btn btn-danger btn-flat" data-dismiss="modal" id="close">Close</button>

	</div>

   </div>

  </div>

</div> 

    

	<!-- Mobile App Approval -->
	<div class="modal fade" id="app_aproval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->
					<h4 class="modal-title" id="myModalLabel">Mobile App Approval</h4>
				</div>
				<div class="modal-body">
				Please wait, Till This Transaction Get Approval From Management ...<span style="color:red" class="show_timer"></span>
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-success btn-flat get_approval" title=">Get Mobile App Approval">Get Approval Status</button>

					<button type="button" class="cancel_bt_aprvl btn btn-close btn-warning" data-bs-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<!--End of Mobile App Approval -->