<!-- Content Wrapper. Contains page content -->



<style>



	.remove-btn{



		margin-top: -168px;



	    margin-left: -38px;



	    background-color: #e51712 !important;



	    border: none;



	    color: white !important;



	}



	.stickyBlk {



	    margin: 0 auto;



	    top: 0;



	    width: 100%;



	    z-index: 999;



	    background: #fff;



	}



	.tag-form .form-group{



	    margin-bottom: 1px;



	    display: block;



        width: 100%;



        /*height: 25px;*/



        padding: 2px 6px;



	}



	.tag-form .input-group {



	    margin-bottom: 1px;



        width: 100%;



        /*height: 25px;*/



	}



	.tag-form input, .tag-form .input-group-addon{



	    /*height : 25px;*/



	}



	.tag-form select{



	    /*height : 25px;*/



	    padding:0px 0px 0px 12px;



	}



	.add_attributes, .add_charges, #create_other_metal_item_details {



		cursor: pointer;



		color: blue;



	}



	.remove_tag_attribute, .remove_charge_item_details, .remove_other_metal{



		margin-left: 5px;



	}



	#update_attribute_detail tr td .select2-container {



		width: 100% !important;



	}



	.huid-group, .certs-group {



		text-align: center;



	}



	.huids {



		width: 49%;



		display: inline-block !important;



	}



	.certs {



		width: 44%;



		display: inline-block !important;



	}



	.cert_rm_icon {



		width: 9%;



		display: inline-block !important;



		height: 25px;



		padding: 6px;



		margin-bottom: 5px;



		cursor: pointer;



	}



	#cert_img_preview {



		width: 75%;

		height: 75%;



	}



	.cert_img_container {



		display: none;

		padding-top: 25px;

		text-align: center;



	}



	.cert_img_preview_container {



		padding-top: 10px;



	}



	.multimetal {



		display: none;



	}



	.tag_reload_div {

		

		color: white;

		background: red;

		font-size: 17px;

		font-weight: bold;

		padding: 10px;

		margin-bottom: 10px;

		border-radius: 5px;

		display: none;



	}



	#tag_gwt, #gwt_uom_id {



		display: inline-block;



	}



	#tag_gwt {



		width: 100%;



	}



	#gwt_uom_id {

		

		width: 25%;



		display: none;



	}



	.stone_calc {

		display: none;

	}



	



	*[tabindex]:focus {



		outline: 1px black solid;



	}

	

	#tag_wast_perc , #tag_wast_value {

		display: inline-block;

	}



	#tag_wast_perc {

		width: 40%;

	}



	#tag_wast_value {

		width: 40%;

	}



        



</style>



<style>

  .cert_img_preview_container {

    display: flex;

    justify-content: center;

    align-items: center;

    flex-direction: column;

  }



  .cert_img_preview_container img {

    /* Add any additional styling for the image here */

    width: 100px;

    height: 100px;

  }

</style>



<?php if(isset($tag_prints) && trim($tag_prints) != '') { ?>



  	<script type="text/javascript">



  	 	window.open('<?php echo base_url() ?>index.php/admin_ret_tagging/tagging/generate_barcode?tag=<?php echo $tag_prints ?>', '_blank');



  	</script>



<?php } ?>



<div class="content-wrapper">



    <!-- Content Header (Page header) -->



    <!--<section class="content-header">



          <h1>



        	Tagging



            <small>Tag</small>



          </h1>



          <ol class="breadcrumb">



            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>



            <li><a href="#">Tagging</a></li>



            <li class="active">Tag</li>



          </ol>



    </section>-->



    <!-- Main content -->



    <section class="content product">



          <!-- Default box -->



          <div class="box box-primary">



            <div class="box-body">  

                  <div class="row">

				  	<div class="col-md-offset-2 col-md-8">  

	                  <div class="box box-default">  

	                   <div class="box-body">  

						   <div class="row">

						            <div class="col-md-2"> 

    									<label>Tag Code</label>

    									<div class="form-group">

    									    <input type="text" class="form-control" id="tag_number" placeholder="Enter Tag Code">

    									    <!--<input type="hidden" id="tag_id">-->

    									</div>

    								</div>

    								

                                    <div class="col-md-2"> 

                                        <label>Old Tag</label>

                                        <div class="form-group">

                                            <input type="text" class="form-control" id="old_tag_number" placeholder="Enter Tag Code">

                                        </div>

                                    </div>

                                        

    								<div class="col-md-2"> 

    									<label></label>

    									<div class="form-group">

										    <button type="button" id="tagging_history_search" class="btn btn-info">Search</button>   

									    </div>

    								</div>



                                    <div class="col-md-2">

						 			<div class="form-group">

							 			<button type="button" class="btn btn-success" id="duplicate_print" style="margin-top:20px;">Duplicate Print</button>

									</div>

						 		</div>

							</div>

						 </div>

	                   </div> 

	                  </div> 

                   </div> 



            <div class="box-body">



	



             <!-- form container -->



              <div class="row">



	             <!-- form -->



				<!-- <php echo form_open_multipart(( $tagging['tag_id'] != NULL && $tagging['tag_id'] > 0 ? 'admin_ret_tagging/tagging/update/'.$tagging['tag_id']:'admin_ret_tagging/tagging/save'),array('id'=>'tag_form')); ?> -->



				<div class="col-sm-12"> 



					<!-- Lot Details Start Here -->



					<div class="row">		

                        

                    <?php  if($profile['tag_details'] == 1 ){?>  



			    		<div class="col-sm-4">





			    			<div class="row">				    	



					    		<div class="col-sm-3">



					    			<label> Branch </label>



						 		</div>



						 		<div class="col-sm-8">



						 			<div class="form-group">



                                     <input id="branch" class="form-control" name= "order[branch]" type="text" value="" tabindex="18"readonly />

                                     <input id="tag_id" class="form-control"  type="hidden" value="" tabindex="18"readonly />





									</div>



						 		</div>



						 	</div>



                            <div class="row">				    	



                                    <div class="col-sm-3">



                                        <label> Tag Date </label>



                                    </div>



                                    <div class="col-sm-8">



                                        <div class="form-group">



                                        <input id="tag_date" class="form-control" name= "order[tag_date]"  value="" tabindex="18"readonly />





                                        </div>



                                    </div>



                             </div>

                                                                

                            



                    



							<div class="row">



								<div class="col-sm-3">



									<label>Product<span class="error">*</span></label>



								</div>



								<div class="col-sm-8">



									<div class="form-group"> 



                                    <input id="product" class="form-control" name= "order[product]" type="text" tabindex="18" readonly/>



									</div>



								</div>



							</div> 





						 	

						 	





								<!-- <div class="row">



								    <div class="col-sm-12">



						 		            <b><span class="lt_desc_tab">Certification Image</span></b>



						 		   </div>



                                   

						 		   <div class="col-sm-12 cert_img_preview_container">



					 		        <img src="" id="cert_img_preview" />



					 		        </div>



								</div> -->

                                <div class="row">

                                    <div class="col-sm-12">

                                        <b><span class="lt_desc_tab"> Image</span></b>

                                    </div>

                                    <div class="col-sm-12 cert_img_preview_container">

                                        <img src="" id="cert_img_preview" alt="Image" />

                                        <!-- <a href="#" class="btn btn-secondary order_img" data-toggle="modal" data-target="#imageModal_bulk_edit"> -->



                                        <!-- Add the eye icon below the image -->

                                        <!-- <a href="#" class="btn btn-secondary order_img" id="edit" data-toggle="modal" data-id=""> -->

                                        <!-- <i class="fa fa-eye"></i> -->

                                        </a>

                                    </div>

                                    </div>







				 		</div>



                         <?php } ?> 



				 		<div class="col-sm-8"> 



        				 	<div class="row" >



                             <?php  if($profile['tag_details'] == 1){?>  



        					 	<div class="col-sm-7 tag-form">



						 			

				 		        	

				 		        	<div class="row">





                                        <div class="col-sm-7">



                                           <div class="form-group"> 



                                        

                                            </div>





                                            </div>



                                        </div>

                                        



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Design <span class="error">*</span></label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 





                                             <input id="design" class="form-control" name= "order[id_design]" type="text" tabindex="18"readonly />





											</div>



							 			</div>



				 		        	</div>



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Sub Design <span class="error">*</span></label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



                                                 <input id="id_sub_design" class="form-control" name= "order[id_sub_design]" type="text" tabindex="18"readonly />





											</div>



							 			</div>



				 		        	</div>

				 		        	             

                                    



                                     <div class="row">



                                            <div class="col-sm-3">



                                                <label>Section<span class="error">*</span></label>



                                            </div>



                                            <div class="col-sm-7">



                                                <div class="form-group"> 



                                                    <input id="id_section" class="form-control" name= "order[id_section]" type="text" tabindex="18"readonly />





                                            </div>



                                            </div>



                                            </div>



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Pieces <span class="error">*</span></label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



						 		        		<input id="tag_pcs" class="form-control custom-inp" type="number" tabindex="5" readonly/>





											</div>



							 			</div>



				 		        	</div>



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Gross Wgt <span class="error">*</span></label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



                                             <input id="gross_wt" class="form-control" name= "order[gross_wt]" type="number" tabindex="18"readonly />



											</div>



							 			</div>



				 		        	</div> 



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Less Wgt</label>



							 			</div>



							 			<div class="col-sm-7">



							 			    <div class="form-group">



												<div class="input-group ">



													<input id="tag_lwt" class="form-control custom-inp add_tag_lwt" name ="order[less_wt]"type="number" step="any" readonly tabindex="7"/>



												</div>



											</div>



							 			</div>



				 		        	</div>



                                     <div class="row">



                                        <div class="col-sm-3">



                                            <label>Dia wt</label>



                                        </div>



                                        <div class="col-sm-7">



                                            <div class="form-group">



                                            <div class="input-group ">



                                                <input id="tag_dia_wt" class="form-control custom-inp " type="number" step="any" readonly tabindex="7"/>



                                            </div>



                                        </div>



                                        </div>



                                        </div>





                                        <div class="row" style="display:none;">



                                        <div class="col-sm-3">



                                            <label>Stone wt</label>



                                        </div>



                                        <div class="col-sm-7">



                                            <div class="form-group">



                                            <div class="input-group ">



                                                <input id="tag_stone_wt" class="form-control custom-inp " type="number" step="any" readonly tabindex="7"/>



                                            </div>



                                        </div>



                                        </div>



                                        </div>



								



				 		            <div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Net Wgt</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



						 		        		<input id="tag_nwt" class="form-control custom-inp" type="number" step="any" readonly/>



											</div>



							 			</div>



				 		        	</div>





                                     <div class="row">



                                        <div class="col-sm-3">



                                            <label>Wastage %</label>



                                        </div>



                                        <div class="col-sm-7">



                                            <div class="form-group"> 



                                            <input id="tag_wast_perc" class="form-control custom-inp" type="number" step="any" value="0" tabindex="9"readonly>% 

                                            &nbsp;&nbsp;&nbsp;

                                            <input id="tag_wast_value" class="form-control custom-inp" type="number" step="any" value="0" tabindex="9"readonly> Wt



                                                <input type="hidden" id="min_va">



                                        </div>



                                        </div>



                                        </div>





				 		        	<!-- <div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Wastage %</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



                                                 <input id="tag_waste_per" class="form-control custom-inp" name="order[tag_waste_per]" type="number" step="any" readonly="">



											</div>



							 			</div>



				 		        	</div> -->



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>MC Type</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



                                             <input id="tag_mc_type" class="form-control custom-inp" name="order[mc_type]"type="text" step="any" readonly/>



						 		        	



											</div>



							 			</div>



				 		        	</div>  



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>MC <span class="error">*</span></label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



						 		        		<input id="tag_mc_value" class="form-control custom-inp" type="number" step="any" autocomplete="off" value="0" tabindex="10" readonly>





											</div>



							 			</div>



				 		        	</div>



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Size</label>

							 			



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



                                                 <input id="size" class="form-control custom-inp" name="order[size]"type="text" step="any" readonly/>



											</div>



							 			</div>



				 		        	</div>





                                     <div class="row">



                                    <div class="col-sm-3">



                                        <label>Purity</label>



                                    </div>



                                    <div class="col-sm-7">



                                        <div class="form-group"> 



                                            <input id="purity" class="form-control custom-inp" name="order[purity]"type="text" step="any" readonly/>



                                    </div>



                                    </div>



                                    </div>



				 		        



				 		        	<div class="row tag_calc">



				 		        	    <div class="col-sm-3">



							 				<label>Calc Type <span class="error">*</span></label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



                                             <input id="calc_type" class="form-control custom-inp" name="order[calc_type]" type="text" step="any" readonly/>



						 		        	



											</div>



							 			</div>



				 		        	</div>



								



				 		        	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>HUID</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group huid-group"> 



						 		        		<input id="tag_huid" class="huids form-control custom-inp" type="text" step="any" value="" tabindex="12" readonly />



												<input id="tag_huid2" class="huids form-control custom-inp" type="text" step="any" value="" tabindex="13" readonly/>



											</div>



							 			</div>



				 		        	</div>



				 		        	

									 <!--	Charges	 -->



							 		<input type="hidden" id="tag_stone_details"> 



							 		<input type="hidden" id="tag_charge_amt"> 



									<div class="row" style="display: none;">



				 		        	    <div class="col-sm-3">



							 				<label>Charges</label>



							 			</div>



							 			<div class="col-sm-7">



							 			    <div class="form-group">



												<div class="input-group ">



													<input id="tag_charge" class="form-control custom-inp add_tag_charge" type="number" step="any" readonly/>



													<span class="input-group-addon input-sm add_tag_charge">+</span>



												</div>



											</div>



							 				<!--<button type="button" class="btn btn-success btn-sm" data-toggle="tooltip" title="Add Charges" onclick="add_charge()"><i class="fa fa-plus"></i> Add</button>-->



							 			</div>



				 		        	</div>



								



									<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Certification No</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



                                                 <input id="certi_no" class="form-control custom-inp" name="order[certi_no]" type="number" step="any" readonly/>



											</div>



							 			</div>



				 		        	</div>



									<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Manuf Code</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



						 		        		<input id="manufacture_code" class="form-control" type="text" tabindex="18" readonly />



											</div>



							 			</div>



				 		        	</div>





									<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Style Code</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



						 		        		<input id="style_code" class="form-control" type="text" tabindex="19" readonly/>



											</div>



							 			</div>



				 		        	</div>







                                    	<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Sale Value</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



						 		        		<input id="tag_sale_value" class="form-control" type="number" step="any" readonly />



											</div>



							 			</div>



				 		        	</div>  





									<div class="row">



				 		        	    <div class="col-sm-3">



							 				<label>Old Tag Id</label>



							 			</div>



							 			<div class="col-sm-7">



							 				<div class="form-group"> 



						 		        		<input id="old_tag_id" class="form-control" type="text" tabindex="20" readonly />



											</div>



							 			</div>



				 		        	</div>





                                    

									<div class="row">



                                        <div class="col-sm-3">



                                            <label>Tag Status</label>



                                        </div>



                                        <div class="col-sm-7">



                                            <div class="form-group"> 



                                                <b><input id="tag_status" class="form-control" type="text" tabindex="20" readonly /></b>



                                        </div>



                                        </div>



                                        </div>

                                        <div class="row">



                                            <div class="col-sm-3">



                                                <label>Remark</label>



                                            </div>



                                            <div class="col-sm-7">



                                                <div class="form-group"> 



                                                    <input id="remark" class="form-control" type="text" tabindex="20" readonly />



                                            </div>



                                            </div>



                                            </div>





        						</div>



                                <?php } ?> 



                                <?php  if($profile['purchase_details'] == 1){?>  



                                <div class="col-sm-5">



								 <div class="row">



                                  <div class="col-sm-3">



                                       <label>Karigar<span class="error"></span></label>



                                  </div>



                                  <div class="col-sm-8">



                                    <div class="form-group"> 



                                    <input id="tag_karigar" class="form-control" name="order[karigar]" type="text" tabindex="18" readonly="">



                                  </div>



                                </div>



                                </div>





                                <div class="row">



                                    <div class="col-sm-3">



                                    <label>Order No<span class="error">*</span></label>



                                    </div>



                                    <div class="col-sm-8">



                                    <div class="form-group">



                                    <div id="order_link"> 





                                    <input id="order_no" class="form-control" name="order[order_no]" type="text" tabindex="18" readonly="">



                                    </div>



                                    </div>



                                    </div>



                                    </div>

<!-- 

                                   <div class="row">



                                       <div class="col-sm-3">



                                          <label>Purchase Order No<span class="error">*</span></label>



                                        </div>



                                        <div class="col-sm-8">



                                        <div class="form-group"> 

                                            



                                        <input id="purchase_order_no" class="form-control" name="order[purchase_order_no]" type="text" tabindex="18" readonly="">



                                        </div>



                                        </div>



                                    </div> -->



                                    <div class="row">

                                    <div class="col-sm-3">

                                        <label>Purchase Order No<span class="error">*</span></label>

                                    </div>

                                    <div class="col-sm-8">

                                        <div class="form-group">

                                        <div id="purchase_order_link">

                                            <input id="purchase_order_no" class="form-control" name="order[purchase_order_no]" type="text" tabindex="18" readonly>

                                        </div>

                                        </div>

                                    </div>

                                    </div>





    

                                    <div class="row">



                                    <div class="col-sm-3">



                                        <label>Lot No<span class="error">*</span></label>



                                    </div>



                                    <div class="col-sm-8">



                                        <div class="form-group"> 



                                        <input id="lot_no" class="form-control" name= "order[lot_no]" type="number" tabindex="18" readonly/>



                                        </div>



                                    </div>



                                    </div> 



                           



                                    <div class="row">



                                        <div class="col-sm-3">



                                            <label>Lot Date<span class="error">*</span></label>



                                        </div>



                                        <div class="col-sm-8">



                                            <div class="form-group"> 



                                                <input id="lot_date" class="form-control" name= "order[lot_date]"  tabindex="18" readonly/>



                                            </div>



                                        </div>



                                        </div> 



                                    

                                        <div class="row">



                                            <div class="col-sm-3">



                                            <label>Purchase touch<span class="error">*</span></label>



                                            </div>



                                            <div class="col-sm-8">



                                            <div class="form-group"> 



                                            <input id="purchase_touch" class="form-control" name="order[purchase_touch]" type="number" tabindex="18" readonly="">



                                            </div>



                                            </div>



                                            </div>



                                <div class="row">



                                <div class="col-sm-3">



                                    <label>Purchase wastage<span class="error">*</span></label>



                                </div>



                                <div class="col-sm-8">



                                    <div class="form-group"> 



                                    <input id="purchase_wastage" class="form-control" name="order[purchase_wastage]" type="number" tabindex="18" readonly="">



                                    </div>



                                </div>



                                </div>



                                

                                <div class="row">



                                <div class="col-sm-3">



                                    <label>Purchase Making charges<span class="error">*</span></label>



                                </div>



                                <div class="col-sm-8">



                                    <div class="form-group"> 



                                    <input id="purchase_making_charges" class="form-control" name="order[purchase_making_charges]" type="number" tabindex="18" readonly="">



                                    </div>



                                </div>



                                </div>



                                <div class="row">



                                <div class="col-sm-3">



                                    <label>Age<span class="error">*</span></label>



                                </div>



                                <div class="col-sm-8">



                                    <div class="form-group"> 



                                    <input id="age" class="form-control" name="order[age]" type="number" tabindex="18" readonly="">



                                    </div>



                                </div>



                                </div>





                                <div class="row">



                                        <div class="col-sm-3">



                                            <label>Employee<span class="error">*</span></label>



                                        </div>



                                        <div class="col-sm-8">



                                            <div class="form-group"> 



                                            <input id="employee" class="form-control" name="order[employee]" type="text" tabindex="18" readonly="">



                                            </div>



                                        </div>



                                        </div>





        					   </div>



                               <?php } ?> 



							



    					   </div>



    				    </div>



				 		



				 	</div>



				 	

				 	<p class="help-block"></p>			 



				 	<!--/Block 2--> 



				</div>	<!--/ Col --> 



			</div>	 <!--/ row -->



		   <p class="help-block"> </p>  







           

    <div align="left"  style="background: #f5f5f5">



    <ul class="nav nav-tabs" id="billing-tab">



    <?php  if($profile['stone_details'] == 1){?>  



    <li id="item_summary" class="active"><a id="tab_items" href="#stone_details" data-toggle="tab">Stone Details</a></li>



    <li id="tab_tot_summary"><a href="#metal_details" data-toggle="tab">Metal Details</a></li>



   

    <?php } ?> 

    <?php  if($profile['estimation'] == 1){?>  



    <li id="tab_tot_summary"><a href="#estimation_details" data-toggle="tab">Billing</a></li>

  

    <?php } ?> 

    <?php  if($profile['branch_transfer_details'] == 1){?>  



    <li id="tab_make_pay"><a href="#branch_transfer_details" data-toggle="tab">Branch Transfer Details</a></li>

   

    <?php } ?> 



    <?php if($profile['section_transfer_details'] == 1){?>  



    <li id="tab_make_pay"><a href="#section_transfer_details" data-toggle="tab">Section Transfer Details</a></li>



    <?php } ?> 



    <?php   if($profile['scan_details'] == 1){?>  



    <li id="tab_scan_details"><a href="#scan_details" data-toggle="tab">Scan Details</a></li>



    <?php } ?> 



    <?php  if($profile['scan_details'] == 1){?>  



    <li id="tab_tot_stock_issue"><a href="#stock_issue_details" data-toggle="tab">Stock Issue Details</a></li>



    <?php } ?> 





      </ul>



</div>



            <div class="tab-content">



                			        <!-- Search Block	 -->

                         <?php  if($profile['stone_details'] == 1){?>  

      

                            <div class="tab-pane active" id="stone_details">



                		    <div class="row">



        						<div class="col-sm-6">



        							<div class="box box-default payment_blk">



        								<div class="box-body">



        									<div class="row">



        										<div class="col-md-12">



                                                    <div class="box-header with-border">



                                                	  <h3 class="box-title">STONE DETAILS</h3>

                                                    



                                                	</div>



                                        	      <div class="row">



                                        				<div class="box-body">



                                        				   <div class="table-responsive">



                                        					 <table id="tag_stone_details" class="table table-bordered table-striped text-center">



                                        						<thead>



                                        						  <tr>



                                        							<th>Stone Name</th>



                                        							<th>Uom Name</th>   



                                                                    <th>Clarity</th>   



                                        							<th>Cut</th>   



                                        							<th>Color</th>



                                                                    <th>Shape</th>



                                                                    <th>Pieces</th>



                                                                    <th>Weight</th>

                                                                    <th>Calc Type</th>

                                                                    <th>Rate</th>


                                                                    <th>Amount</th>




                                        						  </tr>



                                        						</thead> 



                                        						<tbody>



                                        						</tbody>



                                        						<tfoot>



                                        							<tr></tr>



                                        						</tfoot>



                                        					 </table>



                                        				  </div>



                                        				</div> 



                                        			</div>



                                                </div>



        									</div>



        									



        								</div>



        							</div>



        						</div>



        					</div>



                		</div>







                        <!-- //Metal Details -->





                        <div class="tab-pane " id="metal_details">



                		    <div class="row">



        						<div class="col-sm-6">



        							<div class="box box-default payment_blk">



        								<div class="box-body">



        									<div class="row">



        										<div class="col-md-12">



                                                    <div class="box-header with-border">



                                                	  <h3 class="box-title">METAL DETAILS</h3>

                                                    



                                                	</div>



                                        	      <div class="row">



                                        				<div class="box-body">



                                        				   <div class="table-responsive">



                                        					 <table id="tag_metal_details" class="table table-bordered table-striped text-center">



                                        						<thead>



                                        						  <tr>



                                        							<th>Category</th>



                                        							<th>Purity</th>   



                                                                    <th>Pieces</th>   



                                        							<th>Weight</th>   



                                        							<th>V.A(%)</th>



                                                                    <th>Rate</th>



                                                                    <th>Amount</th>





                                        						  </tr>



                                        						</thead> 



                                        						<tbody>



                                        						</tbody>



                                        						<tfoot>



                                        							<tr></tr>



                                        						</tfoot>



                                        					 </table>



                                        				  </div>



                                        				</div> 



                                        			</div>



                                                </div>



        									</div>



        									



        								</div>



        							</div>



        						</div>



        					</div>



                		</div>



                        <?php } ?> 



                        <div class="tab-pane" id="scan_details">



                            <div class="row">



                                <div class="col-sm-12">



                                    <div class="box box-default payment_blk">



                                        <div class="box-body">



                                            <div class="row">



                                                <div class="col-md-12">



                                                    <div class="box-header with-border">



                                                    <h3 class="box-title">Scan Details</h3>



                                                        <div class="box-tools pull-right">





                                                        </div>



                                                    </div>



                                                <div class="row">



                                                        <div class="box-body">



                                                        <div class="table-responsive">



                                                            <table id="tag_scan_details" class="table table-bordered table-striped text-center">



                                                                <thead>



                                                                <tr>



                                                                    <th>Date</th>



                                                                    <th>Scanned By</th>   



                                                               

                                                                </tr>



                                                                </thead> 



                                                                <tbody>



                                                                </tbody>



                                                                <tfoot>



                                                                    <tr></tr>



                                                                </tfoot>



                                                            </table>



                                                        </div>



                                                        </div> 



                                                    </div>



                                                </div>



                                            </div>



                                            



                                        </div>



                                    </div>



                                </div>



                            </div>



                            </div>





                        <div class="tab-pane" id="estimation_details">



                        <div class="row">



                            <div class="col-sm-12">



                                <div class="box box-default payment_blk">



                                    <div class="box-body">



                                        <div class="row">



                                            <div class="col-md-12">



                                                <div class="box-header with-border">



                                                <h3 class="box-title">Billing</h3>



                                                    <div class="box-tools pull-right">





                                                    </div>



                                                </div>



                                            <div class="row">



                                                    <div class="box-body">



                                                    <div class="table-responsive">



                                                        <table id="tag_billing" class="table table-bordered table-striped text-center">



                                                            <thead>



                                                            <tr>



                                                                <th>Bill No</th>



                                                                <th>Date</th>   



                                                                <th>Customer</th>   



                                                                <th>Mobile</th>



                                                                <th>Branch</th>



                                                                <th>Bill No</th>



                                                            </tr>



                                                            </thead> 



                                                            <tbody>



                                                            </tbody>



                                                            <tfoot>



                                                                <tr></tr>



                                                            </tfoot>



                                                        </table>



                                                    </div>



                                                    </div> 



                                                </div>



                                            </div>



                                        </div>



                                        



                                    </div>



                                </div>



                            </div>



                        </div>



                        </div>



                       <div class="tab-pane" id="branch_transfer_details">



                                <div class="row">



                                    <div class="col-sm-12">



                                        <div class="box box-default payment_blk">



                                            <div class="box-body">



                                                <div class="row">



                                                    <div class="col-md-12">



                                                        <div class="box-header with-border">



                                                        <h3 class="box-title">Branch Transfer</h3>



                                                            <div class="box-tools pull-right">





                                                            </div>



                                                        </div>



                                                    <div class="row">



                                                            <div class="box-body">



                                                            <div class="table-responsive">



                                                                <table id="tag_branch_transfer" class="table table-bordered table-striped text-center">



                                                                    <thead>



                                                                    <tr>

                                                                       <th>Branch Transfer Code</th>



                                                                        <th>From Branch</th>



                                                                        <th>To Branch</th>   



                                                                        <th>Created By</th>



                                                                        <th>Date</th>   

                                                                        

                                                                        <th>Approved By</th>



                                                                        <th>Approved Date</th>  

                                                                        

                                                                        <th>Download By</th>



                                                                        <th>Download Date</th>  



                                                                        <th>Status</th>





                                                                    </tr>



                                                                    </thead> 



                                                                    <tbody>



                                                                    </tbody>



                                                                    <tfoot>



                                                                        <tr></tr>



                                                                    </tfoot>



                                                                </table>



                                                            </div>



                                                            </div> 



                                                        </div>



                                                    </div>



                                                </div>



                                                



                                            </div>



                                        </div>



                                    </div>



                                </div>



                                </div>

                                <div class="tab-pane" id="section_transfer_details">



                                    <div class="row">



                                        <div class="col-sm-12">



                                            <div class="box box-default payment_blk">



                                                <div class="box-body">



                                                    <div class="row">



                                                        <div class="col-md-12">



                                                            <div class="box-header with-border">



                                                            <h3 class="box-title">Section Transfer</h3>



                                                                <div class="box-tools pull-right">





                                                                </div>



                                                            </div>



                                                        <div class="row">



                                                                <div class="box-body">



                                                                <div class="table-responsive">



                                                                    <table id="tag_section_transfer" class="table table-bordered table-striped text-center">



                                                                        <thead>



                                                                        <tr>



                                                                            <th>From section</th>



                                                                            <th>To section</th>   



                                                                            <th>Employee</th>   



                                                                            <th> Date</th>   



                                                                           

                                                                        </tr>



                                                                        </thead> 



                                                                        <tbody>



                                                                        </tbody>



                                                                        <tfoot>



                                                                            <tr></tr>



                                                                        </tfoot>



                                                                    </table>



                                                                </div>



                                                                </div> 



                                                            </div>



                                                        </div>



                                                    </div>



                                                    



                                                </div>



                                            </div>



                                        </div>



                                    </div>



                                    </div>







                                    <div class="tab-pane" id="stock_issue_details">



                                   <div class="row">



                                    <div class="col-sm-12">



                                        <div class="box box-default payment_blk">



                                            <div class="box-body">



                                                <div class="row">



                                                    <div class="col-md-12">



                                                        <div class="box-header with-border">



                                                        <h3 class="box-title">Stock Issue</h3>



                                                            <div class="box-tools pull-right">





                                                            </div>



                                                        </div>



                                                    <div class="row">



                                                            <div class="box-body">



                                                            <div class="table-responsive">



                                                                <table id="tag_stock_issue" class="table table-bordered table-striped text-center">



                                                                    <thead>



                                                                    <tr>



                                                                        <th>Issue No</th>



                                                                        <th>Karigar/Customer/Employee</th>



                                                                        <th>Issue to</th>



                                                                        <th>Tag Code</th>



                                                                        <th>Issued By</th>   



                                                                        <th>Date</th>   



                                                                        <th>Received By</th>



                                                                        <th>Received Date</th>





                                                                    </tr>



                                                                    </thead> 



                                                                    <tbody>



                                                                    </tbody>



                                                                    <tfoot>



                                                                        <tr></tr>



                                                                    </tfoot>



                                                                </table>



                                                            </div>



                                                            </div> 



                                                        </div>



                                                    </div>



                                                </div>



                                                



                                            </div>



                                        </div>



                                    </div>



                                </div>



                                </div>









            </div>







            </div>  



          <?php echo form_close();?>



            <div class="overlay" style="display:none">



			  <i class="fa fa-refresh fa-spin"></i>



			</div>



             <!-- /form -->



          </div>



    </section>



</div>



<!--  custom items-->



<!-- 

    <div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog" style="width:90%;">

            <div class="modal-content">

                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div id="order_images_preview" style="margin-top: 2%;"></div>

                    </div>

                </div>

                <div class="modal-footer">

                    </br>

                    <button type="button" id="close_stone_details" class="btn btn-warning"

                        data-dismiss="modal">Close</button>

                </div>

            </div>

        </div>

    </div> -->

    <div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog" style="width: 90%;">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div id="order_images_preview" style="margin-top: 2%;"></div>

                </div>

            </div>

            <div class="modal-footer">

                </br>

                <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>

