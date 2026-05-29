      <div class="content-wrapper">
          <style>
              #toast-container>.toast {
                  opacity: 1 !important;
                  box-shadow: 0 0 12px #999;
                  background-image: none !important;
                  padding: 15px 15px 15px 15px !important;
              }

              .toast-error {
                  background-color: #bd362f !important;
              }
          </style>

          <!-- Content Header (Page header) -->
          <section class="content-header">
              <h1>
                  Bank Ledger Master
                  <small>Master</small>
              </h1>
              <ol class="breadcrumb">
                  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="<?php echo base_url('settings/ledger/List'); ?>">Master</a></li>
                  <li class="active">Bank Ledger </li>
              </ol>
          </section>

          <!-- Main content -->
          <section class="content">

              <!-- Default box -->
              <div class="box">
                  <div class="box-header with-border">
                      <h3 class="box-title">Bank Ledger - <?php echo (isset($ledger['id_ledger']) ? 'Edit' : 'Add'); ?></h3>
                      <div class="box-tools pull-right">
                          <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                          <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                      </div>
                  </div>
                  <div class="box-body">
                      <div class="">
                          <form action="<?php echo base_url('settings/ledger/' . (isset($ledger['id_ledger']) ? 'Update/' . $ledger['id_ledger'] : 'Save')); ?>" method="post" id="ledger_form" data-banks='<?php echo json_encode($banks); ?>' data-paymodes='<?php echo json_encode($paymodes); ?>'>
                              <input type="hidden" id="id_ledger" name="id_ledger" value="<?php echo isset($ledger['id_ledger']) ? $ledger['id_ledger'] : ''; ?>">
                              <div class="row">
                                  <div class="form-group">
                                      <label for="ledger_name" class="col-md-2 col-md-offset-1 ">Ledger Name<span class="text-danger">*</span></label>
                                      <div class="col-md-4">

                                          <input type="text" class="form-control" id="ledger_name" name="ledger_name" value="<?php echo isset($ledger['ledger_name']) ? $ledger['ledger_name'] : ''; ?>" placeholder="Ledger Name" required="true" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32) || (event.charCode > 47 && event.charCode < 58) || (event.charCode == 45)">

                                          <p class="help-block"></p>

                                      </div>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="form-group">
                                      <label for="opening_balance" class="col-md-2 col-md-offset-1 ">Opening Balance<span class="text-danger">*</span></label>
                                      <div class="col-md-4">
                                          <input type="number" step="0.01" min="0" class="form-control" id="opening_balance" name="opening_balance" value="<?php echo isset($ledger['opening_balance']) ? $ledger['opening_balance'] : '0.00'; ?>" placeholder="0.00" required="true" onkeypress="return event.charCode != 45">
                                          <p class="help-block"></p>

                                      </div>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="form-group">
                                      <label for="opening_date" class="col-md-2 col-md-offset-1 ">Opening Date<span class="text-danger">*</span></label>
                                      <div class="col-md-4">
                                          <input type="date" class="form-control" id="opening_date" name="opening_date" value="<?php echo isset($ledger['opening_date']) ? $ledger['opening_date'] : ''; ?>" required="true">
                                          <p class="help-block"></p>
                                      </div>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="form-group">
                                      <label for="min_balance" class="col-md-2 col-md-offset-1 ">Minimum Balance</label>
                                      <div class="col-md-4">
                                          <input type="number" step="0.01" min="0" class="form-control" id="min_balance" name="min_balance" value="<?php echo isset($ledger['min_balance']) ? $ledger['min_balance'] : '0.00'; ?>" placeholder="0.00" onkeypress="return event.charCode != 45">
                                          <p class="help-block"></p>
                                      </div>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="form-group">
                                      <label for="bank_ids" class="col-md-2 col-md-offset-1 ">Bank Accounts Mapping</label>
                                      <div class="col-md-4">
                                          <select name="bank_ids[]" id="bank_ids" class="form-control select2" multiple="multiple" data-placeholder="Select Bank Accounts" style="width: 100%;">
                                              <?php foreach ($banks as $bank) {
                                                    $selected = '';
                                                    if (!empty($ledger['mappings'])) {
                                                        foreach ($ledger['mappings'] as $mapping) {
                                                            if ($mapping['type'] == 'BANK' && $mapping['reference_id'] == $bank['id_bank']) {
                                                                $selected = 'selected';
                                                                break;
                                                            }
                                                        }
                                                    }
                                                ?>
                                                  <option value="<?php echo $bank['id_bank']; ?>" <?php echo $selected; ?>>
                                                      <?php echo $bank['bank_name'] . ' (' . $bank['acc_number'] . ')'; ?>
                                                  </option>
                                              <?php } ?>
                                          </select>
                                          <p class="help-block"></p>
                                      </div>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="form-group">
                                      <label for="paymode_ids" class="col-md-2 col-md-offset-1 ">POS Devices Mapping</label>
                                      <div class="col-md-4">
                                          <select name="paymode_ids[]" id="paymode_ids" class="form-control select2" multiple="multiple" data-placeholder="Select POS Devices" style="width: 100%;">
                                              <?php foreach ($devices as $device) {
                                                    $selected = '';
                                                    if (!empty($ledger['mappings'])) {
                                                        foreach ($ledger['mappings'] as $mapping) {
                                                            if ($mapping['type'] == 'PAYMODE' && $mapping['reference_id'] == $device['id_device']) {
                                                                $selected = 'selected';
                                                                break;
                                                            }
                                                        }
                                                    }
                                                ?>
                                                  <option value="<?php echo $device['id_device']; ?>" <?php echo $selected; ?>>
                                                      <?php echo $device['device_name']; ?>
                                                  </option>
                                              <?php } ?>
                                          </select>
                                          <p class="help-block"></p>
                                      </div>
                                  </div>
                              </div>

                              <br />
                              <div class="row col-xs-12">
                                  <div class="box box-default"><br />
                                      <div class="col-xs-offset-5">
                                          <button type="button" id="save_ledger" class="btn btn-primary">Save</button>
                                          <a href="<?php echo base_url('index.php/settings/ledger/list'); ?>" class="btn btn-default btn-cancel">Cancel</a>

                                      </div> <br />
                                  </div>
                              </div>

                          </form>
                      </div>
                  </div><!-- /.box-body -->
                  <div class="box-footer">

                  </div><!-- /.box-footer-->
              </div><!-- /.box -->


          </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
