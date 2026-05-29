  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

<!-- Content Header (Page header) -->

<section class="content-header">

  <h1>

    Reports

     <small>Re-order Items</small>

  </h1>

  <ol class="breadcrumb">

    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

    <li><a href="#">Retail Reports</a></li>

    <li class="active">Retagging Report</li>

  </ol>

</section>



<!-- Main content -->

<section class="content">

  <div class="row">

    <div class="col-xs-12">

       

       <div class="box box-primary">

         <div class="box-body">  

          <div class="row">

              <div class="col-md-12">  

              <div class="box box-default">  

               <div class="box-body">  

                   <div class="row">
                   
                   <div class="col-md-2"> 
								     <label></label>
    								 <div class="form-group">
                                          <button class="btn btn-default btn_date_range"  id="rpt_date_picker">
                                                    <i class="fa fa-calendar"></i> Date range picker
                                                    <i class="fa fa-caret-down"></i>
                                            </button>
                                                <span style="display:none;" id="rpt_from_date"></span>
                                                <span style="display:none;" id="rpt_to_date"></span>
                                         </div><!-- /.form group -->
                                </div>

                
                                    <div class="col-md-2"> 

                                    <div class="form-group">

                                        <label>Select Receipt Type</label>

                                        <select class="form-control" id="receipt_type">

                                            <option value="1">Sales</option>

                                            <option value="2">Sales Return</option>

                                            <option value="3">Partly Sold</option>

                                            <option value="4">Other issue</option>

                                            <option value="5">Old Metal</option>

                                        </select>

                                    </div>

                                    </div>

                        <div class="col-md-2"> 

                            <div class="form-group">    

                                <label>Select Karigar</label>

                                <select id="karigar" style="width:100%;"></select>

                            </div> 

                        </div>

                        

                        <div class="col-md-2"> 

                            <div class="form-group">    

                                <label>Select Metal</label>

                                <select id="metal" style="width:100%;"></select>

                            </div> 

                        </div>

                        

                        <div class="col-md-2"> 

                            <div class="form-group">    

                            	<label>Select Category</label>

                                <select id="category" style="width: 100%;" ></select>

                            </div> 

                        </div>
                       
                    </div>

                    <div class="row"> 

                        
                        <div class="col-md-2"> 

                            <div class="form-group tagged">

                            <label>Report Type</label>

                                <select id="report_type" class="form-control">

                                    <option value="1" selected>Detailed</option>

                                    <option value="2">Summary</option>

                                </select>

                            </div> 

                            </div>


                             <div class="col-md-2 bt_code"> 
								    <label>BT Code</label>
										  <div class="form-group">
										  <input type="text" id="bt_code" class="form-control" placeholder="Enter BT Code">
										  </div>
								  </div> 

                        

                        <div class="col-md-2"> 

                            <label> </label>

                            <div class="form-group">

                                <button type="button" id="retagging_items_search" class="btn btn-info">Search</button>   

                            </div>

                        </div>

                        

                        <!-- <div class="col-md-1"> 

                            <label></label>

                            <div class="form-group">

                                <button type="button" id="add_to_cart" class="btn btn-primary">+Cart</button>   

                            </div>

                        </div> -->

                        
                        </div>

                    </div>

                 </div>

               </div> 

              </div> 

           </div> 

        

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

                 <table id="retagging_item_list"class="table table-striped table-bordered">

                    <thead>

                      <tr>

                        <th  rowspan="2">BT Code</th>

                        <th  rowspan="2">BT date</th>

                        <th  rowspan="2">Process date</th>

                        <th rowspan="2">Karigar Name</th>

                        <th colspan="3" style="text-align: center;">OPENING</th>

                        <th colspan="3" style="text-align: center;">RECEIPT</th>

                        <th colspan="4" style="text-align: center;">PURCHASE RETURN</th>

                        <th colspan="4" style="text-align: center;">METAL ISSUE </th>

                      </tr>

                      <tr>

                        <!-- <th >OP/PCS</th> -->

                        <th >OP/ GRS WT</th>

                        <th>OP/ NET WT</th>

                        <th >OP/ DIA WT</th>


                        

                        <!-- <th>I/W PCS</th> -->

                        <th>I/W GRS WT</th>

                        <th>I/W NET WT</th>

                        <th>I/W DIA WT</th>


                        

                        <th>RET REF NO</th>

                        <!-- <th>RET PCS</th> -->

                        <th>RET GRS WT</th>

                        <th>RET NET WT</th>

                        <th>RET DIA WT</th>

                        

                        <th>ISSUE REF NO</th>

                        <!-- <th>ISSUE PCS</th> -->

                        <th>ISSUE GRS WT</th>

                        <th>ISSUE NET WT</th>

                        <th>ISSUE DIA WT</th>



                      </tr>

                    </thead> 

                        <tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>

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





<!-- <div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog">

<div class="modal-content">

    <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <input  type="hidden" value="0" id="i_increment" />	

        <h4 class="modal-title" id="myModalLabel">Add to Cart</h4>

        Product  : <b><span id="product_name"></span> | </b>

        Design  : <b><span id="design_name"></span> | </b>

        Weight Range : <b><span id="weight_name"></span> | </b>

        Min Pcs : <b><span id="min_pcs"></span> | </b>

        Max Pcs : <b><span id="max_pcs"></span> | </b>

    </div>

    <div id="chit_alert" style="width: 92%;margin-left: 3%;"></div>

    <form id="order_cart">

    <div class="modal-body">

        

    </div>

    </form>

  <div class="modal-footer">

    <a href="#" id="create_order" class="btn btn-success">Add to Cart</a>

    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

  </div>

</div>

</div>

</div>
 -->




