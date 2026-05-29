      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Payment Mode

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="<?php echo site_url('postdated/payment/list');?>">Post Dated Payment</a></li>

            <li class="active">Payment</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

     

          <!-- Default box -->

          <div class="box">

            <div class="box-header with-border">

              <h3 class="box-title">Post Dated Payment</h3>

              <div class="box-tools pull-right">

                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

              </div>

            </div>

            <div class="box-body">

              <div class="">

			 <?php echo form_open('postdated/payment/save'); ?>

                

                <div class="row">

		             		<div class="col-sm-4 col-md-offset-1">	

		             		<div class="form-group">

	                    			<label for="" >Customer Mobile</label>

	                    			<select class="form-control" id="customer"></select>

	                    			<input type="hidden" id="id_customer"/>

	                    		</div>			

							 	<div class="form-group">

			                       <label for="" class="">Scheme No <span class="error">*</span></label>

			                      

			                 			<select class="form-control"  id="scheme_account"></select>

		              					<input type="hidden" class="form-control" id="id_scheme_account"/>

			                       

			                    </div>	

		                    </div>

		                    

		                    <div class="col-md-4 col-md-offset-1">			

							 	<div class="form-group">

			                       <label for="" >Type <span class="error">*</span></label>

			                       <div class="form-group">

			                         <label class="radio-inline"><input type="radio" name="paytype[payment_type]" value="CHQ" checked="true">Cheque</label>

			                         <label class="radio-inline"><input type="radio" name="paytype[payment_type]" value="ECS">ECS</label>

			                      </div>

			                    </div>	

		                    </div>	

		                  </div>

		                  <div class="row">

		                 

		             		

		                    <div class="col-md-2 col-md-offset-1">			

							 	<div class="form-group">

			                       <label for="">Start from</label>

	                 	    	   <div class='input-group date'>

					                    <input type='text' class="form-control myDatePicker datemask" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask id='start_date'  data-date-format="yyyy/mm/dd" />

					                    <span class="input-group-addon">

					                        <span class="glyphicon glyphicon-calendar"></span>

					                    </span>

					                    

					                </div>

			                    </div>	

		                    </div>

		                    

		                    <div class="col-md-2">			

							 	<div class="form-group">

			                       <label for="">Execution Day</label>

			                 	  

				                    <input type='text' class="form-control input_number"  id='exe_date' />

				                   

			                    </div>	

		                    </div>

		                    

		                    <div class="col-md-1 col-md-offset-1">			

							 	<div class="form-group">

			                       <label for="" >Installments</label>

			                       <input type="text" class="form-control input_number" id="no_of_installment" readonly="true" />	

			                    </div>	

		                    </div>

		                    <div class="col-md-1">			

							 	<div class="form-group">

			                       <label for="" >Paid</label>

			                       <input type="text" class="form-control input_number" id="paid_installments" readonly="true" />	

			                    </div>	

		                    </div>

		                  	<div class="col-md-2">			

							 	<div class="form-group">

			                       <label for="">Installment Amount</label> 

			                 	   <input type="text" class="form-control input_currency"   id="installment_amount" readonly="true" />

			                    </div>	

		                    </div>

		                 </div> 

		                 <div class="row">	
		                    <div class="col-md-3 col-md-offset-1">			

							 	<div class="form-group">

			                       <label for="" class="">Cheque Starting No</label>

			                 	   <input type="text" class="form-control input_number"  id="chq_start_no" required="true"/>

			                    </div>	

		                    </div>    
							
							
							

		                    <div class="col-md-1 ">			

							 	<div class="form-group">

			                       <label for="" class="">No's</label>

			                 	   <input type="text" class="form-control input_number"  id="chq_leaf" />

			                    </div>	

		                    </div> 

		                    <div class="col-md-2 col-md-offset-1">			

							 	<div class="form-group">

			                       <label for="" class="">Eligible</label>

			                 	   <input type="text" class="form-control input_number"  id="allowed_leaves" readonly="true" />

			                    </div>	

		                    </div>   

		                    <div class="col-md-2">			

							 	<div class="form-group">

			                       <label for="" class="">Existing Cheques</label>

			                 	   <input type="text" class="form-control input_number" readonly="true"  id="pending_chq" />

			                    </div>	

		                    </div>	
		                </div>

		                  <div class="row">

		                    <div class="col-md-4 col-md-offset-1">

		                    	<legend>Payee Details <span class="error">*</span></legend>

		                    	

		                    	 <div  class="col-md-12">			

								 	<div class="form-group">

				                       <label for="">Payee A/c No</label>

				                       <input type="text" class="form-control input_number" id="payee_acc_no" required="true"/>

				                    </div>	

			                    </div> 

	                    		<div class="col-md-12">			

								 	<div class="form-group">

				                       <label for="" >Payee Bank</label>

				                       <select class="form-control bank-master" id="payee_bank" ></select>

				                       <input type="hidden" class="form-control input_number"  id="id_payee_bank" />

				                    </div>	

			                    </div>

			                    

			                    <div  class="col-md-12">			

								 	<div class="form-group">

				                       <label for="">Bank Branch</label>

				                       <input type="text" class="form-control input_text" id="payee_bank_branch" required="true"/>

				                    </div>	

			                    </div>

			                    

		                      

			                    

			                    <div  class="col-md-12">			

								 	<div class="form-group">

				                       <label for="">Payee IFSC Code</label>

				                       <input type="text" class="form-control ucase" id="payee_ifsc" required="true"/>

				                    </div>	

			                    </div>

		                    	

		                    </div> 

		                    

		                    <div class="col-md-4 col-md-offset-1">

		                    	<legend>Drawee Details <span class="error">*</span></legend>

		                    	

		                    	    <div class="col-md-12">			

									 	<div class="form-group">

					                       <label for="" >Drawee A/c No</label>

				

					                       <select class="form-control" id="drawee_acc_no"></select>

					                 	   <input type="hidden" class="form-control" id="id_drawee_bank" />

					                    </div>	

				                    </div>

				                    <div class="col-md-12">			

									 	<div class="form-group">

					                       <label for="">Drawee Bank</label>

					                       <input type="text" class="form-control input_number"  id="drawee_bank" readonly="true" />

					                    </div>	

				                    </div>	

				                    

			                       <div class="col-md-12">			

									 	<div class="form-group">

					                       <label for="">Drawee Branch</label>

					                       <input type="text" class="form-control input_text"  id="drawee_bank_branch" readonly="true"  />

					                    </div>	

				                    </div>

				                    

				                    

				                    

				                    <div class="col-md-12">			

									 	<div class="form-group">

					                       <label for="">Drawee IFSC Code</label>

					                       <input type="text" class="form-control ucase"  id="drawee_ifsc" readonly="true"  />

					                    </div>	

				                    </div>	

		                    	

		                    </div>

		       

		                 

		                    <div class="col-md-4 col-md-offset-1">			

							 	<div class="form-group">

			                       <button class="btn btn-primary" id="generate_payments" type="button">Proceed</button>

			                    </div>	

		                    </div>	

		                 </div>	

		                 

		           <div class="box">

					  <div class="box-header with-border">

					    <h3 class="box-title">Payment Detail</h3>

					    <div class="box-tools pull-right">

					      <!-- Buttons, labels, and many other things can be placed here! -->

					      <!-- Here is a label for example -->

					  

					    </div><!-- /.box-tools -->

					  </div><!-- /.box-header -->

					  <div class="box-body">

					    

				    		<table id="post_payment_detail" class="table table-bordered table-striped text-center">

            				<thead>

		                      <tr>

		                        <th>S.No</th>

		                        <th>Execution Date</th>

		                        <th>Cheque</th>		                        

		                        <th>Amount (<?php echo $this->session->userdata('currency_symbol')?>)</th> 

		                        <!--  <th>Payee Bank</th>

		                        <th>Payee A/c No</th>

		                        <th>Drawee Bank</th>

		                        <th>Drawee A/c No</th>  

		                      <th>Status</th> -->

		                      </tr>

		                     </thead>          

		                     <tbody>

		                     	

		                     </tbody>

                			</table>

					    

					  </div><!-- /.box-body -->

					 <!-- <div class="box-footer">

					    The footer of the box

					  </div><!-- box-footer -->

					</div><!-- /.box -->

 

	

				

				<br/>      

				 <div class="row col-xs-12">

				   <div class="box box-default"><br/>

					  <div class="col-xs-offset-5">

						<button type="submit" id="btn-save" disabled="true" class="btn btn-primary">Save</button> 

						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

						

					  </div> <br/>

					</div>

				  </div>      

				        	

               </form>              	              	

              </div>

            </div><!-- /.box-body -->

             <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

            <div class="box-footer">

              

            </div><!-- /.box-footer-->

           

          </div><!-- /.box -->

         



        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->