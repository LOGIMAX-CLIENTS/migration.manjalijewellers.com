<link href="<?php echo base_url() ?>assets/css/pages/myconversions.css" rel="stylesheet">



<div class="main-container">
<!-- main -->		  
<div class="main" >
<!-- main-inner --> 
<div class="main-inner">
 <!-- container --> 
<div class="container">
<!-- alert -->
<div class="row">
    <div id="ag-dash-history" class="col-md-12">
    <div align="center"><legend class="head">CONVERSION HISTORY</legend></div>
        
        <?php
        if($this->session->flashdata('successMsg')) { ?>
        <div class="alert alert-success" align="center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
        </div>      
        <?php } if($this->session->flashdata('errMsg')) { ?>							 
        <div class="alert alert-danger" align="center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
        </div>
        <?php }?>
    
        <div class="table-responsive">
        <table id="conversion_table" class="table table-bordered table-striped table-responsive display">
            <thead>
                <tr>
                    <th width="12%">Customer Name</th>
                    <th width="10%">Customer Mobile</th>
                    <th width="10%">Scheme Account Number</th>
                    <th width="10%">Scheme Account Name</th>
                    <th width="17%">Joined Date</th>
                    <th width="10%">Paid Amount</th>
                    <th width="18%">Paid Date</th>
                    <th width="3%">Commission</th>
                    <th width="3%">Unsettled Commission</th>
                    <th width="3%">Status</th>
                </tr>
            </thead>
            
           <!-- <tbody>
            
            <?php  if($content['data'] != '') { foreach($content['data'] as $key => $value){  ?>  
            
                <tr class="">
                    <td><?php echo $value['cus_name']; ?></td>
                    <td><?php echo $value['mobile']; ?></td>
                    <td><?php echo $value['scheme_acc_number']; ?></td>
                    <td><?php echo $value['account_name']; ?></td>
                    <td><?php echo $value['date_add']; ?></td>
                    <td><?php echo $value['payment_amount']; ?></td>
                    <td><?php echo $value['date_payment']; ?></td>
                    <td><?php echo $value['cash_point']; ?></td>
                    <td><?php echo $value['unsettled_cash_pts']; ?></td>
                    <td><?php echo $value['status']; ?></td>
                </tr>
                
            <?php } } else { ?>
            <tr class="">
                <td><?php echo $content['message']; ?></td>
            </tr>
                    <?php } ?>
            </tbody>  -->
            
        </table>
        </div>
    </div>
</div>
</div>		
<!-- /alert -->  
</div>
<!-- /container --> 
</div>
<!-- /main-inner --> 
</div>
</div>
<!-- /main -->		  

<br />
<br />
<br />

<script type="text/javascript" src="https://cdn.datatables.net/r/dt/dt-1.10.8/datatables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/pages/dashboard.js"></script>
