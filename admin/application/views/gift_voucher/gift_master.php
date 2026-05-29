  <!-- Content Wrapper. Contains page content --> 
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Gift Voucher
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Gift Voucher</a></li>
        <li class="active">Master</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Voucher Master</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"> 
            <!-- Alert -->
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
             <!-- form -->
             <?php echo form_open_multipart(( $gift['id_gift_voucher']!=NULL && $gift['id_gift_voucher']>0 ?'admin_gift_vocuher/gift_master/update/'.$gift['id_gift_voucher']:'admin_gift_vocuher/gift_master/save'),array('id'=>'')) ?>
			<p class="help-block"></p> 
			<div class="row">
				<div class="col-md-12">
						<div class="col-md-2">
							<div class="form-group">
			                  <label>Voucher Name <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <input type="text" class="form-control" name="gift[name]" placeholder="Enter Voucher Name" value="<?php echo set_value('gift[name]',$gift['name']); ?>" required></select>
				              </div>
			                </div>
						</div>
						
						<div class="col-md-2">
							<div class="form-group">
			                  <label for="">Voucher Type <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <input type="radio" name="gift[voucher_type]"  value="1" checked="" <?php if($gift['voucher_type']==1){ ?> checked <?php } ?>> Amount &nbsp;&nbsp;
			                      <input type="radio" name="gift[voucher_type]"  value="2" <?php if($gift['voucher_type']==2){ ?> checked <?php } ?> > Weight &nbsp;&nbsp;
				              </div>
			                </div>
						</div>
				
						
						<div class="col-md-2">
							<div class="form-group">
			                  <label for="">Gift Utilized For <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <select class="form-control" id="metal_select" name="gift[utilize_for]" style="width:100%;" required></select>
			                      <input type="hidden" id="id_metal"  value="<?php echo set_value('gift[utilize_for]',$gift['utilize_for']); ?>">
				              </div>
			                </div>
						</div>
						<div class="col-md-2">
						    <div class="form-group">
			                  <label for="">Min Sale Value <span class="error"> *</span></label>
			                  <input type="number"  class="form-control" name="gift[sale_value]"  value="<?php echo set_value('gift[sale_value]',$gift['sale_value']); ?>" autocomplete="off" placeholder="Minimum Amount/Weight" required> 
			                </div>
						</div>
						<div class="col-md-2">
						    <div class="form-group">
			                  <label for="">Credit Value <span class="error"> *</span></label>
			                  <input type="number" class="form-control" name="gift[credit_value]"  value="<?php echo set_value('gift[credit_value]',$gift['credit_value']); ?>" autocomplete="off" placeholder="Amount/Weight" required> 
			                </div>
						</div>
						
						<div class="col-md-2">
						    <div class="form-group">
			                  <label for="">Credit Type <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <select class="form-control" id="credit_type" name="gift[credit_type]" style="width:100%;" required>
    			                      <option value="1" <?= ($gift['credit_type'] == 1 ? 'selected' : '' ) ?> >Amount</option>
    			                      <option value="2" <?= ($gift['credit_type'] == 2 ? 'selected' : '' ) ?> >Percentage</option>
			                      </select>
				              </div>
			                </div>
						</div>
						
						<div class="col-md-2">
						    <div class="form-group">
			                  <label for="">Validity Days <span class="error"> *</span></label>
			                  <input type="text" name="gift[Validity]" class="form-control" autocomplete="off" placeholder="No.of Days"   value="<?php echo set_value('gift[validity_days]',$gift['validity_days']); ?>" required>
			                </div>
						</div>
			</div>  
			<p class="help-block"></p> 
			<!--End of row-->
		</div>	 
		<div class="row">
	    	<div class="col-md-10">
				<div class='form-group'>
	                <label for="">Terms and Conditions</label>
	               	<textarea  id="description" name="gift[description]"  ><?php echo set_value('gift[description]',(isset($gift['description'])?$gift['description']:"")); ?></textarea>
	        	</div>
	    	</div>
		</div>
		<div class="box-footer clearfix"> 
			<div class="row">
				<div class="col-xs-offset-5">
					<button type="submit" id="submit" class="btn btn-primary">Save</button> 
					<button type="button" class="btn btn-default btn-flat btn-cancel">Back</button>
				</div> <br/>
			</div>
		</div> 
		</form>
        <div class="overlay" style="display:none">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
  </div>    
 </section>
</div> 
