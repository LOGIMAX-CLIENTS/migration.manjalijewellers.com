      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Offer / Banner / Popup
            <small>Add promotion</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Offer / Banner / Popup</a></li>
            <li class="active">Add Offer / Banner / Popup</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Offer / Banner / Popup</h3>
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
				<?php echo form_open_multipart(( $offer['id_offer']!=NULL && $offer['id_offer']>0 ?'settings/offers/update/'.$offer['id_offer']:'settings/offers/save')); ?>
				<?php if(isset($offer['id_offer'])) { ?>
				<div class="row">
			    	<div class="col-md-12">
				    	<div class="form-group pull-right">
				    		<label>Active</label>
				    		<input type="checkbox" id="active" class="switch" data-on-text="YES" data-off-text="NO" name="offer[active]" value="1" <?php if($offer['active']==1) { ?> checked="true" <?php } ?>/>
				    	</div>
			    	</div>
			    </div>
				<?php } ?>
				<div class="row">
					<div class="form-group">
					   <label for="Offer" class="col-md-3 col-md-offset-1 ">Select Options *</label>
					   <div class="col-md-6">
						 <div class="col-md-4">
								<input type="radio" id="select_options" name="offer[type]" value="0"  <?php if($offer['type']==0) { ?> checked="true" <?php } ?>  > Offer
						</div>
						<div class="col-md-4">						
							<input type="radio" id="select_options"  name="offer[type]" value="1"  <?php if($offer['type']==1) { ?> checked="true" <?php } ?>> Banner							
						</div>
						<?php if($offer['id_offer'] == NULL ){ ?>
							<?php if($offer['isPopupExist'] == 0){ ?>
							<div class="col-md-4">						
								<input type="radio" id="select_options"  name="offer[type]" value="2"  <?php if($offer['type']==2) { ?> checked="true" <?php } ?>> Popup							
							</div>		
						<?php }}else if($offer['type'] == 2 && $offer['id_offer'] != NULL){ ?>
							<div class="col-md-4">						
								<input type="radio" id="select_options"  name="offer[type]" value="2"  <?php if($offer['type']==2) { ?> checked="true" <?php } ?>> Popup							
							</div>	
						<?php }?>
						
						<div class="col-md-4">						
							<input type="radio" id="select_options"  name="offer[type]" value="5"  <?php if($offer['type']==5) { ?> checked="true" <?php } ?>> Footer							
						</div>
						<p class="help-block"></p>
						
					   </div>
					</div>
				</div><br/>
			    
				<div class="row">
					<div class="form-group">
					   <label for="Offer" class="col-md-3 col-md-offset-1 ">Product Name</label>
					   <div class="col-md-6">
						 <input class="form-control" id="name" name="offer[name]" type="text" value="<?php echo set_value('offer[name]',$offer['name']); ?>" />	
						  <input class="form-control"  name="offer[id_offer]" type="hidden" value="<?php echo set_value('offer[id_offer]',$offer['id_offer']); ?>" />		   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	 
			   
			    <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Product Description</label>
					   <div class="col-md-6">
						 <textarea class="form-control" id="offer_content" name="offer[offer_content]"><?php echo set_value('offer[offer_content]',$offer['offer_content']); ?></textarea>			   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	
				 
				 	  	<?php if($entry_date[0]['branch_settings']==1 && $entry_date[0]['is_branchwise_cus_reg']==1) { ?> <!-- based on the branch settings to showed branch filter HH-->
				 	<div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Select Branch </label>
					   <div class="col-md-6">
						<select required id="branch_select" class="form-control"></select>
    								<input id="id_branch" name="offer[id_branch]"  type="hidden" value="<?php echo set_value('offer[id_branch]',$offer['id_branch']);?>"  		   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	
								  <?php }?> 
			   
			    <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Upload image</label>
					    
					   <div class="col-md-6">
						   <div for="chargeseme_name">
						    	<b>Note: </b>
						    	 <br/>Popup : &nbsp;Image size should be <b>600*600</b>
						    	 <br/>Banner / Offer : &nbsp;Image size should be  <b>960*525</b>
						    </div>
							 <input id="offer_img" name="offer_img" accept="image/*" type="file" >
							 <img src="<?php echo(isset($offer['offer_img_path'])?$offer['offer_img_path']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="offer_img_preview" style="width:304px;height:100%;" alt="Offer image">    
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	 
				 
				 <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Send Notification</label>
					    
					   <div class="col-md-6">
						   <div for="chargeseme_name">
						    	<b style="color:red;">Note: &nbsp;
						    	 Offer Notification will be sent to user if activated.</b>
						    
						    </div>
							 <input id="offer_noti" name="offer[offer_notification]" value=1 type="checkbox" <?php if($offer['offer_notification']==1) { ?> checked="true" <?php } ?>>
							    
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>
			   
			    
			     <div class="row">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit"  class="btn btn-primary">Save</button> 
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
