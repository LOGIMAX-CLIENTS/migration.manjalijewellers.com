  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Wastage Discount Range
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Masters</a></li>
        <li class="active"> Wastage Discount</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Wastage Discount Range List</h3> <span id="total_va_disc" class="badge bg-green"></span>
              <!-- <a class="btn btn-success pull-right" id="add_wt" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a>  -->
              <a class="btn btn-success pull-right" id="add_va" href="<?php echo base_url('index.php/admin_ret_catalog/wastage_discount/new'); ?>"><i class="fa fa-user-plus"></i> Add</a>

            </div><!-- /.box-header -->
            <div class="box-body">

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
              <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                  <div id="chit_alert"></div>
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Filter Metal By</label>
                  <select id="metal" class="form-control">
                    <!-- <input type="hidden" id="va_metal" value=''> -->
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                </div>
              </div>
              <div class="table-responsive">
                <table id="va_disc_list" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Metal</th>
                      <th>From Wastage %</th>
                      <th>To Wastage %</th>
                      <th>Disc Value</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div><!-- /.box-body -->
          </div><!-- /.box -->
        </div><!-- /.col -->
      </div><!-- /.row -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->


  <!-- modal -->
  <div class="modal fade" id="confirm-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Delete Wastage Discount</h4>
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to delete this discount percentage record?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger btn-confirm">Delete</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->
  <!-- modal -->
  <!-- <div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Wastage Range</h4>
      </div>
  <div class="modal-body">
               
                	<div id="chit_alert"></div>
									 
                <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select Metal<span class="error">*</span></label>
                       <div class="col-md-4">
                       	    <select id="weight_prod" class="form-control" style="width:100%;"></select>
                       	    <input type="hidden" id="metal" value=''>
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>
                </div><p class="help-block"></p>
                
                 <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Value<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="number" class="form-control" id="name" name="name" placeholder="Weight Range Value" autocomplete="off"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>
                
                <div class="row">
				 	<div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">From Weight<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="number" step="any" class="form-control" id="from_weight" name="from_weight" placeholder="Enter From Weight"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div><p class="help-block"></p>
				 <div class="row">
				     <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">To Weight<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="number" step="any" class="form-control" id="to_weight" name="to_weight" placeholder="Enter To Weight"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 <div class="row">
				     <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">To Description<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="weight_desc" name="weight_desc" placeholder="Enter Description"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_weight" class="btn btn-success">Save and New</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
 / modal -->
  <!-- modal -->
  <div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Edit Wastage Range</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="row">
            <div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
          </div>
            <div class="form-group">
              <label for="" class="col-md-4 col-md-offset-1 ">Select Metal<span class="error">*</span></label>
              <div class="col-md-4">
                <select id="ed_metal" class="form-control" style="width:100%;"></select>
                <input type="hidden" id="edmetal">
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <p class="help-block"></p>

          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">From Wastage %<span class="error">*</span></label>
              <div class="col-md-4">
                <input type="hidden" id="edit-id" value="" />
                <input type="number" step="any" class="form-control" id="ed_from_va" name="wastage" placeholder="Enter From Wastage %">
              </div>
            </div>
          </div>
          <p class="help-block"></p>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-4 col-md-offset-1 ">To Wastage %<span class="error">*</span></label>
              <div class="col-md-4">
                <input type="hidden" id="edit-id" value="" />
                <input type="number" step="any" class="form-control" id="ed_to_va" name="wastage" placeholder="Enter To Wastage %">
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="" class="col-md-4 col-md-offset-1 ">Value<span class="error">*</span></label>
              <div class="col-md-4">
                <input type="number" class="form-control" id="ed_value" name="ed_value" placeholder="Wastage Range Value" autocomplete="off">
                <p class="help-block"></p>
              </div>
            </div>

          </div>
          <p class="help-block"></p>

        </div>
        <div class="modal-footer">
          <a href="#" id="update_wastage" class="btn btn-success">Update</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->