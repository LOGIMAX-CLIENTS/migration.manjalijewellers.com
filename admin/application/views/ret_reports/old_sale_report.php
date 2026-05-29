  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <h1>
              Old Sale Report
          </h1>
          <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#">Inventory</a></li>
              <li class="active">Old Sale Report</li>
          </ol>
      </section>

      <!-- Main content -->
      <section class="content">
          <div class="row">
              <div class="col-xs-12">

                  <div class="box box-primary">
                      <div class="box-header with-border"><span id="old_Sale_report
                              class="badge bg-green"></span>
                      </div>
                      <div class="box-body">
                          <div class="row">
                              <div class="col-md-offset-2 col-md-8">
							  <div class="box box-default">
                                      <div class="box-body">
									  
									  
                                          <div class="row">
										  
                               <div class="form-group">
                        <label for="" class="col-md-2">Branch<span class="error">*</span></label>
                            <div class="col-md-3">
                                <?php if($this->session->userdata('id_branch')=='')  {  ?>
                                     <select id="branch_select" style="width:100%;" required tabindex="1"></select>
                                <?php } else  { ?>
                                    <select id="branch_select" style="width:100%;" required disabled></select>
                                    <?php  } ?>
                            </div>
                               </div>
							   </div>
							   <br>
							   <div class = "row">
							   <div class="form-group">
                <label for="collection_desc" class="col-md-2">Import Type
					   </label>
			<div class="col-md-3">
			  <input type="radio" class = "is_check_import_type" id = "click_label_yes" name="is_check_import_type" value="1"><label for="click_label_yes">&nbsp;&nbsp;New Tags</label>&nbsp;&nbsp;
			  &nbsp;&nbsp;
			  <input type="radio" class = "is_check_import_type" id = "click_label_no" name="is_check_import_type" value="2"><label for="click_label_no">&nbsp;&nbsp;Old Tags</label>
			</div>	
			</div>
							   </div>
							   <div class="row">
							   <div class="form-group">
							  
                                                  <form id="upload_csv" method="post" enctype="multipart/form-data">
         
		  <label for="" class="col-md-2" style="padding-top:28px;">Choose File<span class="error">*</span></label>
		  
          <div class="col-md-3">
              <input type="file" name="csv_file" id="csv_file" required="required" accept=".xlsx" style="margin-top:30px; !important"/>
			  <span class="error">*Xlsx File Only Support</span>
          </div>
          <div class="col-md-3">
            <?php if($access['add']==1){ ?>
              <button name="upload" id="upload" value="Upload" onclick="uploadFile_new()" class="btn btn-success" style="margin-top:27px;margin-left:72px;">Upload</button>
              <!-- onclick="reloads()" -->
               <?php }?>
          </div>
          <div style="clear:both"></div>
        </form>
                                             </div>
											   </div>
										   
                                              </div>
                                          </div>
                                  <div class="box box-default">
                                      <div class="box-body">
									  
									  
                                          <div class="row">

                                              <div class="col-md-3">
                                                  <div class="form-group">
                                                      <button class="btn btn-default" id="old_tag_report_date"
                                                          style="margin-top: 20px;">
                                                          <span style="display:none;" id="old_tag_report_date1"></span>
                                                          <span style="display:none;" id="old_tag_report_date2"></span>
                                                          <i class="fa fa-calendar"></i> Date range picker
                                                          <i class="fa fa-caret-down"></i>
                                                      </button>
                                                  </div>
                                              </div>

                                             

                                              <div class="col-md-2">
                                                  <label></label>
                                                  <div class="form-group">
                                                      <button type="button" id="old_tag_report_search"
                                                          class="btn btn-info">Search</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="box box-info stock_details">
                              <div class="box-header with-border">
                                  <h3 class="box-title">Old Sale Report</h3>
                                  
                              </div>
                              <div class="box-body">
                                  <div class="row">
                                      <div class="box-body">
                                          <div class="table-responsive">
                                              <table id="old_tag_report_list"
                                                  class="table table-bordered table-striped text-center">
                                                  <thead>
                                                      <tr>
                                                          <th>Import Date</th>
                                                          <th>Total Tags</th>
                                                          <th>Sold Marked</th>
                                                          <th>Mismatched Tags</th>
														  <th>Branch</th>
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