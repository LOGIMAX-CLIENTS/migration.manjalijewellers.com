<html><head>
		<meta charset="utf-8">
		<title>Vendor Acknowledgement - <?php echo $order['pur_no'];?> </title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/order_ack.css">
	
		<style type="text/css">
		body, html {
		margin-bottom:0
		}
		 span { display: inline-block; }
		 
		 .order_details ,.order_items
		 {
		        border: 0.1px solid black;
		        border-collapse: collapse;
         }

	</style>
</head><body>
<span class="PDFReceipt">
      
			 <div style="width: 100%; text-transform:uppercase;font-size: 11px !important;" align="center;">
			        <div style="display: inline-block; width: 100%;">
                          <img alt=""  src="<?php echo base_url();?>assets/img/receipt_logo.jpg" ><br>
                          <label><?php echo 'GST NO : '.$comp_details['gst_number'];?></label><br>
                          <label><?php echo 'CIN NO : '.$comp_details['cin_number'];?></label><br><br>
    				</div>
			    </div><br>
			    
			    <div style="width: 100%; text-transform:uppercase;font-size: 11px !important;">

    				<div style="display: inline-block; width: 30%;font-size: 11px;">
    				      <div style="text-align: left; width:100%;height: 18px; ">
        					<div style="width: 50%; display: inline-block"> Order Date &nbsp;&nbsp; : &nbsp; </div>
        					<div style="width: 50%; display: inline-block; margin-top: -2px"> <?php echo $order['order_date']; ?></div>
        				  </div>
        				  
        				  <div style="text-align: left; width:100%;height: 18px; ">
        					<div style="width: 50%; display: inline-block"> PO NO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; </div>
        					<div style="width: 50%; display: inline-block; margin-top: -2px"> <?php echo $order['pur_no']; ?></div>
        				  </div>
        				  
        				  <div style="text-align: left; width:100%;height: 18px; ">
        					<div style="width: 50%; display: inline-block"> Karigar &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; </div>
        					<div style="width: 50%; display: inline-block; margin-top: -2px"> <?php echo $karigar['karigar_name']; ?></div>
        				  </div>
        				  
    				</div>
    				
    				<div style="display: inline-block; width: 18%;font-size: 12px;margin-top:-5%;margin-left:10%;">
    				    <label><b>ORDER DETAILS</b></label>
    				</div>
    		
    				
    				<div style="display: inline-block; width: 50%;margin-left:18%;">
                          <label><?php echo $comp_details['company_name'];?></label>
                          <label><?php echo ($comp_details['address1']!='' ? '<br>'.''.$comp_details['address1'] :'')?></label>
                          <label><?php echo ($comp_details['address2']!='' ? '<br>'.''.$comp_details['address2'] :'')?></label>
                          <label><?php echo ($comp_details['address3']!='' ? '<br>'.''.$comp_details['address3'] :'')?></label>
                          <label><?php echo ($comp_details['city']!='' ? '<br>'.''.$comp_details['city'].($comp_details['pincode']!='' ? ' - '.$comp_details['pincode'] :'') :'')?></label>
                          <label><?php echo ($comp_details['state']!='' ? '<br>'.''.$comp_details['state'] :'')?></label>
                          <label><?php echo ($comp_details['country']!='' ? '<br>'.''.$comp_details['country'] :'')?></label>
    				</div>
			</div>
			
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div id="printable">
                                    <?php 
                                    
                                    if($order['order_for']==1)
                                    {
                                        $i=1;
                                        foreach($order_details as $items)
                                        {?>
                                         <lebel><b><?php echo $i.' . '.$items['product_name'].' - '.$items['design_name'].' - '.$items['sub_design_name'];?></b></lebel><br><br>
                                         <div class="table-responsive">
    									    <table id="order_details" class="table text-center order_details">
    									        <?php 
    									        if(sizeof($items['weight_details'])>0)
    									        {?>
    									           <tr>
    									            <td class="order_items" width="15%;" style="text-align:center;font-weight:bold;">Weight</td>
    									            <?php 
    									                 $total_approx_wt=0;
                                                        foreach($items['weight_details'] as $weight)
                                                        {
                                                         $total_approx_wt+=($weight['approx_wt']*$weight['tot_items']);
                                                        ?>
                                                            <td class="order_items" style="text-align:center;"><?php echo $weight['weight_range'];?></td>
                                                        <?php } ?>
    									        <?php }?>
    									                
    									            </tr>
    									        
    									        <?php 
    									        if(sizeof($items['size_details'])>0)
    									        {?>
    									        <tr>
    									            <td class="order_items" width="15%;" style="text-align:center;font-weight:bold;">Size</td>
    									            <?php 
                                                        foreach($items['size_details'] as $weight)
                                                        {?>
                                                            <td class="order_items" style="text-align:center;"><?php echo ($weight['size']!='' ? $weight['size'] :'-');?></td>
                                                        <?php } ?>
                                                    
                                                </tr>
    									        <?php }?>
    									        
    									        
    									        <?php 
    									        if(sizeof($items['pcs_details'])>0)
    									        {?>
    									        <tr>
    									            <td class="order_items" width="15%;" style="text-align:center;font-weight:bold;">Pcs</td>
    									            <?php 
    									                $total_total_items=0;
                                                        foreach($items['pcs_details'] as $weight)
                                                        {
                                                        $total_total_items+=$weight['tot_items'];
                                                        ?>
                                                            <td class="order_items" style="text-align:center;"><?php echo $weight['tot_items'];?></td>
                                                        <?php } ?>
                                                       
                                                </tr>
                                                
    									        <?php }?>
    									       
    									    </table><br>
    									    <label><b>Total Pcs : <?php echo $total_total_items;?> ; Approx Wt : <?php echo number_format($total_approx_wt,3,'.','');?> </b></label></br>
    									    <?php 
    									    if($items['description']!='')
    									    {?>
    									        </br><label><b>Remarks:</b></label></br>
    									        <label><?php echo $items['description'];?></label>
    									   <?php }
    									    ?>
    									  </div></br>
                                    <?php $i++; } }?>
                                    
                                    <?php 
                                    if($order['order_for']==2)
                                    {?>
                                        
                                        	<div  class="row">
        										<div class="col-xs-12">
        											<div class="table-responsive">
        											<table id="pp" class="table text-center">
        												<!--	<thead> -->
        														<tr>
        															<td><hr class="old_metal_dashed"></td>
        														</tr>
        														<tr>
        															<td class="table_heading" width="15%;">S.No</td>
        															<td class="table_heading" width="25%;">Description</td>
        															<td class="table_heading" width="20%;">Design</td>
        															<td class="table_heading alignRight" width="20%;">PCS</td>
        															<td class="table_heading alignRight" width="20%;">Gwt(g)</td>
        														</tr>
        														<tr>
        															<td><hr class="old_metal_dashed"></td>
        														</tr>
        													<!--</thead>
        													<tbody>-->
        														<?php 
        														$i=1; 
        														$weight=0;
        														foreach($order_items as $items){
        														$weight+=$items['weight'];
        														?>
        															<tr>
        																<td><?php echo $i;?></td>
        																<td><?php echo $items['product_name'];?></td>
        																<td><?php echo $items['design_name'];?></td>
        																<td class="alignRight"><?php echo $items['totalitems'];?></td>
        																<td class="alignRight"><?php echo $items['weight'];?></td>
        															</tr>
        														<?php $i++;}
        														?>
        														
        												<!--</tbody> -->
        													<tr>
        														<td><hr class="old_metal_dashed"></td>
        													</tr>
        													
        													<tr>
        														<td>Total</td>
        														<td></td>
        														<td></td>
        														<td></td>
        														<td class="alignRight"><?php echo number_format($weight,3,'.','');?></td>
        													</tr>
        													
        													<tr>
        														<td><hr class="old_metal_dashed"></td>
        													</tr>
        													<?php if($amount>0){?>
        													<tr>
        															<td colspan="3"><?php echo 'Received with thanks from Mr./Ms.'.$billing['customer_name'].' Towards Order  No : '.$order_no.'';?></td>
        															<td colspan="2"><?php echo 'Rs. '.moneyFormatIndia($amount);?></td>
        													</tr>
        													<?php }?>
        												</table><br>	
        											</div>	
        										</div>	
        									</div><br>
									
                                    <?php }
                                    ?>
                                    
                                <?php 
    							    if(sizeof($items['img_details'])>0)
    							    {
    							         foreach($items['img_details'] as $image)
    							         {?>
    							            <div class="col-md-4">
    								            <a href="<?php echo base_url().'assets/img/order/purchase_order/'.$image['image_name'];?>"><img  src="<?php echo base_url().'assets/img/order/purchase_order/'.$image['image_name'];?>" width="50" height="50"></a>
    								        </div> 
    							         <?php }
    							    }
							    ?>
                				    
                				<div class="col-xs-12">
							     <?php 
							     if(sizeof($description)>0)
							     {?>
							     <label><b>General Instructions</b></label>
							     <?php
							         foreach($description as $des)
							         {?>
							             <p><?php echo $des['description'];?></p>
							         <?php }
							     }
							     ?>
							 </div>    
                				    
							 
				</div>				
				
 </div>
 </div><!-- /.box-body --> 
</div>

 </span>          
</body></html>