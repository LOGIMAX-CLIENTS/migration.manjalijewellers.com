<?php $username=($this->session->userdata['username']?$this->session->userdata['username']:'Admin');
$comp_details=$this->admin_settings_model->get_company();
$metal_rates=$this->admin_settings_model->metal_ratesDB("last");
$promo_blc = $this->admin_settings_model->checkBalance(1);
$trans_blc = $this->admin_settings_model->checkBalance(4);
$walSMS_crt=$this->admin_settings_model->walSMS_settings("get","1","");
$metalrate_set=$this->admin_settings_model->settingsDB('get','1','');
//echo "<pre>";print_r($metalrate_set);echo "</pre>";exit;
$headerData = $this->admin_settings_model->getHeaderData();
$branch_transfer = $this->admin_settings_model->getBTDetails();
$ret_module=$this->admin_settings_model->get_modules('RT');
$pos_module=$this->admin_settings_model->get_modules('POS');
$pos_enabled = (isset($pos_module['m_web']) && $pos_module['m_web']==1 && isset($pos_module['m_active']) && $pos_module['m_active']==1) ? 1 : 0;
$notification_details = $this->admin_settings_model->getNotificationDetails();
$quick_link=$this->admin_settings_model->get_quick_link();
$url= $this->uri->uri_string;
$menu_link = $this->admin_settings_model->get_menu_link($url);

$link1 = $this->uri->segment(3);
$link2= $this->uri->segment(2);
$menu = ucfirst(strtolower($link1));
$label = ucfirst(strtolower(str_replace('_', ' ', $link2)));
//  $label = ucfirst(strtolower($link2));
$EMP_IMG_PATH ='assets/img/employee/';

$EMP_IMG= 'employee.jpg';

$id = $this->session->userdata('uid');
$emp_img = $EMP_IMG_PATH . $id . "/" . $EMP_IMG;

if (file_exists($emp_img)) {
    $img_src = base_url().$emp_img;
} else {
    $img_src = base_url().'assets/dist/img/no_image_available.png';
}
   ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo ($menu_link['label']!='' ? $menu_link['label']: $label .' - '.$menu) .' | ' . $comp_details['company_name'];?></title>
    <link rel="shortcut icon" href="<?php echo base_url() ?>favicon.ico?v=2" type="image/x-icon"><link rel="icon" href="<?php echo base_url() ?>favicon.ico" type="image/x-icon?v=2">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/themes/default/style.min.css" />
    <link href="<?php echo get_asset_url('css/style.css'); ?>" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script src="https://unpkg.com/chart.js@2.8.0/dist/Chart.bundle.js"></script>
<script src="https://unpkg.com/chartjs-gauge@0.3.0/dist/chartjs-gauge.js"></script>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/all.css">
    <!-- Morris chart -->
    <link href="<?php echo base_url(); ?>assets/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
    <!-- jvectormap -->
    <link href="<?php echo base_url(); ?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    <!-- Date Picker -->
    <link href="<?php echo base_url(); ?>assets/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <!-- Daterange picker -->
    <link href="<?php echo base_url(); ?>assets/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
     <!-- Datetime picker -->
    <link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />
    <!-- Time picker -->
    <link href="<?php echo base_url(); ?>assets/plugins/timepicker/src/jquery.timeselector.css" rel="stylesheet" type="text/css" />
	<!-- bootstrap wysihtml5 - text editor -->
    <link href="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-switch/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
     <!-- DATA TABLES -->
    <link href="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/plugins/datatables/extensions/rowReorder/rowReorder.min.css'); ?>" rel="stylesheet" type="text/css" />





        <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/select2.min.css'); ?>">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
  </head>
  <body class="skin-red sidebar-mini fixed sidebar-collapse">
    <div class="wrapper">
      <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo base_url();?>" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b><?php echo $comp_details['company_name']?></b></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b><?php echo $comp_details['company_name']?></b> Admin</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <label style="padding-top: 14px;color:white"><?php echo $this->session->userdata('branch_name');?></label>
                    <button class="button1"><a href="<?php echo base_url(); ?>index.php/payment/add" data-toggle="tooltip" data-placement="bottom" class="text-red1 blink-hard" title="Pay Ema" > PAY EMA </a></button>

          <!-- <?php if(($this->session->userdata['uid'] == 1 || $this->session->userdata['uid'] == 2 || $this->session->userdata['uid'] == 3)  && file_exists(base_url().'assets/adm_app_apk/collectionapp.apk')){?>
		        <a href="<?php echo base_url(); ?>assets/adm_app_apk/collectionapp.apk" style="line-height: 50px;color:white"> Download App</a>
		    <a href="https://play.google.com/apps/internaltest/4701212092663346455" target="_blank" style="line-height: 50px;color:white"> Download App</a>
		  <?php } ?> -->

      <!-- coded by Jothika on 02-10-2025 -->
      <?php   
          $url = base_url()."assets/adm_app_apk/collectionapp.apk";
          $headers = @get_headers($url);
          if(($this->session->userdata['uid'] == 1 || $this->session->userdata['uid'] == 2 || $this->session->userdata['uid'] == 3)  && $headers && strpos($headers[0], '200') !== false){?>
		        <a href="<?php echo base_url(); ?>assets/adm_app_apk/collectionapp.apk" style="color:white" data-toggle="tooltip" data-placement="bottom" title="Collection App"><button style="margin-top: 10px; margin-left:5px; border-radius:5px; border:none;" ><img src="<?php echo base_url(); ?>/assets/img/apk.png" alt="" class="img-fluid" style="height:30px;width:30px;"></button></a>
		    <!--<a href="https://play.google.com/apps/internaltest/4701212092663346455" target="_blank" style="line-height: 50px;color:white"> Download App</a>-->
		  <?php } ?>

          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li class="dropdown user user-menu"  style="max-width:377px;height: 51px;color:#000;padding: 0px 4px 0px 4px;">
                 <span class="header_rate" style=""><marquee scrollamount="3" width="100%" style="line-height: 50px;color:white"><b><?php echo $notification_details;?></b></marquee></span>
              </li>

              <!-- Messages: style can be found in dropdown.less-->
            <?php if($this->uri->segment(1) == 'admin' &&  $headerData['show_dayClose'] == 1 && $ret_module['m_web']==1 && $ret_module['m_active']==1 ){ ?>
              <li class="dropdown user user-menu" style="height: 51px;color:#000;padding: 0px 4px 0px 4px;">
		         <a type="button" class="btn btn-flat" id="day_close" style="background-color: #f39c12;">Day Close</a>
              </li>
              <?php }?>


              <li class="dropdown user user-menu rate_block_button">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <span class="rate_block_button1"><b>Quick Links</b></span>
                  </a>
                  <ul class="dropdown-menu">
                      <!-- Menu Body -->
                      <li class="user-body rate_block_body">
                          <table style="width: 100%;">
                          <?php
                                foreach ($quick_link as $menu) {

                                  $id_profile = $this->session->userdata['profile'];

                                  $permission_set=$this->admin_settings_model->menuPermission($id_profile,$menu['id_menu']);

                                  // echo "<pre>"($permission_set);

                                  if ($permission_set['view'] == 1){
                                    ?>
                                    <tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                                        <td style="width: 40%;"><a href="<?php echo base_url(index_page() . '/' . $menu['link']); ?>"><?php echo $menu['label']; ?></a></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                          </table>
                      </li>
                      <!-- Menu Footer -->
                      <li class="user-footer rate_block_footer">
                          <!-- Footer content goes here -->
                      </li>
                  </ul>
              </li>

               <li>
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell" aria-hidden="true" style="font-size: 17px; !important;"></i>
                        <span class="badge bg-purple" id="notification_count" style="font-size: 14px;color: #fff;padding: 0 4px; vertical-align: top;margin-left: -8px;margin-top: -6px;"><?php echo $headerData['customer_feedback'] ?></span>
                    </a>
                    <!-- <div id="noti_dropdown" class="dropdown-menu dropdown-menu-right" style="min-width: 209px !important;"></div> -->

                    <div id="noti_dropdown" class="dropdown-menu dropdown-menu-right" style="font-size:12px;">
                         <span>Customer Feedback - </span>
                         <a  target="_blank" href="<?php echo base_url('index.php/reports/customer_enquiry'); ?>" ><span class="badge bg-green"><?php echo $headerData['customer_feedback'] ?></span></a>
                      </div>
                </li>


              <?php if($ret_module['m_web']==1 && $ret_module['m_active']==1 && $headerData['show_pending_download']==1){?>
             <!-- <li>
                  <a href="<?php echo base_url('index.php/admin_ret_reports/branch_trans/approval_pending/'.$this->session->userdata('id_branch'));?>">
                       <i class="fa fa-cloud-download" aria-hidden="true" style="font-size: 17px; !important;"></i>
                       <span class="badge bg-purple" id="tot_count" style="font-size: 14px;color: #fff;padding: 0 4px; vertical-align: top;margin-left: -8px;margin-top: -6px;"><?php echo $branch_transfer['tot_pcs'];?></span>
                    </a>
              </li>-->



              <?php }?>

              <li class="dropdown user user-menu" style="height: 51px;color:#000;padding: 0px 4px 0px 4px;">
		        <?php if($this->session->userdata('profile')<=3){?>
				<a>
				  <span class="header_rate"><b><?php echo $headerData['fin_year']['fin_year_name']; ?></b></span>			  	  	</a>
			  	<?php }else{ ?>
			  	<a>
				  <span class="header_rate"><b><?php echo $headerData['fin_year']['fin_year_name']; ?></b></span>			  	  	</a>
			  	<?php } ?>
              </li>

              <li class="dropdown user user-menu" style="height: 51px;color:#000;padding: 0px 4px 0px 4px;">
                <a><span class="header_rate"><b><?php echo $headerData['day_close_date'];?></b></span></a>
               </li>

              <input type="hidden" id="company_name" value="<?php echo $comp_details['company_name'];?>">
              <input type="hidden" id="company_code" value="<?php echo $comp_details['company_name'];?>">
              <input type="hidden" id="company_address1" value="<?php echo $comp_details['address1'];?>">
              <input type="hidden" id="company_address2" value="<?php echo $comp_details['address2'];?>">
              <input type="hidden" id="company_city" value="<?php echo $comp_details['city'];?>">
              <input type="hidden" id="pincode" value="<?php echo $comp_details['pincode'];?>">
              <input type="hidden" id="company_gst_number" value="<?php echo $comp_details['gst_number'];?>">
              <input type="hidden" id="company_email" value="<?php echo $comp_details['email'];?>">
              <input type="hidden" id="phone" value="<?php echo $comp_details['phone'];?>">
             <input type="hidden" id="branch_otp_mobile" value="<?php echo $this->session->userdata('branch_otp_mobile'); ?>">
              <input type="hidden" id="id_profile" value="<?php echo $this->session->userdata('profile'); ?>">


              <input type="hidden" id="company_name" value="<?php echo $comp_details['company_name'];?>">
			   <!-- Current Rate Display -->
              <li class="dropdown user user-menu rate_block_button">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="rate_block_button1">Current gold rate 22KT 1gm: </span><span class="header_rate"><b><?php echo $comp_details['currency_symbol']." ".(isset($metal_rates['goldrate_22ct']) ? $metal_rates['goldrate_22ct']:0); ?></b></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- Menu Body -->
                  <li class="user-body rate_block_body">
                    <table style="width: 100%;">
				  <?php if($metalrate_set['enableGoldrateDisc']==1 && $metalrate_set['goldDiscAmt']!=''){?>
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;"> Mjdma Gold 22KT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['mjdmagoldrate_22ct']; ?></td>
                  	</tr>
					<?php }?>
           <tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 14KT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_14ct']; ?></td>
                  	</tr>
           <tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 9KT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_9ct']; ?></td>
                  	</tr>
					<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 18KT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_18ct']; ?></td>
                  	</tr>
					<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 22KT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_22ct']; ?></td>
                  	</tr>
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 24KT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_24ct']; ?></td>
                  	</tr>
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Silver 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['silverrate_1gm']; ?></td>
                  	</tr>
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Silver 1Kg</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['silverrate_1kg']; ?></td>
                  	</tr>
                  </table>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer rate_block_footer">
                    <table style="width: 100%;">
	                  	<tr>
	                  		<th style="width: 60%;">Updated on </th>
	                  		<td style="width: 40%;"><?php echo date("d-m-Y h:i:s a", strtotime($metal_rates['updatetime'])); ?></td>
	                  	</tr>
                  	</table>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
             <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">

                 <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?php echo base_url(); ?>assets/dist/img/no_image_available.png" class="user-image" alt="User Image"/>
                  <span class="hidden-xs"><?php echo ($username?ucfirst($username):'Admin'); ?></span>
                </a>  -->
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <img src="<?= $img_src ?>" class="user-image" alt="User Image">
                      <span class="hidden-xs">
                          <?= ($username ? ucfirst($username) : 'Admin'); ?>
                      </span>
                  </a>

                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img  src="<?= $img_src ?>" class="img-circle" alt="User Image" />
                    <p>
                      <?php echo ($username?ucfirst($username):'Admin'); ?> - Admin
                      <small></small>
                    </p>
                  </li>
                  <!-- Menu Body -->
             <!--     <li class="user-body">
                    <div class="col-xs-4 text-center">
                      <a href="#">Followers</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Sales</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Friends</a>
                    </div>
                  </li>-->
                  <!-- Menu Footer-->
                  <li class="user-footer">
                      <?php if( $this->session->userdata['profile'] == 1){ ?>
                        <div class="pull-left">
                          <a href="<?php echo base_url('index.php/employee/edit/'.$this->session->userdata('uid'));?>" class="btn btn-default btn-flat">Profile</a>
                        </div>
                      <?php }?>
                    <div class="pull-right">
                      <a href="<?php echo base_url("index.php/admin/logout");?>" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
             <!-- <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
              </li>-->
            </ul>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img  src="<?= $img_src ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo ($username?ucfirst($username):'Admin'); ?></p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- search form -->
     <!-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form> -->
          <!-- /.search form -->
          <!-- sidebar menu: : style can be found in sidebar.less -->
            <?php
              $id_profile = $this->session->userdata['profile'];
              $menu=$this->admin_settings_model->menu_generation($id_profile);
            	if(isset($menu)){
					echo $menu;
				}
            ?>
        </section>
        <!-- /.sidebar -->
      </aside>






<input type="hidden" id="branch_set" name="branch_set" value="<?php echo $this->session->userdata('branch_settings'); ?>"/>
<input type="hidden" id="logged_branch" name="logged_branch" value="<?php echo $this->session->userdata('id_branch'); ?>"/>
<script type="text/javascript">
     var loggedInBranch = "";
     var branchSettings = "";
      var session_data = [];
     branchSettings = <?php echo $this->session->userdata("branch_settings") ?>;
     loggedInBranch = <?php echo ($this->session->userdata("id_branch")?$this->session->userdata("id_branch"):0) ?>;
     session_data = <?php echo json_encode($this->session->all_userdata()) ?>;
     var pos_module_enabled = <?php echo $pos_enabled; ?>;

</script>