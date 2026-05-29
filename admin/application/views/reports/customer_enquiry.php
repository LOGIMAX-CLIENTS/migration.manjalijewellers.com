  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
         Customer Feedback 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Scheme</a></li>
            <!--<li class="active">reg_list</li>-->
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Customer Feedback list</h3> <span class="badge bg-green" id="total_enquiry"></span>
                </div><!-- /.box-header -->
                <div class="box-body">    
	            <?php 
				  $attributes = array('id' => 'feedback');
				//  echo form_open('payment/update_status',$attributes);
				 ?>
            	<div class="row"> 
					<div class="form-group col-md-6">
					   <button class="btn btn-default btn_date_range" id="enquiry_date">
						<i class="fa fa-calendar"></i> Date range picker
						<i class="fa fa-caret-down"></i>
						</button>
					</div> 
					<div class="form-group col-md-2 ">
						<label for="" ><a  data-toggle="tooltip" title="Select feedback category"> Filter Category  </a> <span class="error"></span></label>
						<select id="feed_filter_type" class="form-control">
							<option value="">All</option>
							<option value="1">Enquiry</option>
							<option value="2">Suggestion</option>
							<option value="3">Complaint</option>
							<option value="4">Others</option>
							<option value="5">DTH</option>
							<option value="6">Experience Center</option>
							<option value="7">Coin Enquiry</option>
						</select>
					</div> 
					<div class="form-group col-md-2 ">
						<label for="" ><a  data-toggle="tooltip" title="Select feedback category"> Filter By Status  </a> <span class="error"></span></label>
						<select id="feed_filter_status" class="form-control">
							<option value="">All</option>
							<option value="0">Open</option>
							<option value="1">In Follow Up</option>
							<option value="2">Closed</option>
						</select>
					</div> 
				 </div> 
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
                <div class="table-responsive">
                  <table id="enquiry_list" class="table table-bordered table-striped text-center" style="width:100% !important">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Ticket No.</th>
                        <th>Name</th>
                        <th>Mobile</th>  
                        <!--<th>Email</th>    -->
                        <th>Date</th> 
                        <th>Coin Type</th>
                        <th>Gram</th>
                        <th>Product Name</th>
                        <th>Title</th>   
                        <th width='40%'>Comments</th> 
                        <th>Status</th>
                        <th>Narration</th>  
                        <th>Through</th>     
                        <th>Action</th> 
                      </tr>
                    </thead>
                    <tbody>
                       </form>
                    </tbody>
               <!--  <tfoot>
                      <tr >
                         <td colspan="10"> <p style="text-align:left"></p></td>
                      </tr>
                    </tfoot> -->
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
<div class="modal fade" id="enq_status_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 1000px;">
    <div class="modal-content">
      <div class="modal-header bg-yellow">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel" align="center">Enquiry Status Detail</h4>
      </div>
      <div class="modal-body">
    	       
           <div class="enq_status_dtl"></div>    
      </div>
      <!--<div class="modal-footer">
      	<div class="col-sm-6 col-sm-offset-3">
          <button type="button" class="btn btn-block btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>-->
    </div>
  </div>
</div>
<!-- / modal --> 

<!-- modal -->      
<div class="modal fade" id="update_enq_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-yellow">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel" align="center">Update Enquiry Status</h4>
      </div>
      <div class="modal-body">
          <div class="row">
		 	<div class="form-group">
               <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Status</label>
               <div class="col-md-4">
                 <input type="hidden" class="form-control" id="id_enquiry" name="id_enquiry"/>
               	 <select class="form-control" id="enq_status" name="enq_status" required="true">
               	     <option value="1">In Follow up</option>
               	     <option value="2">Closed</option>
               	 </select> 
                 <p class="help-block"></p>
               </div>
            </div>
		  </div> 
		  <div class="row">
		 	<div class="form-group">  
                 <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Description</label>
                   <div class="col-md-9">
                   	 <textarea id="enq_desc" name="enq_desc"  placeholder="Enter Description" required="true"  rows="5" cols="50"> </textarea>
                     <p class="help-block"></p>
                   </div>
                 <p class="help-block"></p> 
            </div>
		  </div> 
		  <div class="row">
		 	<div class="form-group">  
                 <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Internal Status</label>
                   <div class="col-md-9">
                   	 <textarea id="internal_stat" name="internal_stat"  placeholder="Internal Status" required="true"  rows="5" cols="50"> </textarea>
                     <p class="help-block"></p>
                   </div>
                 <p class="help-block"></p> 
            </div>
		  </div> 
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_enq_status" class="btn btn-success" data-dismiss="modal" >Add</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal --> 

<style type="text/css">
.popover1{
    width:230px;
    height:330px;    
}
.trans tr{
	 width:50%;
    height:50%;
	font-size:15px;
	
}
</style>