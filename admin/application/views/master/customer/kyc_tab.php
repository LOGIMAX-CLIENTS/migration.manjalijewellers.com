<?php if (!empty($kyc_master_list)): ?>
<div class="tab-pane" id="tab_kyc"> 
    <legend>KYC Details</legend>
	<div id="success_kyc" value=''>
		<div class=""  align="center">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong id="suc_mgs_kyc"> </strong>
		</div> 
	</div>
    
    <?php if (isset($kyc_context_message) && !empty($kyc_context_message)): ?>
    <div class="alert" style="margin-top: 15px; background-color: #f4f3fb; color: #4b4887; border: 1px solid #c2bfe4; border-left: 5px solid #605ca8;">
        <strong><i class="fa fa-info-circle"></i> Document Requirement:</strong> <?php echo $kyc_context_message; ?>
    </div>
    <?php endif; ?>
    
    <input type="hidden" id="kyc_rule_logic" value="<?php echo isset($active_rule_logic) ? $active_rule_logic : 0; ?>">
	
    <ul class="nav nav-tabs">
        <?php $first = true; foreach($kyc_master_list as $kyc) { ?>
            <li class="<?php echo $first ? 'active' : ''; ?>">
                <a href="#tab_kyc_<?php echo $kyc['id_mas_kyc']; ?>" data-toggle="tab"><?php echo $kyc['name']; ?></a>
            </li>
        <?php $first = false; } ?>
    </ul>
    
    <div class="tab-content"><br/>
        
        <!-- Shared modal webcam container for this tab -->
        <div class="modal fade" id="kyc_webcam_modal" tabindex="-1" role="dialog" aria-labelledby="kycWebcamModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" onclick="close_kyc_camera()" aria-label="Close" style="margin-top:-10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="kycWebcamModalLabel">Take Snapshot</h4>
              </div>
              <div class="modal-body text-center">
                <div id="modal_kyc_camera" style="width:320px; height:240px; margin:0 auto; border:2px solid #ccc; background:#000;"></div>
              </div>
              <div class="modal-footer" style="text-align: center;">
                <button type="button" id="btn_actually_snap" class="btn btn-success"><i class="fa fa-camera"></i> Snap It</button>
                <button type="button" class="btn btn-default" onclick="close_kyc_camera()"><i class="fa fa-times"></i> Cancel</button>
              </div>
            </div>
          </div>
        </div>

        <?php 
        $first = true; 
        foreach($kyc_master_list as $kyc) { 
        ?>
            <div class="tab-pane <?php echo $first ? 'active' : ''; ?>" id="tab_kyc_<?php echo $kyc['id_mas_kyc']; ?>">
                
                <div class="row">
                    <?php 
                    foreach($kyc['attributes'] as $attr) {
                        $input_name = "kyc_dynamic[" . $kyc['id_mas_kyc'] . "][" . $attr['attribute'] . "]"; 
                        if ($attr['attr_input'] == 'varchar' || $attr['attr_input'] == 'number') { ?>
                            <div class="col-md-5">
                                <label for=""><?php echo $attr['attr_label']; ?></label> 
                                <?php 
                                $val = '';
                                if (isset($customer_kyc) && isset($customer_kyc[$kyc['id_mas_kyc']])) {
                                    $val = $customer_kyc[$kyc['id_mas_kyc']]['number'];
                                }
                                $is_req = ($attr['is_mandatory'] && (empty($active_rule_logic) || $active_rule_logic == 0)) ? 'required' : '';
                                ?>
                                <div class='form-group'>
                                    <input type="text" class="form-control kyc-dynamic-input" name="<?php echo $input_name; ?>" value="<?php echo htmlspecialchars($val); ?>"
                                        autocomplete="off" <?php echo $is_req; ?> 
                                        <?php if($attr['attr_length']) echo 'maxlength="'.$attr['attr_length'].'"'; ?> 
                                        <?php if(!empty($attr['reg_expression'])) echo 'data-regex="'.htmlspecialchars($attr['reg_expression']).'" pattern="'.htmlspecialchars($attr['reg_expression']).'"'; ?> />
                                </div> 
                            </div>
                    <?php } } ?>
                </div>

                <!-- Images: Side-by-side compact grid layout -->
                <div class="row">
                <?php 
                foreach($kyc['attributes'] as $attr) {
                    if ($attr['attr_input'] == 'image' || $attr['attr_input'] == 'file') { 
                        $input_name = "kyc_dynamic[" . $kyc['id_mas_kyc'] . "][" . $attr['attribute'] . "]";
                        $file_field = "kyc_dynamic_file_" . $kyc['id_mas_kyc'] . "_" . $attr['attribute'];
                        $preview_id = "kyc_preview_" . $kyc['id_mas_kyc'] . "_" . $attr['attribute'];
                        $hidden_id = "kyc_hidden_" . $kyc['id_mas_kyc'] . "_" . $attr['attribute'];
                        $file_id = "kyc_file_" . $kyc['id_mas_kyc'] . "_" . $attr['attribute'];
                ?>
                    <div class="col-md-6 col-sm-6" style="margin-bottom: 15px;">
                        <label style="font-size: 13px; font-weight: 600; color: #555; margin-bottom: 8px; display: block;">
                            <?php echo $attr['attr_label']; ?>
                        </label>
                        <?php $mandatory_class = ($attr['is_mandatory'] == 1 && (empty($active_rule_logic) || $active_rule_logic == 0)) ? 'kyc-mandatory-file-group' : 'kyc-optional-file-group'; ?>
                        <div class="<?php echo $mandatory_class; ?>" data-kyc-mas="<?php echo $kyc['id_mas_kyc']; ?>" style="position:relative; width:160px; display:inline-block; border:1px solid #ddd; padding:5px; border-radius:4px; background: #fafafa;">
                            <!-- Remove Button in top right corner -->
                            <button type="button" class="btn btn-danger btn-xs" style="position:absolute; top:-8px; right:-8px; border-radius:50%; z-index:10; width:20px; height:20px; padding:0; line-height:20px; font-size:10px;" onclick="
                                document.getElementById('<?php echo $preview_id; ?>').src = '<?php echo base_url(); ?>assets/img/default.png';
                                document.getElementById('<?php echo $file_id; ?>').value = '';
                                document.getElementById('<?php echo $hidden_id; ?>').value = '';
                            ">
                                <i class="fa fa-times"></i>
                            </button>
                            
                            <?php
                            $img_src = base_url() . "assets/img/default.png";
                            $existing_val = '';
                            if (isset($customer_kyc) && isset($customer_kyc[$kyc['id_mas_kyc']])) {
                                $k_data = $customer_kyc[$kyc['id_mas_kyc']];
                                if (strpos($attr['attribute'], 'front') !== false && !empty($k_data['img_url'])) {
                                    $img_src = $k_data['img_url'];
                                    $existing_val = 'existing';
                                } else if (strpos($attr['attribute'], 'back') !== false && !empty($k_data['back_img_url'])) {
                                    $img_src = $k_data['back_img_url'];
                                    $existing_val = 'existing';
                                } else if (!empty($k_data['document_url'])) {
                                    $img_src = $k_data['document_url'];
                                    $existing_val = 'existing';
                                }
                            }
                            ?>
                            <img src="<?php echo $img_src; ?>" alt="preview" style="width: 100%; height: 100px; object-fit: contain; margin-bottom: 8px; background: #fff;" id="<?php echo $preview_id; ?>">
                            
                            <div style="text-align:center;">
                                <input type="hidden" name="<?php echo $input_name; ?>" id="<?php echo $hidden_id; ?>" value="<?php echo $existing_val; ?>">
                                <input type="file" style="display:none;" id="<?php echo $file_id; ?>" accept="image/*,application/pdf" onchange="
                                    var file = this.files[0];
                                    if (file) {
                                        document.getElementById('<?php echo $preview_id; ?>').src = window.URL.createObjectURL(file);
                                        var reader = new FileReader();
                                        reader.onload = function(e) {
                                            document.getElementById('<?php echo $hidden_id; ?>').value = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                ">
                                <button type="button" class="btn btn-primary btn-xs" onclick="document.getElementById('<?php echo $file_id; ?>').click();">
                                    <i class="fa fa-folder-open"></i> Browse
                                </button>

                                <button type="button" class="btn btn-warning btn-xs" onclick="open_kyc_camera('<?php echo $preview_id; ?>', '<?php echo $hidden_id; ?>')">
                                    <i class="fa fa-camera"></i> Webcam
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } } ?>
                </div>
                
            </div>
        <?php $first = false; } ?>
    </div>
</div>

<?php endif; ?>
