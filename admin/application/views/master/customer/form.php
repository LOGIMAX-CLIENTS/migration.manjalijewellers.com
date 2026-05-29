<style>
    .table-responsive {
        overflow-x: auto;
        white-space: nowrap;
    }

    .table input[type="text"], .table input[type="file"] {
        min-width: 160px;
    }

    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    /* .d-none {
        display: none;
    } */

</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Customer
            <small>Complete profile</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
            <li class="active">Add Customer</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">

        <!-- <?php echo "<pre>";
        print_r($bank_details);
        ?> -->
        
        <!-- form -->
        <?php echo form_open_multipart(($customer['id_customer'] != NULL && $customer['id_customer'] > 0 ? 'customer/update/' . $customer['id_customer'] : 'customer/save'), array('id' => 'cus_create')); ?>
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Profile</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                            class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12">
                    <ul class="nav nav-pills nav-stacked col-md-2">
                        <li class="active"><a href="#tab_1" data-toggle="pill">Personal</a></li>
                        <li><a href="#tab_5" id="upload_img" data-toggle="pill">Upload Image</a></li>
                        <!--//webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB -->
                        <li><a href="#tab_2" id="others_tab" data-toggle="pill">Others</a></li>
                        <?php if (isset($customer['id_customer'])) { ?>
                            <li><a href="#tab_3" style='display:none;' data-toggle="pill">Associate Customer</a></li>
                            <?php if ($customer['cus_type'] == 2) { ?>
                                <li><a href="#tab_4" id="company_user" data-toggle="pill">Company Users</a></li>
                            <?php }
                        } ?>
                    </ul>
                    <?php if ($customer['id_customer']) { ?>
                        <input type="hidden" id="edit_id_cus_id" class="cus_id"
                            value="<?php echo set_value('customer[id_customer]', $customer['id_customer']); ?>" />
                        <input type="hidden" id="edit_id_cus_fname"
                            value="<?php echo set_value('customer[firstname]', $customer['firstname']); ?>" />
                        <input type="hidden" id="edit_id_cus_lname"
                            value="<?php echo set_value('customer[lastname]', $customer['lastname']); ?>" />
                        <input type="hidden" id="edit_id_cus_mob"
                            value="<?php echo set_value('customer[mobile]', $customer['mobile']); ?>" />
                        <input type="hidden" id="edit_id_cus_email"
                            value="<?php echo set_value('customer[email]', $customer['email']); ?>" />
                        <input type="hidden" id="edit_id_cus_address1"
                            value="<?php echo set_value('customer[address1]', $customer['address1']); ?>" />
                    <?php } ?>
                    <!-- esakki #14-12 -->
                    <input id="emp_branch" type="hidden" value="<?php echo $this->session->userdata('id_branch'); ?>" />
                    <div class="tab-content col-md-10">
                        <div class="tab-pane active" id="tab_1">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class='form-group'>
                                            <label for="gender">Customer Type <span class="error">*</span></label>
                                            <div class="form-group">
                                                <p class="help-block"></p>
                                                <input type="radio" class="cus_type" name="customer[cus_type]" value="1"
                                                    class="minimal" <?php if ($customer['cus_type'] == 1) { ?> checked
                                                    <?php } ?> required />Individual
                                                <input type="radio" class="cus_type" name="customer[cus_type]" value="2"
                                                    class="minimal" <?php if ($customer['cus_type'] == 2) { ?> checked
                                                    <?php } ?> />Company
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Send Promotion SMS </label>
                                            <input type="checkbox" id="show_gift_article" class="switch"
                                                data-on-text="YES" data-off-text="NO" name="customer[send_promo_sms]"
                                                value="1" <?php if ($customer['send_promo_sms'] == 1) { ?> checked="true"
                                                <?php } ?> />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>VIP Customer </label>
                                            <input type="checkbox" id="vip_customer" class="switch-small"
                                                data-on-text="YES" data-off-text="NO" name="customer[is_vip]" value="1"
                                                <?php if ($customer['is_vip'] == 1) { ?> checked="true" <?php } ?> />
                                        </div>
                                    </div>
                                    <?php if (isset($customer['id_customer'])) { ?>
                                        <div class="col-md-2">
                                            <div class="form-group pull-right">
                                                <label>Active</label>
                                                <input type="checkbox" id="active" class="switch" data-on-text="YES"
                                                    data-off-text="NO" name="customer[active]" value="1" <?php if ($customer['active'] == 1) { ?> checked="true" <?php } ?> />
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class='row'>
                                    <div class='col-sm-2'>
                                        <div class='form-group'>
                                            <label for="customer_title"> <a data-toggle="tooltip"><span
														id="title_name">Title</span> </a><span
													class="error">*</span></label>
											<select class="form-control" id="title_select" required="true">
												<!-- <option></option> -->
												<option value="Mr" <?php if ($customer['title'] == "Mr") { ?> selected <?php } ?>>Mr</option>
												<option value="Ms" <?php if ($customer['title'] == "Ms") { ?> selected <?php } ?>>Ms</option>
                                                <option value="Mrs" <?php if ($customer['title'] == "Mrs") { ?> selected
                                                    <?php } ?>>Mrs</option>
                                            </select>
                                            <input type="hidden" name="customer[title]" id="cus_title" value="<?php echo set_value('customer[title]', $customer['title']); ?>" />
                                        </div>
                                    </div>
                                    <div class='col-sm-4'>
                                        <div class='form-group'>
                                            <label for="customer_firstname"> <a data-toggle="tooltip"
                                                    title="Invalid characters 0-9"><span id="cus_name">First name
                                                    </span> </a> <span class="error">*</span></label>
                                            <!--<input class="form-control input_text" id="firstname" name="customer[firstname]" required="true" value="<?php echo set_value('customer[firstname]', ($customer['title'] != null ? $customer['title'] . '. ' : null) . '' . $customer['firstname']); ?>" type="text" />-->
                                            <input class="form-control input_text"
                                                oninput="this.value = this.value.toUpperCase()" id="firstname"
                                                name="customer[firstname]" required="true"
                                                value="<?php echo set_value('customer[firstname]', $customer['firstname']); ?>"
                                                type="text" />
                                        </div>
                                    </div>
                                    <div class='col-sm-3' id="last_name">
                                        <div class='form-group'>
                                            <label for="customer_lastname" data-toggle="tooltip"
                                                title="Invalid characters 0-9"> Last name</label>
                                            <input class="form-control input_text"
                                                oninput="this.value = this.value.toUpperCase()" id="lastname"
                                                name="customer[lastname]"
                                                value="<?php echo set_value('customer[lastname]', $customer['lastname']); ?>"
                                                type="text" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="gstno">
                                        <div class='form-group'>
                                            <label for="gst_number">GST Number<span class="error">*</span></label>
                                            <input class="form-control titlecase" id="gst_number"
                                                name="customer[gst_number]"
                                                value="<?php echo set_value('customer[gst_number]', $customer['gst_number']); ?>"
                                                type="text" />
                                        </div>
                                    </div>
                                    <!--	<div class="col-sm-4" id="pan_no">
                                    <div class='form-group'>
                                        <label for="pan">PAN Number</label>
                                        <input class="form-control" minlength="10" maxlength="10" id="pan" name="customer[pan]" value="<?php echo set_value('customer[pan]', $customer['pan']); ?>" onkeypress="return /^[A-Z0-9]$/i.test(event.key)" oninput="this.value = this.value.toUpperCase()" type="text" />						            
                                    </div> 
                                </div>  -->
                                    <div class='col-sm-3'>
                                        <div class='form-group'>
                                            <label for="mobile"><a data-toggle="tooltip"
                                                    title="Enter Valid mobile number"> Mobile </a><span
                                                    class="error">*</span></label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-addon input-sm"><?php echo $this->session->userdata('mob_code') ?></span>
                                                <!--<input class="form-control input_number" id="mobile" name="customer[mobile]" required="true" value="<?php echo set_value('customer[mobile]', $customer['mobile']); ?>" type="text" />-->
                                                <input class="form-control input_number" minlength="10" maxlength="10"
                                                    id="mobile" onkeypress="return /^[0-9]$/i.test(event.key)"
                                                    name="customer[mobile]" required="true"
                                                    value="<?php echo set_value('customer[mobile]', $customer['mobile']); ?>"
                                                    type="text" />
                                            </div>
                                        </div>
                                    </div>
                                    <!--    <div class="col-sm-4" id="adhar">
                                    <div class='form-group'>
                                        <label for="aadharid">Aadhar Number</label>
                                        <input class="form-control" minlength="12" maxlength="12" id="aadharid" name="customer[aadharid]" value="<?php echo set_value('customer[aadharid]', $customer['aadharid']); ?>" onkeypress="return /^[0-9]$/i.test(event.key)"  type="text" />						         
                                    </div> 
                                </div>  -->
                                    <div class="col-sm-4" id="profess">
                                        <div class='form-group'>
                                            <label for="pan">Profession</label>
                                            <select class="form-control" id="profession"
                                                name="customer[id_profession]"></select>
                                            <input class="form-control" id="professionval" type="hidden"
                                                value="<?php echo set_value('customer[id_profession]', $customer['id_profession']); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="first">Opening Balance Amount<span
                                                    class="error">*</span></label>
                                            <div class="input-group" style="width: 200px;">
                                                <input type="number" class="form-control" id="opening_bal_amt"
                                                    name="customer[opening_balance_amount]"
                                                    value="<?php echo set_value('customer[opening_balance_amount]', $customer['opening_balance_amount']); ?>"
                                                    tabindex="11" style="width: 120px;">
                                                <input type="hidden" class="form-control" id="bal_amount">
                                                <span class="input-group-btn">
                                                    <select class="form-control" id="order_fin_year_select"
                                                        name="customer[fin_year_code]" style="width: 100px;">
                                                        <?php foreach ($financial_year as $fin_year) { ?>
                                                            <option value="<?php echo $fin_year['fin_year_code']; ?>" <?php echo ($fin_year['fin_year_code'] == $customer['fin_year_code'] ? 'selected' : ''); ?>>
                                                                <?php echo $fin_year['fin_year_name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="email">E-Mail</label>
                                            <input class="form-control" id="email" name="customer[email]"
                                                value="<?php echo set_value('customer[email]', $customer['email']); ?>"
                                                type="email" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="phone">Phone</label>
                                            <input class="form-control input_number" id="phone" name="customer[phone]"
                                                value="<?php echo set_value('customer[phone]', $customer['phone']); ?>"
                                                type="text" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="customer_lastname"><a data-toggle="tooltip"
                                                    title="Enter login Password">Login Password </a><span
                                                    class="error">*</span></label>
                                            <input type="password" class="form-control" id="passwd"
                                                name="customer[passwd]" required="true"
                                                value="<?php echo set_value('customer[passwd]', $customer['passwd']); ?>" autocomplete="new-password"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="address1">Address1 <span class="error">*</span></label>
                                            <input class="form-control" id="address1" name="customer[address1]"
                                                value="<?php echo set_value('customer[address1]', $customer['address1']); ?>"
                                                type="text" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="address2">Address2</label>
                                            <input class="form-control titlecase" id="address2"
                                                name="customer[address2]"
                                                value="<?php echo set_value('customer[address2]', $customer['address2']); ?>"
                                                type="text" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="address3">Address3</label>
                                            <input class="form-control titlecase" id="address3"
                                                name="customer[address3]"
                                                value="<?php echo set_value('customer[address3]', $customer['address3']); ?>"
                                                type="text" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="country">Country <span class="error">*</span></label>
                                            <input type="hidden" id="countryval" name="countryval"
                                                value="<?php echo set_value('countryval', $customer['id_country']); ?>" />
                                            <select class="form-control" id="country" name="customer[country]"></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="state">State <span class="error">*</span></label>
                                            <input type="hidden" id="stateval" name="stateval"
                                                value="<?php echo set_value('stateval', $customer['id_state']); ?>" />
                                            <select class="form-control" id="state" name="customer[state]"></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="city">City <span class="error">*</span></label>
                                            <input type="hidden" id="cityval" name="cityval"
                                                value="<?php echo set_value('id_cityval', $customer['id_city']); ?>" />
                                            <select class="form-control" id="city" name="customer[city]"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="pincode">Pincode <span class="error">*</span></label>
                                            <!--<input class="form-control input_number" id="pincode" name="customer[pincode]" value="<?php echo set_value('customer[pincode]', $customer['pincode']); ?>"   type="text" />-->
                                            <input class="form-control input_number" id="pin_code_add"
                                                onkeypress="return /^[0-9]$/i.test(event.key)" minlength="6"
                                                maxlength="6" name="customer[pincode]"
                                                value="<?php echo set_value('customer[pincode]', $customer['pincode']); ?>"
                                                type="text" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4" style="display:flex">
                                        <div class='form-group col-md-10'>
                                            <label for="city">Area <span class="error">*</span></label>
                                            <input type="hidden" id="id_village" name="customer[id_village]"
                                                value="<?php echo set_value('customer[id_village]', $customer['id_village']); ?>" />
                                            <select class="form-control" id="Village" required="required"></select>
                                        </div>
                                        <div>
                                            <label></label>
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-success add_new_village_chit"><i
                                                        class="fa fa-plus"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="address2">Post office</label>
                                            <input class="form-control titlecase" id="post_office"
                                                name="customer[post_office]"
                                                value="<?php echo set_value('customer[post_office]', $customer['post_office']); ?>"
                                                type="text" readonly />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="address2">Taluk</label>
                                            <input class="form-control titlecase" id="taluk" name="customer[taluk]"
                                                value="<?php echo set_value('customer[taluk]', $customer['taluk']); ?>"
                                                type="text" readonly />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for=""><a data-toggle="tooltip"
                                                    title="Select branch to create Scheme Account"> Select
                                                    Religion</a></label>
                                            <select id="religion_select" class="form-control">
                                                <option> Select Religion Name</option>
                                                <option value="1">Hindu</option>
                                                <option value="2">Muslim</option>
                                                <option value="3">Christian</option>
                                            </select>
                                            <input type="hidden" name="customer[religion]" id="id_religion"
                                                value="<?php echo set_value('customer[religion]', $customer['religion']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='form-group'>
                                            <label for="language">Languages Known</label>
                                            <input type="hidden" id="languages_known" name="customer[languages_known]" />
                                            <select class="form-control  select2 cls" id="languages_known_select" data-placeholder="Select Languages"  multiple></select>
                                            <div id="sel_lang" data-sel_lang='<?php echo $customer['languages_known'];?>'></div> 
                                        </div>
                                    </div>
                                    
                               	

                                </div>
                                <div class="row">
                                    <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('is_branchwise_cus_reg') == 1) { ?>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Filter By Branch <span class="error">* </label>
                                                <select required id="branch_select" class="form-control"></select>
                                                <input id="id_branch" name="customer[id_branch]" type="hidden"
                                                    value="<?php echo set_value('customer[id_branch]', $customer['id_branch']); ?>" />
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <br />
                            <br />
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class='form-group'>
                                        <input id="cus_image" name="cus_img" accept="image/*" type="file">
                                        <img src="<?php echo base_url((isset($customer['cus_img_path']) ? $customer['cus_img_path'] : 'assets/img/default.png')); ?>"
                                            class="img-thumbnail" id="cus_img_preview" style="width:175px;height:100%;"
                                            alt="Customer image">
                                        <input type="hidden" name="customer[customer_img]"
                                            value="<?php echo set_value('customer[customer_img]', $customer['cus_img']) ?>" />
                                    </div>
                                    <?php if (isset($customer['cus_img_path']) && $customer['id_customer'] != null) { ?>
                                        <a class="btn bg-purple btn-sm"
                                            href="<?php echo base_url('index.php/customer/dload/' . $customer['id_customer'] . '/customer'); ?>"><i
                                                class="fa fa-download"></i> Download</a>
                                    <?php } ?>
                                </div>
                                <div class='col-sm-8'>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class='form-group'>
                                                <label for="gender">Gender <span class="error">*</span></label>
                                                <div class="form-group">
                                                    <p class="help-block"></p>
                                                    <input type="radio" name="customer[gender]" value="0"
                                                        class="minimal" <?php if ($customer['gender'] == 0) { ?> checked
                                                        <?php } ?> required />Male
                                                    <input type="radio" name="customer[gender]" value="1"
                                                        class="minimal" <?php if ($customer['gender'] == 1) { ?> checked
                                                        <?php } ?> />Female
                                                    <input type="radio" name="customer[gender]" value="3"
                                                        class="minimal" <?php if ($customer['gender'] == 3) { ?> checked
                                                        <?php } ?> />Others
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class='form-group'>
                                                <label for="marital">Marital Status<span class="error">*</span></label>
                                                <div class="form-group">
                                                    <p class="help-block"></p>
                                                    <input type="radio" name="customer[marital_status]" value="1"
                                                        class="minimal" <?php if ($customer['marital_status'] == 1) { ?> checked
                                                        <?php } ?> required />Single
                                                    <input type="radio" name="customer[marital_status]" value="2"
                                                        class="minimal" <?php if ($customer['marital_status'] == 2) { ?> checked
                                                        <?php } ?> />Married
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='col-sm-4'>
                                            <div class='form-group'>
                                                <label for="date_of_birth">Date of Birth</label>
                                                <input class="form-control datemask" data-date-format="dd-mm-yyyy"
                                                    id="date_of_birth" name="customer[date_of_birth]"
                                                    value="<?php echo set_value('customer[date_of_birth]', $customer['date_of_birth']); ?>"
                                                    readonly type="text" />
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class='form-group'>
                                                <label for="age">Age</label>
                                                <input class="form-control " id="age" name="customer[age]"
                                                    required="true" size="30" readonly="true" type="text"
                                                    value="<?php echo set_value('customer[age]', $customer['age']); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='col-sm-4'>
                                            <div class='form-group'>
                                                <label for="date_of_birth ">Wedding Date</label>
                                                <input class="form-control datemask" data-date-format="dd-mm-yyyy"
                                                    id="date_of_wed" name="customer[date_of_wed]"
                                                    value="<?php echo set_value('customer[date_of_wed]', $customer['date_of_wed']); ?>"
                                                    readonly type="text" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_2">
                            <legend>Nominee Details</legend>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class='form-group'>
                                        <label for="nominee">Name</label>
                                        <input class="form-control input_text" id="nominee_name"
                                            name="customer[nominee_name]"
                                            value="<?php echo set_value('customer[nominee_name]', $customer['nominee_name']); ?>"
                                            type="text" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class='form-group'>
                                        <label for="customer_lastname">Relationship</label>
                                        <input class="form-control input_text" id="nominee_relationship"
                                            name="customer[nominee_relationship]"
                                            value="<?php echo set_value('customer[nominee_relationship]', $customer['nominee_relationship']); ?>"
                                            type="text" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class='form-group'>
                                        <label for="nominee_mobile">Mobile</label>
                                        <!-- coded by ARVK -->
                                        <div class="input-group">
                                            <span
                                                class="input-group-addon input-sm"><?php echo $this->session->userdata('mob_code') ?></span>
                                            <input class="form-control input_number" id="nominee_mobile"
                                                name="customer[nominee_mobile]"
                                                value="<?php echo set_value('customer[nominee_mobile]', $customer['nominee_mobile']); ?>"
                                                type="text" />
                                        </div>
                                        <!-- /coded by ARVK -->
                                    </div>
                                </div>
                            </div>
							<div class="row">
								<div class="col-sm-4">
									<div class='form-group'>
										<label for="nominee_address1">Address1</label>
										<input type="text" class="form-control" id="nominee_address1"
											name="customer[nominee_address1]"
											value=" <?php echo set_value('customer[nominee_address1]', $customer['nominee_address1']); ?>">
										<!-- <input class="form-control" id="nominee_address1" name="customer[nominee_address1]" value="<?php echo set_value('customer[nominee_address1]', $customer['nominee_address1']); ?>" type="text" /> -->
									</div>
								</div>
								<div class="col-sm-4">
									<div class='form-group'>
										<label for="nominee_address2">Address2</label>
										<input type="text" class="form-control" id="nominee_address2"
											name="customer[nominee_address2]"
											value="<?php echo set_value('customer[nominee_address2]', $customer['nominee_address2']); ?>">
										<!-- <input class="form-control" id="nominee_address2" name="customer[nominee_address2]" value="<?php echo set_value('customer[nominee_address2]', $customer['nominee_address2']); ?>" type="text" /> -->
									</div>
								</div>
							</div>
                            <legend>Others</legend>
                            <legend>Proof</legend>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class='form-group'>
                                        <label for="voterid">Pan No</label>
                                        <input class="form-control" minlength="10" maxlength="10" id="panno"
                                            name="customer[nominee_pan]" type="text"
                                            value="<?php echo set_value('customer[pan]', $customer['nominee_pan']); ?>"
                                            onchange="validate_kyc_pan()"
                                            oninput="this.value = this.value.toUpperCase()" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class='form-group'>
                                        <label for="voterid">Voter ID No</label>
                                        <input class="form-control" id="voterid" name="customer[voterid]" type="text"
                                            value="<?php echo set_value('customer[voterid]', $customer['voterid']); ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class='form-group'>
                                        <label for="customer_lastname">Rationcard No</label>
                                        <input class="form-control" id="rationcard" name="customer[rationcard]"
                                            type="text"
                                            value="<?php echo set_value('customer[rationcard]', $customer['rationcard']); ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="customer_lastname">Attach Pancard</label>
                                    <div class='form-group'>
                                        <input id="pan_proof" name="pan_proof" type="file">
                                        <img src="<?php echo base_url((isset($customer['pan_path']) ? $customer['pan_path'] : 'assets/img/no_image.png')); ?>"
                                            class="img-thumbnail" id="pan_proof_preview" alt="Pan card" width="150"
                                            height="75">
                                        <input type="hidden" name="customer[pan_img]"
                                            value="<?php echo set_value('customer[pan_img]', $customer['pan_proof']) ?>" />
                                        <input type="hidden" id="pan_proof_img"
                                            value="<?php echo $customer['pan_path']; ?>" />
                                    </div>
                                    <?php if (isset($customer['pan_path']) && $customer['id_customer'] != null) { ?>
                                        <a class="btn bg-purple btn-sm"
                                            href="<?php echo base_url('index.php/customer/dload/' . $customer['id_customer'] . '/pan'); ?>"><i
                                                class="fa fa-download"></i> Download</a>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4">
                                    <label for="customer_lastname">Attach Voter ID</label>
                                    <div class='form-group'>
                                        <input id="voterid_proof" name="voterid_proof" type="file">
                                        <img src="<?php echo base_url((isset($customer['voterid_path']) ? $customer['voterid_path'] : 'assets/img/no_image.png')); ?>"
                                            class="img-thumbnail" id="voterid_proof_preview" alt="Voter ID" width="150"
                                            height="75">
                                        <input type="hidden" name="customer[voter_img]"
                                            value="<?php echo set_value('customer[voter_img]', $customer['voterid_proof']) ?>" />
                                        <input type="hidden" id="voterid_proof_img"
                                            value="<?php echo $customer['voterid_path']; ?>" />
                                    </div>
                                    <?php if (isset($customer['voterid_path']) && $customer['id_customer'] != null) { ?>
                                        <a class="btn bg-purple btn-sm"
                                            href="<?php echo base_url('index.php/customer/dload/' . $customer['id_customer'] . '/voterid'); ?>"><i
                                                class="fa fa-download"></i> Download</a>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4">
                                    <label for="customer_lastname">Attach Ration Card</label>
                                    <div class='form-group'>
                                        <input id="rationcard_proof" name="rationcard_proof" type="file">
                                        <img src="<?php echo base_url((isset($customer['rationcard_path']) ? $customer['rationcard_path'] : 'assets/img/no_image.png')); ?>"
                                            class="img-thumbnail" id="rationcard_proof_preview" alt="Ration card"
                                            width="150" height="75">
                                        <input type="hidden" name="customer[ration_img]"
                                            value="<?php echo set_value('customer[ration_img]', $customer['rationcard_proof']) ?>" />
                                        <input type="hidden" id="rationcard_proof_img"
                                            value="<?php echo $customer['rationcard_path']; ?>" />
                                    </div>
                                    <?php if (isset($customer['rationcard_path']) && $customer['id_customer'] != null) { ?>
                                        <a class="btn bg-purple btn-sm"
                                            href="<?php echo base_url('index.php/customer/dload/' . $customer['id_customer'] . '/rationcard'); ?>"><i
                                                class="fa fa-download"></i> Download</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class='form-group'>
                                        <label for="comments">Comments</label>
                                        <textarea class="form-control" id="comments"
                                            name="customer[comments]"><?php echo set_value('customer[comments]', $customer['comments']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($customer['id_customer'])) { ?>
                            <div class="tab-pane" id="tab_3">
                                <legend>Associate Customer</legend>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for=""> <a data-toggle="tooltip"
                                                    title="Enter mobile no. to be associated with your account"> Associate
                                                    Customer</a></label>
                                            <div class='form-group'>
                                                <input class="form-control input_number" id="associate_mobile" type="text"
                                                    placeholder="Enter Associate Customer Mobile Number" />
                                                <input type="hidden" id="ass_mobile_number">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label> </label>
                                            <div class='form-group'>
                                                <button class="btn btn-warning" type="button" id="chk_avail">Submit</button>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="otp_block" style="display: none;">
                                            <label for=""><a data-toggle="tooltip"
                                                    title="Enter OTP to From Associated Mobile Number"> Enter
                                                    OTP</a></label>
                                            <div class='form-group'>
                                                <input class="form-control input_number" id="otp" type="text"
                                                    placeholder="Enter The OTP" />
                                                <input type="hidden" id="associated_cus" name="customer[associated_cus]">
                                                <input type="hidden" id="verified_top" name="customer[verified_top]">
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <label> </label>
                                            <div class='form-group'>
                                                <button class="btn btn-success" type="button" id="otp_submit"
                                                    style="display: none;">Verify</button>
                                            </div>
                                        </div>
                                        <div class="col-sm-1" style="margin-top: 27px;">
                                            <input id="resendotp" value="Resend OTP" class="resendotp"
                                                style="display: none;"></input>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='form-group'>
                                        </div>
                                    </div>
                                </div>
                                <legend>Associated Customer Details</legend>
                                <div class="table-responsive">
                                    <table id="ass_customer_list" class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_4">
                                <legend>Company Users</legend>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for=""> Employee Name</label>
                                            <div class='form-group'>
                                                <input class="form-control text" id="emp_name" type="text"
                                                    placeholder="Enter Name" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="">Mobile Number</label>
                                            <div class='form-group'>
                                                <input class="form-control input_number" id="emp_mobile" type="text"
                                                    placeholder="Enter Mobile Number" />
                                                <input type="hidden" id="id_cmp_emp">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label> </label>
                                            <div class='form-group'>
                                                <button class="btn btn-warning" type="button" id="add_cmp_user">Add</button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label> </label>
                                            <div class='form-group'>
                                                <button class="btn btn-success" type="button"
                                                    id="update_cmp_user">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='form-group'>
                                        </div>
                                    </div>
                                </div>
                                <legend>Company User Details</legend>
                                <div class="table-responsive">
                                    <table id="set_company_user_list"
                                        class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="5%">Name</th>
                                                <th width="5%">Mobile</th>
                                                <th width="5%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                        <!--	//webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB  -->
                        <div class="tab-pane" id="tab_5">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-8">
                                        <label>Note - Click Snapshot Button To Take Your Images Screen Shot</label>
                                        <label>Press CTRL + I to take Images Screen Shot</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="button" value="Take Snapshot" onClick="take_snapshot('pre_images')"
                                            class="btn btn-warning" id="snap_shots"><br>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6" id="my_camera"></div>
                                    <input type="hidden" name="image" class="image-cust">
                                    <input type="hidden" id="customer_images" name="customer[cus_img]">
                                    <div class="col-md-3"></div>
                                </div>
                            </div>
                            <div class="row" id="image_lot_list" style="display:none;">
                                <div class="col-md-12" style="font-weight:bold;">Customer Images</div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-12" id="uploadArea_p_stn"></div>
                            </div>
                        </div>
                        <!-- webcam upload ends -->
                    </div>
                </div> <!-- /Tab content -->
            </div><!-- /.box-body -->
            <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div><!-- /.box -->
        <div class="row">
            <div class="box box-default"><br />
                <div class="col-xs-offset-5">
                    <button type="submit" id="save" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default btn-cancel">Cancel</button>
                </div> <br />
            </div>
        </div>
        <?php echo form_close(); ?>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<div class="modal fade" id="pb_imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">


                <input type="hidden" id="current_row_id">

				<h4 class="modal-title" id="myModalLabel">Passbook Image</h4>

			</div>

			<div class="modal-body">

				<!-- <input type="file" name="order_images_front" id="order_images_front" multiple="multiple"> -->
                
                <!-- <input type="file" id="pb_img" name="bank_img" accept="image/*"> -->
				

			</div></br>

			<div class="row">
                <div class="col-md-12" id="uploadArea_p_stn_pb">
				
				</div>
            </div>
            <input id="bank_images" type="hidden" name="customer[bank_img]">

		  <div class="modal-footer">

			<button type="button" id="download_pb_img" class="btn btn-success">Download</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<div class="modal fade" id="ch_imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">


                <input type="hidden" id="current_row_id">

				<h4 class="modal-title" id="myModalLabel">ChequeLeaf Image</h4>

			</div>

			<div class="modal-body">

				<!-- <input type="file" name="order_images_front" id="order_images_front" multiple="multiple"> -->
                
                <!-- <input type="file" id="ch_img" name="bank_img" accept="image/*"> -->
				

			</div></br>

			<div class="row">
                <div class="col-md-12" id="uploadArea_p_stn_ch">
				
				</div>
            </div>
            <input id="bank_images" type="hidden" name="customer[bank_img]">

		  <div class="modal-footer">

			<button type="button" id="download_ch_img" class="btn btn-success">Download</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- <div class="modal fade" id="docModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Document Upload</h4>

			</div>

			<div class="modal-body">

				<div>	

					

					<input type="file" name="doc_file" id="doc_file" accept=".pdf">

					<input type="hidden" id="doc_active_row" value="0">	

					<input type="hidden" name="kyc_doc_file" id="kyc_doc_file" value="">	



				</div></br>

				<div class="row">

					<div class="col-md-9">

						<div class="col-md-12 box-items no-paddingwidth" style="max-height: 300px;overflow: auto;">

							<div class="col-md-12 col-xs-12 recent_bills no-paddingwidth blog-box">

								<div class="col-md-12 col-xs-12">

									<div class="col-md-12 col-xs-12 no-paddingwidth container-table">

										<table class="table table-bordered" id="kyc_document_pre">

											<thead>

												<tr>

													<th width="1%">FileName</th>

													<th width="2%">Action</th>

												</tr>

											</thead>

											<tbody>



											</tbody>

										</table>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>	

			<div class="modal-footer">

				<button type="button" id="update_doc" class="btn btn-success">Save</button>

				<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div> -->


<div class="modal fade" id="confirm-area" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Village</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label for="pincode" class="col-md-3 col-md-offset-1">Pincode</label>
                        <div class="col-md-6">
                            <input class="form-control titlecase" id="new_pincode" type="text"
                                placeholder="Enter Pincode"
                                onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' readonly required />
                        </div>
                    </div>
                </div></br>
                <div class="row">
                    <div class="form-group">
                        <label for="area" class="col-md-3 col-md-offset-1 ">Area<span class="error">*</span></label>
                        <div class="col-md-6">
                            <input class="form-control" id="village" value="" type="text" placeholder="Enter Area Here "
                                required onkeypress="return /^[a-zA-Z\s]$/.test(event.key)" />
                            <p class="help-block address1 error"></p>
                        </div>
                    </div>
                </div></br>
            </div></br>
            <div class="modal-footer">
                <input type="hidden" name="cus[id_customer]" id="id_customer" value="">
                <a href="#" id="add_new_area" class="btn btn-success">Add</a>
                <button type="button" class="btn btn-warning new_village_close" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var cust_id = "<?php echo $customer['id_customer']; ?>";
    var mob_no_len = "<?php echo $this->session->userdata('mob_no_len') ?>";
</script>