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

.prev_btn

{

    padding-top:75px;

    margin-left: -38px;

}



input::-webkit-outer-spin-button,

input::-webkit-inner-spin-button {

  -webkit-appearance: none;

  margin: 0;

}

      </style>

      <div class="content-wrapper">

          <!-- Content Header (Page header) -->

          <section class="content-header">

              <h1>

                  LOT SPLIT

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

                      <?php  

                        echo form_open_multipart(('admin_ret_lot/lot_split/save'),array('id'=>'lot_split_form')); ?>

                        <div class="row">

                            <div class="col-md-5">

                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="col-md-5">

                                            <div class="form-group">

                                                <label for="">LOT NO. <span class="error"> *</span></label>

                                                <div class="form-group">

                                                    <select class="form-control" id="lot_no_split"></select>

                                                </div>

                                            </div> 

                                        </div>

                    

                                        <div class="col-md-5">

                                            <div class="form-group">

                                                <label for=""><span class="error"> </span></label>

                                                <div class="form-group">

                                                    <button type="button" class="btn btn-info btn-flat lot_split_search">Search</button>

                                                </div>

                                            </div> 

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 table-responsive">

                                <div class="box box-info">

                                    <div class="box-body ">

                                        <p>Lot Summary</p>

                                        <table id="lot_details_summary" class="table table-bordered text-center">

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

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                    <td></td>

                                                </tfoot>

                                            </tbody>



                                        </table>



                                    </div>

                                </div>

                            </div>

                            <div class="col-md-12 prev_btn">   

                            <?php if($access['add']==1){?>

                                <button id="add_to_lot_split_list" type="button" class="btn btn-warning btn-flat pull-right"><i class="fa fa-plus"></i> Add To Preview</button> 
                            <?php }?>
                            </div>

                        </div><br>

                        

                        <div class="table-responsive">

                            <table id="lot_split_list" class="table table-bordered table-striped text-center">

                            <input type="hidden" id="curRow" value="-1">

                                <thead>

                                <tr>

                                    <th>All</th>

                                    

                                    <th width="10%">Lot No</th>



                                    <th width="10%">Item Id</th>



                                    <th width="10%">Employee</th>



                                    <th width="10%">Category</th>



                                    <th width="10%">Product</th> 



                                    <th width="10%">Purity</th>



                                    <th width="10%">Tot Pcs</th> 



                                    <th width="10%">Tot GrsWt</th>



                                    <th width="10%">Split Pcs</th>



                                    <th width="10%">Split GrsWt</th>



                                    <th width="10%">Split NWt</th>

                                            

                                    <th width="10%">Stn Pcs</th>



                                    <th width="10%">Stn Wt</th>



                                    <th width="10%">Dia Pcs</th>



                                    <th width="10%">Dia Wt</th>

                                    

                                </tr>

                                </thead>

                                <tbody></tbody>

                                <tfoot><tr style="font-weight:bold;">

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                                <td></td>

                            </tr></tfoot>

                            </table>

                        </div>

                        

				 	<legend>Preview :</legend>



                        <div class="row"> 



                        <div class="col-sm-12"> 



                            <div class="table-responsive">



                            <table id="lt_split_preview" class="table table-bordered table-striped text-center">

                            <input type="hidden" id="split_curRow" value="0">



                                <thead>



                                    <tr>



                                    <th width="5%">Lot No</th>                                        



                                    <th width="5%">Item Id</th>                                        



                                    <th width="10%">Employee</th>                                        



                                    <th width="10%">Category</th> 

                                    

                                    <th width="10%">Product</th> 



                                    <th width="10%">Purity</th>   



                                    <th width="10%">Split Pcs</th> 



                                    <th width="10%">Split Grswt</th> 



                                    <th width="10%">Split Nwt</th> 



                                    <th width="10%">Split StnPcs</th>



                                    <th width="10%">Split StnWt</th>



                                    <th width="10%">Split DiaPcs</th>



                                    <th width="10%">Split DiaWt</th> 

                                    

                                    <th width="10%">Action</th> 



                                    </tr>



                                </thead> 



                                <tbody></tbody>



                                <tfoot><tr style="font-weight:bold;">

                                    <td>TOTAL:</td>

                                    <td></td>

                                    <td></td>

                                    <td></td>

                                    <td></td>

                                    <td></td>

                                    <td class="tot_split_pcs"></td>

                                    <td class="tot_split_wt"></td>

                                    <td class="tot_split_nwt"></td>

                                    <td class="tot_split_stn_pcs"></td>

                                    <td class="tot_split_stn_wt"></td>

                                    <td class="tot_split_dia_pcs"></td>

                                    <td class="tot_split_dia_wt"></td> 

                                    <td></td>                   

                                </tr></tfoot>



                            </table>



                            </div> 



                        </div>



                        </div> 

                    </div>

                </div>

                      





                      <div class="row sales_submit">

                          <div class="box box-default"><br />

                              <div class="col-xs-offset-5">

                                  <button type="button" id="lot_split_submit" class="btn btn-primary">Save</button>

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

</div>



<input type="hidden" id="lot_active_id" class="lot_active_id"  name="" value="" />

<div class="modal fade" id="stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:80%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>

			</div>

			<div class="modal-body">

				<div class="row">

			<div class="box-tools pull-right">

			<!-- <button type="button" id="create_stone_old" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button> -->

			</div>

			</div>

				<div class="row">

					<table id="lotsplit_stone_details" class="table table-bordered table-striped text-center">

    					<thead>

        					<tr>

                            	<th>Stone Name</th>

                                <th>UOM Name</th>   

                            	<th>Pcs</th>

                            	<th>Wt</th>

                            	<th>split pcs</th>

                            	<th>split Wt</th>				

                            </tr>

    					</thead> 

    					<tbody></tbody>										

    					<tfoot><tr></tr></tfoot>

					</table>

			</div>

		  </div>

		  <div class="modal-footer">

			<button type="button" id="update_lot_stone_details" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

</div>



