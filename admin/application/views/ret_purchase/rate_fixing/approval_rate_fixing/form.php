<!-- Content Wrapper. Contains page content -->

<style>

	.remove-btn{

		margin-top: -168px;

	    margin-left: -38px;

	    background-color: #e51712 !important;

	    border: none;

	    color: white !important;

	}

	.summary_lbl{

		font-weight:bold;

	}

	.stickyBlk {

	    margin: 0 auto;

	    top: 0;

	    width: 100%;

	    z-index: 999;

	    background: #fff;

	}

	.custom-label{

		font-weight: 600 !important;

	    letter-spacing: 0.5px !important;

	    text-transform: uppercase !important;

	}

	

    .form-group {

        margin-bottom: 1px;

    }

    

    input[type=number]::-webkit-inner-spin-button, 

    input[type=number]::-webkit-outer-spin-button { 

    -webkit-appearance: none;

    -moz-appearance: none;

    appearance: none;

    margin: 0; 

    }

    

    

    *[tabindex]:focus {

    outline: 1px black solid;

    }

    

</style>

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <!--<section class="content-header">

          <h1>

        	Billing

            <small>Customer Billing</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Billings</a></li>

            <li class="active">Billing</li>

          </ol>

        </section>-->

        <!-- Main content -->

        <section class="content product">

          <!-- Default box -->

          <div class="box box-primary">

           

            <div class="box-body">

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

         <!-- form container -->

          <div class="row">

             <!-- form -->

			<form id="rate_fixing_form">

				<div class="col-md-12"> 	

                	<div class="tab-content">

                		<div class="tab-pane active" id="pay_items">

                		    <div class="box box-default ">

        						<div class="box-body">

                			        <!-- Search Block	 -->

                					<div class="row">	

                					

                					<div class="col-md-2">

                	                     <div class="form-group">

                	                       <label>Select Karigar<span class="error">*</span></label>

                								<select id="select_karigar" class="form-control" name="order[id_karigar]" style="width:100%;" tabindex="1"></select>

                								<input type="hidden" id="id_karigar" value="<?php echo $po_item['po_karigar_id'];?>">

                								<input type="hidden" id="rate_fix_type"  name="rate_fix[rate_fix_type]" value="2">

                	                     </div> 

            				        </div>

            				        

            				        <div class="col-md-2">

                	                     <div class="form-group">

                	                       <label>Approval Ratefix No<span class="error">*</span></label>

                								<select id="select_approval_ref_no" class="form-control" name="order[id_approval_ratecut]" style="width:100%;" tabindex="1"></select>

                	                     </div> 

            				        </div>

									<div class="col-md-2">

										<div class="form-group">

											<label>Remark<span class="error"></span></label>

											<textarea rows="2" cols="5" name="rate_fix[remark]" class="form-control" placeholder="Enter Here....." type="text"></textarea>

										</div> 

									</div>
        				        

                			    		<!--<div class="col-sm-3 search_esti"> 

                							<div class="row">				    	

                					    		<div class="col-sm-4">

                					    			<label>PO REF NO</label>

                						 		</div>

                						 		<div class="col-sm-8">

                						 			<div class="form-group" > 

                							 			<div class="input-group" > 

                											<input class="form-control" id="po_ref_no"   type="text" placeholder="PO REF No." value="" autocomplete='off' tabindex=18/>

                											<span class="input-group-btn">

                						                      <button type="button" id="rate_fix_item_search" class="btn btn-default btn-flat" tabindex=19><i class="fa fa-search"></i></button>

                						                    </span>

                										</div>

                										<p id="searchEstiAlert" class="error" align="left"></p>

                									</div>

                						 		</div>

                						 	</div>

                						 </div>-->

                							</div>

        							<div class="row sale_details">

        								<div class="col-md-12">

        							       <p>SALES ITEMS</p>

        								   <div class="table-responsive">

        									 <table id="item_details" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">

        										<thead>

        										  <tr>

        											<th>REF NO</th>   

        											<th>DATE</th>   

        											<th>PURE WT</th>   

        											<th>FIXED WT</th>   

        											<th>RET WT</th>   

        											<th>BAL WT</th>   

        											<th>FIX WT</th>   

        											<th>RATE Excl.Tax</th> 
													
													<th>Taxable amount</th> 

													<th>Tax</th>  

        											<th>Payable</th>   

        										  </tr>

        										</thead> 

        										<tbody>

        										</tbody>

        									 </table>

        								  </div>

        								</div> 

        							</div> 

                				</div>

                    		</div>

                		</div>

                		<div class="row">

						    <div class="col-sm-12" align="center">

        						<button type="button" id="rate_fix_submit" class="btn btn-primary" >Save</button> 

        						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

						    </div>

						</div>

                	</div>

				 	<p></p>

				</div>	<!--/ Col --> 

			</div>	 <!--/ row -->

			   <p class="help-block"> </p>  

	            </div>  

	          <?php echo form_close();?>

	            <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

	             <!-- /form -->

	          </div>

             </section>

        </div>

 