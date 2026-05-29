      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Dashboard
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Dashboard</a></li>
            
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Charges Master</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open(( $charges['id_charges']!=NULL && $charges['id_charges']>0 ?'settings/payment_charges/update/'.$charges['id_charges']:'settings/payment_charges/save')) ?>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Payment Mode</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="name" name="charges[payment_mode]" value="<?php echo set_value('charges[payment_mode]',$charges['payment_mode']); ?>" placeholder="Payment Mode" required="true"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Short Code</label>
                       <div class="col-md-2">
                       	 <input type="text" class="form-control" id="code" name="charges[code]" value="<?php echo set_value('charges[code]',$charges['code']); ?>" placeholder="CC" required="true"> 
                         <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_code" class="col-md-2 col-md-offset-1 ">Service Tax (%) </label>
                       <div class="col-md-2">
                       	 <input type="number" step="any" class="form-control" id="service_tax" name="charges[service_tax]" value="<?php echo set_value('charges[service_tax]',$charges['service_tax']); ?>" step="any" placeholder="Percentage"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_code" class="col-md-2 col-md-offset-1 ">Charges </label>
            <div class="col-xs-8">
              <div class="box">
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover table-bordered" id="charges_range">
				  <thead>
                    <tr>
                      <th>Lower Limit</th>
                      <th>Upper Limit</th>
                      <th>Charge Type</th>
                      <th>Charges</th>
                      <th>Action</th>
                    </tr>
				 </thead>
				 <tbody>
				 <?php 
				 if($charges['type'] == 'Add') { ?>
					<tr>
                      <td><input type="number" name="charges[range][lower_limit][]" step="any" value="<?php echo set_value('charges[lower_limit]',$charges['lower_limit']); ?>"/></td>
                      <td><input type="number" name="charges[range][upper_limit][]" step="any" value="<?php echo set_value('charges[upper_limit]',$charges['upper_limit']); ?>"/></td>
                      <td><select name="charges[range][charge_type][]" id="charge_type"><option value="0"> % </option><option value="1">Value</option></select></td>
                      <td><input type="number" step="any" name="charges[range][charges_value][]" value="<?php echo set_value('charges[charges_value]',$charges['charges_value']); ?>"/></td>
                      <td><button type="submit" class="btn btn-success btn-sm" onclick="add_charges_row(this,event)">Add</button></td>
                    </tr>
				<?php } else if($charges['type'] == 'Edit') {
					   $range = $this->admin_settings_model->get_charges_range($charges['id_charges']);
					   if(count($range) > 0)
					   {
						   for($i=0;$i<count($range);$i++) { ?>
						   <tr>
							  <td><input type="number" name="charges[range][lower_limit][]" step="any" value="<?php echo $range[$i]['lower_limit']; ?>"/></td>
							  <td><input type="number" name="charges[range][upper_limit][]" step="any" value="<?php echo $range[$i]['upper_limit']; ?>"/></td>
							  <td><select  name="charges[range][charge_type][]" id="charge_type"><option <?php if($range[$i]['charge_type'] == 0){ ?> selected="selected" <?php } ?> value="0"> % </option><option <?php if($range[$i]['charge_type'] == 1){ ?> selected="selected" <?php } ?> value="1">Value</option></select></td>
							  <td><input type="number" step="any"  name="charges[range][charges_value][]" id="charges_value" value="<?php echo $range[$i]['charges_value']; ?>"/></td>
							  <td><?php if($i == (count($range)-1)) { ?> <button type="submit" class="btn btn-success btn-sm" onclick="add_charges_row(this,event)">Add</button>  <button type="submit" class="btn btn-danger  btn-sm" onclick="del_charges_row(this,event)">Delete</button> <?php } ?></td>
						  </tr>
					   <?php }?>
						<tr>
				<?php } else { ?> 
					 <tr>
                      <td><input type="number" name="charges[range][lower_limit][]" step="any" value=""/></td>
                      <td><input type="number" name="charges[range][upper_limit][]" step="any" value=""/></td>
                      <td><select name="charges[range][charge_type][]" id="charge_type"><option value="0"> % </option><option value="1">Value</option></select></td>
                      <td><input type="number" step="any" name="charges[range][charges_value][]" value=""/></td>
                      <td><button type="submit" class="btn btn-success btn-sm" onclick="add_charges_row(this,event)">Add</button></td>
                    </tr>
					<?php } } ?>
                  </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
  
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="metal" class="col-md-2 col-md-offset-1 ">Active</label>
                       <div class="col-md-4">
                         <input type="checkbox" id="active" name="charges[active]" value="1" <?php if($charges['active']==1){?>checked="true" <?php } ?> />
                       	
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