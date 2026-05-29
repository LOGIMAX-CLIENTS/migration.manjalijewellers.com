<!-- line added by durga 28/12/2022 to get usertype -->
 <?php $username=($this->session->userdata['profile']);?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Sync Tool Records
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Inter Records</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
      
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
				
					<div class="box-header with-border">
					  <h3 class="box-title">Sync Tool Inter Table Records</h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
					  </div>
					</div>
                </div><!-- /.box-header -->
               

			   <div class="box-body">
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
			
					       
<!-- table wise Filter & change  option for cus reg, trans hh -->				       
					       
					     
					       
				 <div class="row">
                <div class="col-sm-4">	
                      <div class="form-group">
                          <label><a></a>Select Table</a></label>
                          <select id="Table_Select" class="form-control" style="width:150px;">
                              <option>Table</option>
                              <option value=1>Customer Reg</option>
                              <option value=2>Transactions</option>
                          </select>
                          <input id="id_cus" name="customer_reg[id_cus]" type="hidden" value=""/>
                      </div>
                </div>
                <div class="col-sm-8">
                  <div class="pull-right">
                        <div class="form-group">
                          <div class="btn-group" data-toggle="buttons">
                              <label class="btn btn-success" id="update">
                              <input type="radio" name="upd_mob_btn" value="1"><i class="icon fa fa-check"></i> Update
                              </label>
                          </div>
                        </div>
                    </div>
                  </div>
					  </div>
					       
					       
					       
				       </br><div class="row">
				         
              <div class="col-md-12" id="table" style="display: none;">
                 <div class="pull-left">
                 <div class="form-group" style="    margin-left: 40px;">
                      <label>Search By: &nbsp;&nbsp;</label>
                       <label id="mob">Mobile No </label>
                      <input type="text" name="" id="mobilenumber" >
                       <label>ClientId</label>
                        <input type="text" name="" id="clientid" >
                        <label>Ref No</label>
                        <input type="text" name="" id="ref_no" >
                        <label id="mob1">Group Code</label>
                        <input type="text" name="" id="group_code" >
                       <!-- <label>Sch A/c No</label>
                        <input type="text" name="" id="scheme_ac_no" >-->
                      <button type="submit" id="mob_submit" name="mob_submit" class="btn btn-primary">Submit</button>
												
                      
                    </div>

                 </div>
            </div> 
            
          </div></br> 
		<!-- table wise Filter & change  option for cus reg, trans hh -->	
				
				<!-- Alert -->
                     <!-- line added by durga 28/12/2022 to get usertype -->
                  <input type="hidden" id="hiddenuserdata" value=<?php echo $username ?> >
                  <div class="table-responsive" id="table1">
                  <table id="intertable_list" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr> 
                        <!--<th><label class="checkbox-inline"><input type="checkbox" id="select_mob"  name="select_all" value="all"/>All</label></th>-->
	                    <th>Cus Reg ID</th>
                        <th>Client ID</th>
                        <th>Branch</th>
                        <th>Record To</th>
                        <th>Is Modified</th>
                        <th>Reg Date</th>
                        <th>Acc Name</th>
                        <th>First Nmae</th>
                        <th>Last Name</th>
                        <th>Add1</th>
                        <th>Add2</th>
                        <th>Add3</th>
                        <th>Mobile No</th>
                        <th>New Customer</th>
                        <th>Ref No</th>
                        <th>Id Sch Acc</th>
                        <th>Sync Sch Code</th> 
                        <th>Group Code</th>  
                        <th>Scheme Acc No</th>
                        <th>Is Closed</th>
                        <th>Closed By</th>
                        <th>Closing Date</th>
                        <th>Closing Amount</th>
                        <th>Closing Weight</th>
                        <th>Is Transferred</th>
                        <th>Trans Date</th>
                        <th>Date Upd</th>
                        <th>Date Add</th>
                       <th>Is Reg Online</th>
            
                      </tr>
                    </thead>
                  </table>
               
				</div>  	<div class="overlay" style="display: none;">
                   <i class="fa fa-refresh fa-spin"></i>
                	</div>
				
			 <div class="table-responsive" id="table2" style="display: none;">
                  <table id="intertable_translist" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr> 
                        <!--<th><label class="checkbox-inline"><input type="checkbox" id="select_mob"  name="select_all" value="all"/>All</label></th>-->
	                    <th>Trans ID</th>
                        <th>Client ID</th>
                        <th>Record To</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Weight</th>
                        <th>Metal Rate</th>
                        <th>Pay Mode</th>
                        <th>Ref No</th>
                        <th>Is Transferred</th>
                        <th>Is Modified</th>
                        <th>Trans Date</th>
                        <th>New Customer</th>  
                        <th>Id Sch Acc</th>
                        <th>Id Branch</th>
                        <th>Pay Status</th>
                        <th>Pay Type</th>
                        <th>Due Type</th>
                        <th>Receipt No</th>
                        <th>Date Add</th>
                        <th>Date Upd</th>
                        <th>Install No</th>
                        <th>Emp Code</th>
            
                      </tr>
                    </thead>
                  </table>
               
				</div>
			      <div class="overlay" style="display: none;">
                   <i class="fa fa-refresh fa-spin"></i>
                	</div>
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->