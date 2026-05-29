<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Prestaapi_model extends CI_Model
{
	function __construct()
    {      
        parent::__construct();
	}
	
    /**  Read data functions  **/
	
	function fetchHomePgProds($catId=2)
    {  
    	$date = date("Y-m-d"); 
		$today = strtotime($date); 
    	$presta_db = $this->load->database('presta',true);
    	
    	$set_sql = "SELECT value from ps_configuration WHERE name='PS_NB_DAYS_NEW_PRODUCT'";
    	$set = $presta_db->query($set_sql);
    	$new_expiry = $set->row('value');
    	
    	$homeProd = "SELECT p.`id_product` as id,`id_category_default`,p.`id_tax_rules_group`,p.`price`,`additional_shipping_cost`,`weight`,`width`,`height`,p.`active`,`show_price`,p.`date_add`,(UNIX_TIMESTAMP(date_add(p.date_add,INTERVAL ".$new_expiry."  DAY)) >= ".$today.") as new,
    	if(sp.reduction_type='percentage',(sp.reduction*100),sp.reduction) as reduction,i.id_image as id_default_image,(((t.rate/100)*p.price)+p.price) as price_tax_incl,
    	sp.reduction_type,lang.name,t.rate as tax_percent,sp.reduction_tax , ((t.rate/100)*p.price) as tax,trg.name as tax_name
		FROM `ps_product` p
		LEFT JOIN ps_category_product cp on cp.id_product=p.id_product 
		LEFT JOIN ps_image i on i.id_product=p.id_product and i.cover=1
		LEFT JOIN ps_product_lang lang on lang.id_product=p.id_product and lang.id_lang=1
		LEFT JOIN ps_specific_price sp on sp.id_product=p.id_product and ".$today." >= UNIX_TIMESTAMP(sp.from) and  ".$today." <= UNIX_TIMESTAMP(sp.to)
		LEFT JOIN ps_tax_rules_group trg on trg.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax_rule tr on tr.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax t on t.id_tax=tr.id_tax and t.active=1
		WHERE p.active=1 and cp.id_category=".$catId."
		group by p.id_product order by p.id_product desc limit 6";  
		$hp = $presta_db->query($homeProd);
		$prods['homeProd'] = $hp->result_array();
		
		$offerProd = "SELECT p.`id_product` as id,`id_category_default`,p.`id_tax_rules_group`,p.`price`,`additional_shipping_cost`,`weight`,`width`,`height`,p.`active`,`show_price`,p.`date_add`,(UNIX_TIMESTAMP(date_add(p.date_add,INTERVAL ".$new_expiry."  DAY)) >= ".$today.") as new,
    	if(sp.reduction_type='percentage',(sp.reduction*100),sp.reduction) as reduction,i.id_image as id_default_image,(((t.rate/100)*p.price)+p.price) as price_tax_incl,
    	sp.reduction_type,lang.name,t.rate as tax_percent,sp.reduction_tax , ((t.rate/100)*p.price) as tax,trg.name as tax_name
		FROM `ps_product` p
		LEFT JOIN ps_category_product cp on cp.id_product=p.id_product 
		LEFT JOIN ps_image i on i.id_product=p.id_product and i.cover=1
		LEFT JOIN ps_product_lang lang on lang.id_product=p.id_product and lang.id_lang=1
		LEFT JOIN ps_specific_price sp on sp.id_product=p.id_product 
		LEFT JOIN ps_tax_rules_group trg on trg.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax_rule tr on tr.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax t on t.id_tax=tr.id_tax and t.active=1
		WHERE p.active=1 and (".$today." >= UNIX_TIMESTAMP(sp.from) and  ".$today." <= UNIX_TIMESTAMP(sp.to))
		group by p.id_product order by p.id_product desc limit 6";   
		$op = $presta_db->query($offerProd);
		$prods['offerProd'] = $op->result_array();
		
		$newProd = "SELECT p.`id_product` as id,`id_category_default`,p.`id_tax_rules_group`,p.`price`,`additional_shipping_cost`,`weight`,`width`,`height`,p.`active`,`show_price`,p.`date_add`,
    	if(sp.reduction_type='percentage',(sp.reduction*100),sp.reduction) as reduction,i.id_image as id_default_image,(((t.rate/100)*p.price)+p.price) as price_tax_incl,
    	sp.reduction_type,lang.name,t.rate as tax_percent,sp.reduction_tax , ((t.rate/100)*p.price) as tax,trg.name as tax_name
		FROM `ps_product` p 
		LEFT JOIN ps_image i on i.id_product=p.id_product and i.cover=1
		LEFT JOIN ps_product_lang lang on lang.id_product=p.id_product and lang.id_lang=1		
		LEFT JOIN ps_specific_price sp on sp.id_product=p.id_product and ".$today." >= UNIX_TIMESTAMP(sp.from) and  ".$today." <= UNIX_TIMESTAMP(sp.to)
		LEFT JOIN ps_tax_rules_group trg on trg.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax_rule tr on tr.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax t on t.id_tax=tr.id_tax and t.active=1
		WHERE p.active=1 and (UNIX_TIMESTAMP(date_add(p.date_add,INTERVAL ".$new_expiry."  DAY)) >= ".$today.")
		group by p.id_product  order by p.id_product desc limit 6"; 
		$np = $presta_db->query($newProd);
		$prods['newProd'] = $np->result_array();
		
		return $prods;
	}
	
	function fetchofferProds()
    {  
    	$presta_db = $this->load->database('presta',true);
    	$date = date("Y-m-d"); 
		$today = strtotime($date); 
		
    	$set_sql = "SELECT value from ps_configuration WHERE name='PS_NB_DAYS_NEW_PRODUCT'";
    	$set = $presta_db->query($set_sql);
    	$new_expiry = $set->row('value');
    	
		$offerProd = "SELECT p.`id_product` as id,`id_category_default`,p.`id_tax_rules_group`,p.`price`,`additional_shipping_cost`,`weight`,`width`,`height`,p.`active`,`show_price`,p.`date_add`,(UNIX_TIMESTAMP(date_add(p.date_add,INTERVAL ".$new_expiry."  DAY)) >= ".$today.") as new,
    	if(sp.reduction_type='percentage',(sp.reduction*100),sp.reduction) as reduction,i.id_image as id_default_image,(((t.rate/100)*p.price)+p.price) as price_tax_incl,
    	sp.reduction_type,lang.name,t.rate as tax_percent,sp.reduction_tax , ((t.rate/100)*p.price) as tax,trg.name as tax_name
		FROM `ps_product` p
		LEFT JOIN ps_category_product cp on cp.id_product=p.id_product 
		LEFT JOIN ps_image i on i.id_product=p.id_product and i.cover=1
		LEFT JOIN ps_product_lang lang on lang.id_product=p.id_product and lang.id_lang=1
		LEFT JOIN ps_specific_price sp on sp.id_product=p.id_product 
		LEFT JOIN ps_tax_rules_group trg on trg.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax_rule tr on tr.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax t on t.id_tax=tr.id_tax and t.active=1
		WHERE p.active=1 and (".$today." >= UNIX_TIMESTAMP(sp.from) and  ".$today." <= UNIX_TIMESTAMP(sp.to))
		group by p.id_product order by p.id_product desc limit 7";   
		$op = $presta_db->query($offerProd);
		$result = $op->result_array(); 
		
		return $result;
	}
	
	function fetchProdsByCatId($catId)
    {  
    	$presta_db = $this->load->database('presta',true);
    	$date = date("Y-m-d"); 
		$today = strtotime($date); 
    	
    	$set_sql = "SELECT value from ps_configuration WHERE name='PS_NB_DAYS_NEW_PRODUCT'";
    	$set = $presta_db->query($set_sql);
    	$new_expiry = $set->row('value');
    	
    	
    	$sql = "SELECT p.`id_product` as id,`id_category_default`,p.`id_tax_rules_group`,p.`price`,`additional_shipping_cost`,`weight`,`width`,`height`,p.`active`,`show_price`,p.`date_add`,(UNIX_TIMESTAMP(date_add(p.date_add,INTERVAL ".$new_expiry."  DAY)) >= ".$today.") as new,
    	if(sp.reduction_type='percentage',(sp.reduction*100),sp.reduction) as reduction,i.id_image as id_default_image,(((t.rate/100)*p.price)+p.price) as price_tax_incl,
    	sp.reduction_type,lang.name,t.rate as tax_percent,sp.reduction_tax , ((t.rate/100)*p.price) as tax,trg.name as tax_name
		FROM `ps_product` p
		LEFT JOIN ps_category_product cp on cp.id_product=p.id_product 
		LEFT JOIN ps_image i on i.id_product=p.id_product and i.cover=1
		LEFT JOIN ps_product_lang lang on lang.id_product=p.id_product and lang.id_lang=1
		LEFT JOIN ps_specific_price sp on sp.id_product=p.id_product and ".$today." >= UNIX_TIMESTAMP(sp.from) and  ".$today." <= UNIX_TIMESTAMP(sp.to)
		LEFT JOIN ps_tax_rules_group trg on trg.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax_rule tr on tr.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax t on t.id_tax=tr.id_tax and t.active=1
		WHERE p.active=1 and cp.id_category=".$catId."
		group by p.id_product order by p.id_product desc"; 
		//echo $sql;  
		$prods = $presta_db->query($sql);
		return $prods->result_array();
	}
	function fetchNewProds()
    {  
    	$presta_db = $this->load->database('presta',true);
    	$date = date("Y-m-d"); 
		$today = strtotime($date); 
    	
    	$set_sql = "SELECT value from ps_configuration WHERE name='PS_NB_DAYS_NEW_PRODUCT'";
    	$set = $presta_db->query($set_sql);
    	$new_expiry = $set->row('value');
    	
    	$sql = "SELECT p.`id_product` as id,`id_category_default`,p.`id_tax_rules_group`,p.`price`,`additional_shipping_cost`,`weight`,`width`,`height`,p.`active`,`show_price`,p.`date_add`,
    	if(sp.reduction_type='percentage',(sp.reduction*100),sp.reduction) as reduction,i.id_image as id_default_image,(((t.rate/100)*p.price)+p.price) as price_tax_incl,
    	sp.reduction_type,lang.name,t.rate as tax_percent,sp.reduction_tax , ((t.rate/100)*p.price) as tax,trg.name as tax_name
		FROM `ps_product` p 
		LEFT JOIN ps_image i on i.id_product=p.id_product and i.cover=1
		LEFT JOIN ps_product_lang lang on lang.id_product=p.id_product and lang.id_lang=1		
		LEFT JOIN ps_specific_price sp on sp.id_product=p.id_product and ".$today." >= UNIX_TIMESTAMP(sp.from) and  ".$today." <= UNIX_TIMESTAMP(sp.to)
		LEFT JOIN ps_tax_rules_group trg on trg.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax_rule tr on tr.id_tax_rules_group=p.id_tax_rules_group
		LEFT JOIN ps_tax t on t.id_tax=tr.id_tax and t.active=1
		WHERE p.active=1 and (UNIX_TIMESTAMP(date_add(p.date_add,INTERVAL ".$new_expiry."  DAY)) >= ".$today.")
		group by p.id_product  order by p.id_product desc limit 100"; 
		//echo $sql;
		$prods = $presta_db->query($sql); 
//		echo $this->db->_error_message();
		// LEFT JOIN ps_specific_price sp on sp.id_product=p.id_product and ".$date." >= date_format(sp.from,'%Y-%m-%d') and  ".$date." <= date_format(sp.to,'%Y-%m-%d')
		return $prods->result_array();
	}
	
	// associations -> images , product_features ; description ,description_short , name
	
	function fetchProdFeatures($id_lang)
    { 
    	$presta_db = $this->load->database('presta',true);
    	$result = [];
    	$sql1 = "SELECT id_feature_value,value FROM `ps_feature_value_lang` WHERE id_lang =".$id_lang;
		$prod_features = $presta_db->query($sql1);
		$result['features_val'] = $prod_features->result_array();

		$sql2 = "SELECT id_feature,name FROM `ps_feature_lang` WHERE id_lang =".$id_lang;
		$features_lang = $presta_db->query($sql2);
		$result['prod_fea'] = $features_lang->result_array();
			
		return $result;
	}
}
?>