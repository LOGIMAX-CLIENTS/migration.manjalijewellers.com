	 <!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<style> 

	</style>

	<section class="content-header">

	  <h1>

	    Reports

		 <small>GST Bills</small>

	  </h1>

	  <ol class="breadcrumb">

	    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

	    <li><a href="#">Retail Reports</a></li>

	    <li class="active">GST Bills</li>

	  </ol>

	</section>



	<!-- Main content -->

	<section class="content">

	  <div class="row">

	    <div class="col-xs-12">

	       <div class="box box-primary">

		    <div class="box-header with-border">

		        

	          <div class="col-md-3"> 

					<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

					<div class="form-group tagged">

						<select id="branch_select" class="form-control branch_filter"></select>

					</div> 

					<?php }else{?>

						<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

						<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

					<?php }?> 

				</div>  

				<div class="col-md-3"> 

					<div class="form-group">    

						<?php   

							$fromdt = date("d/m/Y", strtotime('-0days'));

							$todt = date("d/m/Y");

					    ?>

               		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  

					</div> 

				</div>

				<div class="col-md-3"> 

					<select id="category_select" class="form-control" style="width:100%;"></select>

				</div>

				<div class="col-md-2"> 

					<div class="form-group">

						<button type="button" id="gst_bill_search" class="btn btn-info">Search</button>   

					</div>

				</div>

	        </div>

	         <div class="box-body">  

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

	               <div class="col-md-12">

	               	<div class="table-responsive">

	                 <table id="gst_bill_list" class="table table-bordered table-striped text-center">

	                    <thead>

						  <tr>

						    <th>Bill No</th>

						    <th>Bill Date</th>

						    <th>Customer</th>

						    <th>Mobile</th>

						    <th>GST No</th>

						    <th>PAN No</th>

							<th>Tax Value</th>

							<th>Taxable Amount</th>

						    <th>Bill Amount</th>

						  </tr>

	                    </thead> 

	                     <tbody> 

	                </tbody>
                       <!-- <tfoot>
						<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						 </tr>
						 </tfoot> -->
	                 </table>

	              </div>

	               </div>

	           </div>

	        </div><!-- /.box-body -->

	        <div class="overlay" style="display:none">

			  <i class="fa fa-refresh fa-spin"></i>

			</div>

	      </div>

	    </div><!-- /.col -->

	  </div><!-- /.row -->

	</section><!-- /.content -->

</div><!-- /.content-wrapper -->

      



