<!-- Content Wrapper. Contains page content -->
<style>
   .remove-btn{
   margin-top: -168px;
   margin-left: -38px;
   background-color: #e51712 !important;
   border: none;
   color: white !important;
   }
   #attribute_block {
   display: none;
   }
   legend {
   margin-bottom: 0px;
   }
   #mc_va_block {
   padding-bottom: 20px;
   }
   .apply_filter_row {
   padding: 0px;
   }
</style>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <!-- Main content -->
   <section class="content">
      <!-- Default box -->
      <div class="box box-primary">
         <div class="box-header with-border">
            <h3 class="box-title">Design SETTINGS</h3>
            <div class="box-tools pull-right">
               <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
               <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
            </div>
         </div>
         <div class="box-body">
            <!-- form container -->
            <div class="row">
               <div class="col-sm-12">
                  <div class="box-header with-border">
                     <h3 class="box-title">Filter Details ( <input type="radio" id="mc_va_update" name="mc_va_attr_update" value="1" checked /> <label for="mc_va_update">MC/VA</label> &nbsp; &nbsp; <input type="radio" id="attribute_update" name="mc_va_attr_update" value="2" /> <label for="attribute_update">Attributes</label> )</h3>
                  </div>
                  <!-- /.box-header -->
                  <div class="row">
                     <div class="col-md-2">
                        <div class="form-group">
                           <label><a  data-toggle="tooltip" title="Enter Product">Category</a><span class="error">*</span></label>
                           <select id="des_cat_name" class="form-control" required></select>
                           <div  id="catAlert" name=""></div>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label><a  data-toggle="tooltip" title="Enter Product">Product</a> <span class="error">*</span></label>
                           <select id="des_prod_name" class="form-control" required></select>
                           <div  id="prodAlert" name=""></div>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label><a  data-toggle="tooltip" title="Enter Design">Design</a>  </label>
                           <select id="des_des_name" class="form-control" required></select>
                           <div  id="desdAlert" name=""></div>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label><a  data-toggle="tooltip" title="Enter Sub Design">Sub Design</a>  </label>
                           <select id="select_sub_design" class="form-control" required></select>
                           <div  id="desdAlert" name=""></div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 apply_filter_row">
                        <div class="col-md-2 mcva_filters">
                           <label><a data-toggle="tooltip" title="MC Type">MC Type</a></label>
                           <div class="form-group">
                              <select id="old_mc_type" class="form-control">
                                 <option value="0">Select Type</option>
                                 <option value="1">Per Pcs</option>
                                 <option value="2">Per Grm</option>
                                 <option value="3">% of Price</option>
                              </select>
                              <input type="hidden" id="id_mc_type" name="">
                           </div>
                        </div>
                        <div class="col-md-2 mcva_filters" id="edit_mc_value" >
                           <label><a data-toggle="tooltip" title="Making Charge">Making Charge</a></label>
                           <input type="number" class="form-control" id="old_mc_value" placeholder="Making Charge Value" >
                        </div>
                        <div class="col-md-2 mcva_filters" id="edit_was_per" >
                           <label><a data-toggle="tooltip" title="Wastage Percentage">Wastage(%)</a></label>
                           <input type="number" class="form-control" id="old_wast_per" placeholder="Wastage Percentage" >
                        </div>
                        <div class="col-md-2">
                           <label></label><br>
                           <button class="btn btn-primary" id="get_tag_details" >Apply Filter</button>
                        </div>
                     </div>
                  </div>
                  <p class="help-block"></p>
               </div>
               <!--/ Col --> 
            </div>
            <!--/ row -->
            <div class="box-header with-border">
               <h3 class="box-title">Update Details</h3>
               <div class="box-tools pull-right">
               </div>
            </div>
            <p></p>
            <div class="row" id="editable_block" style="display: block;">
               <div class="col-sm-12">
                  <div class="row" id="mc_va_block">
                     <div class="col-md-2">
                        <div class="form-group">
                           <label>Update MC Type</label>
                           <select class="form-control" id="update_mc_type">
                              <option value="">Change MC Type</option>
                              <option value="1">Per Pcs</option>
                              <option value="2">Per Grm</option>
                              <option value="3">% of Price</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <label for="">Wastage Type</label>
                        <p class="help-block"></p>
                        <input type="radio" id="wastage_type_fixed" name="design[wastage_type]" value="1" checked >  &nbsp;&nbsp;&nbsp;<label for="wastage_type_fixed">Fixed</label>
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" id="wastage_type_flexi" name="design[wastage_type]" value="2" > &nbsp;&nbsp;&nbsp;<label for="wastage_type_flexi">Weight Range</label> 
                     </div>
                     <div class="col-md-2 fixed_type">
                        <div class="form-group">
                           <label>Update MC Value</label>
                           <input class="form-control" id="mc_value" name="tagging[tag_mc_value]" type="number"  step=any  placeholder="Making Charge"/>
                        </div>
                        <div class="form-group">
                           <label>Update VA(%)</label>
                           <input type="hidden" id="metal_rate" name="">
                           <input class="form-control" id="wastag_value" name="tagging[retail_max_wastage_percent]" type="number"  step=any  placeholder="Enter wastage percentage." />
                        </div>
                     </div>
                     <div class="col-md-2 fixed_type">
                        <div class="form-group">
                           <label>Min MC</label>
                           <input class="form-control" id="mc_min" name="tagging[mc_min]" type="number"  step=any  placeholder="Enter min MC" />
                        </div>
                        <div class="form-group">
                           <label>Min VA(%)</label>
                           <input class="form-control" id="wastag_min" name="tagging[wastag_min]" type="number"  step=any  placeholder="Enter min wastage" />
                        </div>
                     </div>
                     <div class="col-md-2 fixed_type">
                        <div class="form-group" style="display:none">
                           <label>Max MC</label>
                           <input class="form-control" id="mc_max" name="tagging[mc_max]" type="number"  step=any  placeholder="Enter max mc" />
                        </div>
                        <div class="form-group" style="display:none">
                           <label>Max VA(%)</label>
                           <input class="form-control" id="wastag_max" name="tagging[wastag_max]" type="number" step=any placeholder="Enter max wastage" />
                        </div>
                     </div>
                     <div class="col-md-8 flexiable_type" style="display:none;">
                        <legend>
                           <i>Weight range wastage information</i><button id="add_wc_weight_info" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Range</button>
                           <input  type="hidden" value="<?php echo sizeof($wcranges); ?>" id="wc_increment" />	
                           <p class="help-block"></p>
                        </legend>
                        <table id="wc_detail" class="table table-bordered table-striped text-center">
                           <thead>
                              <tr>
                                 <th>S.No.</th>
                                 <th>From Weight</th>
                                 <th>To Weight</th>
                                 <th>VA(%)</th>
                                 <th>Min VA(%) </th>
                                 <th  style="display:none">Max VA(%)</th>
                                 <th>Mc</th>
                                 <th>Min Mc</th>
                                 <th  style="display:none">Max Mc</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php  if(!empty($wcranges)) { 
                                 foreach($wcranges as $wckey => $wcval) {
                                 echo "<tr id='wc".$wckey."'><td>".($wckey + 1)."</td><td><input name='wcrange[".$wckey."][from_wt]' style='width:100%;' step='.001' id='from_wt".$wckey."' class='form-control' placeholder='From weight' type='number' value='".$wcval['wc_from_weight']."'></td>".$wckey."<td><input name='wcrange[".$wckey."][to_wt]'  step='.001' style='width:100%;' id='stone_pcs".$wckey."' class='form-control' placeholder='To weight' type='number' value='".$wcval['wc_to_weight']."'></td><td><input name='wcrange[".$wckey."][wc_percent]' value='".$wcval['wc_percent']."'  step='.001' style='width:100%;' id='wc_percent".$wckey."' class='form-control' placeholder='WC(%)' type='number'></td><input name='wcrange[".$wckey."][mc_percent]' value='".$wcval['mc_percent']."'  step='.001' style='width:100%;' id='mc_percent".$wckey."' class='form-control' placeholder='Mc(%)' type='number'></td><td><button type='button' class='btn btn-danger' onclick='wc_remove(".$wckey.")'><i class='fa fa-trash'></i></button></td>";
                                 }
                                 }
                                 ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <div class="row" id="mrp_block">
                     <div class="col-sm-2">
                        <label for="">Margin MRP(%)</label>
                        <p class="help-block"></p>
                        <input class="form-control" id="margin_mrp" name="tagging[margin_mrp]" type="number"  step=any  placeholder="Enter in % to validate MRP sales cost" />
                     </div>
                  </div>
                  <p class="help-block"></p>
                  <div class="row" id="attribute_block">
                     <div class="col-md-2">
                        <div class="form-group">
                           <label>Choose</label>
                           <select class="form-control" id="attribute_type">
                              <option value="1">Add Attribute</option>
                              <option value="2">Delete Attribute</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6" id="update_attribute_block">
                        <legend>
                           <i>Add Attributes</i><button id="add_des_attribute" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add attribute</button>
                           <p class="help-block"></p>
                        </legend>
                        <table id="update_attribute_detail" class="table table-bordered table-striped text-center">
                           <thead>
                              <tr>
                                 <th>S.No.</th>
                                 <th>Attributes</th>
                                 <th>Value</th>
                                 <th>Action</th>
                              </tr>
                           </thead>
                           <tbody></tbody>
                        </table>
                     </div>
                  </div>
                  <p class="help-block"></p>
               </div>
               <!--/ Col --> 
            </div>
            <!--/ row -->
            <div class="table-responsive">
               <table id="edit_mas_design_list" class="table table-bordered table-striped text-center">
                  <thead>
                     <tr>
                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Design</th>
                        <th>Sub Design</th>
                        <th>MC Type</th>
                        <th>VA(%)/ Mc</th>
                        <th>VA Min</th>
                        <th>MC Min</th>
                        <th>MRP Margin</th>
                        <th>Attributes</th>
                     </tr>
                  </thead>
               </table>
            </div>
            <p class="help-block"> </p>
            <p class="help-block"> </p>
            <div class="row">
               <div class="box box-default">
                  <br/>
                  <div class="col-xs-offset-5">
                     <button class="btn btn-info" id="design_bedit_submit" >Submit</button>
                     <button type="button" class="btn btn-default btn-cancel">Cancel</button>
                  </div>
                  <br/>
               </div>
            </div>
         </div>
         <div class="overlay" style="display:none">
            <i class="fa fa-refresh fa-spin"></i>
         </div>
         <!-- /form -->
      </div>
   </section>
</div>
<div id="bulk_edit_confirm_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header ">
            <button type="button" id="close_modal" class="close" >&times;</button>
            <h3 id="myModalLabel">Bulk Design Update </h3>
         </div>
         <div class="modal-body">
            <p> <strong>Are you sure! You want to update this design settings?</strong></p>
            <div>
            </div>
            <div class="modal-footer">
               <a href="#" class="btn btn-success btn-confirm" id="confirm_update_bulk_design">Confirm</a>
               <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="wastage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Weight Range</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <p class="help-block"></p>
               </legend>
               <table id="wcdetail" class="table table-bordered table-striped text-center">
                  <thead>
                     <tr>
                        <th>Design Id</th>
                        <th>From Weight</th>
                        <th>To Weight</th>
                        <th>VA(%)</th>
                        <th>VA Min(%)</th>
                        <th>VA Max(%)</th>
                        <th>Mc</th>
                        <th>Mc Min</th>
                        <th>Mc Max</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="des_attributes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width: 50%;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Attributes</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <p class="help-block"></p>
               </legend>
               <table id="des_attribute_detail" class="table table-bordered table-striped text-center">
                  <thead>
                     <tr>
                        <th>Attribute Name</th>
                        <th>Value</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>