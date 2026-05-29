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
           Notice Board
            <small>Notice Board</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Notice Board</a></li>
            <li class="active">Notice Board</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
                 <div class="box-header with-border">
                    <h3 class="box-title">Notice Board List</h3>    <span id="total_count" class="badge bg-green"></span>      
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
                          <table id="notice_board_list" class="table table-bordered table-striped text-center">
                            <thead>
                              <tr>
                                <th >Id</th>
                                <th >Date</th>
                                <th width="20%">Content</th>
                                <th >Valid Till</th>
                                <th >Profile</th>
                                <th >Created By</th>
                                <th >Status</th>
                                <th >Action</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Information</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You Want To Delete This Information ?</strong>
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
<div class="modal fade" id="confirm-add" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
>
  <div class="modal-dialog" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Information</h4>
      </div>
      <div class="modal-body">
        <form id="task_form">
         <div class="row">
          <div class="col-md-offset-2 col-md-10" id='error_msg'></div>
        </div>
        <?php if($this->session->userdata('branch_settings')==1){?>
         <div class="row">
            <div class="form-group">
              <label for="" class="col-md-4 col-md-offset-1 ">Select Branch</label>
              <div class="col-md-4">
                <select id="branch_select" class="form-group" style="width:100%;" ></select>
                <p class="help-block"></p>
              </div>
            </div>
         </div>
         <?php }?>
         <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1">Select Profile<span class="error">*</span></label>
              <div class="col-md-4">
                <select id="profile_select" class="form-group" style="width:100%;" ></select>
                <input type="hidden" id="id_profile" name="">
                <p class="help-block"></p>
              </div>
            </div>
         </div>

         <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select Employee</label>
              <div class="col-md-4">
                <select id="select_emp" class="form-group" style="width:100%;" multiple=""></select>
                <input type="hidden" id="id_profile" name="">
                <p class="help-block"></p>
              </div>
            </div>
         </div>

         <div class="row">
          <div class="form-group">
            <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Content</label>
            <div class="col-md-6">
              <textarea type="text" class="form-control" id="noticeboard_text" name="task[noticeboard_text]" placeholder="Enter The Notification Content" autocomplete="off"></textarea>  
              <p class="help-block"></p>
            </div>
          </div>
        </div>

         <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Valid Till<span class="error">*</span> </label>
              <div class="col-md-6">
                <input class='form-control date'  name='reminder_date' id="reminder_date" type='text' required='true' placeholder='Valid Till' autocomplete="off"/>
              </div>
            </div>
          </div>

      </form>
      </div>

      <div class="modal-footer">
        <button id="save_notice_board" class="btn btn-success" >Save & Close</a>
        <button type="button" class="close_modal btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div   class="modal fade" id="noticeboard_edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Information</h4>
      </div>
      <div class="modal-body">
          <form id="ed_task_form">
              <div class="row" >
                <div class="col-md-offset-1 col-md-10" id='ed_error_msg'></div>
              </div>
               <input type="hidden" id="edit-id" value="" />
           
         
             <div class="row">
                <div class="form-group">
                  <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select Profile</label>
                  <div class="col-md-4">
                    <select id="ed_profile_select" class="form-group" name="task[id_profile]" style="width:100%;" ></select>
                    <input type="hidden" id="ed_id_profile" name="" value="">
                    <p class="help-block"></p>
                  </div>
                </div>
            </div> 
            
            <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Select Employee</label>
              <div class="col-md-4">
                <select id="ed_select_emp" class="form-group" style="width:100%;" multiple=""></select>
                <div id="sel_emp" data-sel_emp=""></div> 
                <p class="help-block"></p>
              </div>
            </div>
         </div> 
         
           <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Content<span class="error">*</span> </label>
              <div class="col-md-6">
                <textarea type="text" class="form-control" id="ed_noticeboard_text" name="task[noticeboard_text]" placeholder="Enter The Task Name" autocomplete="off"></textarea>  
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Valid Till<span class="error">*</span> </label>
              <div class="col-md-6">
                <input class='form-control date'  name='reminder_date' id="ed_reminder_date" type='text' required='true' placeholder='Valid Till' autocomplete="off"/>
              </div>
            </div>
          </div>

          <!--<div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">Raminder Date<span class="error">*</span> </label>
              <div class="col-md-6">
                <input class='form-control datemask date cus_due_dt' data-date-format='dd-mm-yyyy' name='reminder_date' id="ed_reminder_date" type='text' required='true' placeholder='Raminder Date' />
              </div>
            </div>
          </div>-->

         
        </form> 
      </div>
      <div class="modal-footer">
        <a href="#" id="update_noticeboard" class="btn btn-success">Update</a>
        <button type="button" class="close_modal btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
  

