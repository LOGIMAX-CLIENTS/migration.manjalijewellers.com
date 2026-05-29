<html><head>
	<meta charset="utf-8">
	<title>Passbook</title>
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/a4_passbook.css">
	<style >
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
    /*#header { position: fixed; border-bottom:1px solid gray;}*/
    #footer { bottom:75px;position: fixed; border-top:1px solid gray;} .pagenum:before { content: counter(page); } 
	.row{
		display: table;
      //  height: 1.8cm;
	}
	.c1{
		float: left;
		width: 30%;
        height:100%
	}
	.c2{
		float: right;
		width: 70%;
        height:100%
	}

	@page {
		margin-top: 0px;
		size: 85mm 20mm
	}
	
	</style>
</head>

<body style="font-weight:bold;font-size:12px;font-family: 'Open Sans', sans-serif,'Bodrum Sweet';">
    <!-- <?php
        function moneyFormatIndia($num) {
            return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
        }
            ?> -->

        
    <div class="row" >
    <div class="c1">
         <img style="height:20px;width:80px;margin-left:-40px;margin-top:10px;" src="<?php echo $customer['bar_code'];?>"><br/>
         <label style="margin-left:-30px;"><?php echo strtoupper($customer['account_name']); ?></label>
         
         
        <!--  <label><?php echo "Acc No"; ?>  &nbsp;&nbsp;&nbsp;&nbsp; :        <?php echo $customer['scheme_acc_number'];?></label> <br>
        <label> <?php echo "Code"; ?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : <?php echo $customer['code'];?> </label><br>
        <label> <?php echo "Mob No"; ?> &nbsp; &nbsp; : <?php echo $customer['mobile'];?> </label><br> -->
        <!-- <label> <?php echo "Start Date"; ?> : <?php echo $customer['start_date'];?> </label> -->
     </div>
      <div class="c2">
     <br/>
    <label style="margin-left:-10px;margin-top:20px;"> <?php echo $customer['code'].' - '. $customer['scheme_acc_number'];?> </label>
        <!-- <label style="font-weight:bold;margin-left:25px;">&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; <?php echo $customer['id_scheme_account'];?> </label> -->
    </div>
    </div>
</body></html>

<script type="text/javascript">

	window.print();

</script>