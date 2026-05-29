<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

<!-- Content Header (Page header) -->

<section class="content-header">

  <h1>

    Video Shopping Appointment

  </h1>

  <ol class="breadcrumb">

    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

    <li><a href="#">Slot Master</a></li>

    

  </ol>

</section>

<!-- Main content -->

<section class="content">

  <div class="row">

    <div class="col-xs-12">           

      <div class="box"> 

      	<div class="box-header with-border"> 

      		<h3 class="box-title">Create Slot</h3>

      	</div>

        <div class="box-body"> 

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

  	        <div class="row">

                <div class="col-md-2">

					<label>No. of Slots</label>

					<div class="input-group">

						<input  type="text" class="form-control" placeholder="Ex. 2"  id="no_of_slots" name="no_of_slots"/> 

						<span class="input-group-btn">

						 <?php if($access['add']==1){ ?>

	                      <button type="button" id="proced" class="btn btn-info btn-flat">Create</button>

						  <?php }?>

	                    </span>

					</div> 

					<p class="help-block">Number of slots to be created</p>

				</div>

                <div class="col-sm-10"> 

                	<div class="table-responsive"> 

                         <table id="slot_creation_tbl" class="table table-bordered table-striped text-center">

                         </table> 

                         <div align="center" id="save_blk" style="display: none">

                         	<button type="button" id="save_slot" class="btn btn-success"  required="true">Save</button>	 	

                         </div>

                    </div>

                </div><!-- /.col -->

            </div><!-- /.row -->  

            <p></p>

        </div>

       </div>

       <div class="box"> 

        <div class="box-header with-border"> 

      		<h3 class="box-title">Active Slots</h3>

      	</div> 

        <div class="box-body"> 

        	<?php if($this->session->flashdata('chit_alert_slots')){

        	  $message = $this->session->flashdata('chit_alert_slots'); ?> 

              <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

                <?php echo $message['message']; ?>

              </div> 

	        <?php } ?>

	        <div class="row">

	          <div class="col-md-12">

	          	<span id="alert_message"></span>

	          </div>

	        </div> 

            <div class="row">

                <div class="col-xs-12"> 

                	<div class="table-responsive"> 

                         <table id="available_slots" class="table table-bordered table-striped dataTable text-center " >

		                  <thead>

							  <tr>

		                        <th>#</th>

		                        <th width="10%">Slot No</th>

		                        <th width="15%">Slot Date</th>

		                        <th width="10%">Time From</th>

		                        <th width="10%">Time To</th> 

								<th width="15%">Allowed Bookings</th>

								<th width="20%">Created On</th>

		                        <th width="20%">Action</th>

		                      </tr>

		                    </thead>

		                    <tbody> 

		                    </tbody>

		                   </table> 

                    </div>

                </div> 

            </div> 

        </div><!-- /.box-body -->

        <div class="overlay" style="display:none">

		  <i class="fa fa-refresh fa-spin"></i>

		</div> 

      </div><!-- /.box -->

    </div><!-- /.col -->

  </div><!-- /.row -->

</section><!-- /.content -->

</div><!-- /.content-wrapper -->

      

      

   

<style type="text/css">



.container {

   display: flex;

    flex-direction: row;

    justify-content: center;

    align-items: center;



}

</style>



<div class="modal fade" id="edit_slot" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-primary">

       <h4 class="modal-title" id="myModalLabel" align="center">Edit Slot</h4>

      </div>

      <div class="modal-body">

          <div class="row">

	          <div class="col-md-12">

	          	<span id="chit_alert"></span>

	          </div>

          </div> 

	

          <div class="row">

          	<div class="col-md-offset-2 col-md-3">   

				<input type="hidden" id="id_appointment_slot" value=""/> 

				<label >Slot No</label> 

			</div> 

			<div class="col-md-5">   

				<input type='number' id='slot_no' class='slot_no form-control' value='' name='slot_no' readonly=""> 

			</div> 

		  </div>

		  <p></p>

		  <div class="row">

          	<div class="col-md-offset-2 col-md-3">    

				<label >Slot Date</label> 

			</div> 

			<div class="col-md-5">   

				<input type='date'  id='slot_date' class='slot_date form-control' name='slot_date'>

			</div> 

		  </div>

		  <p></p>

		  <div class="row">

          	<div class="col-md-offset-2 col-md-3">    

				<label >Slot Time From</label> 

			</div> 

			<div class="col-md-5">   

				<input type='time' id='slot_time_from' class='ed_slot_time_from form-control' name='slot_time_from'>

			</div> 

		  </div>

		  <p></p>

		  <div class="row">

          	<div class="col-md-offset-2 col-md-3">    

				<label >Slot Time To</label> 

			</div> 

			<div class="col-md-5">   

				<input type='time'  id='slot_time_to' class='ed_slot_time_to form-control' name='slot_time_to'>

			</div> 

		  </div>

		  <p></p>

		  <div class="row">

          	<div class="col-md-offset-2 col-md-3">    

				<label >Allowed Bookings</label> 

			</div> 

			<div class="col-md-5">   

				<input type='number'  id='allowed_booking' class='ed_allowed_booking form-control' name='allowed_booking'> 

				<input type='hidden'  id='userbookings' class='ed_userbookings form-control' name='userbookings'> 

			</div> 

		  </div> 

		  <p></p>

      </div>

      <div class="modal-footer">

      	<button type="button" id="upd_slot" class="btn btn-danger" data-dismiss="modal" >Update</button>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->

<!-- Delete modal -->      

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Slot</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this slot?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / Delete modal --> 