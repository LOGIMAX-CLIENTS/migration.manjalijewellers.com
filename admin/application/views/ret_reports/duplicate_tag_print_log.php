@ -0,0 +1,249 @@
@ -0,0 +1,247 @@
<!-- Content Wrapper. Contains page content -->

<style>
  .remove-btn {

    margin-top: -168px;

    margin-left: -38px;

    background-color: #e51712 !important;

    border: none;

    color: white !important;

  }
</style>

<div class="content-wrapper">

  <!-- Content Header (Page header) -->

  <section class="content-header">

    <h1>

      Duplicate Tag - Print Log

    </h1>

    <ol class="breadcrumb">

      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

      <li><a href="#">MIS Reports</a></li>

      <li class="active">Duplicate Tag Print Log</li>

    </ol>

  </section>

  <!-- Main content -->

  <section class="content product">

    <!-- Default box -->

    <div class="box box-primary">

      <div class="box-header with-border">

        <h3 class="box-title">Duplicate Tagging Print Log</h3>

      </div>

      <div class="box-body">

        <!-- form container -->

        <div class="row">

          <div class="col-sm-12">

            <!-- Lot Details Start Here -->

            <div class="row">



              <div class="col-md-2">

                <div class="form-group">

                  <label><a data-toggle="tooltip" title="Branch">Select Branch<span class="error">*</span></a></label>

                  <select id="branch_select" class="form-control" required style="width:100%;"></select>

                  <input id="id_branch" name="id_branch" type="hidden" />

                </div>

              </div>

              <div class="col-md-2">
                <div class="form-group">
                  <?php $today = date("d/m/Y"); ?>
                  <label><a data-toggle="tooltip" title="Date">Select Date<span class="error">*</span></a></a></label>
                  <button class="btn btn-default btn_date_range" id="print-dt-btn" style="width:100%;">
                    <input type="hidden" id="print_date1" value="<?php echo date("Y/m/d"); ?>" />
                    <input type="hidden" id="print_date2" value="<?php echo date("Y/m/d"); ?>" />
                    <b><span class="selected_date" style="text-align: center;"> <?php echo $today." - ".$today ?>
                    </span></b>
                  </button>
                </div>
              </div>

              <div class="col-md-2">

                <div class="form-group">

                  <label><a data-toggle="tooltip" title="Enter Product">Select Product<span class="error">*</span></a>
                  </label>

                  <select id="prod_select" class="form-control" style="width:100%;"></select>

                  <input type="hidden" id="id_product" name="">

                </div>

              </div>

              <div class="col-md-2">

                <div class="form-group">

                  <label><a data-toggle="tooltip" title="Enter Design">Select Design</a> </label>

                  <select class="form-control" id="des_select" style="width:100%;"></select>

                </div>

              </div>

              <div class="col-md-2">

                <div class="form-group">

                  <label><a data-toggle="tooltip" title="Enter Design">Select Sub Design</a> </label>

                  <select class="form-control" id="sub_des_select" style="width:100%;"></select>

                </div>

              </div>

              <div class="col-md-2">

                <div class="form-group">

                  <label><a data-toggle="tooltip" title="Select Tag No">Select Tag Code</a></label>

                  <input type="text" class="form-control" id="tag_no" name="tag_no" placeholder="Search Tag Code">

                  <input id="tag_id" name="tagging[tag_id]" type="hidden" />

                  <div id="tagAlert" name=""></div>

                </div>
                
              </div>
            </div>

              <div class="row">
                <div class="col-md-2">
                  
                <div class="form-group">
                  
                  <label><a data-toggle="tooltip" title="Select Tag No">Select Old Tag Code</a></label>

                  <input type="text" class="form-control" id="old_tag_no" name="old_tag_no"
                  placeholder="Search Old Tag Code">
                  
                  <input id="old_tag_id" name="tagging[old_tag_id]" type="hidden" />
                  
                  <div id="tagAlert" name=""></div>
                  
                </div>

              </div>
              
            </div>

            
            <div class="row">
            
            <div class="col-sm-2" style="display:flex;align-items: center">

                <button class="btn btn-warning" id="get_print_log">Apply Filter</button>

              </div>

            </div>

            <p class="help-block"></p>

          </div> <!--/ Col -->

        </div> <!--/ row -->

        <div class="table-responsive">

          <table id="duplicate_print_log" class="table table-bordered table-striped">

            <thead>

              <tr>

                <th width="6%">Branch</th>

                <th width="4%">Tag Code</th>

                <th width="4%">Old Tag Code</th>

                <th width="6%">Product Name</th>

                <th width="5%">Design Name</th>

                <th width="5.5%">Sub Design Name</th>

                <th width="6%">Employee</th>

                <th width="3%">Date Printed On</th>

                <!-- <th width="5%">Printer</th> -->

              </tr>

            </thead>

            <tbody></tbody>

          </table>

        </div>

        <p class="help-block"> </p>

      </div>

      <div class="overlay" style="display:none;">

        <i class="fa fa-refresh fa-spin"></i>

      </div>

      <!-- /form -->

    </div>

  </section>

</div>

</div>

</div>
