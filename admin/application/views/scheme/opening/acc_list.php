<style>
.box-header>.fa, .box-header>.glyphicon, .box-header>.ion, .box-header .box-title{
	display: inline-block;
    font-size: 18px;
    margin: 0;
    line-height: 1;
}
</style>
  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Scheme Details

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Manage Savings Schemes</a></li>

            <li class="active">Scheme Account</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Scheme Account List</h3> <span id="total_accounts" class="badge bg-aqua"></span> 

                      

                          <?php /*?> <a class="btn btn-primary pull-right" href="<?php echo base_url('index.php/account/update/client'); ?>"><i class="fa fa-retweet"></i> Sync Account</a> <?php */?>

                           <a class="btn  pull-right bg-green" id="add" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
        </div><!-- /.box-header -->

        

        

                <div class="box-body">
        
        
         <div class="row">

           <div class="col-sm-8 col-sm-offset-2">

            <div id="error-msg"></div>

            <div id="payment_container"></div>


          </div>
        </div>
        

        
        
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
        
        
        
                <div class="">
        
           <!--<?php 
            $attributes = array('id' => 'scheme_acc', 'name' => 'scheme_acc_number');
            echo form_open_multipart('schemeaccount/update',$attributes);  
         ?> -->
        

                <!-- Alert -->

               </br><div class="row">
              <div class="col-md-12">
                
                  <div class="col-md-8 col-md-offset-2">
                    <div class="col-md-3"></div>
                    <div class="col-md-5">
               
                       <div class="form-group" style="    margin-left: 50px;">
                      <label>Enter Mobile number &nbsp;&nbsp;</label>
                      <input type="text" name="" id="mobilenumber" >
                      <!-- <button type="button"  value="submit" id="submit" name="submit"></button> -->
                    </div>


                  
                    </div>
                                
                 
                 </div>
            </div>    
          </div></br> 

           

      <?php if($this->account_model->get_accnosettings()==1){?> 

                <div class="table-responsive">

                  <table id="sch_acc_list" class="table table-bordered table-striped dataTable text-center grid" >

                    <thead>

                      <tr>

                        <th><label class="checkbox-inline"><input type="checkbox" id="select_aldata"  name="select_all" value="all"/>All</label></th>
                      
                        <th>Sch ID</th>

                     <!--   <th>Account.No</th>

                        <th>Client ID</th>-->

                        

                        <th>Cus Id</th> 
            
                        <th>Customer</th>
            
                        <th>Mobile</th> 

                        <th>A/c Name</th> 

            
                <th>Scheme code</th>
            
            <th> A/c No</th>   

            <th>Type</th>           

                        <th>Start Date</th> 

            <th>Scheme Type</th> 

                        <th>Installment Payable</th>
                        
                        <th>PAN No.</th>
                        
                        <th>Paid Ins</th>

                        <th>Status</th>
                        
                        <th>Gift Articles</th>

                        <th>Created Through</th>

                        <th>Action</th>

                      </tr>

                    </thead>

               

                  </table>

                  </div>

      <?php }else{?>  
      
          <div class="table-responsive">

                  <table id="sch_acc_list" class="table table-bordered table-striped dataTable text-center grid" >

                    <thead>

                      <tr>
                      
                        <th>Sch ID</th>
                        <th>Cus Id</th> 
            
                        <th>Customer Name</th>
            
                        <th>Mobile</th> 

                        <th>A/c Name</th> 
                      
                      <th>Scheme Code</th>

                      <th> A/c No</th>   

            <th>Type</th>           

                        <th>Start Date</th> 

            <th>Scheme Type</th> 

                        <th>Installment Payable</th>
                        
                        <th>PAN No.</th>
                        
                        <th>Paid Ins</th>

                        <th>Status</th>
                        
                         <th>Gift Articles</th>

                        <th>Created Through</th>

                        <th>Action</th>

                      </tr>

                    </thead>

               

                  </table>

                  </div>
          
      <?php }?>       

                </div>
				  <label>Note:&nbsp;Last 7 days Scheme Account List</label>
               </div><!-- /.box-body -->

                 <div class="overlay" style="display:none">

          <i class="fa fa-refresh fa-spin"></i>

        </div>

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

        <h4 class="modal-title" id="myModalLabel">Delete Scheme</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this scheme?</strong>

      </div>

      <div class="modal-footer">

        <a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      

<!-- modal close account -->      

<div class="modal fade" id="confirm-close" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Confirm Close Scheme</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to close this scheme account?</strong>

      </div>

      <div class="modal-footer">

        <a href="#" class="btn btn-danger btn-confirm" >Close Account</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal close account -->  

