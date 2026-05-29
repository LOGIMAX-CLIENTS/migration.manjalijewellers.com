<html>
<head>
    <style type='text/css'>
        body {font-family: Verdana, Geneva, sans-serif}

        h3 {color:#4C628D}

        p {font-weight:bold;
		color: black !important;}
		.im {color: black !important;}
	
    </style>
</head>
    <body>
    <h3>Order Details</h3>
        <div style="display: inline-block; width: 100%;">
		    	    <div style="display: inline-block;">
		    	 <?php 
		    	 $tot_pcs=0;
		    	 $approx_wt=0;
		    	 foreach($orders as $items)
		    	 {
		    	     $tot_pcs+=$items['tot_items'];
		    	     $approx_wt+=($items['approx_wt']*$items['tot_items']);
		    	 }
		    	 ?>
                <table class="meta" style="font-weight:bold;text-transform:uppercase;" align="right">
    				<tr>
    					<td><span >po no</span></td>
    					<td> : </td>
    					<td><span style="text-align:right;"><?php echo $order['pur_no'];?></span></td>
    				</tr>
    				<tr>
    					<td><span>order date</span></td>
    					<td> : </td>
    					<td><span style="text-align:right;"><?php echo $order['order_date'];?></span></td>
    				</tr>
    				<tr>
    					<td><span>Due date</span></td>
    					<td> : </td>
    					<td><span style="text-align:right;"><?php echo $order['smith_due_date'];?></span></td>
    				</tr>
					<tr>
						<td><span>Total Pcs</span></td>
						<td> : </td>
						<td><span style="text-align:right;"><?php echo $tot_pcs;?></span></td>
    				</tr>
    				<tr>
						<td><span>Approx Weight</span></td>
						<td> : </td>
						<td><span style="text-align:right;"><?php echo number_format(($approx_wt),3,'.',''),'GM';?></span></td>
    				</tr>
    				<tr>
    					<td><span>Emp</span></td>
    					<td> : </td>
    					<td><span style="text-align:right;"><?php echo $order['emp_name'];?></span></td>
    				</tr>
    			</table>
    			
            </div>
        </div></br>
        <p>For More Details Please Find The Below Attachement</p>
    </body>
</html>