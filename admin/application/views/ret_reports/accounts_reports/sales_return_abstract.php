	<style>     

	@media print {

            html,

            body {

                height: auto;

                width: 150vh;

                margin: 0 !important;

                padding: 0 !important;

                overflow: hidden;

            }

        }

	</style>

	

 <!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<style> 

	</style>

	<section class="content-header">

	  <h1>

	    Reports

		 <small>Sales Return Bills</small>

	  </h1>

	  <ol class="breadcrumb">

	    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

	    <li><a href="#">Retail Reports</a></li>

	    <li class="active">Sales Return Abstract</li>

	  </ol>

	</section>

	<!-- Main content -->

	<section class="content">

	  <div class="row">

	    <div class="col-xs-12">

	       <div class="box box-primary">

		    <div class="box-header with-border">


			<div class="row">         

	          <div class="col-md-3"> 

				<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

				<div class="form-group tagged">

					<select id="branch_select" class="form-control branch_filter" style="width:100%;" multiple></select>

				</div> 

				<?php }else{?>

					<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

					<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

				<?php }?> 

			 </div>  

            

             <div class="col-md-1">

                <div class="form-group">                              

                    <!-- <div class="input-group"> -->

                         <select class="form-control" style="width:100%;"id="sales_report_type">

            				 <option value="0" selected>All</option>

                             <option value="1">B2C</option>

                             <option value="2">B2B</option>                                  

                         </select>

                     <!-- </div> -->

                </div><!-- /.form group -->

             </div>

							

			 <div class="col-md-2">

				 <div class="form-group">

					 <!-- <div class="input-group"> -->

						 <select class="form-control" style="width:100%;"

							 id="sale_ret_group_by">

							 <option value="0" selected>Detailed</option>

							 <option value="1">Summary</option>

						 </select>

					 <!-- </div> -->

				 </div><!-- /.form group -->

             </div>

                            

			 <div class="col-md-2"> 

					 <div class="form-group">

	                    <!-- <div class="input-group"> -->

	                       <button class="btn btn-default btn_date_range" id="rpt_date_picker">

	                        <i class="fa fa-calendar"></i> Date range picker

	                        <i class="fa fa-caret-down"></i>

	                      </button>

	                      <span  style="display:none;" id="rpt_from_date"></span>

						    <span  style="display:none;" id="rpt_to_date"></span>

	                    <!-- </div> -->

	                 </div><!-- /.form group -->

			 </div>

			 <div class="col-md-2"> 

				<select id="metal" class="form-control" style="width:100%;"></select>

				</div>  		

		     <div class="col-md-2"> 

				<select id="category" class="form-control" style="width:100%;"></select>

			</div>  

			</div>    


  

			<div class="row">         

			<div class="col-md-1"> 

				<div class="form-group">

					<button type="button" id="sales_ret_abstract_search" class="btn btn-info">Search</button>   

				</div>

			</div>

	        </div>

			</div>


	         <div class="box-body">  

	           <div class="row">

	               <div class="col-md-12">

	               	<div class="table-responsive">

	                 <table id="sales_ret_abstract" class="table table-bordered table-striped text-center">

	                    <thead>

						  <tr>

						    <th width="10%">Category</th>

							<th width="10%">Metal</th>

							<th width="5%">HSN Code</th>

						    <th width="3%">Return Bill No</th>

						    <th width="10%">Return Bill Date</th>

						    <th width="1%">Pcs</th>

						    <th width="3%">Gwt(Grams)</th>

						    <th width="3%">NWT(Grams)</th>

						    <th width="3%">DWT(Grams)</th>

						    <th width="3%">Taxable Amount(Rs)</th>

						    <th width="3%">SGST</th>

						    <th width="3%">CGST</th>

						    <th width="3%">IGST</th>

						    <th width="3%">GST</th>

						    <th width="3%">Total Amount(Rs)</th>

						    <th width="3%">Sales Bill No</th>

						    <th width="10%">Sales Bill Date</th>

						  </tr>

	                    </thead> 

	                     <tbody></tbody>

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

      