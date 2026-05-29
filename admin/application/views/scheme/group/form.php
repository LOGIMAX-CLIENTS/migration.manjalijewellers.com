      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Group
            <!--<small>Wallet Category</small>-->
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Scheme</a></li>
            <li class="active">Scheme Group </li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Scheme Group - <?php echo ($group['id_scheme_group']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open(($group['id_scheme_group']!=NULL &&  $group['id_scheme_group']>0 ?'account/scheme_group/update/'.$group['id_scheme_group']:'account/scheme_group/save'),array('id'=>'id_scheme_group')) ?>				
				  <div class="row">
				 	<div class="form-group">
					<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Scheme Code</label>                      
                       <div class="col-md-4" style="padding-left: 0px;">                        
                         <div class="col-md-6">
                         	<div class="form-group">                         			
                         			<select id="scheme_select" class="form-control"  required="true" style="width:100%;"></select>
									<input id="id_scheme" name="group[id_scheme]"  type="hidden" value="<?php echo set_value('group[id_scheme]',$group['id_scheme']); ?>"/>
                         	</div>
                         </div>
      
                         <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
					 <?php if($this->session->userdata('branch_settings')==1){?> 
				 <div class="row">
				 	<div class="form-group">
					<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Branch</label>                      
                       <div class="col-md-4" style="padding-left: 0px;">                        
                         <div class="col-md-6">
                         	<div class="form-group">                         			
                         			<select id="branch_select" class="form-control"  required="true" style="width:100%;"></select>
									<input id="id_branch" name="group[id_branch]"  type="hidden" value="<?php echo set_value('group[id_branch]',$group['id_branch']); ?>"/>
                         	</div>
                         </div>
                         <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
                 <?php } else {?>
		       	<input type="hidden" name="group[id_branch]"  value="<?php echo$this->session->userdata('id_branch'); ?>" >
		       <?php }?>
                 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Group Code</label>  
                       <div class="col-md-4" style="padding-left: 0px;">
                         <div class="col-md-6">
                         		<div class="form-group">                         			
                         			 <div class="input-group">              				
                         			<input type="text" id="group_code" value="<?php echo set_value('group[group_code]',$group['group_code']); ?>"  name="group[group_code]"  class="form-control" required="true"/>
                         		   </div>
                         		</div>
                         </div>                         
      
                         <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Start Date</label>
                       <div class="col-md-4" style="padding-left: 0px;">
	                       <div class="col-md-6">
		                       <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="date_add" name="group[start_date]" value="<?php echo set_value('group[start_date]',$group['start_date']); ?>" type="text" />
		                 	    <p class="help-block"></p>
	                       	
                     	  </div>
                     	  <div class="col-md-6">
                     	  </div>
                       </div>
                    </div>
				 </div>
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">End Date</label>
                       <div class="col-md-4" style="padding-left: 0px;">
	                       <div class="col-md-6">
		                       <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="date_update" name="group[end_date]" value="<?php echo set_value('group[end_date]',$group['end_date']); ?>" type="text"  />
		                 	    <p class="help-block"></p>
	                       	
                     	  </div>
                     	  </div>
				
				<br/>      
				 <div class="row col-xs-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary" id="group" required="true" >Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div>      
				        	
               </form>              	              	
              </div>
           <!-- /.box-body -->
             <div class="overlay" style="display:none" disabled='true'>
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            <div class="box-footer">
              
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
         

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->