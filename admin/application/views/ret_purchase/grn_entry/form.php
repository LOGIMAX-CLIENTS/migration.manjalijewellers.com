      <!-- Content Wrapper. Contains page content -->

    <style>

    	.remove-btn{

			margin-top: -168px;

		    margin-left: -38px;

		    background-color: #e51712 !important;

		    border: none;

		    color: white !important;

		}

		.sm{

			font-weight: normal;

		}

		.col-md-2{
		    padding-right: 0 !important;
		}



		*[tabindex]:focus {

            outline: 1px black solid;

        }





    #total_summary_details td{

        padding : 1px 5px !important;

    }

   #total_summary_details input[type=text],#total_summary_details input[type=number], #total_summary_details button {

        height: 25px !important;

        padding: 1px 5px !important;

    }



    input[type=number]::-webkit-inner-spin-button,

    input[type=number]::-webkit-outer-spin-button {

    -webkit-appearance: none;

    -moz-appearance: none;

    appearance: none;

    margin: 0;

    }



    </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->


		<section class="content-header">
			<h1>
				GRN ENTRY FORM
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
				<li class="active">Grn Entry Form</li>
			</ol>
		</section>


        <!-- Main content -->

        <section class="content order">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">BILL DETAILS</h3>

            </div>

            <div class="box-body">

             <!-- form container -->

	             <!-- form -->

				<form id="grn_entry_form">

				  <div class="tab-content">

				        <div class="row">

        				    <div class="col-md-12">

        				            <?php

                				 		$this->session->unset_userdata('FORM_SECRET');

            				 		    $form_secret=md5(uniqid(rand(), true));

            					        $this->session->set_userdata('FORM_SECRET', $form_secret);

        				 		    ?>

        				        <div class="col-md-3">

            	                     <div class="form-group">

            	                       <label>Type</label>

            							<div class="form-group" style="display: flex; justify-content: space-between; align-items: baseline;">

            							        <input type="hidden" id="FORM_SECRET" name="order[form_secret]" value="<?php echo $form_secret; ?>">

        										<input type="radio" id="oranment_type" name="order[grn_type]" value="1"  <?php echo ($grn_details['grn_type']==1 ? 'checked' :'') ?>><label for="oranment_type">Bill</label>

        										<input type="radio" id="mt_type" name="order[grn_type]" value="2"  <?php echo ($grn_details['grn_type']==2 ? 'checked' :'') ?>><label for="mt_type">Receipt</label>

        										<input type="radio" id="st_type" name="order[grn_type]" value="3"  <?php echo ($grn_details['grn_type']==3 ? 'checked' :'') ?> ><label for="st_type">Charges</label>

        										<input type="radio" id="jb_type" name="order[grn_type]" value="4"  <?php echo ($grn_details['grn_type']==4 ? 'checked' :'') ?>><label for="jb_type">is JB</label>

        									</div>

            	                     </div>

        				        </div>



        				        <div class="col-md-2">

            	                     <div class="form-group">

            	                         <label>Select Karigar<span class="error">*</span></label>



            	                         <div class="input-group">

                                                <select id="select_karigar" class="form-control" name="order[id_karigar]"  tabindex="3"></select>

                                                <input type="hidden" id="grn_id" name="order[grn_id]" value="<?php echo $grn_details['grn_id'];?>">



                                                <input type="hidden" id="id_karigar" value="<?php echo $grn_details['grn_karigar_id'];?>">

                                                <input type="hidden" id="cmp_country" name="order[cmp_country]" value="<?php echo $comp_details['id_country'];?>">

                                                <input type="hidden" id="cmp_state" name="order[cmp_state]" value="<?php echo $comp_details['id_state'];?>">

                                                <input type="hidden" id="supplier_country" name="order[supplier_country]" value="">

                                                <input type="hidden" id="supplier_state" name="order[supplier_state]" value="">

        									<span class="input-group-btn" >

                                                <a class="btn btn-warning" id="edit_karigar" href="#"  data-toggle="tooltip" title="Edit Supplier" style="height: 29px;padding-left: 14px;"><i class="fa fa-user-plus"></i></a>

                                            </span>

    								  </div>

            	                     </div>

        				        </div>





        				        <div class="col-md-2">

            	                     <div class="form-group">

            	                       <label>Ref No<span class="error">*</span></label>

            							<input type="text" class="form-control referenceno" name="order[po_supplier_ref_no]" value="<?php echo $grn_details['grn_supplier_ref_no'];?>" placeholder="Enter supplier bill Ref no." tabindex="3">

            	                     </div>

        				        </div>

        				        <div class="col-md-2">

									<div class="form-group">

										<label>Purchase Type <span class="error">*</span></label>

										<select name="purchase_type" id="purchase_type" class="form-control" tabindex="3">

											<option value="1">Stock</option>

											<option value="2">Walk-in</option>
											
											<option value="3">Expo</option>

										</select>

									</div>

								</div>


        				        <div class="col-md-2">

                                        <div class="form-group">

                                        <label>Ref Date<span class="error">*</span><i class="fa fa-calendar" aria-hidden="true"></i></label>

                                        <input type="text" class="form-control referencedate" name="order[po_ref_date]" value="<?php echo $grn_details['grn_ref_date'];?>" dateformat="d-M-y"  placeholder="Select bill Ref date." tabindex="3">

                                        </div>

                                </div>





            			        <div class="col-md-2">

            	                     <div class="form-group">

            	                       <label>E-Way Bill No</label>

            							<input type="text" class="form-control" id="ewaybillno" name="order[ewaybillno]" value="<?php echo $grn_details['grn_ewaybillno'];?>" placeholder="Enter The Bill No." tabindex="4">

            	                     </div>

        				        </div>





        				        <div class="col-md-2">

            	                     <div class="form-group">

            	                       <label>IRN No</label>

            							<input type="text" class="form-control" id="invoice_ref_no" name="order[invoice_ref_no]" value="<?php echo $grn_details['grn_irnno'];?>" placeholder="Enter The IRN No." tabindex="5">

            	                     </div>

        				        </div>





        				        <div class="col-md-2">

            	                     <div class="form-group">

            	                       <label>Dispatch Through<span class="error">*</span></label>

            							<select id="despatch_through" class="form-control" name="order[despatch_through]" style="width:100%;" tabindex="5">

            							    <option value="1" <?php echo ($grn_details['grn_despatch_through']==1 ? 'selected' :'')?> >Courier</option>

            							     <option value="2" <?php echo ($grn_details['grn_despatch_through']==2 ? 'selected' :'')?> >Manual Delivery</option>

            							</select>

            	                     </div>

        				        </div>

								<div class="col-sm-2 image_block">
                                  <label>Image</label>
                                 <div>
                                    <input type="hidden" id="custom_active_id">
                                    <div id="tag_img" data-img="[]"></div>
                                        <input type="hidden" class="form-control" id="tag_img_copy">
                                        <input type="hidden" class="form-control" id="tag_img_default">
                                        <input type="hidden" class="form-control" id="tag_images">
										<input type="hidden" name="order[tag_img]" class="form-control" id="tag_image">
                                        <a href="#" onclick="grn_update_image_upload();" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a>

									</div>

								</div>







        				        <div class="col-md-2 item_details">

            	                     <div class="form-group">

            	                       <br>

            							<button id="add_item_details" type="button" class="btn btn-success" tabindex="5"><i class="fa fa-plus"></i> Add Item </button>

            	                     </div>

        				        </div>



        				    </div>

        				</div>



        				<div class="row item_details" <?php echo ($grn_details['grn_type']==3 ? 'style="display:none"' :'') ?>>

				            <div class="col-md-12">

            			         <div class="box-body">

								   <div class="table-responsive">

								       <input type="hidden" id="custom_active_id" value="0">

									 <table id="grn_item_details" class="table table-bordered table-striped text-center">

										<thead>

										  <tr>

											<th width="8%;">Category<span class="error">*</span></th>

											<th width="3%;">Pcs<span class="error">*</span></th>

											<th width="5%;">G.Wt<span class="error">*</span></th>

											<th width="10%;">L.Wt</th>

											<th width="10%;">Other Metal</th>

											<th width="5%;">N.Wt<span class="error">*</span></th>

											<th width="3%;">VA(grms)</th>

											<th width="15%;">Rate<span class="error">*</span></th>

											<th width="5%;">Cost<span class="error">*</span></th>

											<th width="5%;">Taxable Amt<span class="error">*</span></th>

											<th width="5%;">Tax<span class="error">*</span></th>

											<th width="5%;">Amount<span class="error">*</span></th>

											<th width="5%;">Action</th>

										  </tr>

										</thead>

										<tbody>

										    <?php

										    if(sizeof($grn_details['item_details']>0))

										    {

										           foreach($grn_details['item_details'] as $ikey => $val)

										           {

										                $stone_data=[];

                                                        $otherMetal_data = [];

										                $stone_data=[];

                                                        $otherMetal_data = [];

                                                        $catTypes = "<option value=''>- Select Category -</option>";

                                                        foreach($categories as $cat)

                                                        {

                                                        	if($cat['id_ret_category']==$val['cat_id'])

                                                        	{

                                                        		$selected =  "selected='selected'";

                                                        		$catTypes .= "<option ".$selected." value='".$cat['id_ret_category']."'>".$cat['name']."</option>";

                                                        	}

                                                        	$catTypes .= "<option value='".$cat['id_ret_category']."'>".$cat['name']."</option>";

                                                        }

                                                        foreach($val['stone_details'] as $stn)

                                                        {

                                                        	$stone_data[]=array(

                                                        		'show_in_lwt'=>$stn['is_apply_in_lwt'],

                                                        		'stones_type' => $stn['stone_type'],

                                                        		'stone_id' => $stn['stone_id'],

                                                        		'stone_pcs'   => $stn['pieces'],

                                                        		'stone_wt'   => $stn['wt'],

                                                        		'stone_uom_id' => $stn['uom_id'],

                                                        		'stone_name'   => $stn['stone_name'],

                                                        		'stone_price'   => $stn['amount'],

                                                        		'stone_rate' => $stn['rate_per_gram'],

                                                        		'stone_cal_type' => $stn['stone_cal_type'],

                                                        	);

                                                        }

                                                        $stone_details=json_encode($stone_data);

                                                        $other_metal_wt = 0;

                                                        foreach($val['othermetal_details'] as $otrm)

                                                        {

                                                        	$other_metal_wt+=$otrm['grn_other_itm_grs_weight'];

                                                        	$otherMetal_data[]=array(

                                                        		'id_metal'    =>  $otrm['omcatid'],

                                                        		'id_purity'   =>  $otrm['grn_other_itm_pur_id'],

                                                        		'pcs'     =>  $otrm['grn_other_itm_pcs'],

                                                        		'gwt'     =>  $otrm['grn_other_itm_grs_weight'],

                                                        		'wastage_perc'   =>  $otrm['grn_other_itm_wastage'],

                                                        		'calc_type'   =>  $otrm['grn_other_itm_cal_type'],

                                                        		'rate_per_gram'   =>  $otrm['grn_other_itm_rate'],

                                                        		'amount'   =>  $otrm['grn_other_itm_amount'],

                                                        		'mc_value'  => $otrm['grn_other_itm_mc'],

                                                        	);



                                                        }

                                                        $metal_details=json_encode($otherMetal_data);

                                                        echo '<tr id="'.$ikey.'"">



                                                        	<td><select class="form-control category_select" name="item[category][]" value="" style="width:150px;">'.$catTypes.'</select><input type="hidden" name="item[grn_item_id][]" value="'.$val['grn_item_id'].'" /></td>



                                                        	<td><input type="number" class="form-control custom-inp pcs" name="item[pcs][]" value="'.$val['grn_no_of_pcs'].'" style="width:100px;" step="any" ></td>



                                                        	<td><input type="number" class="form-control custom-inp gross_wt" name="item[gross_wt][]" style="width:100px;" step="any" value="'.$val['grn_gross_wt'].'" ></td>



                                                        	<td><div class="form-group"><div class="input-group "><input class="form-control custom-inp add_less_wt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="item[less_wt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>



                                                        	<td><div class="form-group"><div class="input-group "><input class="form-control custom-inp add_other_metal_wt" value="'.$other_metal_wt.'"onClick="create_new_empty_other_metal_row($(this).closest(\'tr\'));"  type="number" step="any" style="width:100px;" readonly/><span class="input-group-addon input-sm add_other_metal_wt" onClick="create_new_empty_other_metal_row($(this).closest(\'tr\'));">+</span></div></div></td>



                                                        	<td><input type="number" class="form-control custom-inp net_wt" name="item[net_wt][]" value="'.$val['grn_net_wt'].'" style="width:100px;" step="any" readonly><input type="hidden" class="stone_details" value=\''.$stone_details.'\'

                                                        	name="item[stone_details][]"/><input type="hidden" class="other_metal_details" value='.$metal_details.' name="item[other_metal_details][]" /></td>



                                                        	<td><input type="number" class="form-control custom-inp wastage" name="item[wastage][]" value="'.$val['grn_wastage'].'" style="width:100px;" step="any" ></td>



                                                        	<td class="input-group"><input type="number" class="form-control custom-inp rate_per_gram" name="item[rate_per_gram][]" value="'.$val['grn_rate_per_grm'].'" style="width:100px;" step="any"><span class="input-group-btn" style="width: 70px;"><select class="ratecaltype form-control" name="item[rate_type][]" style="width:100px;"><option value="1" '.($val['itemratecaltype']==1 ? "selected" :'').'>Grm</option><option value="2" '.($val['itemratecaltype']==2 ? "selected" :'').' >Pcs</option></select></span></td>



                                                        	<td><input type="number" class="form-control custom-inp itemcost" style="width:100px;" step="any" value="'.$val['grn_item_cost'].'" ><input type="hidden" class="form-control custom-inp itemcaltype" style="width:100px;" step="any" value="1" ></td>



                                                        	<td><span class="taxable_amt"></span></td>



                                                        	<td><input type="hidden" name="item[item_total_tax][]" class="item_total_tax" ><input type="hidden" name="item[item_cgst][]" class="item_cgst" value="'.$val['grn_item_cgst'].'"><input type="hidden" name="item[item_sgst][]" class="item_sgst" value="'.$val['grn_item_sgst'].'"><input type="hidden" name="item[item_igst][]" class="item_igst" value="'.$val['grn_item_igst'].'"><input type="hidden" name="item[tax_percentage][]" class="tax_percentage"><span class="item_tax_amt"></span></td>



                                                        	<td><input type="number" class="form-control custom-inp item_cost" name="item[item_cost][]" style="width:100px;" step="any" readonly ></td>



                                                        	<td><a href="#" onClick="remove_grn_item_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

                                                        	</tr>';



										           }

										    }

										    ?>

										</tbody>

										<tfoot>

											<tr></tr>

										</tfoot>

									 </table>

								  </div>

								</div>

            			    </div>

        				</div>



        					<div class="row">

        				    <div class="col-md-12">



        				    </div>

        				    <div class="box box-default total_summary_details">

								<div class="box-body">

									<div class="row">

										<div class="col-md-6">

										    <label>Total Summary Details</label>

										   <div class="table-responsive">

											 <table id="total_summary_details" class="table table-bordered table-striped" style="text-transform: uppercase">

												<tbody>

												<?php



                                                    if(sizeof($grn_details['charge_details']>0))

                                                    {

                                                    	$other_ch_cgst = 0;

                                                    	$other_ch_sgst = 0;

                                                    	$other_ch_igst = 0;

                                                    	$char_tax_value = 0;

                                                    	$ChargeData=[];

                                                    	foreach($grn_details['charge_details'] as $val)

                                                    	{

                                                    		$other_ch_cgst += $val['cgst_cost'];

                                                    		$other_ch_sgst += $val['sgst_cost'];

                                                    		$other_ch_igst += $val['igst_cost'];

                                                    		if($other_ch_cgst!='0' && $other_ch_sgst!='0' )

                                                    		{

                                                    			$char_tax_value = $val['cgst_cost']+$val['sgst_cost'];

                                                    		}

                                                    		else

                                                    		{

                                                    			$char_tax_value = $val['igst_cost'];

                                                    		}



                                                    		$ChargeData[]=array(

                                                    			'charge_id' =>$val['grn_charge_id'],

                                                    			'charge_value' => $val['grn_charge_value'],

                                                    			'name_charge' => $val['name_charge'],

                                                    			'char_with_tax' => $val['char_with_tax'],

                                                    			'char_tax' => $val['char_tax'],

                                                    			'charge_tax_value' => $char_tax_value,

                                                    		);

                                                    	}

                                                    	$ChargeDetails=json_encode($ChargeData);

                                                    }

                                                    ?>

													<tr>



														<th>Taxable Amount</th>

														<th></th>

														<th><input type="number" class="form-control total_summary_taxable_amt" readonly></th>

													</tr>

													<tr>

													    <th>TDS</th>

													    <th>(-)</th>

													    <th>

													        <div class="input-group" >

                                			                       <input class="form-control tds_percent" type="number" name="order[tds_percent]" id="tds_percent" tabindex="21"  value="<?php echo $grn_details['grn_pay_tds_percent'];?>" />

                                			                       <span class="input-group-btn"><input type="number" class="form-control item_tds_tax_value" name="order[tds_tax_value]" id="item_tds_tax_value" tabindex="22" style="width:200px;"  readonly/></span>

                                			                </div>

													    </th>

													</tr>

													<tr>



														<th>CGST</th>

														<th>(+)</th>

														<th><input type="number" class="form-control total_summary_cgst_amount" readonly></th>

													</tr>

													<tr>



														<th>SGST</th>

														<th>(+)</th>

														<th><input type="number" class="form-control total_summary_sgst_amount" readonly></th>

													</tr>

													<tr>



														<th>IGST</th>

														<th>(+)</th>

														<th><input type="number" class="form-control total_summary_igst_amount" readonly></th>

													</tr>

													<tr>

													    <th>TCS</th>

													    <th>(+)</th>

													    <th>

													        <div class="input-group" >

                                			                       <input class="form-control tcs_percent" type="number" name="order[tcs_percent]" id="tcs_percent" tabindex="21"  value="<?php echo $grn_details['grn_tcs_percent'];?>" />

                                			                       <span class="input-group-btn"><input type="number" class="form-control item_tcs_tax_value" name="order[tcs_tax_value]" id="item_tcs_tax_value" tabindex="22" style="width:200px;" readonly /></span>

                                			                </div>

													    </th>

													</tr>

													<tr>

														<th>Other Charges</th>

														<th>(+)</th>

														<th>

														    <div class="input-group ">

                            									<input id="other_charges_taxable_amount"  name="order[other_charges_amount]" class="form-control custom-inp add_other_charges" type="number" step="any"  readonly tabindex="19" />

                            									<span class="input-group-addon input-sm add_other_charges">+</span>

                            									<input type="hidden" id="other_charges_details"  name="order[other_charges_details]" value='<?php echo $ChargeDetails;?>' />

                            								</div>

														</th>

													</tr>

													<tr>

													    <th>Charges TDS</th>

													    <th>(-)</th>

													    <th>

													        <div class="input-group" >

                                			                       <input class="form-control charges_tds_percent" type="number" name="order[charges_tds_percent]" id="charges_tds_percent" tabindex="21"  value="<?php echo $grn_details['grn_other_charges_tds_percent'];?>" />

                                			                       <span class="input-group-btn"><input type="number" class="form-control other_charges_tds_tax_value" name="order[other_charges_tds_tax_value]" id="other_charges_tds_tax_value" tabindex="22" style="width:200px;" readonly /></span>

                                			                </div>

													    </th>

													</tr>

													<tr style="display:none;">

														<th>Other Charges Tax</th>

														<th>(+)</th>

													    <th><input type="number" class="form-control other_charges_tax" name="order[other_charges_tax]" id="other_charges_tax" readonly></th>

													</tr>

													<tr>

														<th>OTHER CHARGES CGST</th>

														<th>(+)</th>

														<th><span class="other_charges_cgst"><?php echo $other_ch_cgst;?></span></th>

													</tr>

													<tr>



														<th>OTHER CHARGES SGST</th>

														<th>(+)</th>

														<th><span class="other_charges_sgst"><?php echo $other_ch_sgst;?></span></th>

													</tr>

													<tr>



														<th>OTHER CHARGES IGST</th>

														<th>(+)</th>

														<th><span class="other_charges_igst"><?php echo $other_ch_igst;?></span></th>

													</tr>

													<tr>



														<th>Discount</th>

														<th>(-)</th>

														<th>

														    <input type="number" class="form-control grn_discount" name="order[discount]" value="<?php echo $grn_details['grn_discount']; ?>" tabindex="20"  />

														</th>

													</tr>



													<tr>



														<th>Round Off</th>

														<th><select class="round_off_symbol"  name="order[round_off_type]"><option value="1">+</option><option value="0">-</option></select></th>

														<th><input type="number" class="form-control grn_round_off" name="order[round_off]" value="<?php echo $grn_details['grn_round_off'];?>" ></th>

													</tr>

													<tr>



														<th>Final Price</th>

														<th></th>

														<th>

														    <input type="number" class="form-control grn_total_cost" name="order[total_cost]" readonly>

														 </th>

													</tr>



												</tbody>

												<tfoot>

													<tr></tr>

												</tfoot>

											 </table>



										  </div>

										</div>



										<div class="col-md-6">

										    <div class="table-responsive">

											    <table id="item_details_preview" class="table table-bordered table-striped" style="text-transform: uppercase">

											        <thead>

            										  <tr>

            											<th width="10%;">Category</th>

            											<th width="5%;">Pieces</th>

            											<th width="5%;">Weight</th>

            											<th width="5%;">Rate</th>

            											<th width="5%;">Amount</th>

            										  </tr>

            										</thead>

            										<tbody></tbody>

            										<tfoot>

            											<tr>



            											</tr>

            										</tfoot>

											    </table>

											 </div>

										</div>

									</div>

								</div>

							</div>

        				</div>

        			<div class="row">

        			    <div class="col-md-6">

        			        <textarea style="resize:none" rows="6" cols="50" class="form-control return_remark" name="order[remarks]" placeholder="Enter Remarks"></textarea>

        			    </div>

        			</div>

        	        <div class="row">

        				   <div class=""><br/>

        					  <div class="col-xs-offset-5">

        						<button type="button" id="submit_grn_entry" class="btn btn-primary">Save</button>

        						<button type="button" class="btn btn-default btn-cancel">Cancel</button>



        					  </div> <br/>

        					</div>

        				</div>

                </div>

				<?php echo form_close();?>

				 </div>

				  <div class="overlay" style="display:none;">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

	           </div>





	       </div>

        </section>

    </div>













<div class="modal fade" id="cus_stoneModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:90%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Stone Details</h4>

			</div>



			<div class="modal-body">

			    <div class="row">

        			<div class="col pull-right">

        			    <button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>

        			</div>

    			</div>

    			<div class="row">

    					<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">

        					<thead>

            					<tr>

                					<th width="5%">LWT</th>

                					<th width="10%">Type</th>

                					<th width="13%">Name</th>

									<th width="13%">Code</th>

                					<th width="10%">Pcs</th>

                					<th width="17%">Wt</th>

                					<th width="10%">Cal.Type</th>

									<th width="10%">Cut</th>

									<th width="10%">Color</th>

									<th width="10%">Clarity</th>

									<th width="10%">Shape</th>

                					<th width="10%">Rate</th>

                					<th width="15%">Amount</th>

                					<th width="10%">Action</th>



            					</tr>

        					</thead>

        					<tbody></tbody>

        					<tfoot>

        					    <tr></tr>

        					</tfoot>

    					</table>

    			</div>

		  </div>

		  <div class="modal-footer">

			<button type="button" id="update_grn_stn_details" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>



<div class="modal fade" id="cus_orderdetailsModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:73%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Order Details</h4>

			</div>

			<div class="modal-body">

			    <div class="row">

			         <div class="col-md-12">



        				        <div class="col-md-6">

            	                     <div class="form-group">

            	                       <label>Ordered Pcs : </label>

            							<strong>

            							      <span id="orderedpcs"></span>

        								</strong>

            	                     </div>

        				        </div>

        				        <div class="col-md-6">

            	                     <div class="form-group">

            	                       <label>Ordered Wt : </label>

            							<strong>

            							      <span id="orderedwt"></span>

        								</strong>

            	                     </div>

        				        </div>

        				        <div class="col-md-6">

            	                     <div class="form-group">

            	                       <label>Received Pcs : </label>

            							<strong>

            							      <span id="receivedpcs"></span>

        								</strong>

            	                     </div>

        				        </div>

        				        <div class="col-md-6">

            	                     <div class="form-group">

            	                       <label>Received Wt : </label>

            							<strong>

            							      <span id="receivedwt"></span>

        								</strong>

            	                     </div>

        				        </div>

					</div>

    			</div>

    			<div class="row">

    					<input type="hidden" id="cur_order_row" value="0">

    					<table id="order_items_details" class="table table-bordered table-striped text-center">

        					<thead>

            					<tr>

                					<th width="10%">Category</th>

                					<th width="10%">Product</th>

                					<th width="10%">Design</th>

                					<th width="10%">Sub Design</th>

                					<th width="7%">Pcs</th>

                					<th width="7%">Weight Range</th>

                					<th width="7%">Rec Pcs</th>

                					<th width="10%">Rec Wt</th>

            					</tr>

        					</thead>

        					<tbody></tbody>

        					<tfoot>

        					    <tr></tr>

        					</tfoot>

    					</table>

    			</div>

		  </div>

		  <div class="modal-footer">

			<button type="button" id="close_order_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>



<!-- Charges Modal -->



<div class="modal fade" id="cus_chargeModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



    <div class="modal-dialog" style="width:50%;">



		<div class="modal-content">



			<div class="modal-header">



				<h4 class="modal-title" id="myModalLabel">Add Charges( <span class="add_charges"><i class="fa fa-plus"></i></span> )</h4>



			</div>



			<div class="modal-body">



				<div class="row">



					<input type="hidden" id="charge_active_row" value="0">



					<table id="table_charges" class="table table-bordered table-striped text-center">



    					<thead>



        					<tr>



								<th>SNo</th>



            					<th>Charge Name</th>



            					<th>Charge</th>



            					<th>Action</th>



        					</tr>



    					</thead>



    					<tbody></tbody>



    					<tfoot><tr></tr></tfoot>



					</table>



			    </div>



		    </div>



		  <div class="modal-footer">



			<button type="button" id="update_charge_details" class="btn btn-success">Save</button>



			<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>



		  </div>



		</div>



	</div>



</div>



<div class="modal fade" id="other_metalmodal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:72%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Other Metals</h4>

			</div>

			<div class="modal-body">

				<div class="row">

        			<div class="col pull-right">

        			    <button type="button" id="create_other_metal_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>

        			</div>

    			</div>

				<div class="row">

					<input type="hidden" id="charge_active_row" value="0">

					<table id="other_metal_table" class="table table-bordered table-striped text-center">

    					<thead>

        					<tr>

            					<th width="15%">Category</th>

            					<th width="10%">Purity</th>

            					<th width="10%">Pcs</th>

            					<th width="10%">Gwt</th>

            					<th width="10%">V.A(%)</th>

            					<th width="10%">Mc Type</th>

            					<th width="10%">Mc</th>

            					<th width="10%">Rate</th>

            					<th width="10%">Amount</th>

            					<th width="10%">Action</th>

        					</tr>

    					</thead>

    					<tbody></tbody>

    					<tfoot><tr style="font-weight:bold;"><td>Total</td><td></td><td class="total_pcs"></td><td class="total_wt"></td><td style="display:none;"></td><td style="display:none;"></td><td style="display:none;"></td><td></td><td class="total_amount"></td><td></td></tr></tfoot>

					</table>

			    </div>

		    </div>

		  <div class="modal-footer">

			<button type="button" id="update_grn_other_metal_details" class="btn btn-success">Save</button>

			<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>



<!-- Edit Karigar -->

<div class="modal fade" id="confirm-edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Edit Karigar</h4>

			</div>

			<div class="modal-body">

				<div class="row">

					<div class="form-group">

					   <label for="kar_first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="kar_first_name" name="kar[first_name]" placeholder="Enter Karigar first name" required="true">



							<p class="help-block kar_first_name error"></p>

					   </div>

					</div>

				</div>

				<div class="row">

					<div class="form-group">

					   <label for="kar_pan_no" class="col-md-3 col-md-offset-1 ">Pan No<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="kar_pan_no" name="kar[kar_pan_no]" placeholder="Enter Karigar Pan No" required="true">

							<p class="help-block kar_first_name error"></p>

					   </div>

					</div>

				</div>

				<div class="row">

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select Country<span class="error">*</span></label>

					   <div class="col-md-6">

						 <select class="form-control" id="country" style="width:100%;"></select>

						 <input type="hidden" name="kar[id_country]" id="id_country">

					   </div>

					</div>

				</div></br>

			    <div class="row">

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select State<span class="error">*</span></label>

					   <div class="col-md-6">

						 <select class="form-control" id="ed_state" style="width:100%;"></select>

						  <input type="hidden" name="kar[id_state]" id="ed_id_state">

					   </div>

					</div>

				</div></br>



				 <div class="row">

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select City<span class="error">*</span></label>

					   <div class="col-md-6">

						 <select class="form-control" id="ed_city"  style="width:100%;"></select>

						  <input type="hidden" name="kar[id_city]" id="ed_id_city">

					   </div>



					</div>

				</div></br>



				<div class="row">

					<div class="form-group">

					    <label for="address1" class="col-md-3 col-md-offset-1 ">Address1<span class="error">*</span></label>

						   <div class="col-md-6">

								<input class="form-control" id="address1" name="kar[address1]" value=""  type="text" placeholder="Enter Address Here 1" required />

								<p class="help-block address1 error"></p>

							</div>

					</div>

				</div></br>

				<div class="row">

					<div class="form-group">

					    <label for="address2" class="col-md-3 col-md-offset-1">Address2</label>

						   <div class="col-md-6">

								<input class="form-control" id="address2" name="kar[address2]" placeholder="Enter Address Here 2" value=""  type="text" />

							</div>

					</div>

				</div></br>

				<div class="row">

					<div class="form-group">

					    <label for="address3" class="col-md-3 col-md-offset-1">Address3</label>

						   <div class="col-md-6">

								<input class="form-control titlecase" id="address3" name="kar[address3]" value=""  type="text" placeholder="Enter Address Here 3" />

							</div>

					</div>

				</div></br>

				<div class="row">

					<div class="form-group">

					    <label for="pincode" class="col-md-3 col-md-offset-1">Pin Code<span class="error">*</span></label>

						   <div class="col-md-6">

								<input class="form-control titlecase" id="pin_code_add" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' required />

								<p class="help-block pincode error"></p>

							</div>

					</div>

				</div></br>



				<div class="row gst">

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">GST No<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="gst_no" name="kar[gst_no]" placeholder="Enter GST No">

							<p class="help-block kar_mobile"></p>

					   </div>

					</div>

				</div>

			</div>

		  <div class="modal-footer">

		     <input type="hidden" name="kar[id_karigar]" id="id_karigar" value="">

			 <a href="#" id="update_kardetails" class="btn btn-success">Update</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>


<div class="modal fade" id="grn_imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" style="width:60%;">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">Add Image</h4>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12">
                      <div class="col-md-8">
                          <label>Note - Click Snapshot Button To Take Your Images Screen Shot</label>
                          <input id="bulktag_images" class="bulktag_images" name="bulktag_images" accept="image/*" type="file" multiple="true">
                      </div>
                      <div class="col-md-4">
                          <input type="button" value="Take Snapshot" onclick="take_snapshot('pre_images')" class="btn btn-warning" id="snap_shots"><br>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      <div class="col-md-3"></div>
                      <div class="col-md-6" id="my_camera" style="width: 290px; height: 190px;"><div></div><video autoplay="autoplay" style="width: 290px; height: 190px;"></video></div>
                      <input type="hidden" name="image" class="image-tag">
                      <div class="col-md-3"></div>
                  </div>
              </div>
              <div class="row" id="image_lot_list" style="display:none;">
                  <div class="col-md-12" style="font-weight:bold;"> Images List</div>
              </div><br>
              <div class="row">
                  <div class="col-md-12" id="uploadArea_p_stn"></div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" id="grn_update_img" class="btn btn-success">Save</button>
              <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>




<script type="text/javascript">

     var Categories  = new Array();

     var categoryDet = new Array();

     categoryDet = JSON.parse('<?php echo json_encode($categories); ?>');

</script>



