<!-- Content Wrapper. Contains page content -->

<style>

.remove-btn {

    margin-top: -168px;

    margin-left: -38px;

    background-color: #e51712 !important;

    border: none;

    color: white !important;

}



.custom-bx {

    box-shadow: none;

    border: 0.5px solid #e1e1e1;

}

      </style>

      <div class="content-wrapper">

          <!-- Content Header (Page header) -->

          <section class="content-header">

              <h1>

                  LOT MERGE

              </h1>

          </section>



          <!-- Main content -->

          <section class="content">



              <!-- Default box -->

              <div class="box box-primary">

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

                      <!-- form -->

                      <?php  $attributes =array('id' => 'lot_merge_form', 'name' => 'lotMergeForm','target'=>'_blank');

                        echo form_open_multipart(('admin_ret_lot/lot_merge/save'),$attributes); ?>

                        <div class="row">

                            <div class="col-md-5">

                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="col-md-5 ">

                                            <div class="form-group">

                                                <label for="">Lot Received At <span class="error"> *</span></label>

                                                <div class="form-group">

                                                <?php 					 				

                                                if($inward['lot_receive_settings'] == 1) // Any Branch

                                                {

                                                ?>

                                                <div class="form-group" > 

                                                    <select id="lt_rcvd_branch_sel" class="ret_branch form-control" required="true"></select>

                                                </div>

                                                <?php 

                                                }else{ 

                                                ?>

                                                    <span ><?php echo $inward['rcvd_branch_name']; ?></span>

                                                <?php }?> 

                                                <input id="id_branch" name="inward[lot_received_at]" type="hidden" value="<?php echo set_value('inward[lot_received_at]',$inward['lot_received_at']); ?>"/>

                                                </div>

                                            </div> 

                                        </div>

                                        <div class="col-md-5 ">

                                            <div class="form-group">

                                                <label for="">LOT NO. <span class="error"> *</span></label>

                                                <div class="form-group">

                                                    <!--<input type="text" id="lot_no" class="form-control" value="" placeholder="Enter Lot No..">-->

                                                    <select class="form-control" id="lot_no_merge"></select>

                                                </div>

                                            </div> 

                                        </div>

                                        <div class="col-md-5 ">

                                            <div class="form-group">

                                                <label for=""><span class="error"> </span></label>

                                                <div class="form-group">

                                                    <button type="button" class="btn btn-info btn-flat lot_no_search">Search</button>

                                                </div>

                                            </div> 

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 table-responsive">

                                <div class="box box-info">

                                    <div class="box-body">

                                        <p>Lot Details</p>

                                        <table id="lot_det" class="table table-bordered text-center">

                                            <input type="hidden" id="curRow" value="-1">

                                            <thead>

                                                <tr> 

                                                    <th>Lot No</th>

                                                    <th>Item ID</th>

                                                    <th>Product</th>

                                                    <th>Pcs</th>

                                                    <th>GrsWt</th>

                                                    <th>NetWt</th>

                                                    <th>StnPcs</th>

                                                    <th>StnWt</th>

                                                    <th>DiaPcs</th>

                                                    <th>DiaWt</th>

                                                    <th>Narration</th>

                                                    <th>Action</th>

                                                </tr>

                                            </thead> 

                                            <tbody>

                                                <tfoot style="font-weight:bold;">

                                                    <td>Total:</td>

                                                    <td></td>

                                                    <td></td>

                                                    <td class="tot_lot_pcs"></td>

                                                    <td class="tot_lot_wgt"></td>

                                                    <td class="tot_lot_nwt"></td>

                                                    <td class="tot_stn_pcs"></td>

                                                    <td class="tot_stn_wt"></td>

                                                    <td class="tot_dia_pcs"></td>

                                                    <td class="tot_dia_wt"></td>

                                                    <td></td>

                                                    <td></td>

                                                </tfoot>

                                            </tbody>



                                        </table>



                                    </div>

                                </div>

                            </div>



                            <p class="help-block"></p>

                            <p class="help-block"></p>

                            <div class="row ">

                                <div class="col-md-12">

                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <label for="">GOLD SMITH<span class="error"> *</span></label>

                                            <div class="form-group">

                                            <select id="gold_smith" class="form-control"></select>

                                            <input id="id_gold_smith" name="inward[gold_smith]"type="hidden" value="<?php echo set_value('inward[gold_smith]',$inward['gold_smith']); ?>"/>

                                            </div>

                                        </div> 

                                    </div>

                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <label for="">STOCK TYPE<span class="error"> *</span></label>

                                            <div class="form-group">

                                                <input type="radio" id="stock_type" name="inward[stock_type]" value="1" checked> Tagged

										        &nbsp;&nbsp;&nbsp;

										        <input type="radio" id="stock_type" name="inward[stock_type]" value="2" > Non-Tagged

                                            </div>

                                        </div> 

                                    </div>

                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <label for=""><span class="error"></span></label>

                                            <div class="form-group">

                                            <?php if($access['add']==1){?>

                                                <button class="btn btn-success pull-right" type="button" id="add_lot_merge"><i class="fa fa-plus-circle"></i>Lot Merge</button> 
                                            <?php }?>
                                            </div>

                                        </div> 

                                    </div>

                                </div>

                            </div>

                        </div>

                              <div class="table-responsive">

                                  <table id="lot_search_list" class="table table-bordered table-striped text-center">

                                  <input type="hidden" id="curRow" value="-1">

                                      <thead>

                                        <tr>

                                            <th width="10%">Category</th>



                                            <th width="10%">Product</th> 


                                            <th width="10%">Design</th> 

                                            <th width="10%">Sub Design</th> 


                                            <th width="10%">Purity</th>



                                            <th width="10%">Pieces</th> 



                                            <th width="10%">Gross Wgt</th>



                                            <th width="10%">Stn Pcs</th>



                                            <th width="10%">Stn Wt</th>

                                                    

                                            <th width="10%">Dia Wt</th>

                                            

                                            <th width="10%">Dia Pcs</th>



                                            <th width="10%">Net Wgt</th>



                                        </tr>

                                      </thead>

                                      <tbody></tbody>

                                      <tfoot><tr style="font-weight:bold;">

                                        <td>TOTAL:</td>

                                        <td></td>

                                        <td></td>
                                        
                                        <td></td>

                                        <td></td>

                                        <td class="lot_tot_pcs"></td>

                                        <td class="lot_tot_gwt"></td>

                                        <td class="lot_tot_stnpcs"></td>

                                        <td class="lot_tot_stnwt"></td>

                                        <td class="lot_tot_diapcs"></td>

                                        <td class="lot_tot_diawt"></td>

                                        <td class="lot_tot_nwt"></td>

                                    </tr></tfoot>

                                  </table>

                              </div>

                          </div>

                      </div>





                      <div class="row sales_submit">

                          <div class="box box-default"><br />

                              <div class="col-xs-offset-5">

                                  <button type="button" id="lot_merge_submit" class="btn btn-primary">Save</button>

                                  <button type="button" class="btn btn-default btn-cancel">Cancel</button>

                              </div> <br />

                          </div>

                      </div>





                  </div>



                  <div class="overlay" style="display:none">

                      <i class="fa fa-refresh fa-spin"></i>

                  </div>

              </div>



              <!-- /form -->

          </section>

      </div>



      <div class="modal fade" id="stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



        <div class="modal-dialog" style="width:60%;">



		<div class="modal-content">



			<div class="modal-header">



				<h4 class="modal-title" id="myModalLabel">Stone Details</h4>



			</div>



			<div class="modal-body">



				 	<div class="row">		



				 		<div class="col-xs-offset-1 col-sm-9">	



				 		<input type="hidden" id="row_id" name="row_id" value=""/>



				 		<!--<legend class="sub-title">Stone Details</legend>-->	



						 	<div class="row"> <!-- Precious Stone -->				    	



					    		<div class="col-sm-3">



						 			<input type="checkbox" id="precious_stone" name="inward[precious_stone]" value="0"/>



					    			<label>Precious </label>



						 		</div>



						 		<div class="col-sm-9"> 



							 	<div class="row">



							 		<div class="col-sm-4"> 	



							 			<label>Stone Pcs</label>						 		



						 				<input class="form-control" id="precious_st_pcs" name="inward[precious_st_pcs]" type="number"  step=any  placeholder="No. of Pieces" disabled="true" />							 			



							 		</div>



							 		<div class="col-sm-5"> 



							 			<label>Stone Wgt</label>	



							 			<div class="form-group">



								 			<div class="input-group">



									 			<input class="form-control" id="precious_st_wt" name="inward[precious_st_wt]" type="number"  step=any  placeholder="Enter stone wgt"  disabled="true"/>



									 			<span class="input-group-addon input-sm no-padding">



									 				<select id="pre_wt_uom" class="uom" name="inward[pre_wt_uom]">



								 					<?php foreach($uom as $u){



													echo "<option value=".$u['uom_id'].">".$u['code']."</option>";



													}?>



								 				</select>



									 			</span>



											</div>



										</div>							 			



							 		</div>



							 		<div class="col-sm-3"> 



							 		<label></label>



							 			<input type="hidden" id="imgCount_p_stn" value="0" /> 



							       		<button  type="button" id="uploadImg_p_stn" class="btn btn-success pull-right" disabled="true"><i class="fa fa-plus"></i> Certificate</button>



							 		</div>



							 		<div id="uploadedArea_p_stn" class="col-md-12" style="display: none;">



							       		<p class="text-green">Uploaded Certificates :</p>



							       	</div>



						       		<div id="uploadArea_p_stn" class="col-md-12" style="display: none;">



						       			<input type="file" name="pre_images" id="pre_images" class="pre_images" multiple="multiple">



						       		</div>



						 		</div>



						 		</div>



						 	</div>  					    



						 	<p class="help-block"></p>



						    <!-- / Precious Stone -->



						 	<div class="row">		<!-- Semi-Precious Stone-->		    	



					    		<div class="col-sm-3">



						 			<input type="checkbox" id="semi_precious_stn" name="inward[semi_precious_stone]" value="0"/>



					    			<label>Semi-Precious </label>



						 		</div>



						 		<div class="col-sm-9"> 



							 	<div class="row">



							 		<div class="col-sm-4"> 	



							 			<label>Stone Pcs</label>						 		



						 				<input class="form-control" id="semi_precious_st_pcs" name="inward[semi_precious_st_pcs]" type="number"  step=any  placeholder="No. of Pieces" disabled="true"/>							 			



							 		</div>



							 		<div class="col-sm-5"> 



							 			<label>Stone Wgt</label>	



							 			<div class="form-group">



								 			<div class="input-group">



									 			<input class="form-control" id="semi_precious_st_wt" name="inward[semi_precious_st_wt]" type="number"  step=any  placeholder="Enter stone wgt" disabled="true"/>



									 			<span class="input-group-addon input-sm no-padding">



									 				<select id="semi_wt_uom" class="uom" name="inward[semi_wt_uom]">



								 					<?php foreach($uom as $u){



													echo "<option value=".$u['uom_id'].">".$u['code']."</option>";



													}?>



								 				</select>



									 			</span>



											</div>



										</div>



							 		</div>



							 		<div class="col-sm-3"> 



						 				<label></label>



							 			<input type="hidden" id="imgCount_sp_stn" value="0" /> 



							       		<button type="button" id="uploadImg_sp_stn" class="btn btn-success pull-right" disabled="true"><i class="fa fa-plus"></i> Certificate</button>



							 		</div>



							 		<div id="uploadedArea_sp_stn" class="col-md-12" style="display: none;">



							       		<p class="text-green">Uploaded Certificates :</p>



							       	</div>



							 		<div id="uploadArea_sp_stn" class="col-md-12" style="display: none;">



						       			<input type="file" name="semi_pre_imgs" id="semi_pre_imgs" multiple="multiple">



						       		</div>



						 		</div>



						 		</div>



						 	</div> 



						 	<p class="help-block"></p>



						    <!-- / Semi-Precious Stone -->



						 	<div class="row">		<!-- Normal Stone -->		    	



					    		<div class="col-sm-3">



						 			<input type="checkbox" id="normal_stn" name="inward[normal_stone]" value="0"/>



					    			<label>Normal </label>



						 		</div>



						 		<div class="col-sm-9"> 



								 	<div class="row">



								 		<div class="col-sm-4"> 	



								 			<label>Stone Pcs</label>						 		



							 				<input class="form-control" id="normal_st_pcs" name="inward[normal_st_pcs]" type="number"  step=any  placeholder="No. of Pieces" disabled="true"/>							 			



								 		</div>



								 		<div class="col-sm-5"> 



								 			<label>Stone Wgt</label>	



								 			<div class="form-group">



									 			<div class="input-group">



										 			<input class="form-control" id="normal_st_wt" name="inward[normal_st_wt]" type="number"  step=any  placeholder="Enter stone wgt" disabled="true"/>



										 			<span class="input-group-addon input-sm no-padding">



										 				<select id="nor_wt_uom" class="uom" name="inward[nor_wt_uom]">



								 					<?php foreach($uom as $u){



													echo "<option value=".$u['uom_id'].">".$u['code']."</option>";



													}?>



								 				</select>



										 			</span>



												</div>



											</div>



								 		</div>



								 		<div class="col-sm-3"> 



								 			<label></label>



								 			<input type="hidden" id="imgCount_n_stn" value="0" /> 



								       		<button  type="button" id="uploadImg_n_stn" class="btn btn-success pull-right" disabled="true"><i class="fa fa-plus"></i> Certificate</button>



								 		</div>



								 		<div id="uploadedArea_n_stn" class="col-md-12" style="display: none;">



								       		<p class="text-green">Uploaded Certificates :</p>



								       	</div>



									 	<div id="uploadArea_n_stn" class="col-md-12" style="display: none;">



							       			<input type="file" name="norm_pre_imgs" id="norm_pre_imgs" multiple="multiple">



							       		</div>



							 		</div>



						 		</div>



						 	</div> 



						 	<p class="help-block"></p>



			       <!-- / Normal Stone-->	



				 		</div>



				 	</div>



		  </div>



		  <div class="modal-footer">



			<button type="button" id="update_stone_details" class="btn btn-success">Save</button>



			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>



		  </div>



		</div>



	</div>



</div>



