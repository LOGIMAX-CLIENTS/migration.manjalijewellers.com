  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Payment Gateway List
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
				  <?php if($this->session->userdata('is_branchwise_cus_reg')==1){?>
                    <div class="col-md-5">
                       <div class="form-group" style="    margin-left: 50px;">
                      <label>Select Branch &nbsp;&nbsp;</label>
                      <select id="branch_select" class="form-control" style="width:150px;"></select>
                      <input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
                      <input id="is_branchwise_cus_reg" name="scheme[id_branch]" type="hidden" value="<?php echo$this->session->userdata('is_branchwise_cus_reg'); ?>"  />
                    </div> 
                    </div>
                  <?php }?>
                  <h3 class="box-title">Payment Gateway List</h3>      
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
                  <table id="paymentgateway_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Payment Gateway name</th>
						<th>Type</th>
                        <th>Icon</th>
                        <th>Status</th>
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
        <h4 class="modal-title" id="myModalLabel">Add Payment Gateway</h4>
      </div>
      <div class="modal-body">
         	 <div class="row">
                
				 	        <div class="form-group">
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Gateway Name</label>
                    
               
                        <div class="col-md-4">
                       	 <input type="text" class="form-control" id="gateway_name" name="gateway_name" value="" placeholder="Enter Gateway Name">
                  <p class="help-block"></p>
                       	</div>

                        
               </div>
                    
                </div>

                 <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short code</label>
                       <div class="col-md-4">                       	  
                       	 <input type="text" class="form-control" id="gateway_code" name="gateway_code" value="<?php echo set_value('gateway_code',(isset($wt)?$wt:"")); ?>" placeholder="Enter short code "> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>  
         <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
                   <div class="col-md-4">
             <label>  <input type="radio"  name="gateway_active" value="1" class="minimal"/>&nbsp;Yes </label>
             <label>  <input type="radio"  name="gateway_active" value="0" class="minimal" />&nbsp;No</label>
                      <input type="hidden" class="form-control" id="gateway_active" name="gateway_active" > 
            <p class="help-block"></p>
                 </div>
            </div>
         </div>
				  <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Save Card</label>
           				 <div class="col-md-4">
					   <label>	<input type="radio"  name="save_card" value="1" class="minimal"/>&nbsp;Active </label>
					   <label> 	<input type="radio"  name="save_card" value="0" class="minimal" />&nbsp;Inactive</label>
               	      <input type="hidden" class="form-control" id="save_card" name="save_card" > 
						<p class="help-block"></p>
         	 			 </div>
   					</div>
				 </div>
         <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Debit  Card</label>
                   <div class="col-md-4">
             <label>  <input type="radio"  name="debit_card" value="1" class="minimal"  />&nbsp;Active </label>
             <label>  <input type="radio"  name="debit_card" value="0" class="minimal" />&nbsp;Inactive</label>
                      <input type="hidden" class="form-control" id="debit_card" name="debit_card" > 
            <p class="help-block"></p>
                 </div>
            </div>
         </div>
         <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Credit Card</label>
                   <div class="col-md-4">
             <label>  <input type="radio"  name="credit_card" value="1" class="minimal"  />&nbsp;Active </label>
             <label>  <input type="radio"  name="credit_card" value="0" class="minimal" />&nbsp;Inactive</label>
                      <input type="hidden" class="form-control" id="credit_card" name="credit_card" > 
            <p class="help-block"></p>
                 </div>
            </div>
         </div>
         <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Net Banking</label>
                   <div class="col-md-4">
             <label>  <input type="radio"  name="netbanking" value="1" class="minimal"  />&nbsp;Active </label>
             <label>  <input type="radio"  name="netbanking" value="0" class="minimal" />&nbsp;Inactive</label>&nbsp;
                      <input type="hidden" class="form-control" id="netbanking" name="netbanking" > 
            <p class="help-block"></p>
                 </div>
            </div>
         </div>
         <!-- Commodity (Metal) selection — show_chit=1 metals only -->
         <div class="row">
            <div class="form-group">
              <label for="id_metal" class="col-md-3 col-md-offset-1">Commodity</label>
              <div class="col-md-4">
                <select class="form-control" id="id_metal" name="id_metal">
                  <option value="">-- Select Commodity --</option>
                  <?php if (!empty($chit_metals)): foreach ($chit_metals as $cm): ?>
                  <option value="<?php echo $cm['id_metal']; ?>"><?php echo $cm['metal']; ?></option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
            </div>
         </div>
       <div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload icon</label>
             <div class="col-md-6">
               <input id="pay_gateway_img" name="clsfy" accept="image/*" type="file" >
               <img src="<?php echo(isset($category['catimage'])?$category['catimage']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="pay_gateway_img_preview" style="width:304px;height:100%;" alt="gateway image"> 
                                           
            <p class="help-block"></p>
           </div>
        
             </div> 
        </div>  
        <div class="row">
           <div class="form-group">
            <label for="chargeseme_name" class="col-md-3  col-md-offset-1">Description</label>
            <div class="col-md-6">
              
             <textarea name="Description" id="gateway_description"  style="width: 100%;"></textarea>
                                          
           <p class="help-block"></p>
          </div>
       
            </div> 
       </div> 
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_paymentgateway" class="btn btn-success" data-dismiss="modal" >Add</a>
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
        <h4 class="modal-title" id="myModalLabel">Edit Payment Gateway</h4>
      </div>
      <div class="modal-body">
          <div class="row">
           
              
				 	        <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Gateway Name</label>
                       <div class="col-md-4">
                         <input type="hidden" id="edit-id" value="" />
                       	 <input type="text" class="form-control" id="ed_pg_name" name="ed_pg_name"  placeholder="Enter Gateway Name">                        	 
                 		 <p class="help-block"></p>
                       </div>
                    </div>
               
                    
                  
				 </div> 
         
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Code</label>
                       <div class="col-md-4">
                       <input type="hidden" id="edit-id" value="" />
                       	 <input type="text" class="form-control" id="ed_code" name="ed_short_code"  placeholder="Enter Code">                      	 
                 		 <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
        <div class="row">
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
                   <div class="col-md-4">
             <label>  <input type="radio" id="ed_gateway_active" name="ed_gateway" value="1" class="minimal"/>&nbsp;Yes </label>

             <label>  <input type="radio" id="ed_gateway_inactive"  name="ed_gateway" value="0" class="minimal" />&nbsp;No</label>
                      <input type="hidden" class="form-control" id="ed_gateway" name="ed_gateway" > 
            <p class="help-block"></p>
                 </div>
            </div>
         </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Save Card</label>
                  <div class="col-md-4">
					<label>	<input type="radio" id="ed_save_card_active" name="ed_save_card" value="1"/>&nbsp;Active </label>&nbsp;
					  <label> <input type="radio" id="ed_save_card_inactive" name="ed_save_card" value="0" class="minimal"/>Inactive </label>
					   

						<input type="hidden" class="form-control" id="ed_save_card" name="ed_save_card" > 
						<p class="help-block"></p>
                 </div>
        </div>
           
      </div>
      <div class="row">
          <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Debit Card</label>
                  <div class="col-md-4">
           <label>  <input type="radio" id="ed_debit_card_active" name="ed_debit_card" value="1" />&nbsp;Active </label>
            <label> <input type="radio" id="ed_debit_card_inactive" name="ed_debit_card" value="0" class="minimal"/>&nbsp;Inactive </label>
             

            <input type="hidden" class="form-control" id="ed_debit_card" name="ed_debit_card" > 
            <p class="help-block"></p>
                 </div>
        </div>
           
      </div>
      <div class="row">
          <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Credit Card</label>
                  <div class="col-md-4">
           <label>  <input type="radio" id="ed_credit_card_active" name="ed_credit_card" value="1" />&nbsp;Active </label>
            <label> <input type="radio" id="ed_credit_card_inactive" name="ed_credit_card" value="0" class="minimal"/>&nbsp;Inactive </label>
             

            <input type="hidden" class="form-control" id="ed_credit_card" name="ed_credit_card" > 
            <p class="help-block"></p>
                 </div>
        </div>
           
      </div>
      <div class="row">
          <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Net Banking </label>
                  <div class="col-md-4">
           <label>  <input type="radio" id="ed_netbanking_active" name="ed_netbanking" value="1" />&nbsp;Active </label>
            <label> <input type="radio" id="ed_netbanking_inactive" name="ed_netbanking" value="0" class="minimal"/>&nbsp;Inactive </label>
             

            <input type="hidden" class="form-control" id="ed_netbanking" name="ed_netbanking" > 
            <p class="help-block"></p>
                 </div>
        </div>
           
      </div>
      <!-- Commodity (Metal) selection — show_chit=1 metals only -->
      <div class="row">
         <div class="form-group">
           <label for="ed_id_metal" class="col-md-3 col-md-offset-1">Commodity</label>
           <div class="col-md-4">
             <select class="form-control" id="ed_id_metal" name="ed_id_metal">
               <option value="">-- Select Commodity --</option>
               <?php if (!empty($chit_metals)): foreach ($chit_metals as $cm): ?>
               <option value="<?php echo $cm['id_metal']; ?>"><?php echo $cm['metal']; ?></option>
               <?php endforeach; endif; ?>
             </select>
           </div>
         </div>
      </div>
      <div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload icon</label>

             <div class="col-md-6">
               <input id="edit_pay_gateway_img" name="edit_pay_gateway_img" accept="image/*" type="file" >
               <img src="" class="img-thumbnail" id="edit_paymentgateway_img_preview" style="width:304px;height:100%;" alt="payment_gateway image"> 
                                           
            <p class="help-block"></p>
           </div>
        
             </div> 
        </div>
        <div class="row">
           <div class="form-group">
            <label for="chargeseme_name" class="col-md-3  col-md-offset-1">Description</label>
            <div class="col-md-6">
              
             <textarea name="Description" id="ed_gateway_description"  style="width: 100%;"></textarea>
                                          
           <p class="help-block"></p>
          </div>
       
            </div> 
       </div> 
      <div class="modal-footer">
      	<a href="#" id="update_payment_gateway" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- / modal -->      

