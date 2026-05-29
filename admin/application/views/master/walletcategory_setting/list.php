  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Wallet Category Settings
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Wallet Category Settings List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Wallet Category Settings List</h3>    
				  <span id="total_plans" class="badge bg-aqua"></span>  
                         <!--  <a class="btn btn-success pull-right" id="add_plan" href="<?php echo base_url('index.php/wallet/master/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->
                </div><!-- /.box-header -->
				<div class="row">

				   <div class="col-sm-8 col-sm-offset-2">

						<div id="error-msg"></div>

					</div>
				</div>
				<div class="form-group">
          <?php if($access['edit']==1){ ?> 
					<button type="button" class="btn btn-primary  pull-right wallet_category"><i class="fa "></i> Update</button>							
				<?php }?>
        </div></br>
				
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

				<div class=""> 
					<?php $attributes =	array('id' => 'walletsetting', 'name' => 'wallet');
					 echo form_open_multipart('wallet/category/setting/update',$attributes);?>					 
					   <div class="walletcategory">					
					   </div>
					
					
                  <div class="table-responsive">
                  <table id="walletcatesett_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th width="5%"><label class="checkbox-inline"><input type="checkbox" id="select_walletdata"  name="select_all" value=""/> &nbsp;&nbsp;All</label></th>
                        <th width="15%">Category</th>
                        <th  width="40%">Points</th>                       
                        <th  width="10%">Redeem (%)</th>
                        <th  width="10%">Date Add </th>                 
                        <th  width="10%">Status </th>
                        <th  width="10%">Remark </th>                        
                      </tr>
                    </thead>

                  </table>
                  </div>				  
				  <?php echo form_close(); ?>				 
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
        <h4 class="modal-title" id="myModalLabel">Delete Wallet Setting</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this setting?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
