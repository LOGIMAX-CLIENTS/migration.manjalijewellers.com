  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

      <!-- Content Header (Page header) -->

      <section class="content-header">

          <h1>

              Section Transfer Report

              <small>Section Transfer Report</small>

          </h1>

          <ol class="breadcrumb">

              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

              <li><a href="#">Stock Report</a></li>

              <li class="active">Section Transfer</li>

          </ol>

      </section>



      <!-- Main content -->

      <section class="content">

          <div class="row">

              <div class="col-xs-12">



                  <div class="box box-primary">

                      <div class="box-body">



                          <div class="row">

                              <div class="col-xs-12">

                                  <!-- Alert -->

                                  <?php

                                    if ($this->session->flashdata('chit_alert')) {

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

                              <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>

                                  <div class="col-md-2">

                                      <div class="form-group tagged">

                                          <label>Select Branch</label>

                                          <select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>

                                      </div>

                                  </div>

                              <?php } else { ?>

                                  <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">

                                  <input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">

                              <?php } ?>



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

                              <div class="col-md-2">

                                  <div class="form-group">

                                      <label>Select Product</label>

                                      <select id="prod_select" class="form-control" style="width:100%;"></select>

                                      <input type="hidden" id="id_product">

                                  </div>

                              </div>

                              <div class="col-md-2">

                                  <div class="form-group">

                                      <label>From Section</label>

                                      <select id="select_frm_section" class="form-control" style="width:100%;"></select>

                                      <input type="hidden" id="id_from_sec" value=''>

                                  </div>

                              </div>

                              <div class="col-md-2">

                                  <div class="form-group">

                                      <label>To Section</label>

                                      <select id="select_to_section" class="form-control" style="width:100%;"></select>

                                      <input type="hidden" id="id_to_sec" value=''>

                                  </div>

                              </div>

                              <div class="col-md-2">

                                  <div class="form-group">

                                      <label>Search Type</label>
                                      <select id="sec_rpt_filter_by" class="form-control" style="width:100%;">
                                    <option value="1">Tag</option>
                                    <option value="2">Non -Tag</option>
                                </select>
                                   

                                  </div>

                              </div>



                              <div class="col-md-2">

                                  <label></label>

                                  <div class="form-group">

                                      <button type="button" id="section_trans_report_search" class="btn btn-info">Search</button>

                                  </div>

                              </div>



                          </div>



                          <div class="table-responsive">

                              <table id="section_transfer_list" class="table table-bordered table-striped text-center">

                                  <thead>

                                      <tr>

                                          <th>#</th>

                                          <th width="10%;">Product</th>

                                          <th>Tag No</th>

                                          <th>Pcs</th>

                                          <th>Gwt</th>

                                          <th>Nwt</th>

                                          <th>Dia Pcs</th>

                                          <th>Dia Wt</th>

                                          <th>SaleValue</th>

                                          

                                          <th>From Section</th>

                                          <th>To Section</th>

                                          <th>Transfered By</th>
                                         <th>Transfer Date</th>
                                          <th>Trans Time</th>

                                      </tr>

                                  </thead>

                                  <tbody>



                                  </tbody>

                              </table>

                          </div>

                      </div><!-- /.box-body -->

                      <div class="overlay" style="display:none">

                          <i class="fa fa-refresh fa-spin"></i>

                      </div>



                  </div><!-- /.col -->

              </div><!-- /.row -->

      </section><!-- /.content -->

  </div><!-- /.content-wrapper -->