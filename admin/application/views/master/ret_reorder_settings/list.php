    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <section class="content-header">
            <h1>
              Re-Order settings
              <small></small>
            </h1>
            <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#">Masters</a></li>
              <li class="active">Re-Order Settings List</li>
            </ol>
          </section>

          <!-- Main content -->
          <section class="content">
            <div class="row">
              <div class="col-xs-12">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">Re-Order Settings List</h3>    
                    <span id="total_weights" class="badge bg-green"></span>    
                    <?php if($access['add']==1){ ?>
                    <a class="btn btn-success pull-right" id="add_settings"  href="<?php echo base_url('index.php/admin_ret_catalog/reorder_settings/new');?>"  ><i class="fa fa-user-plus"></i> Add</a>
                    <?php }?>
                  </div><!-- /.box-header -->
                  <div class="box-body">
                      <div class="row">
                          <div class="col-md-2">
                              <div class="form-group">
                                   <label class="product_for">Select Branch</label>
                                   <select id="" class="form-control ret_branch"></select>
                            </div>
                          </div>

                          <div class="col-md-2">
                              <div class="form-group">
                                   <label class="design_for">Select Section</label>
                                   <select id="section_sel" class="form-control"></select>
                            </div>
                          </div>
                          
                          <div class="col-md-2">
                              <div class="form-group">
                                   <label class="product_for">Select Product</label>
                                   <select id="ret_product" class="form-control"></select>
                            </div>
                          </div>
                          
                          <div class="col-md-2">
                              <div class="form-group">
                                   <label class="design_for">Select Design</label>
                                   <select id="ret_design" class="form-control"></select>
                            </div>
                          </div>
                          
                          <div class="col-md-2">
                              <div class="form-group">
                                   <label class="design_for">Select Sub Design</label>
                                   <select id="ret_sub_design" class="form-control"></select>
                            </div>
                          </div>
                          
                          <div class="col-md-2">
                              <div class="form-group">
                                   <label class="design_for">Select Weight Range</label>
                                   <select id="weight_range" class="form-control"></select>
                            </div>
                          </div>
                          

                    </div>
                    <div class="row">

                    <div class="col-md-2">
                              <div class="form-group">
                                   <label class="design_for">Select Size</label>
                                   <select id="select_size" class="form-control"></select>
                            </div>
                          </div>
                          <div class="col-md-2">
                              <div class="form-group">
                                  <br>
                                   <button class="btn btn-primary" id="reorder_set_search">Search</button>
                            </div>
                          </div>
                          
                      </div>
                  <!-- Alert -->
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
                
                    <div class="table-responsive">
                    <table id="reorder_set_list" class="table table-bordered table-striped text-center">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Ref No</th>
                          <th>Branch</th>
                          <th>Section</th>
                          <th>Product</th>
                          <th>Design</th>
                          <th>SubDesign</th>
                          <th>From Weight</th>
                          <th>To Weight</th>
                          <th>WT Range</th>
                          <th>Size</th>
                          <th>Min Pcs</th>
                          <th>Max Pcs</th>
                          <th>Action</th>
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
  <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Delete Settings</h4>
        </div>
        <div class="modal-body">
                 <strong>Are you sure! You want to delete this settings?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger btn-confirm" >Delete</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->  
  <!-- modal -->      
  <div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Add Re-Order Settings</h4>
        </div>
        <div class="modal-body">
            <div id="chit_alert"></div>
          <form id="add_reorder">
                 <input type="hidden" name="settings[branch_settings]" id="branch_settings" value="<?php echo $this->session->userdata('branch_settings');?>">
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-sm-4 col-md-offset-1 ">Select Product<span class="error">*</span></label>
                               <div class="col-sm-4">
                                 <select class="form-control"  name="settings[id_product]" id="product_select" style="width: 100%"></select>
                                 <input type="hidden" id="weight_range_based">
                                <input type="hidden" id="product" name="">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
              <div class="row">
                     <div class="form-group">
                               <label for="" class="col-md-4 col-md-offset-1 ">Select Design<span class="error">*</span></label>
                               <div class="col-md-4">
                                 <select class="form-control des_select" name="settings[id_design]" id="des_select" style="width: 100%"></select>
                                <input type="hidden" id="id_design" name="">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
                 
                 <div class="row">
                     <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select SubDesign<span class="error">*</span></label>
                       <div class="col-md-4">
                         <select class="form-control select_sub_design" name="settings[id_sub_design]" id="select_sub_design" style="width: 100%"></select>
                        <input type="hidden" id="id_sub_design" name="">
                       </div>
                    </div>
                </div><p class="help-block"></p>
                
                 <div class="row">
                      <div class="form-group">
                         <label for="" class="col-md-4 col-md-offset-1 ">Select Weight Range<span class="error">*</span></label>
                         <div class="col-md-4">
                            <select class="form-control" id="wt_select" name="settings[weight_range]" style="width: 100%;px"></select>
                            <input type="hidden" id="wt_range" name="">
                         </div>
                  </div>
                </div><p class="help-block"></p>
                
                
                 
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-md-4 col-md-offset-1">Size</label>
                               <div class="col-md-4">
                                  <select class="form-control"  name="settings[id_size]" id="select_size" style="width: 100%;px"></select>
                               </div>
                            </div>
                 </div><p class="help-block"></p>
            <?php if($this->session->userdata('branch_settings')==1){?>
                <div class="row">
                    <div class="col-md-12">
                        <table id="total_items" class="table table-bordered table-striped text-center" style="width: 67%;margin-left: 8%;">
                            <thead>
                                <tr>
                                <th>Branch</th>
                                <th>Min Pcs</th>
                                <th>Max Pcs</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php }?>
             
                 </form>
        </div>
        <div class="modal-footer">
           <!-- <a href="#" id="add_retsettings" class="btn btn-success">Save</a>-->
           <a href="#" id="add_retsettings_new" class="btn btn-success">Save and New</a>
           <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->
  <!-- modal -->      
  <div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Edit Settings</h4>
        </div>
        <div class="modal-body">
            <div id="update_alert"></div>
              <form id="myform">
                  <input type="hidden" id="edit-id" value="" />
                  <div class="row">
                      <div class="form-group">
                         <label for="branch" class="col-md-4 col-md-offset-1 ">Select Branch<span class="error">*</span></label>
                         <div class="col-md-4">
                            <select  class="form-control" id="ed_branch_select" style="width:100%;"></select> 
                            <input type="hidden" class="id_branch" id="id_branch"> 
                         </div>
                      </div>
                  </div><p class="help-block"></p>
                  
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-sm-4 col-md-offset-1 ">Select Product<span class="error">*</span></label>
                               <div class="col-sm-4">
                                 <select class="form-control" id="ed_prod_select" style="width: 100%"></select>
                                 <input type="hidden" id="ed_weight_range_based">
                                <input type="hidden" id="ed_product" name="">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-md-4 col-md-offset-1 ">Select Design<span class="error">*</span></label>
                               <div class="col-md-4">
                                 <select class="form-control des_select" id="ed_des_select" style="width: 100%"></select>
                                <input type="hidden" id="ed_id_design" name="">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
                 
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-md-4 col-md-offset-1 ">Select Sub Design<span class="error">*</span></label>
                               <div class="col-md-4">
                                 <select class="form-control ed_sub_des_select" id="ed_sub_des_select" style="width: 100%"></select>
                                <input type="hidden" id="ed_id_sub_design" name="">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
                 
                 <div class="row">
                      <div class="form-group">
                         <label for="" class="col-md-4 col-md-offset-1 ">Select Weight Range<span class="error">*</span></label>
                         <div class="col-md-4">
                            <select class="form-control" id="ed_wt_select" style="width: 100%;x"></select>
                            <input type="hidden" id="ed_wt_range" name="">
                         </div>
                  </div>
                </div><p class="help-block"></p>
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-md-4 col-md-offset-1">Size</label>
                               <div class="col-md-4">
                                  <!--<input type="number" class="form-control" id="ed_size" name="size" placeholder="Enter Size">-->
                                 <select class="form-control" id="ed_select_size" style="width: 100%;px"></select>
                                 <input type="hidden" id="ed_id_size" name="">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-md-4 col-md-offset-1 ">Minimum Pieces<span class="error">*</span></label>
                               <div class="col-md-4">
                                  <input type="number" class="form-control" id="ed_min_pcs" name="min_pcs" placeholder="Enter Minimum Pieces">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
                 <div class="row">
                     <div class="form-group">
                               <label for="" class="col-md-4 col-md-offset-1 ">Max Pieces<span class="error">*</span></label>
                               <div class="col-md-4">
                                  <input type="number" class="form-control" id="ed_max_pcs" name="max_pcs" placeholder="Enter Maximum Pieces">
                               </div>
                            </div>
                 </div><p class="help-block"></p>
                 </form>
        </div>
        <div class="modal-footer">
          <a href="#" id="update_retsettings" class="btn btn-success">Update</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->      

