<style type="text/css">

  .ord_img

  {

    padding:5px 10px;

    background:#605CA8;

    border:1px solid #605CA8;

    position:relative;

    color:#fff;

    border-radius:2px;

    text-align:center;

    float:left;

    cursor:pointer;

  }

  .order_images_new{

    position: absolute;

    z-index: 1000;

    opacity: 0;

    cursor: pointer;

    right: 0;

    top: 0;

    height: 100%;

    font-size: 24px;

    width: 100%;

  }

</style>

 <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Selling V.A & M.C Fixing

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Selling V.A & M.C Fixing</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

              <div class="box-header with-border">

                

                          

                </div><!-- /.box-header -->

				

                <div class="box-body">

                   

                   

                <form id="selling_settings">



                <input type="hidden" id="is_va_mc_based_on_branch" name="is_va_mc_based_on_branch"  value="<?php echo set_value('is_va_mc_based_on_branch',$is_va_mc_based_on_branch); ?>" >

                <input type="hidden" id="id_selling_settings" name="id_selling_settings"  value="" >

                    <div class="row">

                            <div class="col-md-12">

                                <div class="col-md-2">

                                    <div class="form-group">

                                        <label><a  data-toggle="tooltip" title="Enter Product">Category</a><span class="error">*</span></label>

                                        <select id="des_cat_name" class="form-control" required></select>

                                        <div  id="catAlert" name=""></div>

                                        <input type="hidden" id="cat_id" name="cat_id"  value="" >

                                    </div>

                                    </div>

                                <div class="col-md-2"> 

                                    <div class="form-group tagged">

                                        <label>Select Product<span class="error">*</span></label>

                                        <select id="des_prod_name" name="settings[id_product]" class="form-control" style="width:100%;"></select>

                                        <input type="hidden" id="pro_id" name="pro_id"  value="" >

                                    </div> 

                                </div>

                                

                                <div class="col-md-2"> 

                                    <div class="form-group tagged">

                                        <label>Select Design<span class="error">*</span></label>

                                        <select id="des_des_name" name="settings[id_design]" class="form-control" style="width:100%;" ></select>

                                        <input type="hidden" id="des_id" name="des_id"  value="" >

                                    </div> 

                                </div>

                                

                                <div class="col-md-2"> 

                                    <div class="form-group tagged">

                                        <label>Select Sub Design</label>

                                        <select id="select_sub_design" name="settings[id_sub_design]" class="form-control" style="width:100%;" ></select>

                                        <input type="hidden" id="sub_des_id" name="sub_des_id"  value="" >

                                    </div> 

                                </div>



                                <?php if($is_va_mc_based_on_branch == 1  ){ ?>

                                    <div class="col-md-2"> 

                                        <div class="form-group">

                                        <label>Select Branch </label>

                                              <?php if($this->uri->segment(3) == 'edit'){?>

                                                    <select id="branch_filter" name="settings[branch][]" class="form-control ret_branch"></select>

                                              <?php }else{?>

                                                    <select id="branch_filter" name="settings[branch][]" class="form-control ret_branch" multiple></select>

                                              <?php }?>

                                              

                                              <input type="hidden" id="id_branch" name="id_branch"  value="" >

                                        </div>

                                    </div>

                                <?php } ?>



                                </div> 

                                </div>



                                <div class="row">

                                  <div class="col-md-12">

                                    <div class="col-md-2"> 

                                        <div class="form-group tagged">

                                            <label>Type</label></br>

                                                <input type="radio" id="wastage_type_fixed" name="settings[wastage_type]" value="1" checked>&nbsp;<label for="wastage_type_fixed">Fixed</label>

                                                <input type="radio" id="wastage_type_flexi" name="settings[wastage_type]" value="2">&nbsp;<label for="wastage_type_flexi">Weight Range</label> 

                                        </div> 

                                    </div>

                                    <div class="col-md-2">

                                      <div class="form-group fixed_type">

                                        <label>Wastage Type</label>

                                        <select class="form-control" name="settings[wastag_method]" id="wastag_method">

                                          <option value="1">Percentage</option>

                                          <option value="2">Weight</option>

                                        </select>

                                      </div>

                                    </div>

                                    <div class="col-md-2"> 

                                      <div class="form-group fixed_type">

                                          <label>Min V.A (%)</label>

                                          <input class="form-control" id="min_wastag_valuess" name="settings[min_wastag_value]" type="number"  step=any  placeholder="Min V.A" />

                                      </div> 

                                    </div>

                                    <div class="col-md-2"> 

                                      <div class="form-group fixed_type">

                                          <label>Min V.A (Wt(g))</label>

                                          <input class="form-control" id="wastag_min_wt" name="settings[wastag_min_wt]" type="number"  step=any  placeholder="Min V.A(Wt(g))" disabled/>

                                      </div> 

                                    </div>

                                    <div class="col-md-2"> 

                                      <div class="form-group fixed_type">

                                          <label>Max V.A (%)</label>

                                          <input class="form-control" id="wastag_value" name="settings[wastag_value]" type="number"  step=any  placeholder="Max V.A" />

                                      </div> 

                                    </div>

                                    <div class="col-md-2"> 

                                      <div class="form-group fixed_type">

                                          <label>Max V.A (Wt(g))</label>

                                          <input class="form-control" id="wastag_wt" name="settings[wastag_wt]" type="number"  step=any  placeholder="Max V.A(Wt(g))" disabled/>

                                      </div> 

                                    </div>

                                  </div>

                                  <div class="col-md-12">

                                    <div class="col-md-2">

                                      <div class="form-group">

                                        <label>Update MC Type</label>

                                        <select class="form-control" name="settings[mc_type]" id="update_mc_type">

                                        <option value="0">Select Type</option>

                                        <option value="1">Per Pcs</option>

                                        <option value="2">Per Grm</option>

                                        <option value="3">% of Price</option>

                                        </select>

                                      </div>

                                    </div>



                                    <div class="col-md-2"> 

                                      <div class="form-group ">

                                        <label>Min M.C</label>

                                        <input class="form-control" id="min_mc_value" name="settings[min_mc_value]" type="number"  step=any  placeholder="Min M.C" disabled/>

                                      </div> 

                                    </div>



                                    <div class="col-md-2"> 

                                      <div class="form-group fixed_type">

                                          <label>Max M.C</label>

                                          <input class="form-control" id="mc_value" name="settings[mc_value]" type="number"  step=any  placeholder="Max M.C" disabled/>

                                      </div>

                                    </div>

                                    <div class="row" id="mrp_block">

                                        <div class="col-sm-2">

                                            <label for="">Margin MRP(%)</label>

                                            <p class="help-block"></p>

                                            <input class="form-control" id="margin_mrp" name="settings[margin_mrp]" type="number"  step=any  placeholder="Enter in % to validate MRP sales cost" />

                                        </div>

                                    </div>

                                  </div> 

                              </div>

                            <div class="row">

                            <div class="col-md-12">

                            <div class="col-md-12">

                                    <div class="row flexiable_type" id="" >

                                        <div class="col-md-12">

                                            <!--<button id="add_wc_weight_info" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Range</button>-->

                                            <table id="wc_detail" class="table table-bordered table-striped text-center">

                                               <thead>

                                                  <tr>

                                                     <th>S.No.</th>

                                                     <th>From Weight</th>

                                                     <th>To Weight</th>

                                                     <th>Method</th>

                                                     <th>Min V.A(%)</th>

                                                     <th>Min V.A(Wt(g)) </th>

                                                     <th>Max V.A(%)</th>

                                                     <th>Max V.A(Wt(g))</th>
                                                     
                                                     <th>MC TYpe</th>

                                                     <th>Min M.C</th>

                                                     <th>Max M.C</th>

                                                     <th>Action</th>

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

                   

                 </form>

                     

                   <p class="help-block"> </p>  

			     <div class="row">

				   <div class="box box-default"><br/>

					  <div class="col-xs-offset-5">

						<button type="button" id="update_weight_range_settings" class="btn btn-primary">Save</button> 

						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

					  </div> <br/>

					</div>

				  </div> 

				  

                 <div class="table-responsive" style="display:none;">

                  <input type="hidden" id="id_selling_settings">

                  <table id="subdesign_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>Product</th>

                        <th>Design</th>

                        <th>Sub Design</th>

                        <th>MC Type</th>

                        <th>V.A / MC</th>

                      </tr>

                 	</thead>

                 

                  </table>

                  </div> 

                    

                </div><!-- /.box-body -->

                 <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      



<!-- modal -->      

<div class="modal fade" id="confirm-delete"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Design Mapping</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Design Mapping ?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>









