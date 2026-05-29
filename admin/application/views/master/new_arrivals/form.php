      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            New Arrivals
            <small>Add New Arrivals</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Add New Arrivals</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">New Arrivals</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
                <?php	$entry_date=$this->admin_settings_model->settingsDB('get','','');?>
             <!-- form container -->
              <div>
	             <!-- form -->
				<?php echo form_open_multipart(( $new_arrivals['id_new_arrivals']!=NULL && $new_arrivals['id_new_arrivals']>0 ?'settings/new_arrivals/update/'.$new_arrivals['id_new_arrivals']:'settings/new_arrivals/save'),array('id'=>'new_arrival_form')); ?>
				<?php if(isset($new_arrivals['id_new_arrivals'])) { ?>
				<div class="row">
			    	<div class="col-md-12">
				    	<div class="form-group pull-right">
				    		<label>Active</label>
				    		<input type="checkbox" id="active" class="switch" data-on-text="YES" data-off-text="NO" name="new_arrivals[active]" value="1" <?php if($new_arrivals['active']==1) { ?> checked="true" <?php } ?>/>
				    	</div>
			    	</div>
			    </div>
				<?php } ?>
			    
				<div class="row">
					<div class="form-group">
					   <label for="new_arrivals" class="col-md-3 col-md-offset-1 ">Product Name</label>
					   <div class="col-md-6">
						 <input class="form-control" id="name" name="new_arrivals[name]" type="text" required="true" placeholder="Name" autofocus="true" value="<?php echo set_value('new_arrivals[name]',$new_arrivals['name']); ?>" required/>	
						  <input class="form-control"  name="new_arrivals[id_new_arrivals]" type="hidden" value="<?php echo set_value('new_arrivals[id_new_arrivals]',$new_arrivals['id_new_arrivals']); ?>" />		   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	 
				 
			  <div class="row">
					<div class="form-group">
					   <label for="new_arrivals" class="col-md-3 col-md-offset-1 ">Product code</label>
					   <div class="col-md-6">
						 <input class="form-control" id="product_code" name="new_arrivals[product_code]" type="text" required="true" placeholder="Product Code" autofocus="true" value="<?php echo set_value('new_arrivals[product_code]',$new_arrivals['product_code']); ?>" required/>	
						 	<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	
			    <div class="row">
					<div class="form-group">
					   <label for="new_arrivals" class="col-md-3 col-md-offset-1 ">Price</label>
					   
					   <div class="col-md-3">
						   <div class="input-group ">
								<span class="input-group-addon">
									<span class="fa fa-inr"></span>
								</span>
								 <input class="form-control" id="price" name="new_arrivals[price]" type="number" required="true" value="<?php echo set_value('new_arrivals[price]',$new_arrivals['price']); ?>" required/>	
						   </div>
						   <p class="help-block"></p>
					   </div>
					   <div class="col-md-3">
						  <div class="col-md-12">
									<input type="radio" id="show_rate" name="new_arrivals[show_rate]" value="1"   <?php if($new_arrivals['show_rate']==1) { ?> checked="true" <?php } ?>> Show price  &nbsp;&nbsp;&nbsp;		
								<input type="radio"  id="show_rate"  name="new_arrivals[show_rate]" value="0"  <?php if($new_arrivals['show_rate']==0) { ?> checked="true" <?php } ?>> Hide price								
							<p class="help-block"></p>
						   </div>
						   <p class="help-block"></p>
					   </div>
					   
					   
					   
					   
					   
					    <div class="col-md-3">
						  <div class="col-md-12">
									<input type="checkbox" id="new_arrival_type" name="new_arrivals[new_type]" value="1"   <?php if($new_arrivals['new_type']==1) { ?> checked="true" <?php } ?>> New arrivals  &nbsp;&nbsp;&nbsp;		
								<input type="checkbox"  id="gift_artical"  name="new_arrivals[gift_type]" value="1"  <?php if($new_arrivals['gift_type']==1) { ?> checked="true" <?php } ?>> Gift artical
							<p class="help-block"></p>
						   </div>
						   <p class="help-block"></p>
					   </div>
					   
					   
					</div>
				 </div>	
				
				  
			   	
				
				 <div class="row">
					<div class="form-group">
  						<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Product Description</label>
  						<div class="col-md-6">
							<textarea class="form-control" id="product_description" name="new_arrivals[product_description]" required="true" placeholder="Eg: Set in 18 Kt Yellow Gold (3.96 gms) with Diamonds (0.25 Ct, IJ-SI) Certified by SGL" ><?php echo set_value('new_arrivals[product_description]',$new_arrivals['product_description']); ?></textarea>	  
							<p class="help-block"></p>

  						</div>
					</div>
				</div> 
				
				
			    <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">General Description</label>
					   <div class="col-md-6">
						 <textarea class="form-control" id="new_arrivals_content" name="new_arrivals[new_arrivals_content]" required="true" placeholder="Eg: Earrings in a traditional design that will appeal to those with a taste for the classic."><?php echo set_value('new_arrivals[new_arrivals_content]',$new_arrivals['new_arrivals_content']); ?></textarea>			   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	
				 <?php if($entry_date[0]['branch_settings']==1 && $entry_date[0]['is_branchwise_cus_reg']==1) { ?> <!-- based on the branch settings to showed branch filter HH-->
				  <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Select Branch</label>
					   <div class="col-md-6">
						 <select required id="branch_select" class="form-control"></select>
    								<input id="id_branch" name="new_arrivals[id_branch]"  type="hidden" value="<?php echo set_value('new_arrivals[id_branch]',$new_arrivals['id_branch']);?>"			   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	 
				  <?php }?> 
				 
				 <br>
				  <div class="row">
					<div class="form-group">			            
						<label class="col-md-3 col-md-offset-1 ">  Expired Date *</label>
						 <div class="col-md-3">
							   <div class="input-group date" data-provide="datepicker">
									<input type='text' class="form-control " id="expiry_date" name="new_arrivals[expiry_date]" placeholder=" Select Expired Date "    value="<?php echo set_value('new_arrivals[expiry_date]',$new_arrivals['expiry_date']); ?>"  data-inputmask="'alias': 'yyyy/mm/dd'" data-mask id='start_date'  data-date-format="yyyy-mm-dd" required="true"/>
										<span class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</span>
									<p class="help-block"></p>
								</div>
							</div>
							<p class="help-block"></p>
						</div>
					</div>
			   <br>
			    <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Upload image</label>
					   <div class="col-md-6">
							 <input id="new_arrivals_img" name="new_arrivals_img" accept="image/*" type="file" required="true"/>
							 <img src="<?php echo(isset($new_arrivals['new_arrivals_img_path'])?$new_arrivals['new_arrivals_img_path']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="new_arrivals_img_preview" style="width:304px;height:100%;" alt="new_arrivals image" required="true"/>  
						<p class="help-block"></p>
						<div>
						 <label for="chargeseme_name">Note:&nbsp;*Image size should be less than 960*525</label>
						<label for="chargeseme_name">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Image size should not be greater than 1 MB</label>
						</div>
					   </div>
					</div>
				 </div>	 
				 
				 <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Send Notification</label>
					    
					   <div class="col-md-6">
						   <div for="chargeseme_name">
						    	<b style="color:red;">Note: &nbsp;
						    	 New Arrivals Notification will be sent to user if activated.</b>
						    
						    </div>
							 <input id="new_arrivals" name="new_arrivals[send_notification]" value=1 type="checkbox" <?php if($new_arrivals['send_notification']==1) { ?> checked="true" <?php } ?>>
							    
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>
			   
			    
			     <div class="row">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" id="new_arrival_submit"  class="btn btn-primary">Save</button>
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div> 
			    
	            </div>  
	          <?php echo form_close();?>
	             <!-- /form -->
	          </div>
             <!-- /form container -->
            </div><!-- /.box-body -->
             <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
