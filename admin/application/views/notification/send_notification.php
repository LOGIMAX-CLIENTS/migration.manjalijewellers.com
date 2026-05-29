     <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Send
            <small>Send Notification</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-paper-plane"></i> Send Notification</a></li>          
			
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
         
		<div class="box" >
            <div class="box-header with-border" >
			 <h3 class="box-title">Send Notification</h3>
            </div>
            <div class="box-body" id="sendnotification">
                <?php	$entry_date=$this->admin_settings_model->settingsDB('get','','');?>
                <!-- put your content here -->
				<div class="col-md-12">
					 <div class="">

	                                       <?php 

											    echo form_open_multipart( 'send/notification');																					  

										   ?>  
							    
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
					<div class="form-group">
						<div class="row">
						
							<label class="control-label col-sm-2 col-xs-offset-2">Select *</label>
								<div class="col-sm-4">	
									 <select  name="notidata[notification_service]" class="form-control" id="new_arrivals"  required/>
									 
									 <option class="form-control" value="1">General</option>
									 <!--<option class="form-control" value="2">Offers</option>
									 <option class="form-control" value="3">New Arrivals</option> -->
									 
															 </select>
														 </div>
													<p class="help-block"></p>                       	
											   </div>
										  </div>								
								
									<div class="row">									
										<label class="control-label col-sm-2 col-xs-offset-2">Notification Title * </label>
										<div class="col-sm-4">
											<input class="form-control" type="text"  required placeholder="Heading content" name="notidata[header]"   <?php if($notidata['header']=!""){?>checked="true" <?php } ?>/></br>									
										</div>						
									</div>
									<div class="row">									
										<label class="control-label col-sm-2 col-xs-offset-2">Notification Subtitle * </label>
										<div class="col-sm-4">
											<input class="form-control" type="text" required  placeholder="Sub heading content" name="notidata[footer]"   <?php if($notidata['footer']=!""){?>checked="true" <?php } ?>  /></br>
											</div>							
										</div>
									<?php if($entry_date[0]['branch_settings']==1 && $entry_date[0]['is_branchwise_cus_reg']==1) { ?> <!-- based on the branch settings to showed branch filter HH-->
									<div class="row">									
										<label class="control-label col-sm-2 col-xs-offset-2">Select Branch * </label>
										<div class="col-sm-4">
											<select required id="branch_select" class="form-control"></select>
    								<input id="id_branch" name="notidata[id_branch]"  type="hidden" value="<?php echo set_value('notidata[id_branch]',$notidata['id_branch']);?>"
    									<p class="help-block"></p>
											</div>							
										</div>
									 <?php }?> 
									<div class="row">								
										<label class="control-label col-sm-2 col-xs-offset-2">Message *</label>
										<div class="col-sm-4 ">
										<textarea class="form-control" required  placeholder="Maximum 160 characters allowed"  name="notidata[message]" cols="35" rows="5" tabindex="4" <?php if($notidata['message']=!""){?>checked="true" <?php } ?> ></textarea>
											</br>
										</div>					
									</div>
								   <div class="row">
									<!--	<div class="form-group">-->
										   <label for="chargeseme_name" class="control-label col-sm-2 col-xs-offset-2">Upload image</label>
										   <div class="col-md-6">
												 <input id="notification_img" name="notification_img" accept="image/*" type="file" >
												 <img src="<?php echo base_url().('assets/img/no_image.png');?>" class="img-thumbnail" id="img_preview" style="width:304px;height:100%;" alt="Image">    
											<p class="help-block"></p>
										   </div>
										<!--</div>-->
								    </div>	
								 </div>	 
								
							 <div class="row">
							   <div class="box box-default"><br/>
								  <div class="col-xs-offset-5">
									<button   type="submit" class="btn btn-primary"> Send</button>
									<button   type="submit" class="btn btn-default btn-cancel"> Cancel</button>
								  </div> <br/>
							   </div>
							</div>
					
					 <?php echo form_close();  ?>
				</div> 				
									
				</div>
            </div><!-- .box-body End -->
          </div>
  </section><!-- /.content -->
 </div><!-- /.content-wrapper -->


