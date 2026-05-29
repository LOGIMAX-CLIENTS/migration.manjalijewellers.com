  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Scheme Classification
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Scheme Classification</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Classification List</h3>    <span id="total_weights" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_cls" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?>  
								<div class="row">
									<div class="col-sm-10 col-sm-offset-1">
									<div id="chit_alert"></div>
									 
									</div>
								</div>				
                  <div class="table-responsive">
                  <table id="sch_clsfy_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Classification Name</th>
                          <th>Logo</th>
                        <th>Action</th>
                      </tr>
                      
                 </thead>
                 
                  
                    
                     <?php  /*
                     	if(isset($weights)) {                     		
                     	 foreach($weights as $weight)
						{
                      ?>
                       <tr>
                         <td><?php echo $weight['id_weight'];?></td>
                       	 <td><?php echo $weight['weight'];?></td>
                       	
                       	
                       	 <td>
                       	 	<a href="#" data-id="<?php echo $weight['id_weight'];?>" class="btn btn-primary btn-edit"  data-toggle="modal" data-target="#confirm-edit"><i class="fa fa-edit"></i> Edit</a>
                       	<!-- 	<?php echo ($customer['username']!=NULL? anchor('#', '<i class="fa fa-user-plus"></i> Edit A/c', array( 'title'=>"Get Login Account",'class'=>'btn btn-warning','data-target'=>'#create-login','data-toggle'=>'modal','data-href'=>'')):anchor('#', 'Create A/c', array( 'title'=>"Get Login Account",'class'=>'btn btn-warning','data-target'=>'#create-login','data-toggle'=>'modal','data-href'=>'')));?> -->
                       	 	<a href="#" class="btn btn-danger btn-del" data-href="<?php echo base_url('index.php/settings/weight/delete/'.$weight['id_weight']) ?>" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-user-times"></i> Delete</a>
                       	 </td>
                       </tr>
                       <?php } }  */ ?>
                    
                 <!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
                  </table>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

<!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Classification</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this classification record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  
<!-- modal -->      
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        		<h4 class="modal-title" id="myModalLabel">Add Classification</h4>
      		</div>
      		
		    <div class="modal-body">
				
				<div class="row">
				 	<div class="form-group">
						<label for="scheme_code" class="col-md-4 col-md-offset-1 ">Classification Name</label>
						<div class="col-md-4">
                <input type="text" class="form-control" id="clsfy" onkeypress="return /^[a-zA-Z0-9 ]$/i.test(event.key)" name="clsfy" value="<?php echo set_value('classification_name', (isset($cls) ? $cls : "")); ?>" placeholder="Enter Classification">
                <p class="help-block"></p>
						</div>
					</div>
				</div>
				<div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload classfication image</label>
              <div class="col-md-4">
                <input id="sch_clsfy_img" name="clsfy" accept="image/*" type="file">
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove_image_btn" data-preview="#classfication_img_preview" data-input="#classfication_img" data-toggle="modal" data-target='#confirm-delete-img'>
                  <i class="fa fa-trash"></i>
                </button>
              </div>
              <img src="<?php echo (isset($category['catimage']) ? $category['catimage'] : base_url() . ('assets/img/no_image.png')); ?>" class="img-thumbnail" id="sch_clsfy_img_preview" style="width:304px;height:100%;" alt="classfication image">
            <p class="help-block"></p>
             </div> 
        </div>
				<div class="row">			
			    	<div class="col-md-10 col-md-offset-1">
						<div class='form-group'>
			                <label for="user_lastname"></label>
			               	<textarea  id="description" name="description" ><?php echo set_value('sch[description]',(isset($sch['description'])?$sch['description']:"")); ?></textarea>
			        	</div>
			    	</div>
			    </div> 
			         
			</div>
			
			<div class="modal-footer">
				<a href="#" id="add_clsfy" class="btn btn-success" data-dismiss="modal" >Add</a>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Classification</h4>
      </div>
      <div class="modal-body">
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Classification</label>
                       <div class="col-md-4">
                       <input type="hidden" id="edit-id" value="" />
                <input type="text" class="form-control" id="ed_clsfy" onkeypress="return /^[a-zA-Z0-9 ]$/i.test(event.key)" name="clsfy" value="<?php echo set_value('classification_name', (isset($cls) ? $cls : "")); ?>" placeholder="Enter Classification">
                <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 <div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload Category image</label>
              <div class="col-md-4">
                <input id="edit_sch_clsfy_img" required="true" name="clsfy" accept="image/*" type="file">
               </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-danger btn-sm remove_image_btn" data-preview="#classfication_img_preview" data-input="#classfication_img" data-toggle="modal" data-target='#edit-confirm-delete-img'>
                  <i class="fa fa-trash"></i>
                </button>
               </div>
               <img src="" class="img-thumbnail" id="edit_sch_clsfy_img_preview" style="width:304px;height:100%;" alt="Offer image"> 
                                           
            <p class="help-block"></p>
           </div>
        
             </div> 
        </div>
				<div class="row">			
			    	<div class="col-md-10 col-md-offset-1">
						<div class='form-group'>
			                <label for="user_lastname"></label>
			               	<textarea  id="description1" name="description1" ><?php echo set_value('sch[description]',(isset($sch['description'])?$sch['description']:"")); ?></textarea>
			        	</div>
			    	</div>
			    </div>
			         
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_clsfy" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
   <!-- Code by Kumaran 29/07/2025 -- Need Remove Option - Scheme Classification -->
    <!-- Started -->
  <div class="modal fade" id="confirm-delete-img" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title" id="deleteModalLabel">Delete Image</h5>
        </div>
        <div class="modal-body">
          <strong>Are you sure you want to delete this image?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger remove-img-btn" id="confirm_remove_image" data-action="ADD">Delete</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

    <div class="modal fade" id="edit-confirm-delete-img" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title" id="editDeleteModalLabel">Delete Image</h5>
        </div>
        <div class="modal-body">
          <strong>Are you sure you want to delete this image?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger remove-img-btn" id="edit_confirm_remove_image" data-action="EDIT">Delete</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Ended -->