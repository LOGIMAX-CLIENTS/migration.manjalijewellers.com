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
                  Inventory
                  <small>Sales Return Transfer</small>
              </h1>
              <ol class="breadcrumb">
                  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="#">Inventory</a></li>
                  <li class="active">Sales Return Transfer</li>
              </ol>
          </section>

          <!-- Main content -->
          <section class="content">

              <!-- Default box -->
              <div class="box box-primary">
                  <div class="box-header with-border">
                      <h3 class="box-title">Sales Return Transfer </h3>
                      <div class="box-tools pull-right">
                          <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                              title="Collapse"><i class="fa fa-minus"></i></button>
                          <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i
                                  class="fa fa-times"></i></button>
                      </div>
                  </div>
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
                      <?php  echo form_open_multipart(""); ?>
                      <div class="row">
                          <div class="col-md-5">
                              <div class="row">
                                  <div class="col-md-12">
                                  <div class="form-group">
                                            <?php 
									 		$this->session->unset_userdata('SALES_RET_TRANS_FORM_SECRET');
								 		    $form_secret=md5(uniqid(rand(), true));
									        $this->session->set_userdata('SALES_RET_TRANS_FORM_SECRET', $form_secret);
								 		    ?>
                                              <input type="hidden" id="form_secret" value="<?php echo $form_secret; ?>">
                                              <input type="hidden" id="salesRetTransType" value="1">
                                              <input type="radio" name="sales_ret_transfer_item_type" id="type1" value="1"
                                                  checked> <label for="type1">Sales Return Request</label>
                                              &nbsp;&nbsp;
                                              <input type="radio" name="sales_ret_transfer_item_type" id="type2" value="2">
                                              <label for="type2">Sales Return Download</label>&nbsp;&nbsp;
                                          </div>
                                      </div>
                                  </div>
                          </div>
                          <div class="col-md-7">
                              <div class="row">
                                  <div class="col-md-12">
                                      <div class="box box-default custom-bx">
                                          <div class="box-body">
                                              <div class="row">
                                                  <div class="col-md-5">
                                                      <div class="form-group">
                                                          <div class="row">
                                                              <div class="col-md-5 ">
                                                                  <label for="" class="control-label pull-right">From
                                                                      Branch <span class="error"> *</span></label>
                                                              </div>
                                                              <div class="col-md-7">
                                                                 
                                                                  <select class="form-control from_branch" id="from_brn"
                                                                      required></select>
                                                                  
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-1 tagged" align="right"> </div>
                                                  <div class="col-md-5 to_branch_blk">
                                                      <div class="form-group">
                                                          <div class="row">
                                                              <div class="col-md-5 ">
                                                                  <label for="" class="control-label pull-right">To
                                                                      Branch <span class="error"> *</span></label>
                                                              </div>
                                                              <div class="col-md-7">
                                                                  <select class="form-control to_branch" id="to_brn"
                                                                      required></select>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <p class="help-block"></p>
                                              <div class="row">
                                                  <div class="col-md-5">

                                                      <div class="form-group sales_trans_calc_type" style="display:none;">
                                                          <div class="row">
                                                              <div class="col-md-5">
                                                                  <label for="" class="control-label pull-right">Calc
                                                                      Type<span class="error"> *</span></label>
                                                              </div>
                                                              <div class="col-md-7">
                                                                  <select class="form-control"
                                                                      id="select_calculation_type" style="width:100%;">
                                                                      <option value="1" selected>Per Gram</option>
                                                                      <option value="2" >Per Piece</option>
                                                                  </select>

                                                              </div>
                                                          </div>
                                                      </div>



                                                      <div class="form-group sales_trans_tag_no" style="display:none;">
                                                          <div class="row">
                                                              <div class="col-md-5">
                                                                  <label for="" class="control-label pull-right">Tag
                                                                      No</label>
                                                              </div>
                                                              <div class="col-md-7">
                                                                  <input type="text" class="form-control" id="tag_no"
                                                                      placeholder="Tag No" autocomplete="off">
                                                              </div>
                                                          </div>
                                                      </div>

                                                      <div class="form-group sales_trans_bill_no" style="">
                                                          
                                                          <div class="row">
                                                          <div class="col-md-4">
                                                              <label for="" class="control-label pull-right">Against Bill</label>
                                                          </div>
                                                          <div class="col-md-8">
                                                              <div class="form-group" > 
    														    <input type="radio" name="aganist_bill" id="aganist_bill_yes" value="1" checked> <label for="type1">Yes</label>
    														    <input type="radio" name="aganist_bill" id="aganist_bill_no" value="0" > <label for="type1">No</label>
													    		</div>
                                                          </div>
                                                          </div>
                                                          
                                                          <div class="row bill_no">
                                                          <div class="col-md-5">
                                                              <label for="" class="control-label pull-right">Bill
                                                                  No<span class="error"> *</span></label>
                                                          </div>
                                                          <div class="col-md-7">
                                                              <div class="form-group" > 
    														    <div class="input-group" > 
                                                                    <span class="input-group-btn">
                                                                        <select class="form-control" id="fin_year_code" style="width:100px;">
                                                                        <?php 
                                                                            foreach($fin_year as $val)
                                                                            {?>
                                                                            <option value=<?php echo $val['fin_year_code'];?> <?php echo ($val['fin_status']==1 ?'selected' :'')  ?> ><?php echo $val['fin_year_name'];?></option>
                                                                        <?php }
                                                                        ?>
                                                                    </select>
                                                                    </span>
                                                                    <input type="text" class="form-control" id="bill_no" placeholder="Bill No" autocomplete="off" style="width: 100px;">
                    											</div>
        														</div>
                                                          </div>
                                                          </div>

                                                      </div>


                                                      <div class="form-group sales_trans">
                                                          <div class="row">
                                                              <div class="col-md-offset-5 col-md-7">
                                                                  <button type="button"
                                                                      class="btn btn-info btn-flat sales_ret_transfer_search pull-right">Search</button>
                                                              </div>
                                                          </div>
                                                      </div>

                                                      <div class="form-group sales_trans_download_search"
                                                          style="display:none;">
                                                          <div class="row">
                                                              <div class="col-md-offset-5 col-md-7">
                                                                  <button type="button"
                                                                      class="btn btn-info btn-flat sales_ret_transfer_approval_search pull-right">Search</button>
                                                                      <input type="hidden" id="sales_ret_trans_dnload" value="<?php echo $sales_transfer_download;?>">
                                                                      <input type="hidden" id="ret_actual_pcs_dnload" value="">
                                                              </div>
                                                          </div>
                                                      </div>

                                                  </div>
                                              </div>
                                          </div>
                                          <!-- /.box-body -->
                                      </div>
                                      <!-- /.box -->
                                  </div>
                              </div>
                          </div>
                      </div>

                      <p class="help-block"></p>
                      <div class="row sales_trans">
                          <div class="col-md-12">
                              <div>
                                  <p>
                                      <span style="margin-right:15%;margin-left: 42%;">
                                          <span><b>TOTAL : </b></span>
                                          <span><b>PCS : </b></span>
                                          <span class="tot_bt_pcs" style="font-bold;">0</span>
                                          <span><b>WEIGHT : </b></span>
                                          <span class="tot_bt_gross_wt" style="font-bold;">0.000</span>
                                      </span>
                                  </p>
                              </div></br>
                              <div class="table-responsive">
                                  <table id="bt_search_list" class="table table-bordered table-striped text-center">
                                      <thead>
                                          <tr>
                                              <th width="5%"><label class="checkbox-inline"><input type="checkbox"
                                                          id="select_all" name="select_all" value="all" />All</label>
                                              </th>
                                              <th width="10%">G.wt</th>
                                              <th width="10%">Amount</th>
                                              <th width="10%">Action</th>
                                          </tr>
                                      </thead>
                                      <tbody></tbody>
                                  </table>
                              </div>
                          </div>
                      </div>


                      <div class="row sales_trans_download" style="display:none;">
                          <div class="col-md-12">
                              <div class="table-responsive">
                                  <table id="bt_search_download_list"
                                      class="table table-bordered table-striped text-center">
                                      <thead>
                                          <tr>
                                              <th width="10%"><label class="checkbox-inline"><input type="checkbox"
                                                          id="sales_return_trans_select_all" name="select_all"
                                                          value="all" />Bill No</label></th>
                                              <th width="10%">Bill Date</th>
                                              <th width="5%">Pcs</th>
                                              <th width="10%">G.wt</th>
                                              <th width="10%">Action</th>
                                          </tr>
                                      </thead>
                                      <tbody></tbody>
                                  </table>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                            <div class="col-md-12 container">
                                <div class="table-responsive">
                                <table id="ret_bill_approval_list_by_scan"  style="display:none;width:80%;margin-left: auto;margin-right: auto;"  class="table table-bordered table-striped text-center ">
                                    <thead>
                                    <tr>
                                        <th>Bill No</th> 
                                        <th>Bill Date</th>
                                        <th>Pcs</th>
                                        <th>G.wt</th>
                                    </tr>
                                    </thead> 
                                    <tbody></tbody>
                                    
                                </table>
                            </div>
                            </div>
				      </div>

                      <div class="form-group tagged" id="ret_tag_scan_code" style="display:none;    margin-top: 20px;"> 
						<div class="row">
							<div class="col-md-5 ">
								<label for="" class="control-label pull-right">Tag Code</label> 
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control" id="ret_scan_tag_no" placeholder="Tag Code"  autocomplete="off">
							</div>
						</div> 
					</div>

                    <div class="table-responsive">
                        <table id="ret_bill_dwnload_list" style="display:none" class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th width="5%">Tag Code</th>   
                                    <th width="10%">Product</th>
                                    <th width="10%">Pcs</th>  
                                    <th width="10%">G.wt</th>  
                                </tr>
                            </thead>
                            <tbody></tbody>

                        </table>
                    </div>

                      <!-- Remark field (for all types) -->
                      <div class="row sales_trans" style="margin-top:10px;">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Remark</label>
                                  <input type="text" class="form-control" id="remark" placeholder="Transfer Remark" autocomplete="off">
                              </div>
                          </div>
                          <?php if(isset($is_metal_for_billing) && $is_metal_for_billing) { ?>
                          <div class="col-md-3" id="srt_metal_selector" style="display:none;">
                              <div class="form-group">
                                  <label>Metal</label>
                                  <select class="form-control" id="select_metal" style="width:100%;"></select>
                              </div>
                          </div>
                          <input type="hidden" id="is_metal_for_billing" value="<?php echo (isset($is_metal_for_billing) ? $is_metal_for_billing : 0); ?>">
                          <?php } ?>
                      </div>

                      <div class="row sales_submit" style="margin-top:10px;">
                          <div class="col-xs-12 text-center">
                              <button type="button" id="sales_ret_trans_submit" class="btn btn-primary">Save</button>
                              <button type="button" class="btn btn-default btn-cancel">Cancel</button>
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

      <!-- OG Detail Modal (reused from Sales Transfer) -->
      <div class="modal fade" id="og_detail_modal" tabindex="-1" role="dialog" aria-labelledby="ogDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header bg-primary">
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                      <h4 class="modal-title" id="ogDetailLabel"><i class="fa fa-info-circle"></i> Old Gold Purchase Detail</h4>
                  </div>
                  <div class="modal-body" id="og_detail_body">
                      <div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>


      <div class="modal fade" id="oi_remark_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
          aria-hidden="true" data-keyboard="false" data-backdrop="static">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">

                      <button type="button" class="close" data-dismiss="modal"><span
                              class="sr-only">Close</span></button>
                      <h4 class="modal-title" id="myModalLabel">Other Issue Remark</h4>
                  </div>
                  <div class="modal-body">
                      <textarea id="oi_remark" name="oi_rem" rows="4" cols="50"></textarea>
                  </div>
                  <div class="modal-footer">
                      <a href="#" class="btn btn-danger btn-confirm" id="oi_save">Save</a>
                  </div>
              </div>
          </div>
      </div>