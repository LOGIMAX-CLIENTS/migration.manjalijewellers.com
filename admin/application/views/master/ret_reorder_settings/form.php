<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

        Add Re-Order settings 

		</h1>

		<ol class="breadcrumb">

		<li><a href="#"><i class="fa fa-dashboard"></i>Master</a></li>

		<li class="active"> Add Re-Order settings </li>

		</ol>

	</section>

     <!-- Default box -->

    <section class="content">

      <form id="add_reorder">  

		<div class="box">

			<div class="box-header with-border">

              <h3 class="box-title"> Add Re-Order settings </h3>

                <div class="box-tools pull-right">

                 <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                 <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>



                </div>

            </div> 

            

            <input type="hidden" name="settings[branch_settings]" id="branch_settings" value="<?php echo $this->session->userdata('branch_settings');?>">

			<input type="hidden" name="ref_no" id="ref_no" value="<?php echo $reorder[0]['ref_no'];?>">

			<input type="hidden" name="reorder_setting" id="reorder" value="<?php echo $reorder[0]['id_reorder_settings'];?>">

            <div class="box-body">

				    <div class='row'>	

                        <div class="col-md-2">

				            <div class='form-group'>

				                <label>Select Product<span class="error"> *</span></label>

						           		<select class="form-control"  name="settings[id_product]" id="product_select_reorder" style="width: 100%"></select>

                                 <input type="hidden" id="weight_range_based">

                                <input type="hidden" id="id_product" name="">

							</div>

				        </div>		

			

                        <div class="col-md-2">

					    	<div class='form-group'>

					           <label>Select Design<span class="error">*</span></label>

							   <select class="form-control des_select" name="settings[id_design]" id="des_select_reorder" style="width: 100%"></select>

                                <input type="hidden" id="id_design" name="">							      

							</div>

						</div>
<!-- 
						<div class="col-md-2">
					    	<div class='form-group'>
					           <label>Select Section<span class="error">*</span></label>
							   <select class="form-control section" name="settings[id_section]" id="section_sel" style="width: 100%"></select>
							</div>
						</div> -->
						

						
						<div class="col-md-2">

					    	<div class='form-group'>

				 	           </br>

							   <button id="add_reorderitems_info" type="button" class="btn btn-success pull-left"><i class="fa fa-plus"></i> Add item</button>

							</div>

						</div>

						

						

						

					</div>

                </div>   

            

                    <div class="box-body">
						
					<div class="row">
                            <div class="col-sm-10 col-sm-offset-1">
                                <div id="chit_alert"></div>
                            </div>
                        </div>

                        <div class="row">

                    <table id="total_reorder_items" class="table table-bordered table-striped text-center"  style="width: 100%;">

                    <input  type="hidden" value="0" id="i_increment" />	

					<input  type="hidden"  id="reorder_based_on" value="" />	


                    <thead>

                      <tr>
 
					   <th style="width: 12%">Section</th>

					    <th>Branch</th>

                        <th style="width: 15%">Sub Design</th>

                        <th>Weight Range</th>

                        <th>Size</th>

                        <th style="width: 10%">Min Pcs</th>

                        <th style="width: 10%">Max Pcs</th>

						<th>Action</th>


                      </tr>

                 	</thead>

                     <tbody>

                     

                     </tbody>

                  </table>

                   </div>



		            <div class="row">

		                <div class="box box-default"><br/>

			                <div class="col-xs-offset-5">

				               <button type="button" id="add_reordersettings_new"  class="btn btn-primary">save</button> 

				               <button type="button" class="btn btn-default btn-cancel">Cancel</button>

				            </div> <br/>

			            </div>

		            </div> 

                    			

            </div>

        </form>

		<div class="overlay" style="display:none">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
    </section>

</div>


  <!-- modal -->      
  <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Delete Settings</h4>
        </div>
        <div class="modal-body">
                 <strong>Are you sure! You want to delete this settings?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger btn-confirm" >Delete</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->  