          </div>
        </div>						
            <div class="">
				<div class="row footer">
					<div class="col-md-4 viswa_copyright"> &copy; 2019 <a href="<?php echo base_url(); ?>" ><?php echo $footer_data['company_name'].'.'?> </a>  All Rights Reserved. </div>
				
					<!-- /span6 --> 
					<div class="col-md-4" style="margin-top: 9px;text-align:center;"><p> 			
						<!--Links disabled-->	
					<!--<a href="<?php echo base_url() ?>index.php/user/faq">FAQ</a> |-->
					
					
					<a href="#">Purchase Plan</a> |
					
					<a href="<?php echo base_url() ?>index.php/user/terms">Terms & Conditions</a> | 
					
					<a href="<?php echo base_url() ?>index.php/user/privacy">Privacy Policy</a>
					
					
					
					
					
					
					<!--links enabled--			
					<!--<a href="<?php echo $this->config->item('base_url'); ?>index.php/user/savingScheme">Saving & Scheme</a> | 
					<a href="<?php echo $this->config->item('base_url'); ?>index.php/user/Terms">Terms & Conditions</a> | 
					<a href="<?php echo $this->config->item('base_url'); ?>index.php/user/Privacy">Privacy Policy</a>-->
					</p></div>
					<div class="col-md-4 log_link"  align="right">Powered by <a target="_blank" href="http://www.logimaxindia.com/"><img src="<?php echo base_url('assets/img/logimax.png');?>"/></a></div>
					<!-- /span6 -->
					<!-- /span6 --> 
				</div>
						  <!-- /row --> 
			</div>
        <!-- /footer --> 
<!-- Le javascript
================================================== --> 
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
   <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  
<script type="text/javascript" src="https://cdn.datatables.net/r/dt/dt-1.10.8/datatables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/excanvas.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/chart.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url() ?>assets/js/bootstrap.js"></script>
<script src="<?php echo base_url() ?>assets/datepicker/js/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url() ?>assets/js/pages/contact.js"></script>
<script src="<?php echo base_url() ?>assets/js/common.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.dataTables.js" type="text/javascript" charset="utf8"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script> 
<!--<script src="<?php echo base_url() ?>assets/js/vendor/bootstrap.min.js"></script> -->
<script src="<?php echo base_url() ?>assets/js/plugins.js"></script> 
<script src="<?php echo base_url() ?>assets/js/main.js"></script>
 <!-- Select2 -->
<script src="<?php echo base_url(); ?>assets/select2/select2.full.min.js"></script>

<!--   referral -->

<script src="<?php echo base_url() ?>assets/js/pages/login.js"></script>



 
<script src="<?php echo base_url() ?>assets/js/pages/referral.js"></script>
<!--<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pages/referral.css">-->

<script src="<?php echo base_url() ?>assets/js/pages/gift_list.js"></script>
<!--   referral -->

<?php  
if ($page == 'signup') {?>
<script src="<?php echo base_url() ?>assets/js/pages/signup.js"></script>
<?php }  else if($page == 'kyc_form') {?>
<script src="<?php echo base_url() ?>assets/js/pages/kyc.js"></script>
<?php } else if($page == 'forgot') { ?>
<script src="<?php echo base_url() ?>assets/js/pages/forgot.js"></script>
<?php } else if($page == 'cust_enquiry') { ?>
<script src="<?php echo base_url() ?>assets/js/pages/enquiry.js"></script>
<?php } else if($page == 'dashboard') {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/dashboard.js"></script>
<?php } else if($page == 'scheme_join') { ?>
<script src="<?php echo base_url() ?>assets/js/pages/scheme_join.js"></script>
<?php } else if($page == 'schemes' || $page == 'rate_history' ) { ?>
<script src="<?php echo base_url() ?>assets/js/pages/schemes.js"></script>
<?php } else if($page == 'profile') {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/profile.js"></script>
<?php } else if($page == 'payment') {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/payment.js"></script>
<?php } else if($page == 'payHistory' || $page == 'giftcard' ) {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/payment_history.js"></script>
<?php }else if($page == 'otherPay') {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/otherpayment.js"></script>
<?php } else if($page == 'changeUser') {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/changeUser.js"></script>
<?php } else if($page == 'closeScheme') {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/closeScheme.js"></script>
<?php } else if($page == 'reset_pass') {  ?>
<script src="<?php echo base_url() ?>assets/js/pages/changeUser.js"></script>
<?php } else if($page == 'purchase') { ?>
<script src="<?php echo base_url() ?>assets/js/pages/purchase.js"></script>
<?php } ?>
<script src="<?php echo base_url(); ?>assets/js/popover-jquery.js"></script>
<script src="<?php echo base_url() ?>assets/js/full-calendar/fullcalendar.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/js/base.js"></script> 

	
</body>

</html>

