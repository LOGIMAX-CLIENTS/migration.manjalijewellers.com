<!-- Content Wrapper. Contains page content -->

<style>

    .remove-btn{

        margin-top: -168px;

        margin-left: -38px;

        background-color: #e51712 !important;

        border: none;

        color: white !important;

    }

</style>

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Tag Unlink

      </h1>

      

    </section>



    <!-- Main content -->

    <section class="content product">



      <!-- Default box -->

      <div class="box box-primary">

        <div class="box-header with-border">

         

          <div class="box-tools pull-right">

            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

          </div>

        </div>

        <div class="box-body">

         <!-- form container -->

          <div class="row">

            <div class="col-sm-12"> 

                <!-- Lot Details Start Here -->

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

                    <?php } ?>  

                    </div>

                </div>

                <div class="row">

                            <?php if($this->session->userdata('branch_settings')==1){?>

                             <div class="col-md-2">

                                 <div class="form-group">

                                     <label><a data-toggle="tooltip" title="Branch" >Select Branch</a><span class="error">*</span></label>

                                     <select id="branch_select" class="form-control" required style="width:100%;"></select>

                                </div>

                             </div>

                             <?php }?>

                             <div class="col-md-2">

                                     <div class="form-group">

                                         <label><a  data-toggle="tooltip" title="Enter Lot">Search Tag Code</a></label>

                                         <input type="text" class="form-control tag_unlink" id="tag_unlink" placeholder="Enter Tag Code"></input>

                                    </div>

                             </div>

                             <div class="col-md-2">

                              <div class="form-group">

                                  <label><a  data-toggle="tooltip" title="Enter Lot">Search Old Tag Code</a></label>

                                  <input type="text" class="form-control old_tag_unlink" id="old_tag_unlink" placeholder="Enter Old Tag Code"></input>

                              </div>

                              </div>


                          
                 </div> 	

                 <p class="help-block"></p>			 

            </div>	<!--/ Col --> 

        </div>	 <!--/ row -->

        <div class="table-responsive">

                 <table id="tagging_unlink_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th width="5%"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>     

                        <th width="5%">Tag Code</th>

                        <th width="5%">Old Tag Code</th>

                        <th width="5%">Lot No</th>

                        <th width="5%">Product Name</th>

                        <th width="5%">Design Name</th>

                        <th width="5%">Sub Design Name</th>

                        <th width="5%">Gross Wgt</th>

                        <th width="10%">Net Wgt</th>

                        <th width="10%">Order No</th>

                        <th width="10%">Order Weight</th>

                        <th width="5%">status</th>

                      </tr>

                    </thead> 

                    <tbody></tbody>

                 </table>

              </div>		



           <p class="help-block"> </p> 

             <div class="row">

               <div class="box box-default"><br/>

                  <div class="col-xs-offset-5">

                    <button type="submit"  class="btn btn-primary" id="tag_unlink_submit">Save All</button> 

                    <input type="hidden" id="order_unlink_otp"  value= <?php echo $order_unlink_otp?> >

                    <button type="button" class="btn btn-default btn-cancel">Cancel</button>

                  </div> <br/>

                </div>

              </div> 

            </div> 

            <div class="overlay" style="display:none">

              <i class="fa fa-refresh fa-spin"></i>

            </div>



             <!-- /form -->

          </div>

         </section>

 

</div>


	
<div class="modal fade" id="confirm-orderUnlink" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Order Unlink</h4>
        </div>
        <div class="modal-body">
          <!-- <strong>Are you sure! You want to cancel this order?</strong>
          <p></p> -->
          <div class="col-md-12  cancel_otp_confirmation" style="display:block;white-space:no-wrap">
            <!-- <div class='form-group'>
              <div class='input-group'>
                <span class="input-group-btn"> -->
                  OTP will be sent to the administrator for approval. Do you wish to proceed?
                <!-- </span>
              </div>
            </div> -->
          </div>
          <div class="col-md-6 cancel_otp" style="display:none;">
            <div class='form-group'>
              <div class='input-group'>
                <input type="text" id="orderunlink_otp" name="cancel_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required />
                <span class="input-group-btn">
                  <!-- <button type="button" id="verify_otp" class="btn btn-primary btn-flat" disabled>Verify</button> -->
                
                  <button type="button" id="resend_order_unlink_otp" class="btn btn-warning" disabled>Resend <span id="timer"></span></button>
                </span>

              </div>
            </div>
          </div>
          <div class="row order_remarks" style="display: none;">
            <div class="col-md-12">
              <label>Remarks<span class="error">*</span></label>
              <input type="hidden" id="order_id" name="">
              <input type="hidden" id="id_orderdetails" name="">
              <textarea class="form-control" id="order_cancel_remark" placeholder="Enter Remarks" rows="5" cols="10"> </textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer order_remarks" style="display: none;">
          <button class="btn btn-danger" type="button" id="cancell_delete" disabled>Delete</button>
        </div>
        <div class="modal-footer cancel_otp_confirmation" style="display: none;">
          <button type="button" class="btn btn-success" id="send_order_unlink_otp_yes">Yes</button>
          <button type="button" class="btn btn-danger" id="send_order_unlink_otp_no">No</button>
        </div>
        <div class="modal-footer verify_otp" style="display: none;">
				<a href="#" id="verfiy_ord_unlink_otp" class="btn btn-success">Verify</a>
				<button type="button" class="btn btn-danger" id="order_unlink_close_modal">Close</button>
			</div>
      </div>
    </div>
  </div>
  
                              
