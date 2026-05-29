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

		

		

		*[tabindex]:focus {

            outline: 1px black solid;

        }

        

    </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

       



        <!-- Main content -->

        <section class="content order">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Halmarking Receipt</h3>

            </div>

            <div class="box-body">

             <!-- form container --> 

	             <!-- form -->

				<form id="qc_entry_form">

				<div class="row">

				    <div class="col-md-12">

				        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>HM REF NO</label>

    							    <select class="form-control" id="select_hm_ref_no"></select>

    	                     </div> 

				        </div>

				        

				        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>Vendor Ref No</label>

    							    <input type="text" id="hm_vendor_ref_id" name="hm_vendor_ref_id" class="form-control" autocomplete="off" placeholder="Enter vendor ref no"> 

    	                     </div> 

				        </div>

				        

				    </div>

				</div>

				

			    <div class="row">

                    <div class="box-body">

    			        <div class="table-responsive">

    			            <input type="hidden" id="active_id">

							<input type="hidden" id="custom_active_id" value="0">

    			             <table id="item_detail" class="table table-bordered table-striped">

        							<thead style="text-transform:uppercase;">

        							      <tr>

        							          <th colspan="4"></th>

        							          <th colspan="4">Issued</th>

        							          <th colspan="4">Rejected</th>

        							          <th colspan="4">Received</th>

        							          <th colspan="2"></th>

        							      </tr>

        						          <tr>

        						            <th width="1%;">#</th> 

        						            <th width="1%;">Product</th> 

        						            <th width="1%;">Design</th> 

        						            <th width="1%;">Sub Design</th> 

        						            <th width="2%;">Pcs</th> 

        						            <th width="5%;">Gwt</th> 

        						            <th width="5%;">Lwt</th> 

        						            <th width="5%;">Nwt</th> 

        						            <th width="15%;">Pcs</th> 

        						            <th width="15%;">GWt</th> 

        						            <th width="1%;">LWt</th> 

        						            <th width="15%;">NWt</th> 

                                            <th width="15%;">Pcs</th> 

        						            <th width="15%;">GWt</th> 

        						            <th width="20%;">LWt</th> 

        						            <th width="20%;">NWt</th> 

        						            <th width="10%;">H.M  Charge/Pcs</th> 

        						            <th width="10%;">Total Amount</th> 

        						          </tr>

        					         </thead>

        					         <tbody> 

        					         </tbody>

        						</table>

    			        </div>

    			    </div>

                </div>

				<p class="help-block"></p>

				<div class="row">

				   <div class="box box-default"><br/>

					  <div class="col-xs-offset-5">

						<button type="button" id="hm_receipt_submit" class="btn btn-primary" >Save</button> 

						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

					  </div> <br/>

					</div>



				  </div> 

				  <?php echo form_close();?>

	           </div>  

	            

	           <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

	       </div>  

        </section>

</div>





<div class="modal fade" id="cus_stoneModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:72%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>

			</div>

			<div class="modal-body">

    			<div class="row">

    			        <input type="hidden" id="activeRow">

    					<table id="hm_issue_stone_details" class="table table-bordered table-striped text-center">

        					<thead>

            					<tr>

                					<th width="15%">#</th>

                					<th width="15%">Stone</th>

                					<th width="5%">Pcs</th>   

                					<th width="22%">Wt</th>

                					<th width="5%">Rejected Pcs</th>   

                					<th width="22%">Rejected Wt</th>

                					<th width="5%">Accepted Pcs</th>   

                					<th width="22%">Accepted Wt</th>

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

			<button type="button" id="update_hm_stone_details" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

            

