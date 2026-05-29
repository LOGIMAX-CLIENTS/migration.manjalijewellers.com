<?php
class Sms_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	/**
	 * NETTY FISH Gateway
	 * 
	 * Promotional SMS :
     * Single Message
     *   https://sms.nettyfish.com/vendorsms/pushsms.aspx?apikey=abc&clientId=xyz&msisdn=919898xxxxxx&sid=SenderId&msg=test%20message&fl=0
     * Multiple Messages
     *   https://sms.nettyfish.com/vendorsms/pushsms.aspx?apikey=abc&clientId=xyz&msisdn=919898xxxxxx,919898xxxxxx&sid=SenderId&msg=test%20message&fl=0
     *   Note : Add one additional parameter gwid=2 in existing API as per example given below.
     * 
     * Transactional SMS :
     * Single Message
     *   https://sms.nettyfish.com/vendorsms/pushsms.aspx?apikey=abc&clientId=xyz&msisdn=919898xxxxxx&sid=SenderId&msg=test%20message&fl=0&gwid=2
     * Multiple Messages
     *   https://sms.nettyfish.com/vendorsms/pushsms.aspx?apikey=abc&clientId=xyz&msisdn=919898xxxxxx,919898xxxxxx&sid=SenderId&msg=test%20message&fl=0&gwid=2
	 * 
	 * */
	 
	 function sendSMS_Nettyfish_old($mobile,$message,$type) {
	    $senderid = "JWLONE";
        $ClientId = "2021e8f9-0305-4c35-b34f-2ab60bc383f5";
        $ApiKey = "20dfdd06-131b-4151-ba88-c6fc09418616";
	    $url = "http://sms.nettyfish.com/vendorsms/pushsms.aspx";
	    $_data = array(
                'apikey' => $ApiKey,
                'clientid' => $ClientId,
                'msisdn' => "91".$mobile,
                'sid' => $senderid,
                'msg' => $message,
                'fl' =>"0",
            );
        if($type == "trans"){
            $_data['gwid'] = 2;
        }
            
        //    echo "\n Response : ".$content; 
	     // convert variables array to string:
        $data = array(); 
        while(list($n,$v) = each($_data))
        {
            $data[] = "$n=$v";
        }
        $data = implode('&', $data);
        // format --> test1=a&test2=b etc.
        // parse the given URL
        $url = parse_url($url);
        /*if ($url['scheme'] != 'http') {
        	echo "<pre>";print_r($url);
            die('Only HTTP request are supported !');
        	
        }*/
        // extract host and path:
        $host = $url['host'];
        $path = $url['path'];
        
        /*echo "<pre> DATA : ";print_r($data);
                echo "<pre> HOST : ";print_r($host);
                echo "<pre> PATH ";print_r($path);*/
                
        // open a socket connection on port 80
        $fp = fsockopen($host, 80);
        // send the request headers:
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);
        $result = '';
        while(!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
        // close the socket connection:
        fclose($fp);
        // split the result header from the content
        $result = explode("\r\n\r\n", $result, 2);
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : ''; 
        $res = (json_decode($content)); 
        return ($res->ErrorCode == 000 ? TRUE : FALSE);
	 }
	 
	 function sendSMS_Nettyfish($mobile,$message,$type)
	 {
	     // Append 91 (coutry code) to mobile number
    	$mobiles = "";
        $mobile_nos =  explode(',',$mobile);
        $length = sizeof($mobile_nos);
        $i = 1;
        foreach($mobile_nos as $m){
            if(strlen($m) == 10){
                $mobiles .="91".$m."".($i==$length?"":","); 
            }
            $i++;
        }  
        // Get SMS URL data
		$sql = $this->db->query("Select sms_sender_id, sms_url FROM sms_api_settings where gateway_type=2");
		
		$sms_data = $sql->row_array(); 	 
        $url = $sms_data['sms_url'];
        $senderid  = $sms_data['sms_sender_id'];
              
        $arr = array("@mobile@" => $mobiles,"@message@" => rawurlencode(str_replace(array("\n","\r"), '', $message)),"@senderid@" => $senderid);
        //print_r($arr);exit;
    	$user_sms_url = strtr($url,$arr);
    	    // print_r($user_sms_url);exit;
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $user_sms_url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	unset($ch);
    	return TRUE; 
	 }
	 
	 function sendSMS_Nettyfish_backup($mobile,$message,$type) {
	    /**
	 * NETTY FISH Gateway :: Promotional root only
	 * 
	 * /api/v2/SendSMS?ApiKey={ApiKey}&ClientId={ClientId}&SenderId={SenderId}&Message={Message}&MobileNumbers={MobileNumbers}
	 * &Is_Unicode={Is_Unicode}&Is_Flash={Is_Flash}&serviceId={serviceId}&CoRelator={CoRelator}&LinkId={LinkId}
	 * 
	 * Have to enable port 6005 in server.
	 * 
	 * */
        $url = "http://45.127.102.185:6005/api/v2/SendSMS?SenderId=@SenderId@&Is_Unicode=false&Is_Flash=false&Message=@Message@&MobileNumbers=91@MobileNumbers@&ApiKey=@ApiKey@&ClientId=@ClientId@";
        $senderid = "JWLONE";
        $ClientId = "e7126180-afd4-4c50-9865-a85248a87398";
        $ApiKey = "wC/ePE0tehvWCcC2IqqUXrv5usSBx8s+hEppxZBbNQ8=";
         
        $arr = array("@ApiKey@" => rawurlencode($ApiKey),"@ClientId@" => $ClientId,"@MobileNumbers@" => $mobile,"@Message@" => rawurlencode(str_replace(array("\n","\r"), '', $message)),"@SenderId@" => $senderid);
        $user_sms_url = strtr($url,$arr);
        echo "URL : " .($user_sms_url); 
        
        $ch = curl_init();
        $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',
        );
        curl_setopt($ch, CURLOPT_URL, $user_sms_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if (curl_errno($ch))
        {
            print "<br/><br/> Error: " . curl_error($ch);
            curl_close($ch);
            unset($ch);
            return FALSE;
        }
        else
        {
            echo "<pre>";var_dump($result);
            curl_close($ch);
            unset($ch);
            return TRUE;
        }
	} 
	
	/* MSG 91
		Transactional VS Promotional 

		Transactional :-									 
		1.  These can be used for sending any type of information.Ex. Order updates, Bank Transactions etc.	    
		2.  Transactional SMS can be sent any time 
		3.  SMS can be sent on DND numbers 	
		4.  In API route=1     
		
		Promotional :-
		1.  These are specifically used for marketing purposes. Ex. Sale, Offers etc.
		2.  Promotional SMS are sent between 9 AM to 9 PM only
		3.  SMS cannot be sent on DND numbers 
		4.  In API route=4
	
	*/

    function sendSMS_MSG91($mobile,$message,$type="",$dlt_te_id="",$attachemeUrl="")
    {		 
    	// Append 91 (coutry code) to mobile number
    	$mobiles = "";
        $mobile_nos =  explode(',',$mobile);
        $length = sizeof($mobile_nos);
        $i = 1;
        foreach($mobile_nos as $m){
            if(strlen($m) == 10){
                $mobiles .="91".$m."".($i==$length?"":","); 
            }
            $i++;
        }  
        // Get SMS URL data
        if($type == 'promo'){
			$sql = $this->db->query("Select promotion_sender_id as sms_sender_id,promotion_url as sms_url FROM promotion_api_settings");
		}else{
			$sql = $this->db->query("Select sms_sender_id, sms_url FROM sms_api_settings");
		}
		$sms_data = $sql->row_array(); 	 
        $url = $sms_data['sms_url'];
        $senderid  = $sms_data['sms_sender_id'];
              
        $arr = array("@customer_mobile@" => $mobiles,"@message@" => rawurlencode(str_replace(array("\n","\r"), '', $message)),"@senderid@" => $senderid,"@dlt_te_id@" => $dlt_te_id);
        //print_r($arr);exit;
    	$user_sms_url = strtr($url,$arr);
    	 //print_r($user_sms_url);exit;
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $user_sms_url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	unset($ch);
    	
    	// Send Whatsapp message
    	
    	$whatsappurl = $this->config->item("whatsappurl");
        $instanceid = $this->config->item("whats-instanceid");
        if($attachemeUrl!='')
        {
            $type = 'sendFileWithCaption?';
        }else
        {
            $type = 'sendText?';
        }
		
		if($whatsappurl != '' && $instanceid != '' && $type != ''){
			
			$url = $whatsappurl.$type;
			$data = "token=".$instanceid."&phone=".$mobiles."&message=".urlencode($message)."&link=".$attachemeUrl;
			$wa_msg_url	= $url.$data;
		  
        	
        	
        	$curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $wa_msg_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "authorization: Basic cHJlY2lzZXRyYTpIaXJoTmwxMA==",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));
            
            $response = curl_exec($curl);
            
            $err = curl_error($curl);
            curl_close($curl); 
            return TRUE;
		}
		
    	return TRUE; 
    }
    
    public function SMS_dataByServCode($serv_code, $where)
    {
		//Declaration of variables
		$message ="";
		$params = [];
        $sms_msg = "";
        $template_name = "";
		$sms_footer = "";
		$customer_data = array();
		$cus_data = array();
		if($serv_code == "VS_REQST")
		{
			$resultset = $this->db->query("
				SELECT 
					req.name,req.email,req.mobile,
					CONCAT(DATE_FORMAT(s.slot_date,'%d-%m-%Y'),' ',CONCAT(TIME_FORMAT(s.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(s.slot_time_to, '%h:%i %p'))) as slot,IF(req.status = 0, 'yet to be confirmed', IF(req.status = 1, 'Confirmed', IF(req.status = 2, 'Not Available', IF(req.status = 3, 'Completed', IF(req.status = 4, 'Closed', ''))))) as status,
					if(pref_category =1,'Gold',if(pref_category =2,'Silver',if(pref_category =3,'Platinum',if(pref_category =4,'Diamond','')))) as pref_category,
					if(whats_app_no is null or whats_app_no ='','-',whats_app_no) as whats_app_no, 
					if(pref_item is null or pref_item ='','-',pref_item) as pref_item, 
					if(location is null or location ='','-',location) as location,
					if(description is null or description ='','-',description) as description,
					if(reject_reason is null or reject_reason ='','-',reject_reason) as reject_reason,
					if(customer_feedback is null or customer_feedback ='','-',customer_feedback) as customer_feedback,
					cmp.company_name as cmp_name,cmp.email as cmp_email
				FROM appt_request req
				LEFT JOIN appt_slots s ON s.id_appointment_slot = req.preferred_slot
			    	JOIN company cmp
			    WHERE req.id_appt_request='".$where."'"
			);
			$cus_data = $resultset->row_array();
		} 
		else if($serv_code == "VS_STATUS")
		{
			$resultset = $this->db->query("
				SELECT 
					req.name,req.email,req.mobile,
					CONCAT(DATE_FORMAT(s.slot_date,'%d-%m-%Y'),' ',CONCAT(TIME_FORMAT(s.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(s.slot_time_to, '%h:%i %p'))) as slot,IF(req.status = 0, 'yet to be confirmed', IF(req.status = 1, 'Confirmed', IF(req.status = 2, 'Not Available', IF(req.status = 3, 'Completed', IF(req.status = 4, 'Closed', ''))))) as status,
					if(pref_category =1,'Gold',if(pref_category =2,'Silver',if(pref_category =3,'Platinum',if(pref_category =4,'Diamond','')))) as pref_category,
					if(whats_app_no is null or whats_app_no ='','-',whats_app_no) as whats_app_no, 
					if(pref_item is null or pref_item ='','-',pref_item) as pref_item, 
					if(location is null or location ='','-',location) as location,
					if(description is null or description ='','-',description) as description,
					if(reject_reason is null or reject_reason ='','-',reject_reason) as reject_reason,
					if(customer_feedback is null or customer_feedback ='','-',customer_feedback) as customer_feedback,
					cmp.company_name as cmp_name,cmp.email as cmp_email
				FROM appt_request req
				LEFT JOIN appt_slots s ON s.id_appointment_slot = req.preferred_slot
			    	JOIN company cmp
			    WHERE req.id_appt_request='".$where."'"
			);
			$cus_data = $resultset->row_array();
		} 
		
		foreach($resultset->result() as $row)
		{
			$customer_data = $row;
			$mobile=$row->mobile;
		}
		$resultset = $this->db->query("SELECT sms_msg, sms_footer,serv_sms,serv_email, serv_whatsapp, template_name,dlt_te_id from services where serv_code = '".$serv_code."'");
		foreach($resultset->result() as $row)
		{
			$serv_sms = $row->serv_sms;
			$serv_email = $row->serv_email;
			$sms_msg = $row->sms_msg;
			$sms_footer = $row->sms_footer;
			$template_name = $row->template_name;
			$serv_whatsapp = $row->serv_whatsapp;
			$dlt_te_id = $row->dlt_te_id;
		}
		$resultset->free_result();
		//Generating Message content
		$field_name = explode('@@', $sms_msg);	
		for($i=1; $i < count($field_name); $i+=2) 
		{
            $field =  $field_name[$i];
			if(isset($customer_data->$field)) 
			{ 
			    $params[] = [ "type" => "text", "text" => $customer_data->$field ];
			    $sms_msg = str_replace("@@".$field."@@",$customer_data->$field,$sms_msg);					
			}	
		}
		$field_name_footer = explode('@@', $sms_footer);	
		for($i=1; $i < count($field_name_footer); $i+=2)
		 {
			if(isset($customer_data->$field_name_footer[$i]))
			 { 
				$sms_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$sms_footer);					
			}	
		}
		$sms_msg .= " ".$sms_footer;					
		return (array('cus_data'=>$cus_data,'message'=>$sms_msg,'mobile'=>$mobile,'serv_email'=>$serv_email,'serv_sms'=>$serv_sms, "params" => $params, "template_name" => $template_name, "serv_whatsapp" => $serv_whatsapp, "dlt_te_id" =>$dlt_te_id));
	}
	
		//Added by Durga 18.05.2023 starts here
	function sendSMS_SpearUC($mobile,$message,$type,$dlt_te_id="")
	 {
		
	     // Append 91 (coutry code) to mobile number
    	$mobiles = "";
        $mobile_nos =  explode(',',$mobile);
        $length = sizeof($mobile_nos);
        $i = 1;
        foreach($mobile_nos as $m){
            if(strlen($m) == 10){
                $mobiles .="91".$m."".($i==$length?"":","); 
            }
            $i++;
        }  
        // Get SMS URL data
		$sql = $this->db->query("Select sms_sender_id, sms_url FROM sms_api_settings where gateway_type=3");
		
		$sms_data = $sql->row_array(); 	
		
        $url = $sms_data['sms_url'];
		
        $senderid  = $sms_data['sms_sender_id'];
              
        $arr = array("@mobile@" => $mobiles,"@message@" => rawurlencode(str_replace(array("\n","\r"), '', $message)),"@senderid@" => $senderid,"@dlt_te_id@" => $dlt_te_id);
        //print_r($arr);exit;
    	$user_sms_url = strtr($url,$arr);
    	//print_r($user_sms_url);exit;
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $user_sms_url);
		
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
    	$result = curl_exec($ch);
		
    	curl_close($ch);
    	unset($ch);
    	return TRUE; 
	 }
	 //Added by Durga 18.05.2023 ends here
        	//Added by gopal 22.05.2024 starts here
	function sendSMS_Asterixt($mobile,$message,$type,$dlt_te_id="")
	{
	   
		// Append 91 (coutry code) to mobile number
	   $mobiles = "";
	   $mobile_nos =  explode(',',$mobile);
	   $length = sizeof($mobile_nos);
	   $i = 1;
	   foreach($mobile_nos as $m){
		   if(strlen($m) == 10){
			   $mobiles .="91".$m."".($i==$length?"":","); 
		   }
		   $i++;
	   }  
	   // Get SMS URL data
	   $sql = $this->db->query("Select sms_sender_id, sms_url FROM sms_api_settings where gateway_type=4");
	   
	   $sms_data = $sql->row_array(); 	
	   
	   $url = $sms_data['sms_url'];
	   
	   $senderid  = $sms_data['sms_sender_id'];
			 
	   $arr = array("@mobile@" => $mobiles,"@message@" => rawurlencode(str_replace(array("\n","\r"), '', $message)),"@senderid@" => $senderid,"@dlt_te_id@" => $dlt_te_id);
		   //   echo '<pre>'; print_r($arr);

	   //  echo '<pre>' ;  print_r($message);exit;
	   $user_sms_url = strtr($url,$arr);
   //  	print_r($user_sms_url);exit;
	   $ch = curl_init();
	   curl_setopt($ch, CURLOPT_URL, $user_sms_url);
	   
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	   $result = curl_exec($ch);
	   
	   curl_close($ch);
	   unset($ch);
	   return TRUE; 
	}
	//Added by Gopal 22.05.2024 ends here
	
	function sendSMS_Qikberry($mobile,$message,$type="",$dlt_te_id="",$attachemeUrl="")
    {
    	// Append 91 (coutry code) to mobile number
	   $mobiles = "";
	   $mobile_nos =  explode(',',$mobile);
	   $length = sizeof($mobile_nos);
	   $i = 1;
	   foreach($mobile_nos as $m){
		   if(strlen($m) == 10){
			   $mobiles .="91".$m."".($i==$length?"":","); 
		   }
		   $i++;
	   }  
	   // Get SMS URL data
	   $sql = $this->db->query("Select sms_sender_id, sms_url FROM sms_api_settings where gateway_type=5");
	   
	   $sms_data = $sql->row_array(); 	
	   
	   $url = $sms_data['sms_url'];
	   
	   $senderid  = $sms_data['sms_sender_id'];
			 
	   $arr = array("@service@" => "T","@mobile@" => $mobiles,"@message@" => rawurlencode(str_replace(array("\n","\r"), '', $message)),"@senderid@" => $senderid,"@dlt_te_id@" => $dlt_te_id);
       //  echo '<pre>'; print_r($arr);
        
       //  echo '<pre>' ;  print_r($message);exit;
	   $user_sms_url = strtr($url,$arr);
      //   print_r($user_sms_url);//exit;
	   $ch = curl_init();
	   curl_setopt($ch, CURLOPT_URL, $user_sms_url);
	   
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	   $result = curl_exec($ch);
	   //echo "<pre>";print_r($result);exit;
	   curl_close($ch);
	   unset($ch);
    	
       return TRUE; 
    }
    
    
    function send_whatsApp_message($mobile,$msgData)
    {
        //$msgData = ["message" => "", "template_name", "params" => ""];
        // Append 91 (coutry code) to mobile number
    	$mobiles = "";
        $mobile_nos =  explode(',',$mobile);
        $length = sizeof($mobile_nos);
        $i = 1;
        foreach($mobile_nos as $m){
            if(strlen($m) == 10){
                $mobiles .="91".$m."".($i==$length?"":","); 
            }
            $i++;
        } 
        
        if($this->config->item("wa_gateway") == 2){ // Qikchat
            $postData = [
			"to_contact"	=> $mobiles,
			"type"			=> "template",
			"template"		=> [
								"name"		=> $msgData['template_name'],
								"language"	=> "en",
								"components"=> [
									[
										"type"		=> "body",
										"parameters"=> $msgData['params']
									]
								]
							]
            ];
            //echo "<pre>";print_r(json_encode($postData));//exit;
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.qikchat.in/v1/messages',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => json_encode($postData),
              CURLOPT_HTTPHEADER => array(
                'QIKCHAT-API-KEY: '.$this->config->item("qikchat-api-key"),
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            echo $response;//exit;
        }else if($this->config->item("wa_gateway") == 1){ // Creative point
            $whatsappurl = $this->config->item("whatsappurl");
            $instanceid = $this->config->item("whats-instanceid");
            $whatsappurl = $whatsappurl."sendText?token=".$instanceid."&phone=91".$mobile."&message=".urlencode($message);
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $whatsappurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "authorization: Basic cHJlY2lzZXRyYTpIaXJoTmwxMA==",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl); 
            
            if($err)
            {
                return false;
            }
            else
            {
                $res = json_decode($response);
    //            echo "<pre>";print_r($res);
                return $res->message;
            }  
        }
        
    }
    
    
    
}	
?>