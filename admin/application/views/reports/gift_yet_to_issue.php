<style>
#table-container{
  padding:23px;
  /* font-size:16px; */
}
.center-table {
  margin-left: auto; 
  margin-right: auto;
  
}

  </style>



<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Gift - Yet To Issue
            <small></small>
          </h1>
          
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
                <div class="box-body">
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
				<div class="row">
                    
                     <?php 
        $branchWiseLogin=$this->session->userdata('branchWiseLogin');
        if($branchWiseLogin==1) { 
                     ?>


                     <div class="col-md-2">
                         <label>Select Branch</label>
                         <select id="branch_select" class="form-control"></select>
                     </div>
                     <?php  } ?>
                     <div class="col-md-2">
                         <label>Select Scheme</label>
                         <select id="scheme_select" class="form-control"></select>
                     </div>
                     <div class="col-md-2">
                         <label>Select Gift</label>
                         <select id="gift_select" class="form-control"></select>
                     </div>
                     <div class="col-md-2">
                         <label>GiftWise/Schemewise</label>
                         <select id="report_type" class="form-control">
                          <option value="1">Schemewise</option>
                          <option  selected value="2"> GiftWise </option>

                         </select>
                     </div>
                     <div class="col-md-1">
                        <label></label>
                        <div class="form-group">
                            <button type="button" id="gift_report_search" class="btn btn-info">Search</button>
                        </div>
                    </div>
                                            
                </div>	
                <div class="box box-info stock_details">
						<div class="box-header with-border">
						  <h3 class="box-title">Summary Gift - Yet To Issue <span class="summery_description"></span></h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
					    <div class="box-body collapse" style="display: block;">
					       <div class="row" >
					           
					    <div id="table-container" style=" margin-top: -23px;"></div>
					       </div>
							
						</div>

					</div>
						  <br>
                <div class="table-responsive">
                  <table id="gift_issue_report" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>S.No</th>
                       
                        <th>Id scheme account</th>
                        <th>Mobile</th>
                        <th>Customer name</th>
                         <th>Start date</th>
                         <th>Joined Branch</th>
                        <th>Acc number </th>
                        <th>Acc name </th> 
                        <th>Scheme  code </th> 
                        <th>Paid ins</th> 
                         <th>Gift name (yet to issue)</th> 
                        <th>Gift quantity (yet to issue)</th>
                        <th>Current stock(avail in inventory)</th>                                           
                        <th>Total gifts issued/assigned</th>                                           
                       <th>Referred EMP</th>      
                      </tr>
                 	</thead>
                        <tbody></tbody>
                        <tfoot>
                       
    </tfoot>
                  </table>
                  </div>
				  
                </div><!-- /.box-body -->
               
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->