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

         

         figure {

              display: inline-block;

            }



	</style>



<style>

        .image-container {

            display: table;

            width: 100%;

            table-layout: fixed;

            border-spacing: 10px; /* Adjust the gap value as per your requirement */

        }



        .image-wrapper {

            display: table-cell;

            text-align: center;

            vertical-align: middle;

            width: 33.33%; /* Set the width to one-third of the container to fit three images per row */

        }



        .image-container img {

            width: 100px;

            height: 100px;

            object-fit: cover; /* Ensures the image fits within the 100x100 dimensions */

        }

    </style>



</head><body>

<span class="PDFReceipt">

    
			 <div style="width: 100%; text-transform:uppercase;font-size: 11px !important;" align="center;">

			        <div style="display: inline-block; width: 100%;">

                          <img alt=""  src="<?php echo base_url();?>assets/img/logimax.png" ><br>

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

        					<div style="width: 100%; display: inline-block; margin-top: -2px"> <?php echo $karigar['karigar_name']; ?></div>

        				  </div>

        				  

        				  <div style="text-align: left; width:100%;height: 18px; ">

        					<div style="width: 50%; display: inline-block"> Due date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; </div>

        					<div style="width: 50%; display: inline-block; margin-top: -2px"> <?php echo $order['smith_due_date']; ?></div>

        				  </div>

        				  

    				</div>

    				

    				<div style="display: inline-block; width: 18%;font-size: 12px;margin-top:-6%;margin-left:10%;">

    				    <label><b><?php echo ($order['order_for']==3 ? "REPAIR ORDER" : "ORDER DETAILS" )?></b></label>

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

                                         $grand_total_wt=0;

    									 $grand_total_pcs=0;

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

                                                         $grand_total_wt+=($weight['approx_wt']*$weight['tot_items']);

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

                                                        $grand_total_pcs+=$weight['tot_items'];

                                                        ?>

                                                            <td class="order_items" style="text-align:center;"><?php echo $weight['tot_items'];?></td>

                                                        <?php } ?>

                                                       

                                                </tr>

                                                

    									        <?php }?>

    									       

    									    </table><br>

    									    <label><b>TOTAL PCS : <?php echo $total_total_items;?> ; APPROX WT : <?php echo number_format($total_approx_wt,3,'.','');?> </b></label></br>

    									    <?php 

    									    if($items['description']!='')

    									    {?>

    									        </br><label><b>Remarks:</b></label></br>

    									        <label><?php echo $items['description'];?></label>

    									   <?php }

    									    ?>

    									  </div></br>

                                    <?php $i++; } ?>

                                        <br><label><b>GRAND TOTAL PCS : <?php echo $grand_total_pcs;?> ; APPROX WT : <?php echo number_format($grand_total_wt,3,'.','');?> </b></label></br>

                                    <?php }?>

                                    

                                    <?php 

                                    if($order['order_for']==2 || $order['order_for']==3)

                                    {?>

                                        

                                        	<div  class="row">

        										<div class="col-xs-12">

        											<div class="table-responsive">

        											<table id="pp" class="table text-center">

        												<!--	<thead> -->

        														<tr>

        															<td><hr class="old_metal_dashed" style="width:700px !important;"></td>

        														</tr>

        														<tr>

        															<td class="table_heading" width="15%;">S.No</td>

        															<td class="table_heading" width="20%;">OrderNo</td>

        															<td class="table_heading" width="20%;">Description</td>

        															<td class="table_heading" width="20%;">Design</td>

        															<td class="table_heading" width="25%;">Sub Design</td>

        															<td class="table_heading alignRight" width="20%;">PCS</td>

        															<td class="table_heading alignRight" width="20%;">Gwt(g)</td>

																	<td class="table_heading alignRight" width="20%;">Nwt(g)</td>

																	<td class="table_heading alignRight" width="20%;">Size</td>

        														</tr>

        														<tr>

        															<td><hr class="old_metal_dashed" style="width:700px !important;"></td>

        														</tr>

        													<!--</thead>

        													<tbody>-->

        														<?php 

        														$i=1; 

        														$weight=0;

																$net_wt = 0;

        														foreach($order_items as $items){

        														$weight+=$items['weight'];

																$net_wt+=$items['net_wt'];

        														?>

        															<tr>

        																<td><?php echo $i;?></td>

        																<td><?php echo $items['cusorderno'];?></td>

        																<td><?php echo $items['product_name'];?></td>

        																<td><?php echo $items['design_name'];?></td>

        																<td><?php echo $items['sub_design_name'];?></td>

        																<td class="alignRight"><?php echo $items['tot_items'];?></td>

        																<td class="alignRight"><?php echo $items['weight'];?></td>

																		<td class="alignRight"><?php echo $items['net_wt'];?></td>

																		<td class="alignRight"><?php echo $items['size'];?></td>

        															</tr>

        															<?php 

        															if(sizeof($items)>0)

        															{

        															foreach($items['stone_details'] as $val)

        															{?>

        															    <tr>

        															        <td></td>

        															        <td colspan="4"><?php echo $val['stone_name']?></td>

        															        <td><?php echo $val['pieces']?></td>

        															        <td><?php echo $val['wt'].'/'.$val['uom_name']?></td>

        															    </tr>

        															<?php }

        															?>

        															    

        															<?php }

        															?>

        															<?php

        															if($items['description']!='')

        															{?>

        															    <tr>

        															        <td></td>

        															        <td colspan="3">Remarks :- <?php echo $items['description']?> </td>

        															    </tr>

        															<?php }

        															?>

        														<?php $i++;}

        														?>

        														

        												<!--</tbody> -->

        													<tr>

        														<td><hr class="old_metal_dashed" style="width:700px !important;"></td>

        													</tr>

        													

        													<tr>

        														<td>Total</td>

        														<td></td>

        														<td></td>

        														<td></td>

        														<td></td>

																<td></td>

        														<td class="alignRight"><?php echo number_format($weight,3,'.','');?></td>

        														<td class="alignRight"><?php echo number_format($net_wt,3,'.','');?></td>

        													</tr>

        													

        													<tr>

        														<td><hr class="old_metal_dashed" style="width:700px !important;"></td>

        													</tr>

        													

        												</table><br><br>

        												

        												<?php 

                            							    if(sizeof($order_items)>0)

                            							    {

                            							         foreach($order_items as $ordDetails)

                            							         {

                            							            foreach($ordDetails['images'] as $image)

                            							            {?>

                                								        <div><figure><a href="<?php echo base_url().'assets/img/order/purchase_order/'.$image['image'];?>"><img  src="<?php echo base_url().'assets/img/order/purchase_order/'.$image['image'];?>" width="200" height="200"></a><figcaption><?php echo $image['orderno'];?></figcaption></figure></div>

                                                                         

                            							            <?php }

                            							         ?>

                            							            

                            							         <?php }

                            							    }

                        							    ?>

							    

        											</div>	

        										</div>	

        									</div><br>

									

                                    <?php }

                                    ?>

 

                                <!-- <?php 

    							    if(sizeof($items['img_details'])>0)

    							    {



    							         foreach($items['img_details'] as $image)

    							         {?>

    							            <div class="col-md-4">

    								            <a href="<?php echo base_url().'assets/img/order/purchase_order/'.$image['image_name'];?>"><img  src="<?php echo base_url().'assets/img/order/purchase_order/'.$image['image_name'];?>" width="50" height="50"></a>

    								        </div> 

    							         <?php }

    							    }

							    ?> -->



							<div class="image-container">

										<?php

										if (sizeof($items['img_details']) > 0) {

											foreach ($items['img_details'] as $image) {

												$imageUrl = base_url() . 'assets/img/order/purchase_order/' . $image['image_name'];

												echo '<div class="image-wrapper">';

												echo '<a href="' . $imageUrl . '"><img src="' . $imageUrl . '" alt="Image"></a>';

												echo '</div>';

											}

										}

										?>

									</div>

								

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
							 
							 
							 <div class="col-xs-12" style="width: 100%;margin-top:100px">

								<table>
									<tr>
										<td style="width: 25%;text-align:center;">Audit by</td>
										<td style="width: 25%;text-align:center;">Party</td>
										<td style="width: 25%;text-align:center;">Manager</td>
										<td style="width: 25%;text-align:center;">Operator</td>
									</tr>
								</table>

                				    
							 </div>  
							 

				</div>				

				

 </div>

 </div><!-- /.box-body --> 

</div>



 </span>          

</body></html>