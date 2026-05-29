<?php

ob_start();

$strNo = rand(1,1000000);

date_default_timezone_set('Asia/Calcutta');

//echo date_default_timezone_get();

$strCurDate = date('d-m-Y');

require_once 'TransactionRequestBean.php';
require_once 'TransactionResponseBean.php';

session_start();

if($_POST && isset($_POST['submit'])){

    $val = $_POST;

    $_SESSION['iv'] = $val['iv'];
    $_SESSION['key']   = $val['key'];

    $transactionRequestBean = new TransactionRequestBean();

    //Setting all values here
    $transactionRequestBean->setMerchantCode($val['mrctCode']);
    $transactionRequestBean->setAccountNo($val['tpvAccntNo']);
    $transactionRequestBean->setITC($val['itc']);
    $transactionRequestBean->setMobileNumber($val['mobNo']);
    $transactionRequestBean->setCustomerName($val['custname']);
    $transactionRequestBean->setRequestType($val['reqType']);
    $transactionRequestBean->setMerchantTxnRefNumber($val['mrctTxtID']);
    $transactionRequestBean->setAmount($val['amount']);
    $transactionRequestBean->setCurrencyCode($val['currencyType']);
    $transactionRequestBean->setReturnURL($val['returnURL']);
    $transactionRequestBean->setS2SReturnURL($val['s2SReturnURL']);
    $transactionRequestBean->setShoppingCartDetails($val['reqDetail']);
    $transactionRequestBean->setTxnDate($val['txnDate']);
    $transactionRequestBean->setBankCode($val['bankCode']);
    $transactionRequestBean->setTPSLTxnID($val['tpsl_txn_id']);
    $transactionRequestBean->setCustId($val['custID']);
    $transactionRequestBean->setCardId($val['cardID']);
    $transactionRequestBean->setKey($val['key']);
    $transactionRequestBean->setIv($val['iv']);
    $transactionRequestBean->setWebServiceLocator($val['locatorURL']);
    $transactionRequestBean->setMMID($val['mmid']);
    $transactionRequestBean->setOTP($val['otp']);
    $transactionRequestBean->setCardName($val['cardName']);
    $transactionRequestBean->setCardNo($val['cardNo']);
    $transactionRequestBean->setCardCVV($val['cardCVV']);
    $transactionRequestBean->setCardExpMM($val['cardExpMM']);
    $transactionRequestBean->setCardExpYY($val['cardExpYY']);
    $transactionRequestBean->setTimeOut($val['timeOut']);

   // $url = $transactionRequestBean->getTransactionToken();

    $responseDetails = $transactionRequestBean->getTransactionToken();
    $responseDetails = (array)$responseDetails;
    $response = $responseDetails[0];

    if(is_string($response) && preg_match('/^msg=/',$response)){
        $outputStr = str_replace('msg=', '', $response);
        $outputArr = explode('&', $outputStr);
        $str = $outputArr[0];

        $transactionResponseBean = new TransactionResponseBean();
        $transactionResponseBean->setResponsePayload($str);
        $transactionResponseBean->setKey($val['key']);
        $transactionResponseBean->setIv($val['iv']);

        $response = $transactionResponseBean->getResponsePayload();
        echo "<pre>";
        print_r($response);
        exit;
    }elseif(is_string($response) && preg_match('/^txn_status=/',$response)){
		echo "<pre>";
        print_r($response);
        exit;
	}

    echo "<script>window.location = '".$response."'</script>";
    ob_flush();

}else if($_POST){

    $response = $_POST;

    if(is_array($response)){
        $str = $response['msg'];
    }else if(is_string($response) && strstr($response, 'msg=')){
        $outputStr = str_replace('msg=', '', $response);
        $outputArr = explode('&', $outputStr);
        $str = $outputArr[0];
    }else {
        $str = $response;
    }

    $transactionResponseBean = new TransactionResponseBean();

    $transactionResponseBean->setResponsePayload($str);
    $transactionResponseBean->setKey($_SESSION['key']);
    $transactionResponseBean->setIv($_SESSION['iv']);

    $response = $transactionResponseBean->getResponsePayload();
    echo "<pre>";
    print_r($response);
    echo "<br><br><br><br>";

    session_destroy();?>

    <a href='<?php echo "http://".$_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME'];?>'>GO TO HOME</a>

    <?php
    exit;
}

?>

<html>
<body>
<form method="post">
	<table class="tbl" width="60%" border="1" cellpadding="2" cellspacing="0">
	<tr>
    	<th width="40%">Field Description</th>
        <th width="20%">Field Name</th>
    </tr>
    <tr>
    	<td><label>Request Type</label>o</td>
        <td><select name="reqType">
        	<option value="T">T</option>
        	<option value="S">S</option>
        	<option value="O">O</option>
        	<option value="R">R</option>
        	<option value="TNR">TNR</option>
        	<option value="TCI">TCI</option>
        	<option value="TWC">TWC</option>
			<option value="TRC">TRC</option>
			<option value="TCC">TCC</option>
			<option value="TWI">TWI</option>
			<option value="TIC">TIC</option>
			<option value="TIO">TIO</option>
			<option value="TWD">TWD</option>
        	</select>
        </td>
    </tr>
    <tr>
    	<td><label>Merchant Code</label></td>
        <td><input type="text" name="mrctCode" value="T3135"/></td>
    </tr>
	<tr>
    	<td><label>Merchant Transaction ID</label></td>
        <td><input type="text" name="mrctTxtID" value="<?php echo $strNo; ?>"/></td>
    </tr>
     <tr>
    	<td><label>Currency Code</label></td>
        <td><input type="text" name="currencyType" value="INR"/></td>
    </tr>
    <tr>
    	<td><label>Amount</label></td>
        <td><input type="text" name="amount" value="1.00"/></td>
    </tr>
    <tr>
    	<td><label>Client Meta Data</label></td>
        <td><input type="text" name="itc" value="NIC~TXN0001~122333~rt14154~8 mar 2014~Payment~forpayment"/></td>
    </tr>
    <tr>
    	<td><label>Request Detail</label></td>
        <td><input type="text" name="reqDetail" value="Test_1.0_0.0"/></td>
    </tr>
     <tr>
    	<td><label>Transaction Date</label></td>
        <td><input type="text" name="txnDate" value="<?php echo $strCurDate;?>"/></td>
    </tr>
    <tr>
    	<td><label>Bank Code</label></td>
        <td><input type="text" name="bankCode" value="470"/></td>
    </tr>
     <tr>
    	<td><label>Locator URL</label></td>
        <td><select name="locatorURL">
		        <option value="https://www.tekprocess.co.in/PaymentGateway/TransactionDetailsNew.wsdl">TEST</option>
	        	<option value="http://10.10.60.46:8080/PaymentGateway/services/TransactionDetailsNew">E2EWithIP</option>
	        	<option value="https://tpslvksrv6046/PaymentGateway/services/TransactionDetailsNew">E2EWithDomain</option>
	        	<option value="https://www.tekprocess.co.in/PaymentGateway/services/TransactionDetailsNew">UATWithDomain</option>
	        	<option value="http://10.10.102.157:8081/PaymentGateway/services/TransactionDetailsNew">UATWithIP</option>
	        	<option value="http://10.10.102.158:8081/PaymentGateway/services/TransactionDetailsNew">EAP UATWithIP</option>
				<option value="https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl">LIVE</option>
				<option value="http://10.10.60.247:8080/PaymentGateway/services/TransactionDetailsNew">Linux E2E</option>
        	</select>
        </td>
    </tr>
	<tr>
    	<td><label>S2S URL </label></td>
        <td>
	       <input type="text" name="s2SReturnURL" value="https://tpslvksrv6046/LoginModule/Test.jsp"/>
		</td>
    </tr>
    <tr>
    	<td><label>TPSL Transaction ID</label></td>
        <td><input type="text" name="tpsl_txn_id" value="<?php echo 'TXN00'.rand(1,10000); ?>"/></td>
    </tr>
	 <tr>
    	<td><label>Card ID</label></td>
        <td><input type="text" name="cardID" value=""/></td>
    </tr>
    <tr>
    	<td><label>Customer ID</label></td>
        <td><input type="text" name="custID" value="19872627"/></td>
    </tr>
    <tr>
    	<td><label>Customer Name</label></td>
        <td><input type="text" name="custname" value="test"/></td>
    </tr>
	<tr>
    	<td><label>Timeout</label></td>
        <td><input type="text" name="timeOut" value=""/></td>
    </tr>
	<tr>
    	<td><label>Mobile Number</label></td>
        <td><input type="text" name="mobNo" value=""/></td>
    </tr>
	<tr>
    	<td><label>Account Number</label></td>
        <td><input type="text" name="accNo" value=""/></td>
    </tr>
	<tr>
    	<td><label>Tpv Account Number</label></td>
        <td><input type="text" name="tpvAccntNo" value=""/></td>
    </tr>
    <tr>
    	<td><label>MMID</label></td>
        <td><input type="text" name="mmid" value=""/></td>
    </tr>
	<tr>
    	<td><label>OTP</label></td>
        <td><input type="text" name="otp" value=""/></td>
    </tr>
	<tr>
    	<td><label>Transaction Type</label></td>
        <td><input type="text" name="TxnType" value=""/></td>
    </tr>
	<tr>
    	<td><label>Transaction SubType</label></td>
        <td><input type="text" name="TxnSubType" value=""/></td>
    </tr>
	<tr>
    	<td><label>Card name</label></td>
        <td><input type="text" name="cardName" value=""/></td>
    </tr>
	<tr>
    	<td><label>Card Number</label></td>
        <td><input type="text" name="cardNo" value=""/></td>
    </tr>
	<tr>
    	<td><label>Card CVV Number</label></td>
        <td><input type="text" name="cardCVV" value=""/></td>
    </tr>
	<tr>
    	<td><label>Card Exp MM</label></td>
        <td><input type="text" name="cardExpMM" value=""/></td>
    </tr>
	<tr>
    	<td><label>Card Exp YY</label></td>
        <td><input type="text" name="cardExpYY" value=""/></td>
    </tr>
	<tr>
    	<td><label>Key</label></td>
        <td><input type="text" name="key" value=""/></td>
    </tr>
	<tr>
    	<td><label>IV</label></td>
        <td><input type="text" name="iv" value=""/></td>
    </tr>
	<tr>
    	<td><label>Return URL </label></td>
        <td>
	        <input type="text" name="returnURL" value='<?php echo "http://".$_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME'];?>'/>
		</td>
    </tr>
	<tr>
        <td colspan=2>
         <input type="submit" name="submit" value="Submit" />
        </td>
	</tr>
</table>
</form>
</body>
</html>