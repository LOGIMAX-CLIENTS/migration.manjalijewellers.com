<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Reports
       <small>TOBE Pending</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Reports</a></li>
      <li class="active">Tobe Pending</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
         
         <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Tobe Pending List</h3>  <span id="total_count" class="badge bg-green"></span>  
           
          </div>
           <div class="box-body">  
            <div class="row">
                <div class="col-md-offset-1 col-md-10">  
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
                              <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
                              <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
                          <?php }?> 
                          
                          <!-- <div class="col-md-3">
                          <div class="form-group">
                                   <div class="input-group">
                               <br>
                                <button class="btn btn-default btn_date_range"  id="rpt_date_picker">
                                               <i class="fa fa-calendar"></i> Date range picker
                                                     <i class="fa fa-caret-down"></i>
                                  </button>
                                      <span style="display:none;" id="rpt_from_date"></span>
                                                  <span style="display:none;" id="rpt_to_date"></span>
                                     </div>
                               </div>
                              </div> -->
                              <!-- /.form group -->
                              
                          <div class="col-md-2"> 
                              <label>Balance Zero</label>
                              <div class="form-group">
                                  <select id="balance_filter" class="form-control rpt_balance_filter">
                                      <option value="all">All</option>
                                      <option value="exclude_zero">Exclude Zero</option>
                                  </select>
                              </div>
                          </div>

                          <div class="col-md-2"> 
                              <label></label>
                              <div class="form-group">
                                  <button type="button" id="tobe_pending_search" class="btn btn-info">Search</button>   
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
                   <table id="tobe_issued_list" class="table table-bordered table-striped text-center">
                      <thead>
                        <tr>
                          <th>Branch</th>
                          <th>Customer</th>
                          <th>Mobile</th>
                          <th>Bill No</th>
                          <th>Bill Date</th>
                          <th>Total Bill Amount</th>
                          
                          <th>Due Amount</th>
                          <th>Due Date</th>
                          <th>Due Date Count</th>
                          <th>Paid Amount</th>
                          <th>Return Amount</th>
                          <th>Balance Amount</th>
                          <th>Details</th>
                        </tr>
                      </thead> 
                       <tbody></tbody>
                        <tfoot><tr>
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
                        </tr></tfoot>
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


