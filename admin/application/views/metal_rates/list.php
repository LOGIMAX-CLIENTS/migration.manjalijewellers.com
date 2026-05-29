  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Rate Master 
            <small>Update metal rates</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"> Masters</a></li>
            <li class="active"><a href="#">Metal Rates</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Metal Rate List</h3>   <span id="total_metals" class="badge bg-green"></span>  
                  <?php //if($access['add']==1){ ?>
                           <a class="btn btn-success pull-right" id="add_metal" href="<?php echo base_url('index.php/settings/rate/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
                            <?php //} ?>
                          </div><!-- /.box-header -->
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


			</br>	
			
			<!--<div class="row">
	                 <div class="col-md-12">
	                 <div class="col-md-7"></div>							<div class="col-md-5">
						   <?php if($this->session->userdata('branch_settings')==1){?>
									<div class="form-group" style=" margin-left: 140px;">
									   <label>Select Branch &nbsp;&nbsp; </label>
									   <select id="branch_select" class="form-control" style="width:150px;" ></select>
									  <input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								 </div>
							 <?php }?>														</div>
		                 </div>
	                 </div></br>	-->
					 
					 
                  <div class="table-responsive">
                  <table id="metalrate_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>MJDMA Gold 22KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>MJDMA Silver 1gm (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>Market Gold 18KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                         <th>Gold 14KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                         <th>Gold 9KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>Gold 18KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>Gold 20KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>Gold 22KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>Gold 24KT (<?php echo $this->session->userdata('currency_symbol')?>)</th>  
                        <th>Gold Coin (<?php echo $this->session->userdata('currency_symbol')?>)</th>  
                        <th>Silver 1gm (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>Silver 1kg (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th> Platinum 1gm (<?php echo $this->session->userdata('currency_symbol')?>)</th>
                        <th>Employee</th>
                        <th>Updated time</th>
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

<!-- / modal -->      
