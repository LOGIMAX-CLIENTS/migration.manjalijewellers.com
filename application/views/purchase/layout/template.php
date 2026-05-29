<?php $this->load->view('purchase/layout/header',$header_data); ?>

<?php $this->load->view('include/contact'); ?>

<?php
/*if($this->session->userdata('allow_referral')==1){
 $this->load->view('include/referral');  
} */?>

<?php $this->load->view($fileName, $content); ?>

<?php $this->load->view('purchase/layout/footer',$footer_data); ?>