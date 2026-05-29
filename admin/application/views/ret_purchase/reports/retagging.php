  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

<!-- Content Header (Page header) -->

<section class="content-header">

  <h1>

    Retag Report


  </h1>


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

                                    <label>From branch</label>

                                    <select id="branch_select" style="width:100%;"></select>

                                </div> 

                                </div>
                
                                <div class="col-md-2"> 

                                    <div class="form-group">

                                        <label>Select Receipt Type</label>

                                        <select class="form-control" id="receipt_type" multiple>
                                        
                                          <option value="0" selected>All</option>

                                            <option value="2">Sales Return</option>

                                            <option value="3">Partly Sold</option>
                                            
                                            <option value="1">Old Metal</option>

                                            <option value="4">Non tag Return</option>
                                            
                                            <option value="5">H.O Other issue</option>

                                            <option value="6">Non tag Other issue</option>


                                        </select>

                                    </div>

                                    </div>

    

                        <div class="col-md-2"> 

                            <div class="form-group">    

                                <label>Select Metal</label>

                                <select id="select_metal" style="width:100%;" multiple></select>

                            </div> 

                        </div>

                        

                        <div class="col-md-2"> 

                            <div class="form-group">    

                            	<label>Select Category</label>

                                <select id="select_category" style="width: 100%;" multiple ></select>

                            </div> 

                        </div>
                        
                        <div class="col-md-2 bt_code"> 
                          <label>BT Code</label>
                            <div class="form-group">
                            <input type="text" id="bt_code" class="form-control" placeholder="Enter BT Code">
                            </div>
                        </div> 
                      </div>
                      <div class="row"> 
                        <div class="col-md-2"> 

                              <label> </label>

                              <div class="form-group">

                                  <button type="button" id="retagging_items_search" class="btn btn-info">Search</button>   

                              </div>

                          </div>
                       
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
                        
                        <th  rowspan="2">From Branch</th>

                        <th colspan="5" style="text-align: center;">OPENING</th>

                        <th colspan="5" style="text-align: center;">RECEIPT</th>

                        <th colspan="5" style="text-align: center;">PURCHASE RETURN</th>

                        <th colspan="5" style="text-align: center;">METAL ISSUE </th>
                        
                        <th colspan="5" style="text-align: center;">LOT </th>

                        <th colspan="5" style="text-align: center;">POCKET PROCESS</th>
                    
                        <th colspan="5" style="text-align: center;">CLOSING</th>

                      </tr>

                      <tr>


                        <th style="border-left: 1px solid black;">GRS WT</th>

                        <th>NET WT</th>

                        <th >DIA WT</th>

                        <th >GRAM WT</th>

                        <th >CARAT WT</th>


                        <th style="border-left: 1px solid black;">GRS WT</th>

                        <th>NET WT</th>

                        <th>DIA WT</th>

                        <th >GRAM WT</th>

                        <th >CARAT WT</th>



                        <!-- <th>RET PCS</th> -->

                        <th style="border-left: 1px solid black;">GRS WT</th>

                        <th>NET WT</th>

                        <th>DIA WT</th>

                        <th >GRAM WT</th>

                        <th >CARAT WT</th>


                        <!-- <th>ISSUE PCS</th> -->

                        <th style="border-left: 1px solid black;">GRS WT</th>

                        <th>NET WT</th>

                        <th>DIA WT</th>

                        <th >GRAM WT</th>

                        <th >CARAT WT</th>



                        <th style="border-left: 1px solid black;">GRS WT</th>

                        <th>NET WT</th>

                        <th>DIA WT</th>

                        <th >GRAM WT</th>

                        <th >CARAT WT</th>



                        <th style="border-left: 1px solid black;">GRS WT</th>

                        <th>NET WT</th>

                        <th>DIA WT</th>

                        <th >GRAM WT</th>

                        <th >CARAT WT</th>



                        <th style="border-left: 1px solid black;">GRS WT</th>

                        <th>NET WT</th>

                        <th>DIA WT</th>

                        <th >GRAM WT</th>

                        <th >CARAT WT</th>



                        </tr>

                    </thead> 
                    
                    <tfoot>
                        <tr style="font-weight:bold;">
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>

                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>


                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>

                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          
                        </tr>
                    </tfoot>

                        
                 </table>

              </div>

               </div>

           </div>
           
           <div class="overlay" style="display:none">

          <i class="fa fa-refresh fa-spin"></i>

        </div>

        </div><!-- /.box-body -->

        

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




