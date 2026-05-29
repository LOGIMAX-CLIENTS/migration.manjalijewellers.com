<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/offcanvas.css" type="text/css" media="screen" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>


<style>
  .offcanvas-body label {
    white-space: nowrap;
    text-align: left;
  }
</style>
<script type="text/javascript">
  var $ = jQuery.noConflict();
  $(document).ready(function() {
      // Manage mandatory fields UI from config
      if (typeof customerattributes !== 'undefined' && Array.isArray(customerattributes.mantory)) {
          var configArray = customerattributes.mantory;
          function getMantoryValue(key) {
              var item = configArray.find(function(obj) { return obj.hasOwnProperty(key); });
              return item ? item[key] : "no";
          }
          function updateMandatoryUI(fieldId, configKey, isRadio = false) {
              var isMandatory = getMantoryValue(configKey) === "yes";
              var $slider = $('#demo');
              var $field = isRadio ? $slider.find('input[name="' + fieldId + '"]') : $slider.find('#' + fieldId);
              var $label = isRadio ? $field.closest('.form-group').find('label') : $slider.find('label[for="' + fieldId + '"]');

              // Fallback to finding label within the same form-group
              if (!isRadio && ($label.length === 0 || $label.attr('for') === "")) {
                  $label = $field.closest('.form-group').find('label');
              }

              // Special handling for Title select inside input-group
              if (fieldId === 'title') {
                  $label = $slider.find('#cus_first_name').closest('.form-group').find('label');
              }

              if (isMandatory) {
                  if ($label.length > 0 && $label.find('.error').length === 0) {
                      $label.append('<span class="error">*</span>');
                  }
                  $field.attr('required', true);
              } else {
                  if ($label.length > 0) {
                      $label.find('.error').remove();
                      $label.html($label.html().replace(/\s?\*$/, ''));
                      $label.html($label.html().replace(/<span.*?>\*<\/span>/g, ''));
                  }
                  $field.removeAttr('required');
              }
          }

          updateMandatoryUI('customer[vip]', 'is_vip', true);
          updateMandatoryUI('title', 'is_title');
          updateMandatoryUI('cus_first_name', 'is_cus_first_name');
          updateMandatoryUI('customer[gender]', 'is_cus_gender', true);
          updateMandatoryUI('cus_mobile', 'is_cus_mobile');
          updateMandatoryUI('cus_email', 'is_cus_email');
          updateMandatoryUI('country', 'is_country');
          updateMandatoryUI('state', 'id_state');
          updateMandatoryUI('city', 'id_city');
          updateMandatoryUI('address1', 'is_address1');
          updateMandatoryUI('address2', 'is_address2');
          updateMandatoryUI('address3', 'is_address3');
          updateMandatoryUI('pin_code_add', 'is_pin_code_add');
          updateMandatoryUI('sel_village', 'is_sel_village');
          updateMandatoryUI('profession', 'is_profession');
          updateMandatoryUI('date_of_birth', 'is_date_of_birth');
          updateMandatoryUI('date_of_wed', 'is_date_of_wed');
          updateMandatoryUI('gst_no', 'is_gst_no');
          updateMandatoryUI('pan', 'is_pan');
          updateMandatoryUI('aadharid', 'is_aadharid');
          updateMandatoryUI('dl', 'is_dl');
          updateMandatoryUI('pp', 'is_pp');
      }
  });

</script>
<script type="text/javascript">
  // Image preview — update the thumbnail when a file is chosen via the styled label
  $(document).on('change', '#cus_image', function () {
    var file = this.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#cus_img_preview').attr('src', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  });
</script>

<div class="offcanvas offcanvas-start" id="demo">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title">Add Customer</h4>
    <div class="header-buttons">
      <input type="hidden" name="cus[id_customer]" id="id_customer" value="">
      <a href="#" id="add_newcutomer" class="btn btn-success">Add</a>
      <button type="button" class="btn btn-warning Close_button" data-bs-dismiss="offcanvas">Close</button>
    </div>
  </div>
  <div class="offcanvas-body">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#tab_general" data-toggle="tab">GENERAL</a></li>
      <li><a href="#tab_kyc" data-toggle="tab">KYC</a></li>
    </ul>
    <div class="tab-content"><br />
      <?php
      $CI =& get_instance();
      $CI->load->model('ret_billing_model');
      $pan_verification = $CI->ret_billing_model->get_ret_settings('pan_verification');
      ?>
      <input type="hidden" id="pan_verification_slider" value="<?php echo $pan_verification; ?>">
      <div class="tab-pane active" id="tab_general">


        <div class="row">

          <div class="form-group">

            <label for="cus_gender" class="col-md-4 col-md-offset-1 ">VIP</label>

            <div class="col-md-6">

              <input type="radio" name="customer[vip]" id="vip1" value="1" class="minimal"> Yes

              <input type="radio" name="customer[vip]" id="vip0" value="0" class="minimal" checked="" required=""> No

              <p class="help-block cus_vip error"></p>

            </div>

          </div>

        </div>

        <div class="row">

          <div class="form-group">

            <label for="cus_first_name" class="col-md-4 col-md-offset-1 ">First Name <span class="error">*</span></label>

            <div class="input-group">

              <span class="input-group-addon">

                <select name="title" id="title" required>

                  <option value="none" disabled="" hidden=""></option>

                  <option value="Mr" selected>Mr</option>

                  <option value="Ms">Ms</option>

                  <option value="Mrs">Mrs</option>

                  <option value="Dr">Dr</option>

                  <option value="Prof">Prof</option>

                </select>

              </span>

              <input type="text" class="form-control" style="width:65%;" id="cus_first_name" name="cus[first_name]" placeholder="Enter customer first name" required>

            </div>

          </div>

        </div>

        <div class="row">

          <div class="form-group">

            <label for="cus_gender" class="col-md-4 col-md-offset-1 ">Gender</label>

            <div class="col-md-6">

              <input type="radio" name="customer[gender]" value="0" class="minimal" <?php if ($customer['gender'] == 0) { ?> checked <?php } ?>/>Male

              <input type="radio" name="customer[gender]" value="1" class="minimal" <?php if ($customer['gender'] == 1) { ?> checked <?php } ?>/>Female

              <input type="radio" name="customer[gender]" value="3" class="minimal" <?php if ($customer['gender'] == 3) { ?> checked <?php } ?>/>Others



              <p class="help-block cus_gender error"></p>

            </div>

          </div>

        </div>

        <div class="row">

          <div class="form-group">

            <label for="cus_mobile" class="col-md-4 col-md-offset-1 ">Mobile</label>

            <div class="col-md-6">

              <input type="number" class="form-control" id="cus_mobile" name="cus[mobile]" placeholder="Enter customer mobile">

              <p class="help-block cus_mobile error"></p>

            </div>

          </div>

        </div>

        <!-- GST No — moved here, right after Mobile -->
        <div class="row gst" style="display:none">

          <div class="form-group">

            <label for="" class="col-md-4 col-md-offset-1 ">GST No <span class="error">*</span></label>

            <div class="col-md-6">

              <input type="text" class="form-control" id="gst_no" name="cus[gst_no]" placeholder="Enter GST No">

              <p class="help-block cus_mobile"></p>

            </div>

          </div>

        </div>

        <div class="row">

          <div class="form-group">

            <label for="cus_email" class="col-md-4 col-md-offset-1 ">Email</label>

            <div class="col-md-6">

              <input type="text" class="form-control" id="cus_email" name="cus[cus_email]" placeholder="Enter Email ID">



              <p class="help-block cus_email error"></p>

            </div>

          </div>

        </div>

        <div class="row">

          <div class="form-group">

            <label for="" class="col-md-4 col-md-offset-1 ">Select Country <span class="error">*</span></label>

            <div class="col-md-6">

              <select class="form-control select-field" id="country" style="width:100%;" required></select>

              <input type="hidden" name="cus[id_country]" id="id_country">

            </div>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="" class="col-md-4 col-md-offset-1 ">Select State <span class="error">*</span></label>

            <div class="col-md-6">

              <select class="form-control select-field" id="state" style="width:100%;" required></select>

              <input type="hidden" name="cus[id_state]" id="id_state">

            </div>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="" class="col-md-4 col-md-offset-1 ">Select City <span class="error">*</span></label>

            <div class="col-md-6">

              <select class="form-control select-field" id="city" style="width:100%;" required></select>

              <input type="hidden" name="cus[id_city]" id="id_city">

            </div>



          </div>

        </div></br>


        <div class="row">

          <div class="form-group">

            <label for="address1" class="col-md-4 col-md-offset-1 ">Address1 <span class="error">*</span></label>

            <div class="col-md-6">

              <input class="form-control" id="address1" name="customer[address1]" value="" type="text" placeholder="Enter Address Here 1" required />

              <p class="help-block address1 error"></p>

            </div>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="address2" class="col-md-4 col-md-offset-1">Address2 <span class="error">*</span></label>

            <div class="col-md-6">

              <input class="form-control" id="address2" name="customer[address2]" placeholder="Enter Address Here 2" value="" type="text" required />

            </div>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="address3" class="col-md-4 col-md-offset-1">Address3</label>

            <div class="col-md-6">

              <input class="form-control titlecase" id="address3" name="customer[address3]" value="" type="text" placeholder="Enter Address Here 3" />

            </div>

          </div>

        </div></br>



        <div class="row">

          <div class="form-group">

            <label for="pincode" class="col-md-4 col-md-offset-1">Pin Code</label>

            <div class="col-md-6">

              <input class="form-control titlecase" id="pin_code_add" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' />
              <p class="help-block pincode error"></p>

            </div>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="" class="col-md-4 col-md-offset-1 ">Select Area</label>

            <div class="col-md-5">

              <select class="form-control" id="sel_village" style="width:100%;"></select>

              <input type="hidden" id="id_village">

            </div>

            <span class="input-group-btn">

              <button type="button" class="btn btn-success add_new_village"><i class="fa fa-plus"></i></button>

            </span>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="" class="col-md-4 col-md-offset-1 ">Select Profession</label>

            <div class="col-md-6">

              <select class="form-control" id="profession" style="width:100%;"></select>

              <input type="hidden" name="cus[profession]" id="professionval">

            </div>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="pincode" class="col-md-4 col-md-offset-1">Date of Birth</label>

            <div class="col-md-6">

              <input class="form-control ed_date_of_birth" id="date_of_birth" name="customer[date_of_birth]" value="<?php echo set_value('customer[date_of_birth]', $customer['date_of_birth']); ?>" type="text" />

              <p class="help-block pincode error"></p>

            </div>

          </div>

        </div></br>

        <div class="row">

          <div class="form-group">

            <label for="pincode" class="col-md-4 col-md-offset-1">Wedding Date</label>

            <div class="col-md-6">

              <input class="form-control ed_date_of_wed" id="date_of_wed" name="customer[date_of_wed]" value="<?php echo set_value('customer[date_of_wed]', $customer['date_of_wed']); ?>" type="text" />

              <p class="help-block pincode error"></p>

            </div>

          </div>

        </div></br>

        <!-- Upload Image — redesigned as a clean image card -->
        <div class="row">
          <div class="form-group">
            <label for="" class="col-md-4 col-md-offset-1">Photo</label>
            <div class="col-md-7">

              <!-- Image preview card -->
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                <div style="width:72px;height:72px;border-radius:8px;overflow:hidden;border:2px solid #e5e7eb;flex-shrink:0;background:#f3f4f6;">
                  <img src="<?php echo base_url('assets/img/default.png') ?>" class="img-thumbnail" id="cus_img_preview"
                    style="width:72px;height:72px;object-fit:cover;border:none;padding:0;border-radius:6px;" alt="Customer">
                </div>
                <div style="flex:1;">
                  <!-- Styled file picker -->
                  <label for="cus_image" style="display:inline-block;padding:5px 10px;font-size:11px;font-weight:600;background:#f3f4f6;border:1px solid #d1d5db;border-radius:4px;cursor:pointer;color:#374151;margin-bottom:5px;">
                    <i class="fa fa-upload" style="margin-right:4px;"></i> Choose Photo
                  </label>
                  <input id="cus_image" name="cus_img" accept="image/*" type="file" style="display:none;">
                  <br>
                  <button type="button" class="btn btn-default btn-xs" id="snap_shots"
                    style="font-size:11px;font-weight:600;border:1px solid #d1d5db;padding:4px 10px;">
                    <i class="fa fa-camera" style="margin-right:3px;"></i> Take Snapshot
                  </button>
                </div>
              </div>

              <!-- Webcam area (hidden by default) -->
              <div class="row" style="margin:0;">
                <div class="col-md-12" style="padding:0;">
                  <div id="my_camera"></div>
                  <input type="hidden" name="image" class="image-tag">
                </div>
              </div>

              <input type="hidden" id="customer_img" name="customer[customer_img]" value="<?php echo set_value('customer[customer_img]', $customer['cus_img']) ?>" />
              <p class="help-block cus_mobile"></p>
            </div>
          </div>
        </div>


        <!-- GST row moved to after Mobile above — removed from here -->

      </div>
      <div class="tab-pane" id="tab_kyc">

        <div class="row">
          <div class="form-group">
            <label for="cus_pan" class="col-md-4 col-md-offset-1 ">Pan <span class="error">*</span></label>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control pan_no" id="pan" name="cus[pan]" placeholder="Enter Pan ID" required>
                <?php if (isset($pan_verification) && $pan_verification == 1) { ?>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-primary" id="btn_verify_billing_pan">Verify</button>
                </span>
                <?php } ?>
              </div>
              <span id="status_verify_billing_pan" style="font-weight: bold; margin-left: 5px;"></span>
              <input type="hidden" id="billing_pan_verified_flag" value="0">
              <p class="help-block cus_email error" style="text-transform:uppercase"></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label for="cus_aadhar" class="col-md-4 col-md-offset-1 ">Aadhar</label>
            <div class="col-md-6">
              <input type="text" class="form-control" id="aadharid" name="cus[cus_aadhar]" maxlength="14" placeholder="Enter aadhar ID">
              <p class="help-block cus_email error"></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label for="cus_dl" class="col-md-4 col-md-offset-1 ">Driving License</label>
            <div class="col-md-6">
              <input type="text" class="form-control dl_no" id="dl" name="cus[cus_dl]" maxlength="15" placeholder="Enter Driving License No">
              <p class="help-block cus_email error"></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label for="cus_dl" class="col-md-4 col-md-offset-1 ">PassPort</label>
            <div class="col-md-6">
              <input type="text" class="form-control pp_no" id="pp" name="cus[cus_pp]" maxlength="15" placeholder="Enter Passport No" style="text-transform:uppercase">
              <p class="help-block cus_email error"></p>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- area modal -->

    <div class="modal fade" id="confirm-area" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

            <h4 class="modal-title" id="myModalLabel">Add Village</h4>

          </div>

          <div class="modal-body">






            <div class="row">

              <div class="form-group">

                <label for="pincode" class="col-md-3 col-md-offset-1">Pincode</label>

                <div class="col-md-6">

                  <input class="form-control titlecase" id="new_pincode" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' readonly required />

                </div>

              </div>

            </div></br>

            <div class="row">

              <div class="form-group">

                <label for="area" class="col-md-3 col-md-offset-1 ">Area<span class="error">*</span></label>

                <div class="col-md-6">

                  <input class="form-control" id="village" value="" type="text" placeholder="Enter Area Here " required />

                  <p class="help-block address1 error"></p>

                </div>

              </div>

            </div></br>



          </div></br>



          <div class="modal-footer">



            <a href="#" id="add_new_area" class="btn btn-success">Add</a>
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>



          </div>

        </div>

      </div>

    </div>


    <!-- area modal -->