      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Gift Master 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('index.php/settings/gift/list');?>">Master</a></li>
            <li class="active">Gift</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
     
          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Gift - <?php echo ( $gift['id_gift']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open((  $gift['id_gift']!=NULL &&  $gift['id_gift']>0 ?'settings/bank/update/'.$gift['id_gift']:'settings/gift/save')) ?>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Gift Name <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="gift_name" name="gift[gift_name]" value="<?php echo set_value('gift[gift_name]',$gift['gift_name']); ?>" placeholder="Enter Gift name" required="true" autofocus> 
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