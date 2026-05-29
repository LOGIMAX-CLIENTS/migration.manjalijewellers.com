 

  

  <!-- Content Wrapper. Contains page content -->

    

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Stock Report

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">Reports</a></li>

        <li class="active">Stock report</li>

      </ol>

    </section>

<style>
  .highlight-row{
											background-color: #bdbdbd !important;
											font-weight: bold !important;
										  }
  @media print {
    .prt_header {
        font-weight: bold !important;
        text-align: left;
    }
    /* You can also hide certain elements if needed */
    .no-print {
        display: none;
    }
}
  
</style>

    <!-- Main content -->

    <section class="content">

      <div class="row">

        <div class="col-xs-12">

           

           <div class="box box-primary">

             <div class="box-body">  

                   <div class="box box-info stock_details">

                    <div class="box-header with-border">

                      <div class="row">

                            <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

                            <div class="col-md-2"> 

                                <div class="form-group tagged">

                                    <label>Select Branch</label>

                                    <select id="branch_select" class="form-control branch_filter"></select>

                                </div> 

                            </div> 

                            <?php }else{?>

                                <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

                                <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

                            <?php }?> 

                            

                            <div class="col-md-2"> 

                                <div class="form-group">

                                    <label>Select Metal</label>

                                    <select id="metal" style="width: 100%;" multiple></select>

                                </div> 

                            </div>
                              <div class="col-md-2"> 

                                <div class="form-group">
                                    <div class="input-group">
                                      <label>Date</label>
                                      <br>
                                      <button class="btn btn-default btn_date_range" id="rpt_date_picker">
                                      <i class="fa fa-calendar"></i> Date range picker
                                      <i class="fa fa-caret-down"></i>
                                      </button>
                                      <span  style="display:none;" id="rpt_from_date"></span>
                                    <span  style="display:none;" id="rpt_to_date"></span>
                                      <span id="reportrange"></span>
                                    </div>
                                    </div>

                            </div>


                            <div class="col-md-2 pro" style="display:none;"> 

                                <label>Select Product</label>

                                <select id="prod_select" class="form-control" style="width:100%;"></select>

                            </div>



                            <div class="col-md-2 des" style="display:none;"> 

                                <label>Select Design</label>

                                <select id="des_select" class="form-control" style="width:100%;"></select>

                            </div>

                            <div class="col-md-2 des" style="display:none;"> 

                            <label>Select Sub Design</label>

                            <select id="sub_des_select" class="form-control" style="width:100%;"></select>

                            </div>

                            <div class="col-md-2 sect"> 

                                <label>Select Section</label>

                                <select id="section_select" class="form-control" style="width:100%;" multiple></select>

                            </div>



                           <div class="col-md-2"> 

                              <label>Group By</label>

                              <select id="stock_group_by" class="form-control" style="width:100%;">

                                  <option value="1">Section</option>

                                  <option value="2">Product</option>

                                  <option value="3">Design and Sub Design</option>

                               <!--    <option value="4">Branch</option>

                                  <option value="5">Metal</option>

                                  <option value="6">Vendor</option> -->

                              </select>

                            </div>

                            

 

                            <div class="col-md-1">
                              <label for=""></label>
                              <div class="form-group">
                                <input type="checkbox" name="" id="sec_out_check"> with Section out
                              </div>
                            </div>

                            

                           <!-- <div class="col-md-2"> 

                                <label>Select Metal</label>

                                <select id="metal" class="form-control" style="width:100%;"></select>

                            </div>

                            <div class="col-md-2"> 

                                <label>Select Category</label>

                                <select id="category" class="form-control" style="width:100%;"></select>

                            </div>

                            

                            <div class="col-md-2"> 

                                <label>Select Product</label>

                                <select id="prod_select" class="form-control" style="width:100%;"></select>

                            </div>

                            <div class="col-md-2"> 

                                <label>Group By</label>

                                <select id="select_group_by" class="form-control" style="width:100%;">

                                    <option value="1">Product</option>

                                    <option value="2">Category</option>

                                </select>

                            </div>-->



                            <div class="row">

                            <div class="col-md-1"> 

                                <label></label>

                                <div class="form-group">

                                    <button type="button" id="section_stock_search" class="btn btn-info">Search</button>   

                                </div>

                            </div>

                            <div class="col-md-1"> 
                                <label></label>
                                <div class="form-group">
                                    <button type="button" id="stock_detail_print" class="btn btn-warning"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                        </div>

                            

                        </div>

                       

                        

                    </div>

                    <div class="box-body">

                        <div class="row">

                            <div class="box-body">

                               <div class="table-responsive">

                                  <table id="section_stock_list" class="table table-bordered table-striped text-center">

                                     <thead>

                          <!--<tr>

                              <th colspan="1"></th>

                              <th colspan="3">Opening</th>

                            <th colspan="3">Inward</th>

                            <th colspan="3">Outward</th>

                            <th colspan="3">Closing</th>

                          </tr>-->

                          <tr>

                            <th width="10%">Section/Product</th>

                            <th width="10%">No.Tag</th>
                            <th width="10%">Op Stock</th>   
                            <th width="10%">Op Gross Wt</th>   
                            <th width="10%">Op Net Wt</th>
                            <th width="10%">Op Dia Wt</th>
                            <th width="10%">Op Gram Wt</th>
                            <th width="10%">Op Carat Wt</th>

                            <th width="10%">No.Tag</th>
                            <th width="10%">I/W Pcs</th>  
                            <th width="10%">I/W Gross Wt</th>  
                            <th width="10%">I/W Net Wt</th> 
                            <th width="10%">I/W Dia Wt</th>
                            <th width="10%">I/W Gram Wt</th> 
                            <th width="10%">I/W Carat Wt</th>

                            <th width="10%">No.Tag</th> 
                            <th width="10%">O/W Pcs</th>  
                            <th width="10%">O/W Gross Wt</th>  
                            <th width="10%">O/W Net Wt</th> 
                            <th width="10%">O/W Dia Wt</th>
                            <th width="10%">O/W Gram Wt</th> 
                            <th width="10%">O/W Carat Wt</th>

                            <th width="10%">No.Tag</th> 
                            <th width="10%">Closing Stock</th>  
                            <th width="10%">Closing Gross Wt</th>  
                            <th width="10%">Closing Net Wt</th> 
                            <th width="10%">Closing Dia Wt</th> 
                            <th width="10%">Closing Gram Wt</th> 
                            <th width="10%">Closing Carat Wt</th> 

                            <th width="10%">Intransit</th> 

                            <th width="10%">Closing Stock</th> 

                          </tr>

                        </thead> 

                        <tbody></tbody>

                                 </table>

                              </div>

                            </div> 

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

  



