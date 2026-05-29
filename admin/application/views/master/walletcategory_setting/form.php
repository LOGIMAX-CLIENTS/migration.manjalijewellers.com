      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Wallet
            <small>Wallet Master</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Wallet Category Settings</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Wallet Category Settings </h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open(($wallet['id_wcat_settings']!=NULL &&  $wallet['id_wcat_settings']>0 ?'wallet/category/setting/update/'.$wallet['id_wallet']:'wallet/category/setting/save'),array('id'=>'wallet_master')) ?>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Category </label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="wallet_name" name="wallet[name]" value="<?php echo set_value('wallet[name]',$wallet['name']); ?>" placeholder="Wallet plan name" required="true"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Type</label>
                       <div class="col-md-4">
                            <label class="radio-inline">
						      <input type="radio" id="opt_currency" name="wallet[type]" <?php if($wallet['type']==0){?> checked="true" <?php } ?> value="0">Rupee
						    </label>
						    <label class="radio-inline">
						      <input type="radio" name="wallet[type]"  <?php if($wallet['type']==1){?> checked="true" <?php } ?> value="1">Point
						    </label>
                       	
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	
				
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 "></label>
                       <div class="col-md-4" style="padding-left: 0px;">
                         <div class="col-md-6">
                         		<div class="form-group">
                         			<label>Rupee</label>
                         			 <div class="input-group ">
              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('currency_symbol')?></span>
                         			<input type="number" id="currency" value="<?php echo set_value('wallet[currency]',$wallet['currency']); ?>"  name="wallet[currency]"  class="form-control input_currency" />
                         		</div>
                         		</div>
                         </div>
                         <div class="col-md-6">
                         	<div class="form-group">
                         			<label>Points </label>
                         			<input type="number" id="value" name="wallet[value]" value="<?php echo set_value('wallet[value]',$wallet['value']); ?>"   class="form-control input_number" />
                         	</div>
                         </div>
      
                         <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	 
				 
			 
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Effective Date</label>
                       <div class="col-md-4" style="padding-left: 0px;">
	                       <div class="col-md-6">
		                       <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="effective_date" name="wallet[effective_date]" value="<?php echo set_value('wallet[effective_date]',$wallet['effective_date']); ?>" type="text" />
		                 	    <p class="help-block"></p>
	                       	
                     	  </div>
                     	  <div class="col-md-6">
                     	  </div>
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
				        	
               </form>              	              	
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->