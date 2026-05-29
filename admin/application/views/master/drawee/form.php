      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Bank Master 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('index.php/settings/drawee/list');?>">Master</a></li>
            <li class="active">Drawee Account</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Drawee Account - <?php echo ( $bank['id_drawee']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open((  $bank['id_drawee']!=NULL &&  $bank['id_drawee']>0 ?'settings/drawee/update/'.$bank['id_drawee']:'settings/drawee/save')) ?>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Account No <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control input_number" id="account_no" onkeypress="return /^[0-9]$/i.test(event.key)" name="bank[account_no]" value="<?php echo set_value('bank[account_no]',$bank['account_no']); ?>" placeholder="eg: 10232001122" required="true">
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 input_text">Account Name</label>
                       <div class="col-md-4">
<input type="text" class="form-control" name="bank[account_name]" id="account_name" onkeypress="return /^[A-Za-z ]$/i.test(event.key)" value="<?php echo set_value('bank[account_name]',$bank['account_name']); ?>" placeholder="eg: Logimax Technologies" />               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>				
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 input_text">Bank</label>
                       <div class="col-md-4">
                         <input type="hidden" class="form-control" name="bank[id_bank]" id="id_bank" value="<?php echo set_value('bank[id_bank]',$bank['id_bank']); ?>" />
                         <select id="bank_dropdown"  class="form-control"></select>
               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	
 				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 input_text">Branch</label>
                       <div class="col-md-4">
<input type="text" class="form-control input_text" name="bank[branch]" id="branch" onkeypress="return /^[A-Za-z ]$/i.test(event.key)" value="<?php echo set_value('bank[branch]',$bank['branch']); ?>" placeholder="eg: Main Branch"/>               
                        <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>						 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 input_text">IFSC Code <span class="error">*</span></label>
                       <div class="col-md-4">
<input type="text" class="form-control" name="bank[ifsc_code]" id="ifsc_code" onkeypress="return /^[A-Za-z0-9 ]$/i.test(event.key)" required="true" value="<?php echo set_value('bank[ifsc_code]',$bank['ifsc_code']); ?>" placeholder="eg: SBI102903"/>               
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