<style type="text/css">
table, th, td 
{
border: 1px solid #885555;
text-align: center;
}
</style>
  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Task Management
            <small>Task Management</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Task Management</a></li>
            <li class="active">Task Management</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
                     <div class="box-header with-border">
                        <h3 class="box-title">Task List</h3>    <span id="total_count" class="badge bg-green"></span>      
                        <a class="btn btn-success pull-right" id="add_task" href="#" data-toggle="modal" data-target="#confirm-add" data-backdrop="static" ><i class="fa fa-user-plus"></i> Add</a> 
                    </div><!-- /.box-header -->
                        <div class="box-body">
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
               <div class="table-responsive">
                          <table id="task_list" class="table table-bordered table-striped text-center">
                            <thead>
                              <tr>
                                <th width="1%;">ID</th>
                                <th width="5%;">Assigned Date</th>
                                <th width="15%;">Task Name</th>
                                <th width="5%;">Employee</th>
                                <th width="5%;">Status</th>
                                <th width="2%;">Completed Date</th>
                                <th width="5%;">Creted By</th>
                                <th width="10%;">Action</th>
                              </tr>
                            </thead> 
                          </table>
                        </div>
                        </div><!-- /.box-body -->
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
         </div>
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

 <!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cancel Task</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to Cancel This Task?</strong>
                       <p></p>
                    <div class="row">
                      <div class="col-md-12">
                        <label>Reason</label>
                        <input type="hidden" id="task_id" name="">
                        <textarea class="form-control" id="cancel_remark" placeholder="Cancel Reason"  rows="5" cols="10"> </textarea>
                      </div>
                    </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" type="button" id="task_cancel" >Cancel</button>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  



<!-- modal -->      
<div class="modal fade" id="confirm-add" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
>
  <div class="modal-dialog" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Create Task</h4>
      </div>
      <div class="modal-body">
        <form id="task_form">
        <div class="row">
            <div class="col-md-offset-2 col-md-10" id='error_msg'></div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select Profile</label>
                    <div class="col-md-4">
                        <select id="select_profile" class="form-group" name="task[id_profile]" style="width:100%;"></select>
                        <p class="help-block"></p>
                    </div>
            </div>
        </div> 
        <div class="row">
            <div class="form-group">
                <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select Employee<span class="error">*</span></label>
                    <div class="col-md-4">
                        <select id="select_emp" class="form-group" name="task[id_employee]"  style="width:100%;" multiple></select >
                        <p class="help-block"></p>
                    </div>
            </div>
        </div> 
        <div class="row">
            <div class="form-group">
                <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Task Name <span class="error">*</span> </label>
                    <div class="col-md-6">
                        <textarea type="text" class="form-control" id="task_name" name="task[task_name]" placeholder="Enter The Task Name"></textarea>  
                        <p class="help-block"></p>
                    </div>
            </div>
        </div>
       
        <!-- <div class="row" id="task_checklist_attchement" >
            <div class="form-group">
                <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Task Attachement</label>
                    <div class="col-md-6">
                        <input id="task_list_attachement" type="file" name="file[]" accept="" multiple>
                        <p class="help-block"></p>
                </div>
            </div>
        </div> -->
        
        <div class="row" id="pre_checklist_attchement" >
          <div class="form-group">
            <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Attachement</label>
            <div class="col-md-6">
              <input id="prechecklist_attachement" type="file" name="file[]" accept="" multiple>
              <p class="help-block"></p>
            </div>
          </div>
        </div>
        
        <!--  <div class="row" id="post_checklist_attchement" >
          <div class="form-group">
            <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Post Attachement</label>
            <div class="col-md-6">
           <input id="postchecklist_attachement" type="file" name="file[]" accept="" multiple>
              <p class="help-block"></p>
            </div>
          </div>
        </div> -->
      </form>
      </div>

      <div class="modal-footer">
        <button id="create_task" class="btn btn-success" >Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div   class="modal fade" id="task_edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Task</h4>
      </div>
      <div class="modal-body">
          <form id="ed_task_form">
              <div class="row" >
                <div class="col-md-offset-1 col-md-10" id='ed_error_msg'></div>
              </div>
              <div class="row">
                <div class="form-group">
                  <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select Profile</label>
                  <div class="col-md-4">
                    <select id="ed_select_profile" class="form-group" name="task[id_profile]" style="width:100%;"></select>
                    <p class="help-block"></p>
                  </div>
                </div>
             </div> 
             <div class="row">
                <div class="form-group">
                  <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select Employee<span class="error">*</span></label>
                  <div class="col-md-4">
                    <select id="ed_select_emp" class="form-group" name="task[id_employee]"  style="width:100%;"></select >
                    <p class="help-block"></p>
                  </div>
                </div>
             </div> 
             <div class="row">
              <div class="form-group">
                <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Task Name <span class="error">*</span> </label>
                <div class="col-md-6">
                  <textarea type="text" class="form-control" id="ed_task_name" name="task[task_name]" placeholder="Enter The Task Name"></textarea>  
                  <p class="help-block"></p>
                </div>
              </div>
            </div>  
        </form> 
      </div>
      <div class="modal-footer">
        <a href="#" id="update_task" class="btn btn-success">Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

<div class="modal fade" id="confirm-view" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
>
  <div class="modal-dialog" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Task Details</h4>
      </div>
      <div class="modal-body">
         <div class="row">
          <div class="col-md-offset-2 col-md-10" id='error1_msg'></div>
        </div>
          <input type="hidden" id="status_id" value="" />
           <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Task Name <span class="error">*</span> </label>
              <div class="col-md-6">
                <textarea type="text" class="form-control" id="task" name="task[task_name]" placeholder="Enter The Task Name" readonly=""></textarea>  
                <p class="help-block"></p>
              </div>
            </div>
          </div>
           <p class="help-block"></p>
          <div class="row" id="task_attchhements" style="display: none;"> 
             <div class="form-group">
                <label class="col-md-4 col-md-offset-1 ">Task Attachement</label>
                  <div class="col-md-6">
                    <div class="table-responsive">
                      <table id="task_check_list" class=" class="table table-bordered table-striped text-center"">
                        <thead>
                            <tr>
                              <th width="10%">S.No</th>
                              <th width="10%">File Name</th>
                              <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
             </div>
          </div><p class="help-block"></p> <p class="help-block"></p>

          <div class="row" id="pre_task_attachement" style="display: none;"> 
             <div class="form-group">
                <label class="col-md-4 col-md-offset-1 ">Author Attachement</label>
                  <div class="col-md-6">
                    <div class="table-responsive">
                      <table id="pre_check_list" class=" class="table table-bordered table-striped text-center"">
                        <thead>
                            <tr>
                              <th width="10%">S.No</th>
                              <th width="10%">File Name</th>
                              <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
             </div>
          </div><p class="help-block"></p> <p class="help-block"></p>

           <div class="row" id="post_task_attachement" style="display: none;"> 
             <div class="form-group">
                <label class="col-md-4 col-md-offset-1 ">User Attachement</label>
                  <div class="col-md-6">
                    <div class="table-responsive">
                      <table id="post_check_list" class=" class="table table-bordered table-striped text-center"">
                        <thead>
                            <tr>
                              <th width="10%">S.No</th>
                              <th width="10%">File Name</th>
                              <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
             </div>
          </div><p class="help-block"></p>
            
          <div class="row" id="user_attachement" >
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">User Attachement</label>
              <div class="col-md-6">
                 <input id="attach_documents" type="file" name="file[]" accept="" multiple>
                <p class="help-block"></p>
              </div>
            </div>
          </div>
        <div class="row">
          <div class="form-group">
            <label for="scheme_code" class="col-md-4 col-md-offset-1">Remarks If Any</label>
              <div class="col-md-6">
                <textarea type="text" class="form-control" id="remarks" name="task[remarks]" placeholder="Enter The Remarks"></textarea>  
              <p class="help-block"></p>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="update_status" class="btn btn-success" >Completed</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>