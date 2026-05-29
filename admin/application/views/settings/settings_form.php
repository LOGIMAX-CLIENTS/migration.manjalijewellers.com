      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Settings
            <small>General Settings</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> settings</a></li>
            <li class="active">General settings</li>
            
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">General Settings</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
            <div class="col-md-12">
				  
				  <?php 
				    $attributes = array('autocomplete' => "off",'role'=>'form');
		    		 echo form_open( ($general['id']==NULL?'settings/generalsettings/save':'settings/generalsettings/update/'.$general['id']) , $attributes); 
				  //form validation
				    if(validation_errors())
				    {
						echo '<div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Warning!</h4>    <strong>'.validation_errors().'</strong>              
            </div>';
					} 
					
					?>  
			<p style="height:20px"></p>		
			<legend>Currency</legend>
					<div class="row">
							<div class="col-sm-4">
								<div class="form-group ">
								  <label for="currency_name">Currency Name</label>
								  <input type="text" class="form-control"  name="general[currency_name]" placeholder="Rupees" required="true" value="<?php echo $general['currency_name']; ?>" style="width:70%;" >
								  <p class="help-block"  ></p>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group ">
								  <label for="currency_symbol">Currency Symbol</label>
								  <input type="text" class="form-control"  name="general[currency_symbol]" required="true" value="<?php echo set_value('general[currency_symbol]',$general['currency_symbol']); ?>" style="width:70%;">
								  <p class="help-block"></p>
								</div>		
						    </div>
				   </div>
			 <p style="height:20px"></p>	   
             <legend>Advance Payment</legend>              		 
              		 <div class="row">
              		  		<div class="col-sm-4">
              					<div class="form-group">
									<label class="checkbox-inline">
									<input type="checkbox" id="allow_advance_payment" name="general[adv_payment]" value="1" <?php if($general['adv_payment']==1){?>checked="true" <?php } ?>  />                       
									Allow advance payment 
								    </label>
								</div>
              				</div>	
              			    <div class="col-sm-3">
								<div class="form-group">
									<label for="">Weight Scheme</label></br>
									Grams Allowed
									<select class="form-control" name="general[adv_payment_wgt]" id="Allow_Payment_wt" value="<?php echo $general['adv_payment_wgt']; ?>" >
									
									<?php 
									$a=11;
									$c=8;
									for($i=1;$i<=$a;$i++){

									$b =$i*$c;	
										echo "<option name='".$b."' value='".$b."' ".( $general['adv_payment_wgt'] == $b ? "selected='selected'" : '' )." >".$b.".000</option>"; 
										
										} ?>
									</select>
								 </div>
						   </div>	
              		       <div class="col-sm-3">
								<div class="form-group">
									<label for="">Amount Scheme</label></br>
									No. of months allowed
									   <select class="form-control" name="general[adv_payment_amt]" id="Allow_Payment_amt" value="<?php echo $general['adv_payment_amt']; ?>">
									
									<?php 
									$a=11;
									for($i=1;$i<=$a;$i++){

									echo "<option name='".$i."' value='".$i."' ".( $general['adv_payment_amt'] == $i ? "selected='selected'" : '' )." >".$i."</option>"; 
										
										} ?>
									</select>
								  </div>
              				</div>	
              		</div>	
              	 <p style="height:20px"></p>				
				 <legend>Pending Payment</legend>              		 
						<div class="row">
              		  		<div class="col-sm-4">
              					<div class="form-group">
									  <label class="checkbox-inline">
										<input type="checkbox" id="allow_pending_due" name="general[allow_pending_due]" value="1" <?php if($general['allow_pending_due']==1){?>checked="true" <?php } ?> />                          
										Allow Pending Due
									  </label>
              					</div>
							</div>
              		        <div class="col-sm-3">
								<div class="form-group">
									<label for="">Weight Scheme</label></br>
									Grams Allowed 
									<select class="form-control" name="general[allow_pending_wgt]" id="allow_pending_wgt" value="<?php echo $general['allow_pending_wgt']; ?>">
								
									<?php 
									$a=11;
									$c=8;
									for($i=1;$i<=$a;$i++){
									$b =$i*$c;	
										echo "<option name='".$b."' value='".$b."' ".( $general['allow_pending_wgt'] == $b ? "selected='selected'" : '' )."  >".$b.".000</option>"; 
										} ?>
								 </select>
								</div>
              				</div>	
							<div class="col-sm-3" >
								<div class="form-group">
									<label for="">Amount Scheme</label></br>
									No. of months allowed
									<select class="form-control" name="general[allow_pending_amt]" id="allow_pending_amt" value="<?php echo $general['allow_pending_amt']; ?>">
									
									<?php 
									$a=11;
									for($i=1;$i<=$a;$i++){

									echo "<option name='".$i."' value='".$i."' ".( $general['allow_pending_amt'] == $i ? "selected='selected'" : '' )." >".$i."</option>"; 
										
										} ?>
								 </select>
							   </div>
              				</div>	
              			</div>
						 <p style="height:20px"></p>			
						 <legend>For New Joining</legend>
								 <div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											  <label class="checkbox-inline">
												<input type="checkbox" id="scheme_delete" name="general[scheme_delete]" value="1"<?php if($general['scheme_delete']==1){?>checked="true" <?php } ?> />                          
												Can delete scheme if no payments made
											  </label>
										</div>
									</div>
								</div>
						<p style="height:20px"></p>		
						<legend>Pre-closer</legend>
							<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											  <label class="checkbox-inline">
												<input type="checkbox" id="pre_closer" name="general[pre_closer]" value="1"<?php if($general['pre_closer']==1){?>checked="true" <?php } ?>/>                          
												Allow Pre-closer
											  </label>
										</div>
									</div>	
								<div class="col-sm-3">
										<div class="form-group">
											No. of installments pending
											<select class="form-control" name="general[ins_pending]" id="ins_pending" value="<?php echo $general['ins_pending']; ?>">
											
											<?php 
											$a=11;
											for($i=1;$i<=$a;$i++){
											echo "<option name='".$i."' value='".$i."' ".( $general['ins_pending'] == $i ? "selected='selected'" : '' )." >".$i."</option>"; 
											} ?>
											</select>
										</div>	
								</div>		
								<div class="col-sm-3">
								<p style="height:10px;"></p>
									<label>Avail Benefits</label>
										<input type="checkbox"  id="benefits" class="switch" data-on-text="YES" data-off-text="NO"  name="general[benefits]" value="1"<?php if($general['benefits']==1){?>checked="true" <?php } ?>/>
								</div>
							</div>
						</div>	
						
					 <div class="row">
						   <div class="col-sm-12">
						   <div class="box box-default"><br/>
							  <div class="col-xs-offset-5">
								<button type="submit" class="btn btn-primary">Save</button> 
								<button type="button" class="btn btn-default btn-cancel">Cancel</button>
							  </div> <br/>
							</div>
							</div>
					  </div> 
              		
              		
              	</div>
             </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
            
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
          

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->