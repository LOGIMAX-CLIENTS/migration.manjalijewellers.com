 <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Scheme Group 

           <span id="total_group" class="badge bg-green"></span> 

          <!-- <small>Manage your customer profiles</small>-->

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Scheme</a></li>

            <!--<li class="active">Scheme Group</li>-->

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

                <div class="col-xs-12">

                  

                      <div class="box box-primary">

                                  <!-- <div class="box-header with-border">

                                    <h3 class="box-title">Scheme Group List</h3>   

                                    

                                  </div> -->

                                  <div class="box-body" >

                                          <div class="row"> 

                                                <div class="pull-right">

                                                <?php if($access['add']==1){?>

                                                  <a class="btn btn-success pull-right" id="add_customer" href="<?php echo base_url('index.php/account/scheme_group/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
                                                  <?php }?>
                                                  
                                                </div>

                                                <?php if($this->session->userdata('branch_settings')==1){?>

                                                    

                                                      <div class="col-md-3">

                                                          <div class="form-group">

                                                            <label>Filter By Branch</label>

                                                            <select id="branch_select" class="form-control" ></select>

                                                            <input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>

                                                          </div> 

                                                      </div> 

                                                  

                                                <?php }?>

                                                

                                          </div>   <!-- row add,branch -->

                                          <div class="row">

                                              <div class="col-xs-12">

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

                                                        

                                                      <?php 

                                                  } ?>  

                                              </div>

                                          </div><!-- row alert -->

                                          

                                            <div class="table-responsive">

                                                <table id="group_list" class="table table-bordered table-striped text-center">

                                                    <thead>

                                                      <tr>

                                                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>

                                                      

                                                        <th>Scheme Code</th> 

                                                        <th>Branch</th>

                                                        <th>Group Code</th>  

                                                        <th>Start Date</th>                        

                                                        <th>End Date</th>                        

                                                        <th>Action</th>

                                                      </tr>

                                                    </thead> 



                                                </table>

                                            </div><!-- table -->

                              

                                          <div class="overlay" style="display:none">

                                              <i class="fa fa-refresh fa-spin"></i>

                                          </div><!-- overlay -->

                                  </div><!-- box-body -->

                        </div> <!-- box -->

                </div> <!-- col-xs-12 -->

            </div><!-- row -->

        </section> 

      </div><!-- /.content-wrapper -->

      





<!-- modal -->      

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Group</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Group?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning " data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      