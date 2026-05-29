<?php $comp_details=$this->admin_settings_model->get_company();
 ?>

 <footer class="footer-login center loginfooter">

       <strong>Copyright &copy; <?php echo date("Y"); ?> <a href="<?php echo $comp_details['website']; ?>" target="blank"><?php echo $comp_details['company_name'];?></a>.</strong> All rights reserved.
        
        <div class="pull-right">

         <!-- <b>Version</b> 1.0 <strong>{elapsed_time}</strong> seconds -->

		 Powered by <a href="http://www.logimaxindia.com" target="_blank"><img src="<?php echo base_url('assets/img/logimax.png');?>"/></a>

        </div>

    

        

      </footer>

      
