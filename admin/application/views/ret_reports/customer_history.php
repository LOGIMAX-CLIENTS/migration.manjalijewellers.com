<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

<!-- Content Header (Page header) -->

<section class="content-header">

<h1>

Reports

<small>Customer & Tag History</small>

</h1>

<ol class="breadcrumb">

<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

<li><a href="#">Reports</a></li>

<li class="active">Customer & Tag History</li>

</ol>

</section>



<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-header with-border">

                        <h3 class="box-title">Customer & Tag History Report</h3>  <span id="total_count" class="badge bg-green"></span>  

                    </div>

                    <div class="box-body">  

                        <p></p>

                        <?php 

                        $retail=$this->ret_reports_model->get_modules('RT');

                        $crm=$this->ret_reports_model->get_modules('SS');

                        ?>

                        <div class="nav-tabs-custom">

                            <ul class="nav nav-tabs">

                                <li class="active"><a href="#customer" data-toggle="tab">Customer</a></li>  

                                <li><a href="#tag" data-toggle="tab">Product</a></li>

                            </ul>

                            <div class="tab-content">

                                    <div class="tab-pane active" id="customer">

                                         <div class="box-body">

                                             <div class="col-md-3">

                            					<div class="input-group margin">

                            						<input type="text" name="" id="mobilenumber" placeholder="Enter The Mobile Number" class="form-control"/>

                            						<span class="input-group-btn">

                            							<button type="submit" id="cus_search" name="cus_search" class="btn btn-info btn-flat">Search

                            						</span>

                            					</div> 

                            				 </div>

                            				 

                                            <div class="col-md-12">  

                                                <ul class="nav nav-pills nav-stacked col-md-2">

                                                    <li class="active"> <a href="#cus_details" data-toggle="pill">Personal Details</a></li>

                                                    <?php if($crm['m_web']==1 && $crm['m_active']==1){?>

                                                    <li> <a href="#crm" data-toggle="pill">Scheme Account</a></li>

                                                    <?php }?>

                                                    <?php if($retail['m_web']==1 && $retail['m_active']==1){?>

                                                    <li> <a href="#sales" data-toggle="pill">Sales</a></li>

                                                    <li> <a href="#purchase" data-toggle="pill">Purchase</a></li>

                                                    <li> <a href="#credit" data-toggle="pill">Credit History</a></li>

                                                    <?php }?>

                                                </ul>

                                               

                                        <div class="tab-content col-md-10">

                                             <div class="tab-pane active" id="cus_details">

                                                    <legend>Personal Details</legend>

				                                          <div class="container-fluid">

                                                                <div class="row">

                                                                    <div class="box box-default ">

                                                                        <div class="box-body">

                                                                            <div class="col-md-3">

                                                                                <div class="form-group">

                                                                                    <div id="cus_img"></div>

                                                                                </div>

                                                                            </div>

                                                                        <div class="col-md-3">

                                                                            <div class="form-group">

                                                                                <label>Customer</label>

                                                                                <p id="cus_name"></p>

                                                                            </div>	

                                                                            <div class="form-group">

                                                                                <label>Mobile</label>

                                                                                <p id="cus_mobile"></p>

                                                                            </div>

                                                                            <div class="form-group">

                                                                                <label>E-Mail</label>

                                                                                <p id="cus_mail"></p>

                                                                            </div>

                                                                        </div>

                                                                        <div class="col-md-3">

                                                                            <div class="form-group">

                                                                                <label>Country</label>

                                                                                <p id="cus_country"></p>

                                                                            </div>	

                                                                            <div class="form-group">

                                                                                <label>State</label>

                                                                                <p id="cus_state"></p>

                                                                            </div>

                                                                            <div class="form-group">

                                                                                <label>City</label>

                                                                                <p id="cus_city"></p>

                                                                            </div>

                                                                        </div>

                                                                        <div class="col-md-3">

                                                                            <div class="form-group">

                                                                                <label>Address1</label>

                                                                                <p id="cus_address1"></p>

                                                                            </div>	

                                                                            <div class="form-group">

                                                                                <label>Address2</label>

                                                                                <p id="cus_address2"></p>

                                                                            </div>

                                                                            <div class="form-group">

                                                                                <label>Address3</label>

                                                                                <p id="cus_address3"></p>

                                                                            </div>

                                                                        </div>

                                                                    </div><!-- /.box-body -->

                                                                </div><!-- /.box -->

                                                            </div>

                                                    </div>

                                             </div>

                                             <div class="tab-pane " id="crm">

                                                <legend>Scheme Account Details</legend>

                                                    <div class="container-fluid">

                                                        <div class="row">

                                                            <div class="box box-default ">

                                                                <div class="box-body">

                                                                        <div class="table-responsive">

                                                        	                 <table id="account_list" class="table table-bordered table-striped text-center">

                                                        	                    <thead>

                                                        	                      <tr>

                                                        	                        <th width="5%">Sch Id</th>

                                                        	                        <th width="10%">Acc No</th>

                                                        	                        <th width="10%">Acc Name</th>

                                                        	                        <th width="10%">Scheme</th>

                                                        							<th width="10%">Start Date</th>

                                                        							<th width="10%">Installments</th>

                                                        	                        <th width="10%">Status</th>

                                                        	                      </tr>

                                                        	                    </thead>

                                                        	                    <tbody></tbody>

                                                        	                 </table>

                                                                          </div>

                                                                </div><!-- /.box-body -->

                                                            </div><!-- /.box -->

                                                        </div>

                                                    </div>

                                             </div>

                                             <div class="tab-pane " id="sales">

                                                    <legend>Sales Details</legend>

                                                        <div class="container-fluid">

                                                            <div class="row">

                                                                <div class="box box-default ">

                                                                    <div class="box-body">

                                                                            <div class="table-responsive">

                                                            	                 <table id="sales_list" class="table table-bordered table-striped text-center">

                                                            	                    <thead>

                                                            	                      <tr>

                                                            	                         <th width="10%" style="text-align:left;">Bill No</th>

                                                            	                        <th width="7%" style="text-align:left;">Bill Date</th>

                                                            	                        <th width="5%" style="text-align:right;">Gold Wt</th>

                                                            	                        <th width="5%" style="text-align:right;">Silver Wt</th>

                                                            	                        <th width="7%" style="text-align:right;">MRP Amount</th>

                                                            	                        <th width="7%" style="text-align:right;">Tot Bill Amount</th>

                                                            	                      </tr>

                                                            	                    </thead>

                                                            	                    <tbody></tbody>

                                                            	                 </table>

                                                                              </div>

                                                                    </div><!-- /.box-body -->

                                                                </div><!-- /.box -->

                                                            </div>

                                                        </div>

                                                </div>

                                                <div class="tab-pane " id="purchase">

                                                    <legend>Purchase Details</legend>

                                                        <div class="container-fluid">

                                                            <div class="row">

                                                                <div class="box box-default ">

                                                                    <div class="box-body">

                                                                            <div class="table-responsive">

                                                            	                 <table id="purchase_list" class="table table-bordered table-striped text-center">

                                                            	                    <thead>

                                                            	                      <tr>

                                                            	                        <th width="10%">Bill No</th>

                                                            	                        <th width="10%">Bill Date</th>

                                                            	                        <th width="10%">Tot Bill Amount</th>

                                                            	                      </tr>

                                                            	                    </thead>

                                                            	                    <tbody></tbody>

                                                            	                 </table>

                                                                              </div>

                                                                    </div><!-- /.box-body -->

                                                                </div><!-- /.box -->

                                                            </div>

                                                        </div>

                                                </div>

                                                 <div class="tab-pane " id="credit">

                                                        <legend>Credit History</legend>

                                                           <div class="container-fluid">

                                                                <div class="row">

                                                                    <div class="box box-default ">

                                                                        <div class="box-body">

                                                                                <div class="table-responsive">

                                                                	                 <table id="credit_list" class="table table-bordered table-striped text-center">

                                                                	                    <thead>

                                                                	                      <tr>

                                                                	                        <th width="5%">Bill No</th>

                                                                	                        <th width="10%">Bill Date</th>

                                                                	                        <th width="10%">Tot Bill Amount</th>

                                                                	                        <th width="10%">Status</th>

                                                                	                      </tr>

                                                                	                    </thead>

                                                                	                    <tbody></tbody>

                                                                	                 </table>

                                                                                  </div>

                                                                        </div><!-- /.box-body -->

                                                                    </div><!-- /.box -->

                                                                </div>

                                                            </div>

                                                 </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="tab-pane" id="tag">

                                         <div class="box-body">

                                            <div class="col-md-3">

                            					<div class="input-group margin">

                            						<input type="text" name="" id="tag_number" placeholder="Enter The Tag Number" class="form-control"/>

                            						<span class="input-group-btn">

                            							<button type="submit" id="tag_search" name="tag_search" class="btn btn-info btn-flat">Search

                            						</span>

                            					</div> 

                            				 </div>

                            				 <div class="col-md-12">

                                                <div class="row">  

                                                    <div class="box-body">

                                                        <div class="table-responsive">

                                                            <table id="tag_history" class="table table-bordered table-striped text-center">

                                                                <thead>

                                                                <tr style="text-transform:uppercase;">

                                                                <th width="10%">Image</th>
                                                                <th width="10%">Tag Id</th>
                                                                <th width="10%">Tag Code</th>
                                                                <th width="10%">Old Tag Code</th>
                                                                <th width="10%">Tag Date</th>
                                                                <th width="10%">Supplier</th>
                                                                <th width="10%">Product</th>
                                                                <th width="10%">Design</th>
                                                                <th width="10%">Sub Design</th>
                                                                <th width="10%">Gross Wt</th>
                                                                <th width="10%">Net Wt</th>
                                                                <th width="10%">Tag Status</th>
                                                                <th width="10%">Emp Name</th>
                                                                <th width="10%">Detail</th>

                                                                </tr>

                                                                </thead> 

                                                                <tbody></tbody>

                                                                <tfoot></tfoot>

                                                            </table>

                                                        </div>

                                                    </div> 

                                                </div> 

                            				 </div>

                                        </div>

                                    </div>

                            </div>

                        </div>

                    </div>

                    <div class="overlay" style="display:none;">

                        <i class="fa fa-refresh fa-spin"></i>

                        </div>

                </div>

        </div>

    </div>

 </section>   

</div>

