<html><head>
		<meta charset="utf-8">
		<title>Scheme Account Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/scheme_acc_receipt.css">
		<style >
		 .head
		 {
			 color: black;
			 font-size: 30px;
		 }
		 .headding ::after {
              content: ":";
              position: absolute;
              right: 10px;
            }
		
         </style>
</head><body style="margin-top:12%;">
<span class="PDFReceipt" >
        <div class="box">
            <div class="row" align="center"></br>
                <span style="font-size:14px;"><b>SAVING SCHEME BOND</b></span></br>
                <span style="font-size:14px;"><b><?php echo $scheme['scheme_name'];?></b></span>
            </div></br>
                    <table class="meta" style="width:40%;margin-left:5%;" align="left">
					<tr>
						<td>
							<span>Customer Name</span>
						</td>
						<td>
						    <b><?php echo $scheme['cus_name'].($scheme['lastname']!='' ? '-'.$scheme['lastname'] :'');?></b>
						</td>
					</tr>
					<tr>
						<td>
							<br><span>Account Number</span>
						</td>
						<td>
						    <br><b><?php echo $scheme['scheme_acc_number'];?></b>
						</td>
					</tr>
					
					<tr>
						<td>
							<br><span>Rate Fixed On</span>
						</td>
						<td>
						    <br><b><?php echo ($scheme['fixed_rate_on']!='' ? $scheme['fixed_rate_on'] :'-') ;?></b>
						</td>
					</tr>
					
					<tr>
						<td>
							<br><span>Paid Amount</span>
						</td>
						
						<td>
						    <?php if($scheme['firstPayment_amt']>0){ ?>
						        <br><?php  ?><b><input type="text" value="<?php echo 'Rs.'.$scheme['firstPayment_amt']?>" class="form-control" disabled style="border: 1px solid #222D32;height:20px;width:100px;margin-top:2px;"></b>
						    <?php }else{ ?>
						        <br><b>-</b>
						    <?php }?>
						</td>
					
					</tr>
					
				</table>
                	<table class="meta" style="width:40%;" align="right">
						<tr>
						<td>
							<span>Mobile No</span>
						</td>
						<td>
						    <b><?php echo $scheme['mobile'];?></b>
						</td>
					</tr>
					<tr>
						<td>
							<br><span>Start Date</span>
						</td>
						<td>
						    <br><b><?php echo $scheme['start_date'];?></b>
						</td>
					</tr>
					
					<tr>
						<td>
							<br><span>Fixed Rate</span>
						</td>
						<td>
						    <br><b><?php echo ($scheme['fixed_metal_rate']>0 ? $scheme['fixed_metal_rate'] :' - ');?></b>
						</td>
					</tr>
					
					<tr>
						<td>
							<br><span>Saved Weight</span>
						</td>
						<td>
						    <?php if($scheme['fixed_wgt']>0){ ?>
						        <br><?php  ?><b><input type="text" value="<?php echo $scheme['fixed_wgt'].' Grams'?>" class="form-control" disabled style="border: 1px solid #222D32;height:20px;width:100px;margin-top:2px;"></b>
						    <?php }else{ ?>
						        <br><b>-</b>
						    <?php }?>
						</td>
					</tr>
					
				</table>
        </div></br>
        <div class="row">
            <label><b>Terms and Conditions</b></label><br><br>	
            <?php echo $scheme['description']; ?>
        </div>
 </span>          
</body></html>