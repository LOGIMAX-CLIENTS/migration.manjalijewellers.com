      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Wallet Transaction
            <small>Enter wallet transaction</small>
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
              <h3 class="box-title">Wallet Transaction - <?php echo ( $wallet['id_wallet_transaction']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open((  $wallet['id_wallet_transaction']!=NULL &&  $wallet['id_wallet_transaction']>0 ?'wallet/transaction/update/'.$wallet['id_wallet_transaction']:'wallet/transaction/save'),array('id'=>'wallet_transaction')) ?>
				
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Mobile</label>
                       <div class="col-md-4">
                       	<select id="wallet_account" style="width:100%;"></select>
                       	<input type="hidden" id="id_wallet_account" name="wallet[id_wallet_account]" value="<?php echo set_value('wallet[id_wallet_account]',$wallet['id_wallet_account']); ?>" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>	 	
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Customer</label>
                       <div class="col-md-4">
                       
                       	<input type="text" id="cus_name" class="form-control" readonly="true" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>	 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Transaction Date</label>
                       <div class="col-md-4">
                       	 <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="date_transaction" name="wallet[date_transaction]" value="<?php echo set_value('wallet[date_transaction]',$wallet['date_transaction']); ?>" type="text" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>		 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Available</label>
                       <div class="col-md-4">
                       
                       	<input type="text" id="balance" class="form-control" readonly="true" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>					
				 				
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Type</label>
                       <div class="col-md-4">
                            <label class="radio-inline">
						      <input type="radio" name="wallet[transaction_type]" <?php if($wallet['transaction_type']==0){?> checked="true" <?php } ?> value="0">Issue (+)
						    </label>
						    <label class="radio-inline">
						      <input type="radio" name="wallet[transaction_type]"  <?php if($wallet['transaction_type']==1){?> checked="true" <?php } ?> value="1">Redeem (-)
						    </label>
                       	
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Value<!-- <span id="val_type"></span>--></label>
                       <div class="col-md-4">
                       	<input type="number" id="value" class="form-control input_currency" name="wallet[value]" value="<?php echo set_value('wallet[value]',$wallet['value']); ?>" />
                        <p class="help-block"></p>                       	
                       </div>
                    </div>
				 </div>
						 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Narration</label>
                       <div class="col-md-4">
                       	<textarea class="form-control input_text" name="wallet[description]"><?php echo set_value('wallet[description]',$wallet['description']); ?></textarea>
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