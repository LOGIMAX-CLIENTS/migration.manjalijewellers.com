<?php $username=($this->session->userdata['username']?$this->session->userdata['username']:'Admin');

			   $comp_details=$this->admin_settings_model->get_company();
			   
			   $metal_rates=$this->admin_settings_model->metal_ratesDB("last");	
			   
			   $promo_blc=5000;	
			   
			   $trans_blc=5000;
			   
			   /*$promo_blc = $this->admin_settings_model->checkBalance(1);	
                $trans_blc = $this->admin_settings_model->checkBalance(4);*/
			   
			   $walSMS_crt=$this->admin_settings_model->walSMS_settings("get","1","");
			   
			   $metalrate_set=$this->admin_settings_model->settingsDB('get','1','');
			   
			   //echo "<pre>";print_r($metalrate_set);echo "</pre>";exit;
			   
			    $branch_name=$this->admin_settings_model->get_branch_by_id($this->session->userdata['id_branch']);
			    $finance_code=$this->admin_settings_model->get_financial_data();

   ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo $comp_details['company_name'].' | Admin'?></title><link rel="shortcut icon" href="<?php echo base_url() ?>favicon.ico" type="image/x-icon"><link rel="icon" href="<?php echo base_url() ?>favicon.ico" type="image/x-icon">
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
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css" />

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
  </head>
  <body class="skin-purple sidebar-mini sidebar-collapse">
   
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
			<?php if($this->session->userdata['uid']!=1){?>
						    <label style="padding-top: 14px;color:white"><?php  echo $branch_name['name']?></label>

			<?php } ?>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->
        <?php  /* <li class="dropdown messages-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-envelope-o"></i>
                  <span class="label label-success">4</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">You have 4 messages</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li><!-- start message -->
                        <a href="#">
                          <div class="pull-left">
                            <img src="<?php echo base_url(); ?>assets/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
                          </div>
                          <h4>
                            Support Team
                            <small><i class="fa fa-clock-o"></i> 5 mins</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li><!-- end message -->
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="<?php echo base_url(); ?>assets/dist/img/user3-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            AdminLTE Design Team
                            <small><i class="fa fa-clock-o"></i> 2 hours</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="<?php echo base_url(); ?>assets/dist/img/user4-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            Developers
                            <small><i class="fa fa-clock-o"></i> Today</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="<?php echo base_url(); ?>assets/dist/img/user3-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            Sales Department
                            <small><i class="fa fa-clock-o"></i> Yesterday</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="<?php echo base_url(); ?>assets/dist/img/user4-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            Reviewers
                            <small><i class="fa fa-clock-o"></i> 2 days</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="footer"><a href="#">See All Messages</a></li>
                </ul>
              </li> 
              <!-- Notifications: style can be found in dropdown.less -->
              <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bell-o"></i>
                  <span class="label label-warning">10</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">You have 10 notifications</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li>
                        <a href="#">
                          <i class="fa fa-users text-aqua"></i> 5 new members joined today
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the page and may cause design problems
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <i class="fa fa-users text-red"></i> 5 new members joined
                        </a>
                      </li>

                      <li>
                        <a href="#">
                          <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <i class="fa fa-user text-red"></i> You changed your username
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="footer"><a href="#">View all</a></li>
                </ul>
              </li>  
              <!-- Tasks: style can be found in dropdown.less -->
              <li class="dropdown tasks-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-flag-o"></i>
                  <span class="label label-danger">9</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">You have 9 tasks</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Design some buttons
                            <small class="pull-right">20%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">20% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Create a nice theme
                            <small class="pull-right">40%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">40% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Some task I need to do
                            <small class="pull-right">60%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">60% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                      
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Make beautiful transitions
                            <small class="pull-right">80%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">80% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                    </ul>
                  </li>
                  <li class="footer">
                    <a href="#">View all tasks</a>
                  </li>
                </ul>
              </li> */?>	
			 		
			<!-- <?php if(($walSMS_crt['sent_sms'] ) > 0){ ?>	  
 			 <li class="dropdown user user-menu" style="height: 51px;color:#fff;background: #5cbdf5;padding: 0px 4px 0px 4px;text-align: center;"> 			  
				  Wallet SMS Used<br/>
				  <span class="">
				      <?php if($walSMS_crt['sent_sms'] >= 90000){?> 
						 <b><span class="blink blink-infinite"><?php echo $walSMS_crt['sent_sms'];?></span> </b>
					  <?php }else{?>
						 <b><?php echo $walSMS_crt['sent_sms'];?></b> 
					  <?php } ?>  				  			  
				  </span> 
              </li>
			  <?php } ?>-->
			  
			  <li class="dropdown user user-menu" style="height: 51px;color:#fff;padding: 0px 4px 0px 4px;"> 
		        <?php if($finance_code['fin_year_code']!=null){?>
				  <a style="padding:2px;" href="<?php echo base_url('index.php/admin_ret_catalog/financial_year/list');?>">	<b>Financial Year</b>
				   	<br/><span class="header_rate"><b><?php echo $finance_code['fin_year_code']; ?></b></span>			  </a>	  
			  	<?php } ?>
              </li>
			  <?php if($this->config->item('sms_gateway') == 1){?>
			  <li class="dropdown user user-menu" style="height: 51px;color:#fff;background: #262268;padding: 0px 4px 0px 4px;"> 			  
				  
				  <?php if( $this->session->userdata['profile'] == 1 || $this->session->userdata['profile'] == 2){ ?>
				  <a style="padding:2px;" href="<?php echo base_url('index.php/reports/msg91_translog');?>"><b>SMS Balance </b> <br/>Transaction Log <i class="fa fa-external-link"></i></a>
				  <?php } else{?>
				  <b>SMS Balance </b> <br/>
				  <?php } ?>
              </li> 
            <li class="dropdown user user-menu" style="height: 51px;color:#fff;padding: 0px 4px 0px 4px;"> 	
				  <span class="">
				   	  Transactional :
				      <?php if($trans_blc<=2000){?> 
						 <b><span class="blink blink-infinite"><?php echo $trans_blc;?></span> </b>
					  <?php }else{?>
						 <b><?php echo $trans_blc;?></b> 
					  <?php } ?> 
					  <br/>
					  Promotional &nbsp&nbsp:
					  <?php if($promo_blc<=2000){?> 
					  	<b><span class="blink blink-infinite"><?php echo round($promo_blc);?></span></b>
					  <?php }else{?>
					  	<b><?php echo round($promo_blc);?></b>
					  <?php }?>					  			  
				  </span> 
              </li>  
              <?php } ?>




						  
              <!-- Current Rate Display -->
              <!--<li class="dropdown user user-menu rate_block_button">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="rate_block_button1">Current gold rate 22CT 1gm: </span><span class="header_rate"><b><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_22ct']; ?></b></span>
                </a>
                <ul class="dropdown-menu">
                
                  <li class="user-body rate_block_body">
                  <table style="width: 100%;">
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 22CT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_22ct']; ?></td>
                  	</tr>
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 24CT 10gm</th>
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
                 
                  <li class="user-footer rate_block_footer">
                    <table style="width: 100%;">
	                  	<tr>
	                  		<th style="width: 60%;">Updated on </th>
	                  		<td style="width: 40%;"><?php echo date("d-m-Y h:i:s a", strtotime($metal_rates['updatetime'])); ?></td>
	                  	</tr>
                  	</table>
                    
                  </li>
                </ul>
              </li>-->
			  
			   <!-- Current Rate Display -->
			  
              <li class="dropdown user user-menu rate_block_button">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="rate_block_button1">Current gold rate 22CT 1gm: </span><span class="header_rate"><b><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_22ct']; ?></b></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- Menu Body -->
                  <li class="user-body rate_block_body">
                  <table style="width: 100%;">
				  
				  <?php if($metalrate_set['enableGoldrateDisc']==1 && $metalrate_set['goldDiscAmt']!=''){?>
				  
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;"> Mjdma Gold 22CT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['mjdmagoldrate_22ct']; ?></td>
                  	</tr>
					
					<?php }?>
					<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 22CT 1gm</th>
                  		<td style="width: 40%;"><?php echo $comp_details['currency_symbol']." ".$metal_rates['goldrate_22ct']; ?></td>
                  	</tr>
                  	<tr style="border-bottom: 1px solid #bbab3a !important; height: 30px;">
                  		<th style="width: 60%;">Gold 24CT 10gm</th>
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
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?php echo base_url(); ?>assets/dist/img/no_image_available.png" class="user-image" alt="User Image"/>
                  <span class="hidden-xs"><?php echo ($username?ucfirst($username):'Admin'); ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="<?php echo base_url(); ?>assets/dist/img/no_image_available.png" class="img-circle" alt="User Image" />
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
              <img src="<?php echo base_url(); ?>assets/dist/img/no_image_available.png" class="img-circle" alt="User Image" />
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
<!-- branch_settings -->
<?php if($this->session->userdata('branch_settings')==1){?>				
		<input type="text" hidden  id="branch_set" name="branch_set" value=" <?php echo $this->session->userdata('branch_settings'); ?>"/>
		<?php } else{ ?>
		<input type="text" hidden id="branch_set" name="branch_set" value=" <?php echo $this->session->userdata('branch_settings'); ?>"/>
<?php } ?>
				
<!-- branch_settings -->