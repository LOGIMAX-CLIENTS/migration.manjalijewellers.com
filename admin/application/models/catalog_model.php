<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Catalog_model extends CI_Model
{
	const CUS_TABLE			= "customer";
	const EMP_TABLE			= "employee";
	const CATE_TABLE 	    = "category";
	const PROD_TABLE 	    = "products";

	
	 

    // Catalog category // 
	
	function category_settingDB($type="",$id="",$wallet_array="")
	{
	
		switch($type)
		{
			case 'get':    
				  if($id!=NULL)
				  {
				    $sql = "SELECT id_category, categoryname as category_name,id_parent, ifnull(description, '') as description,catimage,active
			        FROM category cat ".($id!=null? 'Where cat.id_category='.$id:'');
				    $r = $this->db->query($sql);	
				  	return $r->row_array(); //for single row
				  }	
				  else
				  {
				    $sql = "SELECT id_category, categoryname as category_name,id_parent, ifnull(description, '') as description,catimage, if(active = 1,'Active','Inactive') as active
			        FROM category cat ".($id!=null? 'Where cat.id_category='.$id:'');
				    $r = $this->db->query($sql);	
				  	return $r->result_array(); //for multiple rows
				  }
				
			 break;
			 case 'get_max':
			       $sql = "SELECT * FROM wallet_category w 
						   Where w.active=1 
						   Order BY w.id_wallet_category DESC
						   Limit 1";
				  $r = $this->db->query($sql)->row_array();
			 
			    break;
			case 'insert': //insert operation
		                $status = $this->db->insert(self::CATE_TABLE,$wallet_array);
 						return	array('status'=>$status,'ID'=>($status == TRUE ? $this->db->insert_id():''));
			      break; 
			case 'update': //update operation
			             $this->db->where("id_category",$id);
			             $status = $this->db->update(self::CATE_TABLE,$wallet_array);
					     return	array('status' => $status, 'ID' => $id);     			
			      break;      
			case 'delete':
				   $this->db->where("id_category",$id);
		           $status = $this->db->delete(self::CATE_TABLE);
				   return	array('status' => $status, 'deleteID' => $id);  	
			      break;      
			 
			default: //empty record
				  $catalog_category =array(
		  							'id_category'          => NULL,
									'id_parent'    	       => 1,
		  					     	'category_name'         => NULL,
		  							'description'  	       => NULL,
		  							'catimage'  	       => NULL,
		  							'active'               => 1
		  					   );	
			      return $catalog_category;
		}
	}
	
    function product_settingDB($type="",$id="",$wallet_array="")
	{
	
		switch($type)
		{
			case 'get':    
			
				  if($id!=NULL)
				  {
					   $sql = "SELECT id_product,pro.id_category as id_category,productname as product_name, ifnull(pro.description, '') as description, pro.active as active,proimage,weight,size,type,price,code,categoryname as category_name,purity
					   FROM products pro
					   LEFT JOIN category as cat ON cat.id_category =pro.id_category 
						".($id!=null? 'Where pro.id_product='.$id:'');
				  
				      $r = $this->db->query($sql);	
				  	  return $r->row_array(); //for single row
				  }	
				  else
				  {
						$sql = "SELECT id_product,pro.id_category as id_category,productname as product_name, ifnull(pro.description, '') as description, if(pro.active = 1,'Active','Inactive') as active,proimage,weight,size,type,price,code,categoryname as category_name,purity
						FROM products pro
						LEFT JOIN category as cat ON cat.id_category =pro.id_category 
					    ".($id!=null? 'Where pro.id_product='.$id:'');
						 $r = $this->db->query($sql);
						return $r->result_array(); //for multiple rows
				  }
				
			 break;
			 case 'get_max':
			       $sql = "SELECT * FROM wallet_category w 
						   Where w.active=1 
						   Order BY w.id_wallet_category DESC
						   Limit 1";
				  $r = $this->db->query($sql)->row_array();
			 
			    break;
			case 'insert': //insert operation
		                $status = $this->db->insert(self::PROD_TABLE,$wallet_array);
 						return	array('status'=>$status,'ID'=>($status == TRUE ? $this->db->insert_id():''));
			      break; 
			case 'update': //update operation
			             $this->db->where("id_product",$id);
			             $status = $this->db->update(self::PROD_TABLE,$wallet_array);
					     return	array('status' => $status, 'ID' => $id);     			
			      break;      
			case 'delete':
				   $this->db->where("id_product",$id);
		           $status = $this->db->delete(self::PROD_TABLE);
				   return	array('status' => $status, 'deleteID' => $id);  	
			      break;      
			 
			default: //empty record
				  $catalog_category =array(
		  							'id_product'           => NULL,
		  							'id_category'          => NULL,
									'id_parent'    	       => 0,
		  					     	'product_name'         => NULL,
		  							'description'  	       => NULL,
		  							'code'  	           => NULL,
		  							'type'  	           => NULL,
		  							'weight'  	           => NULL,
		  							'purity'  	           => 91.6,
		  							'price'  	           => NULL,
		  							'size'  	           => NULL,
		  							'proimage'  	       => NULL,
		  							'active'               => 1
		  					   );	
			      return $catalog_category;
		}
	}	
	
	function hasChild($parent_id)
	{
		$sql = $this->db->query("SELECT COUNT(*) as count FROM category WHERE id_parent = ".$parent_id);
		return $sql->num_rows();
	}

	
function CategoryTree($parent, $append, $currnet){
    // Add highlight class if current selected
    // $isCurrent = ($currnet == $parent['id_category']);
    // $current_class = $isCurrent ? "class='jstree-open' data-jstree='{\"disabled\": true, \"opened\": true, \"selected\": true}'" : "";
    $current_class = "class='jstree-open' data-jstree='{\"opened\": true}'";

    // List start
    $list = '<li ' . $current_class . ' id="' . $parent['id_category'] . '">' . htmlspecialchars($parent['name']);

    if ($parent['id_category'] == $parent['id_parent']) {
		// print_r($list);exit;
        return $list . '</li>';
    }

    // Check and loop children
    if ($this->hasChild($parent['id_category'])) {
        $append++;
        $list .= "<ul class='child child{$append}'>";
        
        $sql = $this->db->query("SELECT id_category, categoryname AS name, id_parent FROM category WHERE id_parent = " . (int)$parent['id_category']);
			$child = $sql->result_array();

        foreach ($child as $chval) {
            $list .= $this->CategoryTree($chval, $append, $currnet);
			}			

		
			$list .= "</ul>";

	} else {
		// If no children, close the list
		$list .= "<ul class='child child{$append}'></ul>";
		}

    $list .= '</li>';
		return $list;
	}
	 

	 
	function CategoryList($currnet){
    $isCurrent = ($currnet == 1);
    $jstree = $isCurrent ? "data-jstree='{\"opened\": true, \"aria-selected\": true, \"selected\": true}'" : "";
    $current_class = $isCurrent ? "class='jstree-open jstree-clicked'" : "";

    $sql = $this->db->query("SELECT id_category, categoryname AS name, id_parent FROM category WHERE id_parent = 1");
		$parent = $sql->result_array();			

    $mainlist = "<div id='idparent'><ul><li id='0' $current_class $jstree>Home<ul>";

    $append = 0;
	// print_r($parent);exit;
    foreach ($parent as $pval) {
		// print_r($pval);exit;
        $mainlist .= $this->CategoryTree($pval, $append, $currnet);
		}
	
		$mainlist .= "</ul></li></ul></div>";
		return $mainlist;
	}


	
}

?>