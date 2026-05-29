  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Stone Rate Settings
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Product</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			          <div class="box-header with-border">
                  
                </div>
                 <div class="box-body">   
    			          <div class="row">
    					        <div class="col-xs-12">
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
                      </div>
    			          </div>
    			         <div class="row">
                      <div class="col-md-3"> 
                          <div class="form-group tagged">
                              <label>Type :<span class="error"></span></label>
                              <div class="form-group">
                                <input type="radio" class = "stone_type" id="wast_approval" name="stone_rate_type"  value="1" checked><label for="">&nbsp;&nbsp;Loose Stones</label>&nbsp;&nbsp;
                                <input type="radio" class = "stone_type"  id="stn_approval"  name="stone_rate_type"  value="2" ><label for="">&nbsp;&nbsp;Loose Produts</label>&nbsp;&nbsp;
                            </div> 
                        </div> 
                      </div>
                    </div>   
                    <div class="row">    
                        <div class="col-md-2 lse_stn"> 
                            <div class="form-group">
                                <label>Select Stone Type</label>
                                <select id="rate_stn_type" class="form-control" style="width:100%;"></select>
                            </div> 
                        </div>
                        <div class="col-md-2 lse_stn"> 
                            <div class="form-group">
                                <label>Select Stone Name</label>
                                <select id="rate_stn_id" class="form-control" style="width:100%;"></select>
                            </div> 
                        </div>
                        <div class="col-md-2 lse_pro" style="display:none;"> 
                            <div class="form-group">
                                <label>Select Product</label>
                                <select id="rate_stn_pro" class="form-control" style="width:100%;"></select>
                            </div> 
                        </div>
                        <div class="col-md-2 lse_pro" style="display:none;"> 
                            <div class="form-group">
                                <label>Select Design</label>
                                <select id="rate_stn_des" class="form-control" style="width:100%;"></select>
                            </div> 
                        </div>
                        <div class="col-md-2 lse_pro" style="display:none;"> 
                            <div class="form-group">
                                <label>Select Sub Design</label>
                                <select id="rate_stn_sub_des" class="form-control" style="width:100%;"></select>
                            </div> 
                        </div>
                        <div class="col-md-2"> 
                            <div class="form-group">
                                <label>Select Quality Code</label>
                                <select id="rate_quality_code" class="form-control" style="width:100%;"></select>
                            </div> 
                        </div>

                          <div class="col-md-2"> 
                              <div class="form-group">
                              <label>Select Branch  </label>
                                      <select id="branch_filter" name="settings[branch][]" class="form-control ret_branch"></select>
                                      <input type="hidden" id="id_branch" name="id_branch"  value="" >
                              </div>
                          </div>
                  

                        <div class="col-md-2"> 
                            <div class="form-group">
                                <label></label></br>
                                <button type="button" id="search_stone_rate" class="btn btn-info">Search</button>   
                            </div> 
                        </div>

                        <div class="col-md-2 pull-right">
                        <div class="form-group">
                                <label></label></br>
                                <?php if($access['add'] ==1){ ?>
                                <a class="btn btn-success pull-right" id="add_product" href="<?php echo base_url('index.php/admin_ret_catalog/ret_stone_rate_settings/add');?>" ><i class="fa fa-plus-circle"></i> Add</a>
                                
                                <?php }?>
                            </div> 
                  	       
				             </div>
                        
                  </div>
                                        
                  <div class="table-responsive lse_stn">
	                 <table id="stone_rate_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
                          <th>ID</th>
                          <th>Stone Type</th>
                          <th>Stone Name</th>
                          <th>UOM</th>
                          <th>Stone Calc Type</th>
                          <th>Quality Code</th>
                          <th>Branch</th>
                          <th>From Cent</th>
                          <th>To Cent</th>
                          <th>Min rate</th>
                          <th>Max rate</th>
                          <th>Action</th>
	                      </tr>
	                    </thead> 
	                 </table>
                  </div>

                  <div class="table-responsive lse_pro" style="display:none">
	                 <table id="product_stone_rate_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
                          <th>ID</th>
                          <th>Product</th>
                          <th>Design</th>
                          <th>Sub Design</th>
                          <th>UOM</th>
                          <th>Stone Calc Type</th>
                          <th>Quality Code</th>
                          <th>Branch</th>
                          <th>From Cent</th>
                          <th>To Cent</th>
                          <th>Min rate</th>
                          <th>Max rate</th>
                          <th>Action</th>
	                      </tr>
	                    </thead> 
	                 </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none;">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
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
               <strong>Are you sure! You want to delete this Settings?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
