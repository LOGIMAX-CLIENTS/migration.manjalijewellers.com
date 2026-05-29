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

      <h3 class="box-title">RECEIPT ENTRY</h3>

    </div>

    <div class="box-body">

     <!-- form container -->

         <!-- form -->

        <form id="metal_entry_form">

        <div class="row">

            <div class="col-md-12">

                <div class="col-md-2">

                     <div class="form-group">

                       <label>Metal Issue Ref No </label>

                            <select class="form-control" name="id_metal_issue" id="metal_issue_ref_no"> </select>

                     </div>

                </div>

                <div class="col-md-2">

                    <div class="form-group">

                    <label>Receipt Date </label>

                      <input class="form-control" placeholder="Choose Date"  data-date-format="dd-mm-yyyy"   type="text" name="receipt_date" id="receipt_date" readonly>

                </div>

                </div>

                <div class="col-md-2">

                    <div class="form-group">

                        <br>

                        <button id="metal_search" type="button" class="btn btn-success" tabindex="2"><i class="fa fa-search"></i> SEARCH </button>

                    </div>

                </div>

                 <div class="col-md-2">

                    <div class="form-group">

                        <br>

                        <button id="metal_update" type="button" class="btn btn-primary" >UPDATE</button>

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

                            <th colspan="4"></th>

                            <th colspan="4">Issued</th>

                            <th colspan="4">Received</th>

                            <td></td>

                          </tr>

                          <tr>

                            <th width="5%;">#</th>

                            <th width="5%;">Product</th>

                            <th width="5%;">Design</th>

                            <th width="5%;">Sub Design</th>

                            <th width="5%;"> PCS</th>

                            <th width="5%;"> GWT</th>

                            <th width="5%;"> NWT</th>

                            <th width="5%;"> LWT</th>

                            <th width="5%;"> PCS</th>

                            <th width="5%;"> GWT</th>

                            <th width="5%;"> NWT</th>

                            <th width="5%;"> LWT</th>

                            <th width="5%;"> ACTION</th>


                            <!-- <th width="5%;">Entry Weight</th>

                            <th width="5%;">Balanced Weight</th> -->

                          </tr>

                     </thead>

                     <tbody>

                     </tbody>

                     <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="total_pwt"></td>
                            <td class="total_gwt"></td>
                            <td class="total_nwt" ></td>
                            <td class="total_lwt"></td>
                            <td></td>
                        </tr>
                     </tfoot>

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

<div class="modal-dialog" style="width:95%;">

<div class="modal-content">

	<div class="modal-header">

		<h4 class="modal-title" id="myModalLabel">Stone Details</h4>

	</div>

	<div class="modal-body">

		<div class="row">

				<input type="hidden" id="stone_active_row" value="0">

				<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">

					<thead>

						<tr>

							<th width="5%">LWT</th>

							<th width="5%" style="display:none" class="metalissse_product">Product</th>

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

						<tr style="font-weight:bold;font-size:15px">
							<td>Total:</td>
							<td></td>
							<td style="display:none" class="metalissse_product"></td>
							<td></td>
							<td></td>
							<td class="stn_tot_pcs"></td>
							<td class="stn_tot_weight"></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td class="stn_tot_amount"></td>
							<td></td>
						</tr>

					</tfoot>

				</table>

		</div>
	  </div>

  <div class="modal-footer">

	<button type="button" id="update_stone_details" class="btn btn-success">Save</button>

	<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

  </div>

</div>

</div>

</div>