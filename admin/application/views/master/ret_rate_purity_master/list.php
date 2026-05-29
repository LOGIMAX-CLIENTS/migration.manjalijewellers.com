<!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Metal Rate Purity

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Metal Rate Purity</a></li>

            <li class="active">List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                <div class="box-header with-border">

                  <h3 class="box-title">Metal Purity Rate List</h3><span id="total_count" class="badge bg-green"></span>  
                  <?php if($access['add']==1){ ?>     
                  <a class="btn btn-success pull-right"  data-dismiss="modal" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                  <?php }?>


                </div><!-- /.box-header -->

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

			<div class="row">

					<div class="col-sm-10 col-sm-offset-1">

					<div id="chit_alert"></div> 

					</div>

				  </div>

			<div class="row">

	                 <div class="col-md-12">

	             

	                 	<div class="col-md-2" style="margin-top: 20px;">

	                 		         	 <!-- Date and time range -->



		                </div>	

							</div>

					

	                 </div>	

                   <!-- <div id="temp"></div>		   -->

                  <div class="table-responsive">

                  <table id="PurityTable" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>ID</th>

                        <th>Metal Name</th>

                        <th>Purity </th>

					            	<th>Rate Field </th>

                        <th>Market Rate Field</th>

                        <th>Action</th>

                      </tr>

                      

                 </thead>

                 

                  </table>

                  </div>

                </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      

      <!-- Add Metal modal -->  

<form id="myofrm">

<div class="modal fade" data-backdrop="static"  id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close " data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Metal Rate Purity</h4>



      </div>

      <div class="modal-body">

	  <div class="row" >

					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>

				</div> 

				<div class="row">

				 	<div class="form-group">

                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal

					   <span class="error">*</span></label>

					   <div class="col-md-4">

						 <select id="SelectMetal" class="form-control"></select>

            <input id="Metal_input"  value="" name="Metal_input" type="hidden" value="" />

                      </div>

                    </div>

				 </div>

         <br></br>

				 <div class="row">   

                    <div class="form-group">

                       <label for="scheme_code" class="col-md-3 col-md-offset-1 " >Purity

					   <span class="error">*</span></label>

					   <div class="col-md-4">

						<select id="SelectPurityid" class="form-control"></select>

				 <input id="Purity_input" value=""name="Purity_input" type="hidden" /> 

                      </div>               

                    </div>

				 </div> 

         <br></br>

				<div class="row">

				 	<div class="form-group">

                       <label for="scheme_code" class="col-md-3 col-md-offset-1 " width="100">Rate

					   <span class="error">*</span></label>

					   <div class="col-md-4">

						<select id="RatePurityid" class="form-control"></select>

						<input id="Rate_input" value="" name="Rate_input" type="hidden" />

                      </div>

                    </div>

				 </div>  

				 <div class="row">

				   <p class="help-block"></p>

				 </div>

      </div>

      <div class="modal-footer">  

		<a href="#" id="Metalratepurity" data-dismiss="modal" class="btn btn-success">Save</a>

		<!-- <a href="#" id="newmetal" class="btn btn-success">Save</a> -->

      	<!-- <a href="#" id="add_newmetal" class="btn btn-warning" data-dismiss="modal">Save & Close</a> -->

        <button type="reset" id="closebtn"class="btn btn-danger" data-dismiss="modal" >Close</button>

      </div>

    </div>

  </div>

</div>

                   </form>

                  

<!-- /End of  Add Metal  modal -->

<!-- Edit Modal -->

<div class="modal fade" id="confirm-edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Edit Metal Rate Purity</h4>

      </div>

      <div class="modal-body">

	  <div class="row" >

					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>

				</div> 
        <input type="hidden" id="edit_id" />

				<div class="row">

				 	<div class="form-group">

                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal

					   <span class="error">*</span></label>

					   <div class="col-md-4">

						 <select id="ed_SelectMetal" class="form-control"></select>

            <input id="ed_Metal_input" value="" name="ed_Metal_input" type="hidden" value="" />

                      </div>

                    </div>

				 </div>

         <br></br>

				 <div class="row">   

                    <div class="form-group">

                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Purity

					   <span class="error">*</span></label>

					   <div class="col-md-4">

						<select id="ed_SelectPurityid" class="form-control"></select>

				 <input id="ed_Purity_input" value="" name="ed_Purity_input" type="hidden" /> 

                      </div>               

                    </div>

				 </div> 

         <br></br>

				<div class="row">

				 	<div class="form-group">

             <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Rate

					   <span class="error">*</span></label>

					   <div class="col-md-4">

						<select id="ed_RatePurityid" class="form-control"></select>

						<input id="ed_Rate_input" value="" name="ed_Rate_input" type="hidden" />

                      </div>

                    </div>

				 </div>  

				 <div class="row">

				   <p class="help-block"></p>

				 </div>

      </div>

      <div class="modal-footer">

		<a href="#" id="update_PurityRate"  class="btn btn-warning">Update</a>

        <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>

      </div>

    </div>

  </div>

</div>

<!--End of  Edit Modal -->

<!-- Delete -->

<!--Delete Confirmation modal -->      

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Metal</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Metal record?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- /Delete Confirmation modal -->  

<!-- End of Delete Modal -->