      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Wallet
            <small>Wallet Category</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Wallet Category </li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Wallet Category - <?php echo ($wallet['id_wallet_category']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open(($wallet['id_wallet_category']!=NULL &&  $wallet['id_wallet_category']>0 ?'wallet/category/update/'.$wallet['id_wallet_category']:'wallet/category/save'),array('id'=>'id_wallet_category')) ?>				
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Code</label>  
                       <div class="col-md-4" style="padding-left: 0px;">
                         <div class="col-md-6">
                         		<div class="form-group">                         			
                         			 <div class="input-group">              				
<input type="text" id="wallet_code" onkeypress="return /^[a-zA-Z ]$/i.test(event.key)" value="<?php echo set_value('wallet[code]',$wallet['code']); ?>"  name="wallet[code]"  class="form-control" />                         		   </div>
                         		</div>
                         </div>                         
      
                         <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	 
				 <div class="row">
				 	<div class="form-group">
					<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Category Name</label>                      
                       <div class="col-md-4" style="padding-left: 0px;">                        
                         <div class="col-md-6">
                         	<div class="form-group">                         			
<input type="text" id="wallet_name" onkeypress="return /^[a-zA-Z ]$/i.test(event.key)" name="wallet[name]" value="<?php echo set_value('wallet[name]',$wallet['name']); ?>"   class="form-control" />
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
		                       <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="date_add" name="wallet[date_add]" value="<?php echo set_value('wallet[date_add]',$wallet['date_add']); ?>" type="text" />
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