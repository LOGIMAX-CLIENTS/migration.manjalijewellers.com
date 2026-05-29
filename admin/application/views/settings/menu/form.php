      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Settings
            <small>Menu</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('index.php/settings/menu/list');?>">Settings</a></li>
            <li class="active">Menu </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Menu Item - <?php echo ( $menu['id_menu']!=NULL?'Edit' :'Add'); ?></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="">
				<?php echo form_open((  $menu['id_menu']!=NULL &&  $menu['id_menu']>0 ?'settings/menu/update/'.$menu['id_menu']:'settings/menu/save')) ?>
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Menu Label</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="menu_name" name="menu[label]" value="<?php echo set_value('menu[label]',$menu['label']); ?>" placeholder="Dashboard" required="true"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				
				<div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Parent</label>
                       <div class="col-md-4">
                         <input type="hidden" name="menu[parent]" id="id_parent" value="<?php echo set_value('menu[parent]',$menu['parent']); ?>"/>
                         <select id="parent" name="menu[parent]"></select>
                       	
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	
				
				 <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Link</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="link" name="menu[link]" value="<?php echo set_value('menu[link]',$menu['link']); ?>" placeholder="home/dashboard" required="true"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	 
				 
			 
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Order</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="link" name="menu[sort]" value="<?php echo set_value('menu[sort]',$menu['sort']); ?>" placeholder="1" required="true"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	 
				 
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Icon</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="link" name="menu[icon]" value="<?php echo set_value('menu[icon]',$menu['icon']); ?>" placeholder="fa fa-dashboard" > 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 
				  <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Active</label>
                       <div class="col-md-4">
				          <input type="checkbox" id="active" class="switch" data-on-text="YES" data-off-text="NO" name="menu[active]" value="1" <?php if($menu['active']==1) { ?> checked="true" <?php } ?>/>
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