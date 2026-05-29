      <!-- Content Wrapper. Contains page content -->
      <style>
.remove-btn {
    margin-top: -168px;
    margin-left: -38px;
    background-color: #e51712 !important;
    border: none;
    color: white !important;
}

.sm {
    font-weight: normal;
}
}
      </style>
      <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <section class="content-header">
              <h1>
                  Item Details
              </h1>
          </section>

          <!-- Main content -->
          <section class="content order">

              <!-- Default box -->
              <div class="box box-primary">
                  <div class="box-body">

                      <!-- form container -->
                      <!-- form -->
                      <form id="order_submit">
                          <div class="row">
                              <div class="col-sm-12">
                                  <div class="row">

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Item Type</label>

                                              <select id="item_type" class="form-control" style="width:100%;">

                                                  <option value="3">Repair</option>

                                                  <option value="1">Non Tag</option>

                                              </select>
                                          </div>
                                      </div>



                                      <input type="hidden"  class="id_branch" id="id_branch" 
											name="issue[id_branch]"
											value="<?php echo $id_branch ?>">




                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Select Section</label>
                                              <input type="hidden" id="id_section" name="id_section">
                                              <select id="section" class="form-control" style="width:100%;"></select>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Select Category</label>
                                              <input type="hidden" id="id_orderdetails" name="id_orderdetails">
                                              <select id="category" class="form-control" style="width:100%;"></select>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Select Purity</label>
                                              <select id="purity" class="form-control" style="width:100%;"></select>
                                          </div>
                                      </div>


                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Select Product</label>
                                              <select id="prod_select" class="form-control custom-inp"
                                                  style="width:100%;"></select>
                                          </div>
                                      </div>

							</div>	
							<div class="row">					

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Select Design</label>
                                              <select id="des_select" class="form-control custom-inp"
                                                  style="width:100%;"></select>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Select Sub Design</label>
                                              <select id="sub_design_select" class="form-control custom-inp"
                                                  style="width:100%;"></select>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Pcs</label>
                                              <input type="number" class="form-control custom-inp" id="pcs"
                                                  placeholder="Enter Gwt">
                                              <span class="stock_pcs">
                                                  Stock :
                                                  <span class="blc_pcs">0</span>
                                                  <input type="hidden" class="tot_blc_pcs" value="0">
                                              </span>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Gross Wt</label>
                                              <input type="number" class="form-control custom-inp" id="gross_wt"
                                                  placeholder="Enter Gwt">
                                              <span class="stock_gwt">
                                                  Stock :
                                                  <span class="blc_gwt">0</span>
                                                  <input type="hidden" class="tot_blc_gwt" value="0">
                                              </span>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Less Wt</label>
                                              <div class="input-group ">
                                                  <input id="less_wt" class="form-control custom-inp" type="number"
                                                      step="any" readonly />
                                                  <span class="input-group-addon input-sm add_lwt">+</span>
                                                  <input type="hidden" id="stone_details">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Nwt</label>
                                              <input type="number" class="form-control custom-inp" id="net_wt"
                                                  placeholder="Enter Nwt" readonly>
                                          </div>
                                      </div>

							</div>
							<div class="row">

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>V.A(%)</label>
                                              <input type="number" class="form-control custom-inp" id="wast_per"
                                                  placeholder="V.A(%)">
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>M.C Type</label>
                                              <select class="form-control" id="mc_type">
                                                  <option value="1">Per Pcs</option>
                                                  <option value="2">Per Grams</option>
                                                  <option value="3">On Price</option>
                                              </select>
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>M.C</label>
                                              <input type="number" class="form-control custom-inp" id="mc_value"
                                                  placeholder="Enter MC Value">
                                          </div>
                                      </div>

                                      <div class="col-md-2">
                                          <div class="form-group tagged">
                                              <label>Service Charge(Rs)</label>
                                              <input type="number" class="form-control custom-inp" id="service_charge"
                                                  name="service_charge" placeholder="Enter The Amount">
                                          </div>
                                      </div>

                                      <div class="col-md-3">
                                          <div class="form-group">
                                              <label></label>
                                              <button type="button" class="btn btn-success" id="add_repair_item"
                                                  style="margin-top: 20px;">Add</button>
                                          </div>
                                      </div>

                                  </div>
                              </div>
                          </div>
                          <p class="hepl-block"></p>
                          <div class="row cus_repair">
                              <div class="col-md-12">
                                  <table id="custrepair_item_detail" class="table table-bordered table-striped">
                                      <thead>
                                          <tr>
                                              <th width="10%;">Item Type</th>
                                              <th width="10%;">Section</th>
                                              <th width="10%;">Category</th>
                                              <th width="10%;">Purity</th>
                                              <th width="10%;">Product</th>
                                              <th width="10%;">Design</th>
                                              <th width="10%;">Sub Design</th>
                                              <th width="10%;">Pcs</th>
                                              <th width="10%;">Gwt</th>
                                              <th width="10%;">Lwt</th>
                                              <th width="10%;">Nwt</th>
                                              <th width="10%;">V.A(%)</th>
                                              <th width="10%;">MC</th>
                                              <th width="10%;">Action</th>
                                          </tr>
                                      </thead>
                                      <tbody> </tbody>
                                  </table>
                              </div>
                          </div>

                          <!--End of row-->

                          <div class="row">
                              <div class="box box-default"><br />
                                  <div class="col-xs-offset-5">
                                      <button type="button" class="btn btn-primary" id="save_repair_item">Save</button>
                                      <button type="button" class="btn btn-default btn-cancel">Cancel</button>

                                  </div> <br />
                              </div>
                          </div>
                  </div> <!-- box-body-->
                  <div class="overlay" style="display:none">
                      <i class="fa fa-refresh fa-spin"></i>
                  </div>
              </div> <!-- Default box-->
              <?php echo form_close();?>

              <!-- /form -->
          </section>
      </div>

      <!--  Image Upload-->
      <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
          aria-hidden="true">
          <div class="modal-dialog" style="width:60%;">
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Add Image</h4>
                  </div>

                  <div class="modal-body">
                      <input type="file" name="order_images" id="order_images" multiple="multiple">
                  </div></br>
                  <div id="uploadArea_p_stn" class="col-md-12"></div>
                  <div class="modal-footer">
                      <button type="button" id="update_img" class="btn btn-success">Save</button>
                      <button type="button" id="close_stone_details" class="btn btn-warning"
                          data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>
      </div>

      <div class="modal fade" id="order_des" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal"><span
                              aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                      <h4 class="modal-title" id="myModalLabel">Add Description</h4>
                  </div>

                  <div class="modal-body">

                      <div class="row">

                          <div class="col-md-10 col-md-offset-1">
                              <label for="user_lastname">Item Description</label>
                              <div class='form-group'>
                                  <textarea cols="70" id="description"
                                      name="description"><?php echo set_value('sch[description]',(isset($sch['description'])?$sch['description']:"")); ?></textarea>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="modal-footer">
                      <a href="#" class="btn btn-success add_order_desc">Add</a>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>
      <!-- / modal -->



      <!--  Image Upload-->
      <div class="modal fade" id="BillModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
          aria-hidden="true">
          <div class="modal-dialog" style="width:60%;">
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Billing Details</h4>
                  </div>

                  <div class="modal-body">

                      <div class="row" id="bill_items_for_return" style="display:none;">
                          <div class="box-body">
                              <div class="table-responsive">
                                  <table id="bill_items_tbl_for_return"
                                      class="table table-bordered table-striped text-center">
                                      <thead>
                                          <tr>
                                              <th>Select</th>
                                              <th>Product</th>
                                              <th>Design</th>
                                              <th>Pcs</th>
                                              <th>Purity</th>
                                              <th>Size</th>
                                              <th>G.Wt</th>
                                              <th>L.Wt</th>
                                              <th>N.Wt</th>
                                              <th>Amount</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                      </tbody>
                                      <tfoot>
                                          <tr></tr>
                                      </tfoot>
                                  </table>
                                  <p></p>
                              </div>
                          </div>
                      </div>
                  </div></br>
                  <div id="uploadArea_p_stn" class="col-md-12"></div>
                  <div class="modal-footer">
                      <button type="button" id="update_bill_details" class="btn btn-success">Save</button>
                      <button type="button" id="close_stone_details" class="btn btn-warning"
                          data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>
      </div>


      <div class="modal fade" id="cus_stoneModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

          <div class="modal-dialog" style="width:80%;">

              <div class="modal-content">

                  <div class="modal-header">

                      <h4 class="modal-title" id="myModalLabel">Add Stone</h4>

                  </div>

                  <div class="modal-body">

                      <!--<div class="row">-->

                      <!--    			<div class="col pull-right">-->

                      <!--    			    <button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>-->

                      <!--    			</div>-->

                      <!--			</div>-->

                      <div class="row">

                          <input type="hidden" id="stone_active_row" value="0">

                          <table id="estimation_stone_cus_item_details"
                              class="table table-bordered table-striped text-center">

                              <thead>

                                  <tr>

                                      <th>LWT</th>

                                      <th>Type</th>

                                      <th>Name</th>

                                      <th>Pcs</th>

                                      <th>Wt</th>

                                      <th>Cal.Type</th>

                                      <th>Rate</th>

                                      <th>Amount</th>

                                      <th>Action</th>

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

                  <div class="modal-footer">

                      <button type="button" id="update_repair_item_stone_details" class="btn btn-success">Save</button>

                      <button type="button" id="close_stone_details" class="btn btn-warning"
                          data-dismiss="modal">Close</button>

                  </div>

              </div>

          </div>

      </div>