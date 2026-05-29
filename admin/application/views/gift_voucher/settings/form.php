  <!-- Content Wrapper. Contains page content --> 
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Gift Voucher
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Gift Voucher</a></li>
        <li class="active">Settings</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Purchase Voucher Settings</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
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
             <!-- form -->
             <?php echo form_open_multipart(( $gift['id_set_gift_voucher']!=NULL && $gift['id_set_gift_voucher']>0 ?'admin_gift_vocuher/gift_voucher_settings/update/'.$gift['id_set_gift_voucher']:'admin_gift_vocuher/gift_voucher_settings/save'),array('id'=>'')) ?>
			<p class="help-block"></p> 
			<div class="row">
			    
				<div class="col-md-12">
					<div class="row">
					    <?php if($this->session->userdata('branch_settings') == 1) { ?>
						<div class="col-md-2">
							<div class="form-group">
			                  <label for="">Select Branch<span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <select class="form-control" id="branch_select" name="gift[id_branch]" style="width:100%;" required ></select>
			                      <input type="hidden" id="id_branch" value="<?php echo set_value('gift[id_branch]',$gift['id_branch']); ?>">
				              </div>
			                </div>
						</div>
						<?php }?>
						<div class="col-md-3">
							<div class="form-group">
			                  <label for="">Voucher Type <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <select id="gift_type" class="form-control" name="gift[gift_type]">
			                          <option value="1"  <?php if($gift['gift_type']==1){ ?> selected <?php } ?> >Amount to Amount</option>
			                          <option value="2" <?php if($gift['gift_type']==2){ ?> selected <?php } ?> >Amount to Weight</option>
			                          <option value="3"<?php if($gift['gift_type']==3){ ?> selected <?php } ?> >Weight to Amount</option>
			                          <option value="4" <?php if($gift['gift_type']==4){ ?> selected <?php } ?> >Weight to Weight</option>
			                      </select>
				              </div>
			                </div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
			                  <label for="">Calculation Type<span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <select id="calc_type" class="form-control" name="gift[calc_type]">
			                          <option value="1"  <?php if($gift['calc_type']==1){ ?> selected <?php } ?> >Flat</option>
			                          <option value="2"  <?php if($gift['calc_type']==2){ ?> selected <?php } ?> >Each</option>
			                      </select>
				              </div>
			                </div>
						</div>
						
						<div class="col-md-2">
							<div class="form-group">
			                  <label for="">Voucher Issue For <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <select class="form-control" id="issue_for" name="gift[metal]" style="width:100%;" required></select>
			                       <input type="hidden" id="metal"  value="<?php echo set_value('gift[metal]',$gift['metal']); ?>">
				              </div>
			                </div>
						</div>
						
						<div class="col-md-2">
							<div class="form-group">
			                  <label for="">Voucher Redeem For <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <select class="form-control" id="utilized_for" name="gift[utilized_for]" style="width:100%;" required></select>
			                      <input type="hidden" id="id_metal"  value="<?php echo set_value('gift[utilize_for]',$gift['utilize_for']); ?>">
				              </div>
			                </div>
						</div>
						
					</div>
				</div>
				<div class="col-md-12" >
					<div class="row">		
						<div class="col-md-3">
							<div class="form-group">
			                  <label for="">Min Bill Value <span class="error"> *</span></label>
			                  <input type="text" id="min_value" class="form-control" name="gift[sale_value]"  value="<?php echo set_value('gift[sale_value]',$gift['sale_value']); ?>" autocomplete="off" placeholder="Enter The Amount" required> 
			                </div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
			                  <label for="">Credit Value <span class="error"> *</span></label>
			                  <input type="text" id="credit_value" class="form-control" name="gift[credit_value]"  value="<?php echo set_value('gift[credit_value]',$gift['credit_value']); ?>" autocomplete="off" placeholder="Enter The Amount" required> 
			                </div>
						</div>
						
						<div class="col-md-4">
							<div class="form-group">
			                  <label for="">Validity Days <span class="error"> *</span></label>
			                  <input type="text" name="gift[Validity]" class="form-control" autocomplete="off" placeholder="No.of Days"   value="<?php echo set_value('gift[validity_days]',$gift['validity_days']); ?>" required>
			                </div>
						</div>
					</div> 
				</div>
				<div class="col-md-12">
				    <div class="row">
                        <div class="col-md-6" id="issue" >
                            <h4 style="text-align:center;text-transform:uppercase;">Credit Applicable</h4>
                            <div class="table-responsive">
                                <table id="issue_product" class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                        <th><label class="checkbox-inline"><input type="checkbox"  id="issue_select" name="select_all" value="all"/>All</label></th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        </tr>
                                    </thead> 
                                   <!-- <tbody>
                                        <?php if($this->uri->segment(3) == 'edit')
                                        {
                                            foreach($gift['products'] as $product)
                                            {   
                                                if($product['issue']==1)
                                                {
                                                    echo '<tr>
                                                    <td><input type="checkbox" class="pro_id" name="issue_pro[]" checked value='.$product['id_product'].'/>'.$product['id_product'].'</td>
                                                    <td>'.$product['name'].'</td>
                                                    <td>'.$product['product_name'].'</td>';   
                                                }
                                            }
                                        }?>
                                    </tbody>-->
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6" id="utilized" >
                            <h4 style="text-align:center;text-transform:uppercase;">Redeem Applicable</h4>
                            <div class="table-responsive">
                                <table id="utilize_product" class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                        <th><label class="checkbox-inline"><input type="checkbox" id="utilize_select" name="select_all" value="all"/>All</label></th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        </tr>
                                    </thead> 
                                    <!--<tbody>
                                       <?php if($this->uri->segment(3) == 'edit')
                                        {
                                            foreach($gift['products'] as $product)
                                            {   
                                                if($product['utilize']==1)
                                                {
                                                    echo '<tr>
                                                    <td><input type="checkbox" class="pro_id" name="issue_pro[]" checked value='.$product['id_product'].'/>'.$product['id_product'].'</td>
                                                    <td>'.$product['name'].'</td>
                                                    <td>'.$product['product_name'].'</td>';   
                                                }
                                            }
                                        }?>
                                    </tbody>-->
                                </table>
                            </div>
                        </div>
				    </div>
				</div>
			</div>  
			<div class="row">
    	    	<div class="col-md-10">
    				<div class='form-group'>
    	                <label for="">Terms and Conditions</label>
    	               	<textarea  id="description" name="gift[description]"  ><?php echo set_value('gift[note]',(isset($gift['note'])?$gift['note']:"")); ?></textarea>
    	        	</div>
    	    	</div>
		    </div>
			<p class="help-block"></p> 
			<!--End of row-->
		</div>	     
		<div class="box-footer clearfix"> 
			<div class="row">
				<div class="col-xs-offset-5">
					<button type="submit" id="submit" class="btn btn-primary">Save</button> 
					<button type="button" class="btn btn-default btn-flat btn-cancel">Back</button>
				</div> <br/>
			</div>
		</div> 
		</form>
        <div class="overlay" style="display:none">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
  </div>    
 </section>
</div> 
  

 