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

       



        <!-- Main content -->

        <section class="content order">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">QC ENTRY</h3>

            </div>

            <div class="box-body">

             <!-- form container --> 

	             <!-- form -->

				<form id="qc_entry_form">

				<div class="row">

				    <div class="col-md-12">

				        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>Supplier Bill Entry's</label>

    							    <select class="form-control" name="order[po_ref_no]" id="select_ref_no"></select>

    	                     </div> 

				        </div>

				        <div class="col-md-2">

				            <div class="form-group">

				                <br>

				                <button id="po_item_search" type="button" class="btn btn-success" tabindex="2"><i class="fa fa-search"></i> SEARCH </button>

				            </div>

				        </div>

				         <div class="col-md-2">

				            <div class="form-group">

				                <br>

				                <button id="update_qc_status" type="button" class="btn btn-primary" >UPDATE</button>

				            </div>

				        </div>

				    </div>

				</div>

				

				<div class="row">

					<div class="col-md-12">

					    <div class="table-responsive">

                         <h4>Item Details</h4>

                         <input type="hidden" id="custom_active_id" value="0">

						 <table id="item_detail" class="table table-bordered table-striped">

							<thead style="text-transform:uppercase;">

							      <tr>

							          <th colspan="8"></th>

							          <th colspan="4">Rejected</th>

							          <th colspan="4">Accepted</th>

							      </tr>

						          <tr>

						            <th width="5%;">#</th> 

						            <th width="5%;">Product</th> 

						            <th width="5%;">Design</th> 

						            <th width="5%;">Sub Design</th> 

						            <th width="5%;">Pcs</th> 

						            <th width="5%;">Gwt</th> 

						            <th width="5%;">Lwt</th> 

						            <th width="5%;">Nwt</th> 

						            <th width="10%;">Pcs</th> 

						            <th width="10%;">GWt</th> 

						            <th width="10%;">LWt</th> 

						            <th width="10%;">NWt</th> 

                                    <th width="10%;">Pcs</th> 

						            <th width="10%;">GWt</th> 

						            <th width="10%;">LWt</th> 

						            <th width="10%;">NWt</th> 

						          </tr>

					         </thead>

					         <tbody> 

					         </tbody>

						</table>

					    </div>

					</div> 

				</div>	

				<p class="help-block"></p>

 

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

    			        

    					<table id="qc_issue_stone_details" class="table table-bordered table-striped text-center">

        					<thead>

            					<tr>

                					<th width="15%">#</th>

                					<th width="15%">Stone</th>

                					<th width="5%">Issue Pcs</th>   

                					<th width="22%">Issue Wt</th>

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

			<button type="button" id="update_qc_receipt_stone_details" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

            

