<style>
.stickyBlk {
    margin: 0 auto;
    top: 0;
     max-width: 1200px
    z-index: 999;
    background: #fff;
}

</style>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Reports
     <small>Item Delivery Report</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Reports</a></li>
    <li class="active">Item Delivery Report</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
       
       <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Item Delivery Report</h3>  <span id="total_count" class="badge bg-green"></span>  
         
        </div>
         <div class="box-body">  
          <div class="row">
              <div class="col-md-offset-2 col-md-8">  
              <div class="box box-default">  
               <div class="box-body">  
                   <div class="row">
                        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
                        <div class="col-md-3"> 
                            <div class="form-group tagged">
                                <label>Select Branch</label>
                                <select id="branch_select" class="form-control branch_filter"></select>
                            </div> 
                        </div> 
                        
                        <?php }else{?>
                        <div class="col-md-3"> 
                            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
                            <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
                        </div>
                        <?php }?> 
                        <div class="col-md-2">
                            <label for="">Status</label>
                            <select id="item_status" class="form-control" style="width:100%;">
                                <option value="" selected>All</option>
                                <option value="0" >Yet To Deliver</option>
                                <option value="2">Delivered</option>
                            </select>
                        </div>
                        <div class="col-md-2"> 
                            <label></label>
                            <div class="form-group">
                                <button type="button" id="item_delivery_search" class="btn btn-info">Search</button>   
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
               <div class="box box-info stock_details">
                <div class="box-header with-border">
                  <h3 class="box-title">Item Delivery Details</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="box-body">
                           <div class="table-responsive">
                              <table id="item_delivery_list" class="table table-bordered table-striped text-center">
                                 <thead>
                                    <tr>
                                        <th width="5%">Bill No</th>
                                        <th width="5%">Bill Date</th>
                                        <th width="5%">Branch</th>
                                        <th width="10%">Customer</th>
                                        <th width="10%">Mobile</th>
                                        <th width="5%">Tag Code</th>
                                        <th width="5%">Product</th>
                                        <th width="5%">Grswt</th>
                                        <th width="5%">Netwt</th>
                                        <th width="5%">Amount</th>
                                        <th width="5%">Status</th>
                                        <th width="5%">Delivered Date</th>
                                        <th width="5%">Delivered By</th>
                                    </tr>
                                </thead> 
                             <tbody>
                                <tfoot style="font-weight:bold;">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align:right;"></td>
                                    <td style="text-align:right;"></td>
                                    <td style="text-align:right;"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tfoot>
                             </tbody>
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


