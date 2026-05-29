<!-- Content Wrapper. Contains page content -->
<style>
    .remove-btn {
        margin-top: -168px;
        margin-left: -38px;
        background-color: #e51712 !important;
        border: none;
        color: white !important;
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
           NON - TAG RECEIPT
        </h1>
    </section>
    <!-- Main content -->
    <section class="content product">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <form id="nt_receipt">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">              
                                    
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <label type="text">Lot No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select id="select_lot" class="form-control" name="nt_receipt[id_lot]" style="width:150px;"></select>
                                                    <input type="hidden" id="nontag_weight_per" value='<?php echo $nontag_weight_per; ?>' />
                                                    <input type="hidden" id="id_lot_inward_detail" name="nt_receipt[id_lot_inward_detail]" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <label type="text">Select Product</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select id="product_sel" class="form-control" name="nt_receipt[id_product]" style="width:150px;"></select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <label type="text">Select Design</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select id="design_sel" class="form-control" name="nt_receipt[id_design]" style="width:150px;"></select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <label type="text">Select Sub Design</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select id="sub_design_sel" class="form-control" name="nt_receipt[id_sub_design]" style="width:150px;"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <label type="text">NonTag Branch</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select id="branch_select" class="form-control" name="nt_receipt[id_branch]" style="width:150px;"></select>
                                                    <input type="hidden" id="id_branch" value="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <label type="text">NonTag Section</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select id="select_section" class="form-control" name="nt_receipt[id_section]" style="width:150px;"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <label type="text">Pieces</label>
                                        </div>
                                        <div class="col-xs-4">
                                            <input class="form-control nt_pcs" name="nt_receipt[nt_pcs]" placeholder="Pieces" type="number" style="width:150px;"></input>
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <label type="text">Gross Wt</label>
                                        </div>
                                        <div class="col-xs-4">
                                            <input class="form-control nt_grswt" name="nt_receipt[nt_grswt]" placeholder="Gross Wt" type="number" style="width:150px;"></input>
                                            <input type="hidden" id="actual_nt_grswt" value="">
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <label type="text">Less Wt</label>
                                        </div>
                                        <div class="col-xs-4">
                                            <input class="form-control nt_lesswt" name="nt_receipt[nt_lesswt]" placeholder="Less Wt" type="number" style="width:150px;" disabled></input>
                                            
                                        </div>
                                    </div><br>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <label type="text">Net Wt</label>
                                        </div>
                                        <div class="col-xs-4">
                                            <input class="form-control nt_netwt" name="nt_receipt[nt_netwt]" placeholder="Net Wt" type="number" style="width:150px;"></input>
                                        </div>
                                    </div><br>
                                    
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <label type="text">Remark</label>
                                        </div>
                                        <div class="col-xs-4">
                                          <textarea class="form-control"  name="nt_receipt[remark]" id="remark" rows="2" cols="100"> </textarea>
                                        </div>
                                    </div><br>

                                </div>
                                <div class="col-md-6">
                                    <div class="table-responsive">

                                        <table id="lt-det" class="table table-bordered text-center">

                                            <thead>

                                                <tr> 

                                                    <th width="10%"></th>

                                                    <th width="10%"><span class="lt_desc_tab">Pieces</span></th>

                                                    <th width="10%"><span class="lt_desc_tab">Grs Wt</span></th>

                                                    <th width="10%"><span class="lt_desc_tab">Net Wt</span></th> 

                                                </tr>

                                            </thead> 

                                            <tbody>

                                                <tr>

                                                    <td><b>Lot</b></td>

                                                    <td><span class="lt_disp_val disp_lot_pcs"></span></td>

                                                    <td><span class="lt_disp_val disp_lot_wt"></span></td>

                                                    <td><span class="lt_disp_val disp_lot_nwt"></span></td>

                                                </tr>

                                                <tr>

                                                    <td><b>Completed</b></td>

                                                    <td><span class="lt_disp_val disp_lot_ntag_pcs"></span></td>

                                                    <td><span class="lt_disp_val disp_lot_ntag_wt"></span></td>

                                                    <td><span class="lt_disp_val disp_lot_ntag_nwt"></span></td>


                                                </tr>

                                                <tr>

                                                    <td><b>Balance</b></td>

                                                    <td><span class="lt_disp_val disp_lot_bal_pcs"></span></td>

                                                    <td><span class="lt_disp_val disp_lot_bal_wt"></span></td>

                                                    <td><span class="lt_disp_val disp_lot_bal_nwt"></span></td>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                            <div class="col-sm-3">
                                                <b><span class="lt_desc_tab">Metal</span></b>
                                            </div>
                                            <div class="col-sm-8">
                                                <span id="lt_metal" class="">-</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <b><span class="lt_desc_tab">Category</span></b>
                                            </div>
                                            <div class="col-sm-8">
                                                <span id="lt_category" class="">-</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <b><span class="lt_desc_tab">Receipt On</span></b>
                                            </div>
                                            <div class="col-sm-8">
                                                <span id="lt_date" class="">-</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                 <b><span class="lt_desc_tab">Supplier</span></b>
                                            </div>
                                            <div class="col-sm-8">
                                                <span id="lt_karigar_name" class="">-</span>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
      							<div class="box box-default"><br />
      								<div class="col-xs-offset-5">
      									<button type="button" id="nt_receipt_submit" class="btn btn-primary">Save</button>
      									<button type="button" class="btn btn-default btn-cancel">Cancel</button>

      								</div> <br />
      							</div>
      						</div>
      					</div>
						<div class="overlay" style="display:none">
      						<i class="fa fa-refresh fa-spin"></i>
      					</div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>