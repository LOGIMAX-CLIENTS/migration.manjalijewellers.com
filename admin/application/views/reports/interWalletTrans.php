  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Wallet Transactions
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Wallet Transactions</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Wallet Transactions List</h3> <span id="total" class="badge bg-green"></span>     
                         
                </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                 
                   <div class="row">
                   
                    <div class="col-sm-12">
                                 <!-- Date and time range -->
                      <div class="form-group">
                        <div class="input-group">
                           <button class="btn btn-default btn_date_range" id="wallet_trans_date">
                            <i class="fa fa-calendar"></i> Date range picker
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                     </div><!-- /.form group -->
                     </div>
                    
                 
                                        
                   </div> 
                   <div class="row">
                       <?php if($this->session->userdata('branch_settings')==1){?>
                          <div class="col-md-4">
                              <div class="form-group" >
                              <label>Select Branch </label>
                              <select id="branch_select" class="form-control" style="width:200px;" ></select>
                              <input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
                            </div>
                          </div>
                        <?php }?>
                        <div class="col-sm-3">
                                     <!-- Date and time range -->
                          <div class="form-group">
                            <select class="form-control" id="filter_by">
                                  <option value="">Filter by</option>
                                  <option value="mobile">Mobile</option>
                                  <option value="name">Name</option>
                                  <option value="pincode">Pincode</option>
                                  <option value="billno">Bill No</option>
                            </select>
                         </div><!-- /.form group -->
                        </div>
                         <div class="col-sm-3">
                                     <!-- Date and time range -->
                          <div class="form-group">
                           <div style='display:none;' class='filter_by_ip'>
                                 <input type='text' class="form-control text" id="searchTerm" placeholder="Search"  name="search" />
                                   <br/>
                          </div>
                         </div><!-- /.form group -->
                        </div>
                         <div class="col-sm-2 filter_by_ip" style='display:none;'>
                           <div class="form-group" >
                            <input type="button" id="searchWalTrans" class="btn btn-default" value="submit">
                         </div>
                                            
                       </div> 
                </div>
                      <table id="interWalList" style="width:100% !important" class="table table-bordered table-striped text-center">
                      <thead>
                        <tr>
                          <tr>
                          <th>S.NO</th>
                          <th>Branch</th>
                          <th>Mobile</th>
                          <th>Name</th>
                          <th>Type</th>
                          <th>Bill Date</th>
                          <th>Trans date</th>
                          <th>Bill No</th>
                          <th>Category</th>
                          <th>Amount</th>
                          <th>Credit</th>
                          <th>Debit</th>
                        </tr>                                                        
                        </tr>
                      </thead> 

                   </table>
                  
                </div><!-- /.box-body -->
              <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
			  </div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


<!-- / modal -->  
