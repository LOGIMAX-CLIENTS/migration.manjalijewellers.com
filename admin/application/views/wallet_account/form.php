      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Wallet Account
            <small>Create wallet account</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Wallet </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Wallet Account- <?php echo ( $wallet['id_wallet_account']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
			
			<div class="nav-tabs-custom">
			
				<ul class="nav nav-tabs">

				  <li class="active"><a href="#CUS" data-toggle="tab">Customer</a></li>

				  <li><a href="#EMP" data-toggle="tab">Employee</a></li>

				</ul>
				
				 <div class="tab-content">
				 
				  <div class="tab-pane active" id="CUS">
				   
				   <div class="">		  
			  
						<?php echo form_open((  $wallet['id_wallet_account']!=NULL &&  $wallet['id_wallet_account']>0 ?'wallet/account/update/'.$wallet['id_wallet_account']:'wallet/account/save')) ?>
				
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Customer</label>
                       <div class="col-md-4">
                       	<select id="customer_name" style="width:100%;"></select>
                       	<input type="hidden" id="id_customer" name="wallet[id_customer]" value="<?php echo set_value('wallet[id_customer]',$wallet['id_customer']); ?>" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>	 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Issued Date</label>
                       <div class="col-md-4">
                       	 <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="issued_date" name="wallet[issued_date]" value="<?php echo set_value('wallet[issued_date]',$wallet['issued_date']); ?>" type="text" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>		 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Wallet A/c Number</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="wallet_acc_number" name="wallet[wallet_acc_number]" value="<?php echo set_value('wallet[wallet_acc_number]',$wallet['wallet_acc_number']); ?>" readonly="true"/>
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>
						 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Remark</label>
                       <div class="col-md-4">
                       	<textarea class="form-control" id="cusremark" name="wallet[remark]"><?php echo set_value('wallet[remark]',$wallet['remark']); ?></textarea>
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Active</label>
                       <div class="col-md-4">
				          <input type="checkbox" id="active" class="switch" data-on-text="YES" data-off-text="NO" name="wallet[active]" value="1" <?php if($wallet['active']==1) { ?> checked="true" <?php } ?>/>
                      <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>	
                 <?php echo form_close(); ?>			   
              </div>
			  
			  
              </div>
			  
			  
			  <div class="tab-pane" id="EMP">
				   
				   <div class="">		  
			  
						<?php echo form_open((  $wallet['id_wallet_account']!=NULL &&  $wallet['id_wallet_account']>0 ?'wallet/account/update/'.$wallet['id_wallet_account']:'wallet/account/save')) ?>
				
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Employee</label>
                       <div class="col-md-4">
                       	<select id="employee_name" style="width:100%;"></select>
                       	<input type="hidden" id="idemployee" name="wallet[idemployee]" value="<?php echo set_value('wallet[idemployee]',$wallet['idemployee']); ?>" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>	 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Issued Date</label>
                       <div class="col-md-4">
                       	 <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="issued_date" name="wallet[issued_date]" value="<?php echo set_value('wallet[issued_date]',$wallet['issued_date']); ?>" type="text" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>		 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Wallet A/c Number</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="wallet_acc_number" name="wallet[wallet_acc_number]" value="<?php echo set_value('wallet[wallet_acc_number]',$wallet['wallet_acc_number']); ?>" readonly="true"/>
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>
						 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Remark</label>
                       <div class="col-md-4">
                       	<textarea class="form-control" id="empremark" name="wallet[remark]"><?php echo set_value('wallet[remark]',$wallet['remark']); ?></textarea>
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Active</label>
                       <div class="col-md-4">
				          <input type="checkbox" id="empactive" class="switch" data-on-text="YES" data-off-text="NO" name="wallet[active]" value="1" <?php if($wallet['active']==1) { ?> checked="true" <?php } ?>/>
                      <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>	
                 <?php echo form_close(); ?>			   
              </div>
			  
			  
              </div>
			  
			  
			  
			  
            </div>
			
			
            </div>
            </div>
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->