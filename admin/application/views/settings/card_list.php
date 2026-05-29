  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Payu cards List
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Settings</a></li>
            <li class="active">Payu cards List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Payu cards List</h3>      
                           <a class="btn btn-success pull-right" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
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
                  <div class="table-responsive">
                  <table id="cardbrand_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Card type</th>
                        <th>Card Brand</th>
                        <th>Code</th>
                        <th>Action</th>
                      </tr>
                      
                 </thead>
                
                    
                 <!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
                  </table>
                  </div>
                </div><!-- /.box-body -->
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
        <h4 class="modal-title" id="myModalLabel">Delete card</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this card record?</strong>
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
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Card</h4>
      </div>
      <div class="modal-body">
         	 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Card Brand</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="card_brand" name="card_brand" value="<?php echo set_value('card_branch',(isset($wt)?$wt:"")); ?>" placeholder="Enter Card Brand">
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Short code</label>
                       <div class="col-md-4">                       	  
                       	 <input type="text" class="form-control" id="short_code" name="short_code" value="<?php echo set_value('short_code',(isset($wt)?$wt:"")); ?>" placeholder="Enter short code "> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>  
				  <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Card Type</label>
           				 <div class="col-md-4">
					   <label>	<input type="radio"  name="cardtype" value="1" class="minimal"  />Credit </label>
					   <label> 	<input type="radio"  name="cardtype" value="2" class="minimal" />Debit</label>
               	      <input type="hidden" class="form-control" id="card_type" name="card_type" > 
						<p class="help-block"></p>
         	 			 </div>
   					</div>
				 </div>      
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_cardbrand" class="btn btn-success" data-dismiss="modal" >Add</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Card</h4>
      </div>
      <div class="modal-body">
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Card Brand</label>
                       <div class="col-md-4">
                       <input type="hidden" id="edit-id" value="" />
                       	 <input type="text" class="form-control" id="edcard_brand" name="card_branch"  placeholder="Enter Card Brand">                        	 
                 		 <p class="help-block"></p>
                       </div>
                    </div>
				 </div>  
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Short Code</label>
                       <div class="col-md-4">
                       <input type="hidden" id="edit-id" value="" />
                       	 <input type="text" class="form-control" id="edshort_code" name="short_code"  placeholder="Enter Card Brand">                      	 
                 		 <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Card Type</label>
                  <div class="col-md-4">
					 <label>	<input type="radio" id="ed_cc" name="edcardtype" value="1" />Credit </label>
					  <label> <input type="radio" id="ed_dc" name="edcardtype" value="2" class="minimal"/>Debit </label>
					   

						<input type="hidden" class="form-control" id="edcard_type" name="card_type" > 
						<p class="help-block"></p>
                 </div>
 </div>	
				 </div>      
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_card_branch" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

