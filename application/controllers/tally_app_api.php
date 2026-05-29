<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Retail Admin app api's
*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php');

class Tally_app_api extends REST_Controller
{
	const ADM_MODEL = "ret_tally_api_model";
	const USERNAME  = "lmxTallyInt";
	const PASSWORD  = "$2y$10$0FMx8I0pSr.J3xi6oDMVSekMRoAGXiWj/FkgiV0T9hhljvSTlbwDO";
	
	function __construct()
	{
		parent::__construct();
		$this->response->format = 'json';
	
		$this->load->model('ret_tally_api_model');
		ini_set('date.timezone', 'Asia/Calcutta');
	}

	/**
	* General functions
	*/
    //funtion to get post values
    function get_values()
    {
		return (array)json_decode(file_get_contents('php://input'));

	}
	
	function validateCredentials($data)
	{
	    //print_r($data);exit;
	    if($data['userName'] === self::USERNAME && password_verify("sknetail", $data['password'])){
	        return TRUE;
	    }else{
	        return FALSE;
	    }
	}

	//array sorting
	function array_sort($array, $on, $order=SORT_ASC){
		$new_array = array();
		$sortable_array = array();
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
			foreach ($sortable_array as $k => $v) {
				//$new_array[$k] = $array[$k];
				$new_array[] = $array[$k];
			}
		}
		return $new_array;
	}

	public function __encrypt($str)
	{
		return base64_encode($str);
	}
	
	function branchtransferdetails_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getbranchtransferInitiatedList();
		$responsedata = array('status' => true,'transferdata'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	
	function importbt_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->getBTTransferList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	    
	}
	
	function importsales_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getsalesList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	
	
	function importrepairsales_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getrepairsalesList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function importsalesreturn_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getsalesreturnList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function importpurchase_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getpurchaseList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function importsupplierpurchase_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getsupplierpurchaseList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function importreceipts_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->getAllreceiptsList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function imporissuepayments_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->getAllimporissuepaymentsList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function imporchitutlizationpayments_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->getChitUtlizationPayments();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	
	function importkarigarmetalissue_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->getKarigarMetalIssueList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	    
	}
	
	function importpurchasereturn_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getpurchasereturnList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function importsupplierpayment_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getsupplierpaymentList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function updateSalesguid_post()
	{
	   
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateSalesGUID($this->get_values());
		//$responsedata = array('status' => true,'message'=>'Updated successfully', 'updatedguids' => $response['updatedguids']);
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updatePurchaseguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updatePurchaseGUID($this->get_values());
		//$responsedata = array('status' => true,'message'=>'Updated successfully', 'updatedguids' => $response['updatedguids']);
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updateReceiptsguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateReceiptsGUID($this->get_values());
		//$responsedata = array('status' => true,'message'=>'Updated successfully', 'updatedguids' => $response['updatedguids']);
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updatePaymentsguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->updatePaymentsGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updateSalesReturnguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->updateSalesReturnsGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updateguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->updateSalesReturnsGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updateKarigarMetalIssueguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->updateKarigarMetalIssueGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	function updateBTIssueguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->updateBTIssueGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updatePurchaseReturnguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->updatePurchaseReturnGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function createchitdate_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->createChitdate();
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function purchaseplan_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->getPurchasePlanList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	
	function importonetimescheme_get()
	{
	    $model 		= self::ADM_MODEL;
		$respose	= $this->$model->getAllonetimeschemeList();
		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	
	function importsalesvouchers_post()
	{
		$model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getSalesNewVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	function importsalesvouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getSalesNewVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	function importpurchasevouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getPurchasaeNewVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	function importsalesreturnvouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getSalesReturnNewVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	function importsupplierpurchasevouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getSupplierPurchaseVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	function importsupplierpurchasereturnvouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getSupplierPurchaseReturnVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	function importmaterialreceiptvouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getMaterialReceiptVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	function importmaterialissuevouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getMaterialIssueVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	function importdeliverynotevouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getDeliveryNoteIssueVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	function importreceiptnotevouchersList_post()
	{
	    $model 		= self::ADM_MODEL;
		$request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getReceiptNoteReceiptVoucherList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	
	
	function importallmetalissues_post()
	{
	    $model 		= self::ADM_MODEL;
	    $request_data = $this->get_values();
		if($this->validateCredentials($request_data)){
    		$respose	= $this->$model->getAllMetalIssueList($request_data);
    		$responsedata = array('status' => true,'VOUCHERDETAILS'=>$respose);
    	   	$this->response($responsedata, 200);	
		}else{
		    $responsedata = array('status' => false,'VOUCHERDETAILS'=>[], 'message' => 'Authentication Faild');
    	   	$this->response($responsedata, 401);	
		}
	}
	
	
	function updatesalesvouchersListguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateSalesVoucherGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	function updatepurchasevouchersListguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updatePurchaseVoucherGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	function updatesalesreturnvouchersListguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateSalesReturnVoucherGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}

    function updatematerialinvouchersListguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateMaterialInVoucherGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	function updatematerialoutvouchersListguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateMaterialOutVoucherGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function updatedeliverynotevouchersListguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateDeliveryNoteVoucherGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	function updatereceiptnotevouchersListguid_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateReceiptNoteVoucherGUID($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	function repprofitloss_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->upateProfitLossList();
		$respose	=  $this->$model->upateOldMetalProfitLossList();
		//$respose	= $this->$model->getProfitLossList();
		$responsedata = array('status' => true,'transferdata'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function repprofitlosspcs_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getProfitLossPcsList();
		$responsedata = array('status' => true,'transferdata'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function silverprofitlosspcs_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->getSilverOrnamentsPcsList();
		$responsedata = array('status' => true,'transferdata'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	function diamondprofitloss_get()
	{
		$model 		= self::ADM_MODEL;
		$respose	= $this->$model->updateDiamondProfitLossList();
		$respose	= $this->$model->updateOldDiamondProfitLossList();
		
		//$respose	= $this->$model->getDiamondProfitLossList();
		$responsedata = array('status' => true,'transferdata'=>$respose);
	   	$this->response($responsedata,200);	
	}
	
	
		/*
	    To revert the sales bills
	    Need to pass the bills ids
	*/
	function updateSalesBills_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateSalesBills($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	/*
	    To revert the purchase bills
	    Need to pass the bills ids
	*/
	function updatePurchaseBills_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updatePurchaseBills($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	/*
	    To revert the sales return bills
	    Need to pass the bills ids
	*/
	function updateSalesReturnBills_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateSalesReturnBills($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	/*
	    To revert the payment or receipts
	    Need to pass the bills ids
	*/
	function updateBillPayments_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateBillPayments($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	/*
	    To revert the general issue / receipts
	    Need to pass the bills ids
	*/
	function updateIssueReceiptsPayment_post()
	{
	    $model 		= self::ADM_MODEL;
		$response	= $this->$model->updateIssueReceiptsPayment($this->get_values());
		$responsedata = array('status' => true,'message'=>'Updated successfully');
	   	$this->response($responsedata,200);	
	}
	
	
	
	
}
?>