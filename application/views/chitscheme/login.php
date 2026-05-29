<?php $data=$this->login_model->company_details(); ?>
<link href="<?php echo base_url() ?>assets/css/pages/signin.css" rel="stylesheet" type="text/css">
<div class="main-container">
	<div  class="container-fluid " align="center">
		<div class="loginForm" >
			<div class="container-fluid header">
				<div class="container">	
					<div class="member_login">
						<div class="">
							<?php 
								$attributes 		=	array('id' => 'loginForm', 'name' => 'loginForm');
								echo form_open_multipart('user/validateUser',$attributes);  
							?>
							<!--<span class="title">MEMBER LOGIN</span>-->
							<h3 class="titleLogin theme-txt">LOGIN</h3>	
							<div class="login-fields" align="center">
								<?php if($this->session->flashdata('successMsg')) { ?>
								<div class="alert alert-success" align="center">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
								</div>      
								<?php } else if($this->session->flashdata('errMsg')) {  ?>							 
								<div class="alert alert-danger" align="center">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
								</div>
								<?php } ?>
								<div class="login_register">
									<a href="<?php echo base_url() ?>index.php/user/login" title="Click here to sign in" rel="tooltip">Login</a> | <a href="<?php echo base_url(); ?>index.php/user/register_add" title="Click here to sign-up" rel="tooltip">New User?</a>
								</div>
								<div class="login_form">
									<div class="form-group">
										<label>Mobile Number</label>
										<input type="text" id="username" name="username" value="" placeholder="Registered Mobile Number" class="form-control"/>
									</div>
									<div class="form-group">
										<label for="password">Password:</label>
										<input type="password" id="passwd" name="passwd" value="" placeholder="Password" class="form-control"/>
									</div>
									<button type="submit" class="login_button"  value="LOGIN">LOGIN</button>
								</div>
							</div>
							<div class="login-actions">
								<span class="pull-left">
									<label class="remember_me" for="remember_me">
										<input id="remember_me" name="remember_me" type="checkbox" class="checkbox-inline" />&nbsp;Remember Me
									</label>
								</span> 
								<span class="pull-right ">
									<a class="forget_pwd" href="<?php echo base_url(); ?>index.php/user/forget"  title="Click here to reset password" rel="tooltip">Forgot Password?</a>
								</span>
							</div>
							<div   class="cmp_details theme-txt">
								<div >Customer Care | <?php echo (!empty($data['phone1'])?$data['mob_code'].'  '.$data['phone1']:'');?></div>
								<?php if($data['tollfree1']!='') { ?>
								<div >Toll-Free number | <?php echo (!empty($data['tollfree1'])?$data['tollfree1']:'');?></div>
								<?php } ?>
								<div>E-mail | <?php echo $data['email'];?></div>  	
							</div>
							</form>
						</div> <!-- /content -->
					</div><!-- /member_login -->
				</div><!--container-->	
			</div><!--container-fluid header-->
		</div>
	</div>