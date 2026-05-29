  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Collection Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Collection List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Collection Report</h3> <span id="total" class="badge bg-green"></span>     
                         
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
	                 <div class="row">
    	                   <div class="col-md-12">
    	                       
    	                 	<div class="col-md-2">
    		                  <div class="form-group">
    		                    <div class="input-group">
    		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
    							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
    							    <span  style="display:none;" id="rpt_payments1"></span>
    							    <span  style="display:none;" id="rpt_payments2"></span>
    		                        <i class="fa fa-calendar"></i> Date range picker
    		                        <i class="fa fa-caret-down"></i>
    		                      </button>
    		                    </div>
    		                 </div><!-- /.form group -->
    		                </div>
    		                
		                  <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-2">
									<div class="form-group" >
									<label>Select Branch </label>
									<select id="branch_select" class="form-control" style="width:200px;" ></select>
									<input id="id_branch" name="scheme[id_branch]"  type="hidden" value=""/>
								</div>
							</div>
							<?php }?>
        		                
                                
                                <div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="schemw_wise_collection" class="btn btn-info">Search</button>   
									</div>
							    </div>
    					</div>
	                 </div>
					 </br>
					<!-- <div class="row">
				        <div class="col-md-2">
                            <div class="form-group">
                                <a href="<?php echo base_url('index.php/reports/payment_schemewise');?>" target="_blank"><button class="btn btn-warning">Summary Report</button></a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-warning">Mode Wise Report</button>
                            </div>
                        </div>
					 </div>-->
                    <div class="row">
                        <div class="box-body">
                             <div class="table-responsive">
                                    <table id="collection_report" class="table table-bordered table-striped text-center">
                                        <thead>
                                                <tr  style="text-transform:uppercase;">
                                                <th width="5%">S.No</th>
                                                <th width="5%">Scheme</th>
                                                <th width="2%">op Blc Amt</th>
                                                <th width="2%">op Blc Bonus</th>
                                                <th width="2%">op Blc Weight</th>
                                                <th width="2%">Collection Amt</th>
                                                <th width="2%">Bonus Allocated</th>
                                                <th width="2%">Collection Weight</th>
                                                <th width="2%">Closed Amount</th>
                                                <th width="2%">Bonus Deduction</th>
                                                <th width="2%">Closed Weight</th>
                                                <th width="2%">Closing Blc AMt</th>
                                                <th width="2%">Closing Bonus </th>
                                                <th width="2%">Closing Blc Weight</th>
                                            </tr>
                                        </thead> 
                                        <tbody></tbody>
                                        
        	                        </table>
                             </div>
                            
                        </div>
                    </div>
				<!-- /.box-body -->
              </div><!-- /.box -->
              	<div class="overlay" style="display:block">
				  <i class="fa fa-refresh fa-spin"></i>
				  </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

<!-- / modal -->  