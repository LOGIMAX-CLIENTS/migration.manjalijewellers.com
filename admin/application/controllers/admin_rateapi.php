<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Admin_rateapi extends CI_Controller 
{
	const SET_MODEL  = "admin_settings_model";
	const PAY_MODEL	 ="payment_model";
	public function __construct()
    {
        parent::__construct();
          $this->load->model('admin_settings_model');
          $this->load->model(self::PAY_MODEL);
          ini_set('date.timezone', 'Asia/Calcutta');
    }
      
   function update_rateapi()    //On Market rate update, get last rate and add data in table based on config settings //HH
   {
        $model=self::SET_MODEL; 
        $pay_model=self::PAY_MODEL;  
        $rate_settings =  $this->$model->allow_autorate_update();
        $data = $_POST;
		 /*$data=array(
   	  			'goldrate_22ct'=>3750.00,
   	  			'silverrate_1gm'=>48.80,
   	  			'updatetime'	=>'1569222571'
 	  );*/
	 
   	   if($rate_settings['rate_update']!=0)
   	   {
	   		if($data)
			{
			    if($this->config->item('last_rate_req_G24ct') == 1 || $this->config->item('last_rate_req_G18ct') == 1 || $this->config->item('last_rate_req_P1g') == 1){
                    $rate = $this->$pay_model->getMetalRate();
                    
                    if($this->config->item('last_rate_req_G24ct') == 1)
                    {
			        $metal['goldrate_24ct'] = (isset($rate['goldrate_24ct']) ? $rate['goldrate_24ct'] : 0 );
                    }
                    else if($this->config->item('last_rate_req_G18ct') == 1)
                    {
                    $metal['goldrate_18ct'] = (isset($rate['goldrate_18ct']) ? $rate['goldrate_18ct'] : 0 );
                    
			        $metal['market_gold_18ct'] = (isset($rate['market_gold_18ct']) ? $rate['market_gold_18ct'] : 0 );
                    }
                    else if($this->config->item('last_rate_req_P1g') == 1)
			        {
			        $metal['platinum_1g'] = (isset($rate['platinum_1g']) ? $rate['platinum_1g'] : 0 );
			        }
			        
			    }
			    
				$this->db->trans_begin();
			 //  file_put_contents('../api/rate.txt',json_encode($_POST));
			    $metal = $data;
			 	$insertData = array( 
                                    'mjdmagoldrate_22ct'=> (isset($metal['goldrate_22ct']) && $metal['goldrate_22ct']!=''? $metal['goldrate_22ct']:0.00),
                                    
                                    'goldrate_22ct' 	=> (isset($metal['goldrate_22ct']) && $metal['goldrate_22ct']!='' ? ($metal['goldrate_22ct']- $rate_settings['goldDiscAmt']):(isset($metal['goldrate_22ct']) && $metal['goldrate_22ct']!='' && $rate_settings['enableGoldrateDisc']==0 ? $metal['goldrate_22ct']:0.00)),
                                    'goldrate_24ct' 	=> (isset($metal['goldrate_24ct']) && $metal['goldrate_24ct']!='' ? $metal['goldrate_24ct']:(isset($rate['goldrate_24ct']) ?$rate['goldrate_24ct'] :0.00)),
                                    
                                   // 'market_gold_18ct' 	=> (isset($metal['goldrate_18ct']) && ($metal['goldrate_18ct']!='' )? $metal['goldrate_18ct']:(isset($rate['market_gold_18ct'])  ?$rate['market_gold_18ct'] :0.00)),
                                    
                                    'market_gold_18ct' 	=> (isset($metal['goldrate_18ct']) && ($metal['goldrate_18ct']!='' )? $metal['goldrate_18ct']:(isset($rate['goldrate_18ct'])  ?$rate['goldrate_18ct'] :0.00)),
									'goldrate_18ct' 	=> (isset($metal['goldrate_18ct']) && ($metal['goldrate_18ct']>0)  ? ($rate_settings['enableGoldrateDisc_18k']==1 && $rate_settings['goldDiscAmt_18k']!='' ? ($metal['goldrate_18ct']-$rate_settings['goldDiscAmt_18k']):$metal['goldrate_18ct']):0.00),
									
                                    'mjdmasilverrate_1gm' 	=> (isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!=''? $metal['silverrate_1gm']:0.00),
                                    
                                    'silverrate_1gm' 	=>(isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!='' && $rate_settings['enableSilver_rateDisc']==1? ($metal['silverrate_1gm']- $rate_settings['silverDiscAmt']):(isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!='' && $rate_settings['enableSilver_rateDisc']==0 ? $metal['silverrate_1gm']:0.00)),
                                    'silverrate_1kg' 	=> (isset($metal['silverrate_1kg']) && $metal['silverrate_1kg']> 0? $metal['silverrate_1kg']:0.00), 
                                    
                                    
                                    'platinum_1g' 	    => (isset($metal['platinum_1g']) && $metal['platinum_1g']!=''? $metal['platinum_1g']:(isset($rate['platinum_1g'])  ?$rate['platinum_1g'] :0.00)),
                                    
                                    
                                    'updatetime' 		=> date("Y-m-d H:i:s", $metal['updatetime']),
                                    'add_date'          => date("Y-m-d H:i:s"),
                                    
                                    'id_employee' 		=> 0
				                   );
				$insertData['goldrate_18ct' ] = (isset($insertData['market_gold_18ct']) && ($insertData['market_gold_18ct']>0)  ? ($rate_settings['enableGoldrateDisc_18k']==1 && $rate_settings['goldDiscAmt_18k']!='' ? ($insertData['market_gold_18ct']-$rate_settings['goldDiscAmt_18k']):$insertData['market_gold_18ct']):0.00);
				
				 //print_r($insertData);exit;
				$rateTxt_data = array(
				 'goldrate_22ct' =>	number_format($insertData['goldrate_22ct'],'2','.',''),
				'silverrate_1kg'	=>$insertData['silverrate_1kg'],
				'silverrate_1gm'	=>$insertData['silverrate_1gm'],
				'mjdmagoldrate_22ct'=>number_format($insertData['mjdmagoldrate_22ct'],'2','.',''),
				'mjdmasilverrate_1gm'=>$insertData['mjdmasilverrate_1gm'], 
				'market_gold_18ct'=>$insertData['market_gold_18ct'], 
				'goldrate_18ct'=>$insertData['goldrate_18ct'], 
				'updatetime'	=>$metal['updatetime']
				);
				 file_put_contents('../api/rate.txt',json_encode($rateTxt_data));
				$this->db->trans_begin();				 	
				//inserting rates in DB 
				 if($rate_settings['is_branchwise_rate']==0)
			    {
			    	$status = $this->$model->metal_ratesDB("insert","",$insertData);
			    }
				else
				{
					$status = $this->$model->metal_ratesDB("insert","",$insertData);
			    	$data['branches'] = $this->$model->get_branches_for_rate();	
			    	foreach($data['branches'] as $branch){
						if($status)
						{
                            // Automatic
							if($branch['metal_rate_type']==1)
							{
									$branch_info=array(								
									'id_metalrate'		=> ($status['insertID']),								
									'id_branch'			=> (isset($branch['id_branch']) && $branch['id_branch']!=''? $branch['id_branch']:1),
									'status'			=> 1,								
									'date_add'			=> date("Y-m-d H:i:s")															
									);
									//Before update set previous status to 0
									$branch_update=array(
									'status'			=> 0,
									);
									$this->$model->update_metalrate_status($branch_update,$branch['id_branch']);
									
									$this->$model->insert_metalrate($branch_info,'branch_rate'); 
									
							}
						
						}
                        // Partial rate update
			    		if($branch['metal_rate_type']==2)
			    		{
							
			    			//$prev_branch_rate= $this->$model->get_branch_rate($branch['id_branch']);
			    			$rate_diff = $this->$model->get_rate_diff($branch['id_branch']); 
			    			$insertData = array( 
                                            'mjdmagoldrate_22ct'=> (isset($metal['goldrate_22ct']) && $metal['goldrate_22ct']!=''? $metal['goldrate_22ct']-$rate_diff['gold']:0.00),
                                            'goldrate_22ct' 	=> (isset($metal['goldrate_22ct']) && $metal['goldrate_22ct']!='' && $rate_settings['enableGoldrateDisc']==1? (($metal['goldrate_22ct']-$rate_diff['gold'])- $rate_settings['goldDiscAmt']):(isset($metal['goldrate_22ct']) && $metal['goldrate_22ct']!='' && $rate_settings['enableGoldrateDisc']==0 ? $metal['goldrate_22ct']-$rate_diff['gold']:0.00)),
                                            'goldrate_24ct' 	=> (isset($metal['goldrate_24ct']) && $metal['goldrate_24ct']!=''? $metal['goldrate_24ct']:(isset($rate['goldrate_24ct']) ?$rate['goldrate_24ct'] :0.00)),
                                            'market_gold_18ct' 	=> (isset($metal['market_gold_18ct']) && ($metal['market_gold_18ct']!='')? $metal['market_gold_18ct']:(isset($rate['market_gold_18ct']) ?$rate['market_gold_18ct'] :0.00)),
                                            'goldrate_18ct' 	=> (isset($rate['market_gold_18ct']) && ($rate['market_gold_18ct']>0)? ($rate_settings['enableGoldrateDisc_18k']==1 && $rate_settings['goldDiscAmt_18k']!='' ? ($rate['market_gold_18ct']-$rate_settings['goldDiscAmt_18k']):$rate['market_gold_18ct']):0.00),
                                            /*'mjdmasilverrate_1gm' 	=> (isset($prev_branch_rate['silverrate_1gm']) && $prev_branch_rate['silverrate_1gm']!=''? $prev_branch_rate['silverrate_1gm']:(isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!=''? $metal['silverrate_1gm']:0.00)),
                                            'silverrate_1gm' 	=>(isset($prev_branch_rate['silverrate_1gm']) && $prev_branch_rate['silverrate_1gm']!='' && $rate_settings['enableSilver_rateDisc']==1? ($prev_branch_rate['silverrate_1gm']- $rate_settings['silverDiscAmt']):(isset($prev_branch_rate['silverrate_1gm']) && $prev_branch_rate['silverrate_1gm']!='' && $rate_settings['enableSilver_rateDisc']==0 ? $prev_branch_rate['silverrate_1gm']:(isset($metal['silverrate_1gm']) ? ($rate_settings['enableSilver_rateDisc']==1 ? $metal['silverrate_1gm']- $rate_settings['silverDiscAmt']: $metal['silverrate_1gm']):0.00))),*/
                                            'mjdmasilverrate_1gm' 	=> (isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!=''? $metal['silverrate_1gm']-$rate_diff['silver']:0.00),
                                            
                                            'silverrate_1gm' 	=>(isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!='' && $rate_settings['enableSilver_rateDisc']==1? (($metal['silverrate_1gm']-$rate_diff['silver'])- $rate_settings['silverDiscAmt']):(isset($metal['silverrate_1gm']) && $metal['silverrate_1gm']!='' && $rate_settings['enableSilver_rateDisc']==0 ? $metal['silverrate_1gm']-$rate_diff['silver']:0.00)),
                                            'silverrate_1kg' 	=> (isset($metal['silverrate_1kg']) && $metal['silverrate_1kg']> 0? $metal['silverrate_1kg']:0.00), 
                                            'platinum_1g' 	    => (isset($metal['platinum_1g']) && $metal['platinum_1g']!=''? $metal['platinum_1g']:(isset($rate['platinum_1g']) ?$rate['platinum_1g'] :0.00)),
                                            
                                            'updatetime' 		=> date("Y-m-d H:i:s", $metal['updatetime']),
                                            'add_date'          => date("Y-m-d H:i:s"),
                                            
                                            'id_employee' 		=> 0
				                   );
			    		$status = $this->$model->metal_ratesDB("insert","",$insertData);
				    		if($status)
				    		{
				    			$branch_info=array(								
								'id_metalrate'		=> ($status['insertID']),								
								'id_branch'			=> (isset($branch['id_branch']) && $branch['id_branch']!=''? $branch['id_branch']:1),
								'status'			=> 1,								
								'date_add'			=> date("Y-m-d H:i:s")															
								);
			    				$this->$model->insert_metalrate($branch_info,'branch_rate');
				    		}
			    		}
			    		
			    		
			    	
			    	}
				}
				
				/*if($status){
					$data['branches'] = $this->$model->get_branches();
					if(!empty($data['branches'])){ 
					foreach($data['branches'] as $branch){								
						$branch_info=array(								
						'id_metalrate'		=> ($status['insertID']),								
						'id_branch'			=> (isset($branch['id_branch']) && $branch['id_branch']!=''? $branch['id_branch']:1),
						'status'			=> 0,								
						'date_add'			=> date("Y-m-d H:i:s")															
						);							
						    $this->$model->insert_metalrate($branch_info,'branch_rate');							
													
					    }
					}*/
					
					
					/* is_ratenoti_sent = 2 -> Rate updated & didn\'t send notification ' */
			     $upd = $this->$model->settingsDB("update",1,array('is_ratenoti_sent'=>2));
			        if($this->db->trans_status() === TRUE){				
					    echo "Rate update successfully";
    					$this->db->trans_commit();	 
    				}else{
    				    echo "Unable to update Rate..";
    					$this->db->trans_rollback();	
    				}
    			} 
				
							 
				 
			}
	}
	
	
   
    function update_rateByBranch_backup()
    {  
        $model = "admin_settings_model";  
        $branchRates = (array)json_decode(file_get_contents('php://input'));  
        if(sizeof($branchRates['rates']) > 0 ){
        	
        	$rate_settings =  $this->$model->allow_autorate_update();
            $result['failed'] = [];
            $result['success']= [];
            
    	    foreach($branchRates['rates'] as $data){ 
                $result['type'] = $branchRates['type'];
        	    $this->db->trans_begin();
    	        if($branchRates['type'] == 'goldrate_22ct'){        		    
        			$id_branch = $this->$model->getBranchId($data->warehouse);
        			if($id_branch > 0){
	        			$originalDate = $data->updatetime;
	                    $updatetime = date("Y-m-d H:i:s", strtotime($originalDate));	        		 	
	        		 	$insertData = array( 
	        	                            'mjdmagoldrate_22ct'=> (isset($data->rate) && $data->rate!=''? $data->rate:0.00),
	        								'goldrate_22ct' 	=> (isset($data->rate) && $data->rate!='' && $rate_settings['enableGoldrateDisc']==1? ($data->rate- $rate_settings['goldDiscAmt']):(isset($data->rate) && $data->rate!='' && $rate_settings['enableGoldrateDisc']==0 ? $data->rate:0.00)),
	        	                            'updatetime' 		=> $updatetime,
	        	                            'add_date'          => date("Y-m-d H:i:s"),
	        							    'id_employee' 		=> 0
	        			                   );
	        				 
	        			$rateTxt_data = array(
	                                        'goldrate_22ct'      =>	number_format($insertData['goldrate_22ct'],'2','.',''),
	                                        'silverrate_1kg'	 =>$insertData['silverrate_1kg'],
	                                        'silverrate_1gm'	 =>$insertData['silverrate_1gm'],
	                                        'mjdmagoldrate_22ct' =>number_format($insertData['mjdmagoldrate_22ct'],'2','.',''),
	                                        'mjdmasilverrate_1gm'=>$insertData['mjdmasilverrate_1gm'], 
	                                        'market_gold_18ct'   =>$insertData['market_gold_18ct'], 
	                                        'goldrate_18ct'      =>$insertData['goldrate_18ct'], 
	                                        'updatetime'	     =>$updatetime
	                                    );
	                                    
	        			file_put_contents('../api/rate.txt',json_encode($rateTxt_data)); // Donot use rate.txt if branch wise rate used
	        			$this->db->trans_begin();
	        			
	        			//inserting rates in DB 
	        			$status = $this->$model->metal_ratesDB("insert","",$insertData);  
						$branch_info = array(								
        								'id_metalrate'		=> ($status['insertID']),
        								'id_branch'			=> ($id_branch),
        								'status'			=> 1,								
        								'date_add'			=> date("Y-m-d H:i:s")															
            						);
	        			//Before update set previous status to 0
	        			$branch_update=array(
	        			    'status'			=> 0,
	        			);
	        			$this->$model->update_metalrate_status($branch_update,$id_branch);
	        			$this->$model->insert_metalrate($branch_info,'branch_rate'); 
	        		    
	        		    /* is_ratenoti_sent = 2 -> Rate updated & didn\'t send notification ' */
	                    $upd = $this->$model->settingsDB("update",1,array('is_ratenoti_sent'=>2));
	                    if($this->db->trans_status() === TRUE){	
	                        $result['success'][] = $data->warehouse;
	                    }else{
	                        $result['failed'][] = $data->warehouse;
	                    }
					}else{
						$result['failed'][] = $data->warehouse;
					}
    	        }
    	        else if($branchRates['type'] == 'silverrate_1gm'){
    	           $id_branch = $this->$model->getBranchId($data->warehouse);
    	           $id_metal_rate = $this->$model->getMetalRateId($id_branch);
    	           if($id_metal_rate > 0){
    	               $updateData = array( 
        	                            'mjdmasilverrate_1gm'=> (isset($data->rate) && $data->rate!=''? $data->rate:0.00),
        								'silverrate_1gm' 	 => (isset($data->rate) && $data->rate!='' && $rate_settings['enableSilver_rateDisc']==1? ($data->rate- $rate_settings['silverDiscAmt']):(isset($data->rate) && $data->rate!='' && $rate_settings['enableSilver_rateDisc']==0 ? $data->rate:0.00)),
        	                           );
        	           $update = $this->$model->update_silverRate($updateData,$id_metal_rate);
        	            
        	           if($this->db->trans_status() === TRUE){	
                           $result['success'][] = $data->warehouse;
                       }else{
                           $result['failed'][] = $data->warehouse;
                       }
    	           }else{
    	               $result['failed'][] = $data->warehouse;	
    	           }
    	        }else{
    	            $result['failed'][] = $data->warehouse;
    	        }	
    	    }
    	}else{
    	    $result['status'] = FALSE;
            $result['msg'] = "Error : Empty array";
    	} 
    	header('Content-Type: application/json');
    	echo json_encode($result);
	}
	
	
	function findByWarehouse($array,$searchVal) {
        foreach ($array as $k => $val) { 
            if ($val['warehouse'] == $searchVal || $val['expo_warehouse'] == $searchVal) {
               return $k;
            }
        }
        return -1;
    }

	function update_rateByBranch()
    {  
        $model = "admin_settings_model";  
        $branchRates = (array)json_decode(file_get_contents('php://input'));  
        $rates = array();
        foreach ($branchRates as $key => $val) {
        	$warhouse = $val->warehouse;
        	if($val->Metaltype == "Gold"){
        		$rates[$warhouse]['Gold'] = $val->Rate;
        		$rates[$warhouse]['updatetime'] = $val->updatetime;
        		$rates[$warhouse]['g_ref_no'] = $val->RECID;
        	}else if($val->Metaltype == "Silver"){
        		$rates[$warhouse]['Silver'] = $val->Rate;
        		$rates[$warhouse]['s_ref_no'] = $val->RECID;
        	}else if($val->Metaltype == "Platinum"){
        		$rates[$warhouse]['Platinum'] = $val->Rate;
        		$rates[$warhouse]['p_ref_no'] = $val->RECID;
        	}
        } 
         
        if(sizeof($rates) > 0 ){ 
            $result['failed'] = [];
            $result['success']= [];
    	    foreach($rates as $key => $data){  
        	    $this->db->trans_begin();     		    
    			$id_branch = $this->$model->getBranchId($key);
    			if($id_branch > 0){
    			    $isRefNoExist = $this->$model->getRef_nos($data);
    			   /* if(sizeof($isRefNoExist) >0 && isset($isRefNoExist['id_metalrates'])){
    			        $id_metalrates = $this->$model->getMetalRateId($id_branch); 
    			        $originalDate = $data['updatetime'];
                        $updatetime = date("Y-m-d H:i:s", strtotime($originalDate));	  
                        if(isset($data['g_ref_no'])){
                            $updData = array( 
                        					'mjdmagoldrate_22ct'    => (isset($data['Gold']) && $data['Gold']!=''? $data['Gold']:0.00),
                        					'goldrate_22ct'         => (isset($data['Gold']) && $data['Gold']!=''? $data['Gold']:0.00),
                        					'g_ref_no'              => (isset($data['g_ref_no']) && $data['g_ref_no']!=''? $data['g_ref_no']:NULL)
                        				);
                            
                			//updating rates in DB 
                			$this->$model->metal_ratesDB("update",$id_metalrates,$updData);  
                        }
                        if(isset($data['s_ref_no'])){
                            $updData = array( 
                        					'mjdmasilverrate_1gm'   => (isset($data['Silver']) && $data['Silver']!=''? $data['Silver']:0.00),
                        					'silverrate_1gm'        => (isset($data['Silver']) && $data['Silver']!=''? $data['Silver']:0.00),
                        					's_ref_no'              => (isset($data['s_ref_no']) && $data['s_ref_no']!=''? $data['s_ref_no']:NULL),
                        				);
                        	
                			//updating rates in DB 
                			$this->$model->metal_ratesDB("update",$id_metalrates,$updData);  
                        }
                        if(isset($data['p_ref_no'])){
                            $updData = array( 
                        					'platinum_1g'           => (isset($data['Platinum']) && $data['Platinum']!=''? $data['Platinum']:0.00),
                        					'p_ref_no'              => (isset($data['p_ref_no']) && $data['p_ref_no']!=''? $data['p_ref_no']:NULL)
                        				);
                        	
                			//updating rates in DB 
                			$this->$model->metal_ratesDB("update",$id_metalrates,$updData);  
                        } 
            			echo $this->db->last_query();exit;
            			$this->db->trans_begin();
            			
            			if($this->db->trans_status() === TRUE){	
                        	$this->db->trans_commit();
                            $result['success'][] = $key;
                        }else{
                        	$this->db->trans_rollback();
                            $result['failed'][] = $key;
                        }
    			    }
    			    else */
    			    if(sizeof($isRefNoExist) == 1){ // Skip Duplicate
    			         $result['success'][] = $key;
    			         $result['skipped'][] = $key;
    			    }
    			    else{
    			        $originalDate = $data['updatetime'];
                        $updatetime = date("Y-m-d H:i:s", strtotime($originalDate));	        		 	
            		 	$insertData = array( 
                        					'mjdmagoldrate_22ct'    => (isset($data['Gold']) && $data['Gold']!=''? $data['Gold']:0.00),
                        					'goldrate_22ct'         => (isset($data['Gold']) && $data['Gold']!=''? $data['Gold']:0.00),
                        					'mjdmasilverrate_1gm'   => (isset($data['Silver']) && $data['Silver']!=''? $data['Silver']:0.00),
                        					'silverrate_1gm'        => (isset($data['Silver']) && $data['Silver']!=''? $data['Silver']:0.00),
                        					'platinum_1g'           => (isset($data['Platinum']) && $data['Platinum']!=''? $data['Platinum']:0.00),
                        					'g_ref_no'              => (isset($data['g_ref_no']) && $data['g_ref_no']!=''? $data['g_ref_no']:NULL),
                        					's_ref_no'              => (isset($data['s_ref_no']) && $data['s_ref_no']!=''? $data['s_ref_no']:NULL),
                        					'p_ref_no'              => (isset($data['p_ref_no']) && $data['p_ref_no']!=''? $data['p_ref_no']:NULL),
                        					'updatetime' 		    => $updatetime,
                        					'add_date'              => date("Y-m-d H:i:s"),
                        					'id_employee' 		    => 0
                        				);
            		
            			$rateTxt_data = array(
                                            'mjdmagoldrate_22ct'=> number_format($insertData['mjdmagoldrate_22ct'],'2','.',''),
                                            'goldrate_22ct'     => number_format($insertData['goldrate_22ct'],'2','.',''),
                                            'silverrate_1gm'	=> $insertData['silverrate_1gm'],
                                            'platinum_1g' 		=> number_format($insertData['platinum_1g'],'2','.',''),                                        
                                            'mjdmasilverrate_1gm'=> $insertData['mjdmasilverrate_1gm'], 
                                            'updatetime'	     => $updatetime
                                        );
                                        
            			file_put_contents('../api/rate.txt',json_encode($rateTxt_data)); // Donot use rate.txt if branch wise rate used
            			$this->db->trans_begin();
            			
            			//inserting rates in DB 
            			if($insertData['goldrate_22ct'] > 0){
            			    $status = $this->$model->metal_ratesDB("insert","",$insertData);  
            			    if($status == TRUE){
                                $branch_info = array(								
            								'id_metalrate'		=> ($status['insertID']),
            								'id_branch'			=> ($id_branch),
            								'status'			=> 1,								
            								'date_add'			=> date("Y-m-d H:i:s")															
                						);
                    			//Before update set previous status to 0
                    			$branch_update=array(
                    			    'status'			=> 0,
                    			);
                    			$upd= $this->$model->update_metalrate_status($branch_update,$id_branch);
                    			if($upd == TRUE){
                    			    $ins = $this->$model->insert_metalrate($branch_info,'branch_rate'); 
                    			    if($ins){
                    			        /* is_ratenoti_sent = 2 -> Rate updated & didn\'t send notification ' */
                                        $this->$model->settingsDB("update",1,array('is_ratenoti_sent'=>2));
                                        if($this->db->trans_status() === TRUE){	
                                        	$this->db->trans_commit();
                                            $result['success'][] = $key;
                                        }else{
                                        	$this->db->trans_rollback();
                                            $result['failed'][] = $key;
                                        }
                    			    }else{
                                    	$this->db->trans_rollback();
                                        $result['failed'][] = $key;
                                    }
                    			}else{
                                	$this->db->trans_rollback();
                                    $result['failed'][] = $key;
                                }
                            }else{
                            	$this->db->trans_rollback();
                                $result['failed'][] = $key;
                            }
                		}
                		else{
                        	$this->db->trans_rollback();
                            $result['failed'][] = $key;
                        }
    			    }
    			    
    			    //exit;
        			
					
				}else{
					$this->db->trans_rollback();
					$result['failed'][] = $key;
				}
    	    }
    	}else{
    	    $result['status'] = FALSE;
            $result['msg'] = "Error : Empty array";
    	} 
    	header('Content-Type: application/json');
        			
    	echo json_encode($result);
	}
   
	function update_daily_collection()
	{
		$model=	self::PAY_MODEL;
		$date = date('Y-m-d',strtotime("-1 days"));
		$payments = $this->$model->yesterday_collection('get',$date);
		
		$collection = array('date' 				=> $date,
							'today_collection' 	=> $payments['total_collection'],
							'closing_balance'	=> number_format((float)$payments['total_collection']+$payments['old_balance'], 2, '.', ''),
							'date_add'			=> date('Y-m-d H:i:s'));
	    
	    $status = $this->$model->yesterday_collection('insert','',$collection);
	    //echo "<pre>";print_r($status);echo "</pre>";exit;
	    
	}
	
}
	
?>