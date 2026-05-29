<html><head>
	<meta charset="utf-8"> 
	<title>Billing Receipt</title>
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/passbook.css">
	<style type="text/css">
	@font-face {
        font-family: 'Bamini Tamil';
        src: url('https://retail.logimaxindia.com/etail_v1/admin/custom_fonts/BaaminiPlain.ttf');
    }
    
	#terms{
	    font-family: 'Bamini Tamil';
	}
	 .head
	 {
		 color: black;
		 font-size: 30px;
	 }
	 .alignCenter {
		 text-align: center;
	 }
	 .alignRight {
		 text-align: right;
	 }
	 .table_heading {
		 font-weight: bold;
	 }
	 .textOverflowHidden {
		white-space: nowrap; 
		overflow: hidden;
		text-overflow: ellipsis;
	 }
	 .border{
	     border-bottom : 2px solid #000 !important;
	 }
    /* .table.row-lines {
        border-collapse: collapse;
        width: 100%;
    }

    .table.row-lines td,
    .table.row-lines th {
        border: none;               
        border-bottom: 1px solid #000; 
        padding: 4px 6px;
        text-align: center;
    }
    .table.row-lines tr:first-child td {
        border-top: 2px solid #000;   
    }

    .table.row-lines tr:last-child td {
        border-bottom: 2px solid #000;
    } */

    .table.row-lines {
    border-collapse: collapse;
    width: 100%;
    }

    .table.row-lines td,
    .table.row-lines th {
        border: none;
        padding: 4px 6px;
        text-align: center;
    }

    /* only rows from ELSE block */
    .table.row-lines tr.with-border td {
		border-top: 0;   
        border-bottom: 1px solid #000;
    }

    /* first and last row rules */
    .table.row-lines tr.with-border:first-child td {
        /* border-top: 1px solid #000; */
    }
    .table.row-lines tr.with-border:last-child td {
        border-bottom: 1px solid #000;
    }


    .company_details,
    .company_placeholder {
        text-align: center;        /* fixed height */
        margin: 0;
        padding: 0;
        justify-content: center; /* center text vertically */
    }
    .company_name{
        font-size: 13px !important;        
        font-weight: bold;      
        margin: 0;              
        padding: 1px 0;
    }

    @page {
    margin-top: 0;          /* removes default Dompdf top margin */
}
    .company_placeholder {
    height: 40px;
}
.company_details{
    border-bottom: 2px solid #000;
}
.table.row-lines tr.table_heading_tr td {
    border-bottom: 1px solid #000;
}



    </style>
</head>
<body>
    <?php
        function moneyFormatIndia($num) {
            return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
        }
    ?>
    <span class="PDFReceipt"> 
        <div class="hare_krishna"></div>
        <!--<div class="header_top"></div>-->
		<?php if($customer['one_time_premium'] == 0){ ?>
		<div  class="content-wrapper">
        	<div class="box">
        		<div class="box-body">
        			<div  class="container-fluid">
        				<div id="printable"> 
        					<!--<hr class="header_dashed"> -->
        					<div class="col-xs-12">
        						<div class="table-responsive">
        						   <?php 
            						    if($payment[0]['is_print_taken'] == 0){ ?>
                                        <div class="company_details">
                                        <h5 class="company_name"><?= $company['company_name'] ?></h5>
                                        <span class="company_address"><?= $company['address1'] .", ".$company['address2'] .", ". $company['city_name'] ." - ". $company['pincode'] ?></span>
                                        </div>
                                        <?php }else{?>
                                           
                                           <div class="company_placeholder"></div>
                                           
                                           <?php }?>
            						<table id="" class="table text-center row-lines" >
            						    <?php 
            						    if($payment[0]['is_print_taken'] == 0){ ?>
            						    <tr class="table_heading_tr">
											<td class="table_heading col" style="width: 10%">Date</td>
											<td class="table_heading col" style="width: 8%">Rate</td>
											<td class="table_heading col" style="width: 10%">Amount</td>
											<td class="table_heading col" style="width: 10%">Weight</td>
											<td class="table_heading col" style="width: 10%">Signature & Seal</td>
										</tr> 
                						<?php
            						    }else{ ?>
            						        <tr><td> </td><td> </td><td> </td><td> </td><td> </td></tr>
            						    <?php } 
                						$tot_wgt = 0;
                						foreach($payment as $pay){
                						    $tot_wgt = $tot_wgt + $pay['metal_weight'];
                						    if($pay['is_print_taken'] == 1){ ?>
                						        <tr class="no-border">
                								    <td> </td><td> </td><td> </td><td> </td><td> </td>
            									</tr>
                						   <?php }else{ ?>
                						       <tr class="with-border">
                									<td><?php echo $pay['date_payment'];?></td> 
                									<td><?php echo $pay['metal_rate'];?></td> <!--class="alignRight"  class='textOverflowHidden'-->
                									<td>
                									    <?php echo (number_format((float)($pay['payment_amount']),0,'.',''));?>
                									    
                									 </td>
                                                    <td><?php echo $pay['metal_weight'];?></td> 
                                                    <td></td>
                								
            								  </tr>
            						    <?php } } ?>
            						</table>
            					</div>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
        </div>
		<?php } ?>
		
		<!-- <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> -->
        <!--<div  class="content-wrapper">
                	<div class="box">
                		<div class="box-body">
                		    <div style="width: 14%; text-align:center;">Terms and Conditions</div>
                		    <p id="terms"><?php echo $customer['description'] ?></p>
                		    </div>
                		    </div>
                		    </div>-->
    </span>    
    
    
    
</body></html>