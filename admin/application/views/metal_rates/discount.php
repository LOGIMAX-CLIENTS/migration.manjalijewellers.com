  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Rate Master
        <small>Updatemetal rates Discount</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"> Masters</a></li>
        <li class="active"><a href="#">Metal Rates Discount</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-body">

              <?php echo form_open(('admin_settings/update_discout'), array('id' => 'discount')); ?>

              <h4 class="page-header">Gold rate Discount</h4>

              <div class="row">

                <div class="col-sm-12">

                  <div class="col-sm-3">

                    <div class="form-group">

                      <label class="checkbox-inline">

                        <input type="checkbox" name="general[enableGoldrateDisc]" value="1" <?php if ($general['enableGoldrateDisc'] == 1) { ?>checked="true" <?php } ?> />

                        Allow Gold rate Discount 22k.

                      </label>
                    </div>

                  </div>

                  <div class="col-sm-9">

                    <label class="col-sm-5 col-sm-offset-1 "> Gold rate Discount Amount 22k.</label>

                    <div class="col-sm-2">

                      <div class="form-group">
                        <input type="number" style="width:100%" name="general[goldDiscAmt]" id="goldDiscAmt" value="<?php echo set_value('general[goldDiscAmt]', $general['goldDiscAmt']); ?>" />

                      </div>

                    </div>

                  </div>

                </div>

              </div>

              <div class="row">

                <div class="col-sm-12">

                  <div class="col-sm-3">

                    <div class="form-group">

                      <label class="checkbox-inline">

                        <input type="checkbox" name="general[enableGoldrateDisc_18k]" value="1" <?php if ($general['enableGoldrateDisc_18k'] == 1) { ?>checked="true" <?php } ?> />

                        Allow Gold rate Discount 18k.

                      </label>
                    </div>

                  </div>

                  <div class="col-sm-9">

                    <label class="col-sm-5 col-sm-offset-1 "> Gold rate Discount Amount 18k.</label>

                    <div class="col-sm-2">

                      <div class="form-group">
                        <input type="number" style="width:100%" name="general[goldDiscAmt_18k]" id="goldDiscAmt_18k" value="<?php echo set_value('general[goldDiscAmt_18k]', $general['goldDiscAmt_18k']); ?>" />

                      </div>

                    </div>

                  </div>

                </div>

              </div>

              <h4 class="page-header">Silver rate Discount</h4>

              <div class="row">

                <div class="col-sm-12">

                  <div class="col-sm-3">

                    <div class="form-group">

                      <label class="checkbox-inline">

                        <input type="checkbox" name="general[enableSilver_rateDisc]" value="1" <?php if ($general['enableSilver_rateDisc'] == 1) { ?>checked="true" <?php } ?> />

                        Allow Silver rate Discount.

                      </label>
                    </div>

                  </div>

                  <div class="col-sm-9">

                    <label class="col-sm-5 col-sm-offset-1 "> Silver rate Discount Amount.</label>

                    <div class="col-sm-2">

                      <div class="form-group">
                        <input type="number" step=".01" style="width:100%" name="general[silverDiscAmt]" id="silverDiscAmt" value="<?php echo set_value('general[silverDiscAmt]', $general['silverDiscAmt']); ?>" />

                      </div>

                    </div>

                  </div>

                </div>

              </div>


              <!-- /tab content -->

              <div class="box-footer clearfix">

                <?php if ($access['delete'] == 1) { ?>
                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
                <?php } ?>
                <?php if ($access['add'] == 1) { ?>
                  <button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
                <?php } ?>
              </div>

            </div><!-- /.box-body -->
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div><!-- /.box -->
        </div><!-- /.col -->
      </div><!-- /.row -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->



  <!-- modal -->

  <!-- / modal -->