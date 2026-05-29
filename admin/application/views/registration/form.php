      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Chit Account
            <small>Customer Chit scheme profile</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Chit</a></li>
            <li class="active">New account (R)</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Account Form</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
            <?php echo form_open('account/registration/save/'.$scheme['id_register']); ?>
              	<div class="col-md-10 col-md-offset-1">
              	
              		<div class="row">
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="" >Customer Name</label>
              					<input  type="hidden" id="customer" name="customer" value="<?php echo set_value('customer',$scheme['id_customer']); ?>"/>
              					<select class="form-control" name="scheme[id_customer]" id="customer_name"></select>
              				</div>
              				
              				<div class="form-group">
              					<div class="box box-default">
								  <div class="box-header with-border">
								    <h3 class="box-title">Customer Details</h3>
								    <div class="box-tools pull-right">
								      <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								    </div><!-- /.box-tools -->
								  </div><!-- /.box-header -->
								  <div class="box-body">
								  
								   	<div class="row">
								   	 <div class="col-sm-4">
								   		<div class="form-group">
								   			<img id="cus_img" src="<?php echo base_url();?>assets/dist/img/no_image_available.png" class="img-thumbnail" alt="Cinque Terre"  height="150"> 
								   		</div>
								   	 </div>	
								   	 <div class="col-sm-8">
								   		<div class="form-group">
								   			<label>Address</label>
								   			<label id="cus_address" ></label>
								   		</div>
								   		<div class="form-group">
								   			<label>Mobile</label>
								   			<label id="cus_mobile"></label>
								   		</div>
								   		<div class="form-group">
								   			<label>Phone</label>
								   			<label id="phone_mobile"></label>
								   		</div>
								   	 </div>	
								   	</div>
								  </div><!-- /.box-body -->
								</div><!-- /.box -->
              				</div>
              			</div>
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="" >Scheme</label>
              	         		<input type="hidden" id="scheme_val" name="scheme_val" value="<?php echo set_value('scheme_val',$scheme['id_scheme']); ?>" />
               					<select class="form-control" id="scheme" name="scheme[id_scheme]" id="scheme"></select>
              				</div>
              				
              				<div class="form-group">
              					<div class="box box-default">
								  <div class="box-header with-border">
								    <h3 class="box-title">Scheme Details</h3>
								    <div class="box-tools pull-right">
								      <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								    </div><!-- /.box-tools -->
								  </div><!-- /.box-header -->
								  <div class="box-body">
									  <div id="sch_content"> <!-- scheme content -->
									  
									   </div> <!--/ scheme content -->
								  </div><!-- /.box-body -->
								</div><!-- /.box -->
              				</div>
              			</div>
              		</div>
              		<legend>Acccount Information</legend>
              		           		<div class="row">
              	
              		<?php if($scheme['scheme_acc_number']!=NULL) {?> 
              			<div class="col-sm-4" >
              				<div class="form-group">
              					<label for="">Account Number</label>
              					<input  type="text" class="form-control" id="scheme_acc_number" name="scheme[scheme_acc_number]" value="<?php echo set_value('scheme[scheme_acc_number]',$scheme['scheme_acc_number']); ?>" readonly="true"/>
              				</div>
              			</div>
              		" <?php }?>	
              			<div class="col-sm-<?php echo ($scheme['scheme_acc_number']==NULL?'6': '4');?>">
              				<div class="form-group">
              					<label for="">A/c Name</label>
              					<input  type="text" class="form-control" id="account_name" name="scheme[account_name]" value="<?php echo set_value('scheme[account_name]',$scheme['account_name']); ?>"/>
              				</div>
              			</div>
              			<div class="col-sm-<?php echo ($scheme['scheme_acc_number']==NULL?'6': '4');?>">
              				<div class="form-group">
              					<label for="">Start Date</label>
              					<div class='input-group date'>
				                    <input type='text' id="start_date" name="scheme[start_date]" class="form-control" value="<?php echo set_value('scheme[start_date',$scheme['start_date']); ?>" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                </div>
              				  </div>
              				</div>
              			
              		 </div>	
              		 <div class="row">
              			  <div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Reference No</label>
              					<div class='form-group' >
				                    <input type='text' class="form-control" id="ref_no" name="scheme[ref_no]" value="<?php echo set_value('scheme[ref_no]',$scheme['ref_no']); ?>" />
				                    
				                </div>
              				  </div>
              				</div>
              			
              		
              			  <div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Installment Paid</label>
              					<div class='form-group' >
				                    <input type='text' class="form-control" id="paid_till" name="scheme[paid_till]" value="<?php echo set_value('scheme[paid_till]',$scheme['paid_till']); ?>" />
				                    
				                    
				                </div>
              				  </div>
              				</div>
              				
              			
              		</div>
              	<legend>Nominee details</legend>
              		<div class="row">
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Nominee Name</label>
              					<input  type="text" class="form-control" id="nominee_name" name="scheme[nominee_name]"  value=""/>
              				</div>
              			</div>
              			
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Relationship</label>
              					<input  type="text" class="form-control" id="nominee_relation" name="scheme[nominee_relation]" value=""/>
              				</div>
              			</div>
              		</div>
       <!--       		<legend>Introducer details</legend>
              		<div class="row">
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Name</label>
              					<input  type="text" class="form-control" id="intro_name" name="scheme[intro_name]"/>
              				</div>
              			</div>
              			
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Mobile</label>
              					<input  type="text" class="form-control" id="intro_mobile" name="scheme[intro_mobile]"/>
              				</div>
              			</div>
              		</div>-->
              		
              		<div class="row">
			    	
			    	<div class="col-md-12">
			    		<div class='form-group'>
			                <label for="user_lastname">Comments</label>
			               <textarea class="form-control" id="remark" name="scheme[remark_open]"><?php echo set_value('scheme[remark_open]',$scheme['remark_open']); ?></textarea>
			        	</div>
			    	</div>
			    	
			    </div>
			     <div class="row">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
					  </div> <br/>
					</div>
				  </div> 
              		
              		
              	</div>
              </form>
            </div><!-- /.box-body -->
            <div class="box-footer">
            
            </div><!-- /.box-footer-->
          </div><!-- /.box -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->