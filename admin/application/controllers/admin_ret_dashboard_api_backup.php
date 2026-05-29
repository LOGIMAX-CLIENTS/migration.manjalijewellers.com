<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php');

class Admin_ret_dashboard_api extends REST_Controller {



	const RET_DAS_MODEL = 'ret_dashboard_api_model';
	const RET_CAT_MODEL = 'ret_catalog_model';
	const RET_PUR_MODEL = 'ret_purchase_order_model';
	const RET_REP_MODEL = 'ret_reports_model';


	const COLOUR_CODE = array(
		'#3366cc', '#109618', '#990099', '#ff9900', '#dc3912',
		'#673AB7', '#F44336', '#009688', '#FF9800', '#3F51B5',
		'#FFEB3B', '#795548', '#9C27B0', '#FF5722', '#607D8B',
		'#00BCD4', '#8BC34A', '#FFEB3B', '#FFC107', '#CDDC39',
		"#FF0000", "#00FF00", "#0000FF", "#FFFF00", "#00FFFF",
		"#FF00FF", "#800000", "#008000", "#000080", "#808000",
		"#800080", "#008080", "#808080", "#C0C0C0", "#FF9999",
		"#99FF99", "#9999FF", "#FFFF99", "#99FFFF", "#FF99FF",
		"#FF6666", "#66FF66", "#6666FF", "#FFFF66", "#66FFFF",
		"#FF66FF", "#FF3333", "#33FF33", "#3333FF", "#FFFF33",
		"#33FFFF", "#FF33FF", "#FF0000", "#00FF00", "#0000FF",
		"#FFFF00", "#00FFFF", "#FF00FF", "#800000", "#008000",
		"#000080", "#808000", "#800080", "#008080", "#808080",
		"#C0C0C0", "#FF9999", "#99FF99", "#9999FF", "#FFFF99",
		"#99FFFF", "#FF99FF", "#FF6666", "#66FF66", "#6666FF",
		"#FFFF66", "#66FFFF", "#FF66FF", "#FF3333", "#33FF33",
		"#3333FF", "#FFFF33", "#33FFFF", "#FF33FF", "#FF0000",
		"#00FF00", "#0000FF", "#FFFF00", "#00FFFF", "#FF00FF",
		"#800000", "#008000", "#000080", "#808000", "#800080",
		"#008080", "#808080", "#C0C0C0", "#FF9999", "#99FF99",
		"#9999FF", "#FFFF99", "#99FFFF", "#FF99FF", "#FF6666",
		"#66FF66", "#6666FF", "#FFFF66", "#66FFFF", "#FF66FF",
		"#FF3333", "#33FF33", "#3333FF", "#FFFF33", "#33FFFF",
		"#FF33FF"
		 );



	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::RET_DAS_MODEL);





	}



	function index(){



	}

	function get_values()
    {
		return (array)json_decode(file_get_contents('php://input'));

	}





    function get_Sales_glance_post()

	{

		$model=	self::RET_DAS_MODEL;

		//print_r($this->get_values());exit;
		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}



		$data = $this->$model->get_dashboard_sales_glance($from_date, $to_date,$id_branch,$id_metal);

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $data), 200);

	}

	function get_top_selling_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$app_response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_top_selling($from_date, $to_date,$id_branch,$id_metal);

		//print_r($this->db->last_query());exit;
		foreach($data as $key => $val){

            $response_data[] = [$val['product_name'],(int) $val['sales_bill_count']];
            $app_response_data['label'][]=$val['product_name'];
            $app_response_data['value'][]=(int) $val['sales_bill_count'];
			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];
        }

		$this->response(array('status'=> true,'responsedata' => $response_data,'app_response_data' =>$app_response_data,'data'=>$data), 200);

	}

	function get_top_sellers_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$app_response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_top_sellers($from_date, $to_date,$id_branch,$id_metal);


		foreach($data as $key => $val){

            $response_data[] = [$val['karigar_name'],(int) $val['sales_bill_count']];
            $app_response_data['label'][]=$val['karigar_name'];
            $app_response_data['value'][]=(int) $val['sales_bill_count'];
			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];
        }

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $response_data,'app_response_data' =>$app_response_data,'data'=>$data), 200);

	}


	function get_monthly_sales_post()

	{

		$model=	self::RET_DAS_MODEL;

		// $from_date	= $this->input->post('from_date');

		// $to_date	= $this->input->post('to_date');

		if ($this->input->is_ajax_request()) {

			$fy_year	= $this->input->post('fy_code');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$fy_year	= $post['fy_code'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_monthly_sales($fy_year,$id_branch,$id_metal);

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $data), 200);

	}

	function get_custome_wise_sale_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$app_response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->input->post('from_date');

			$to_date	= $this->input->post('to_date');

			$id_branch	= $this->input->post('id_branch');

			$id_metal	= $this->input->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}



		$data = $this->$model->get_custome_wise_sale($from_date, $to_date,$id_branch,$id_metal);
		$rdata=[];

		foreach($data as $key => $val){

           // $response_data[] = [$val['karigar_name'],(int) $val['sales_bill_count']];
            $app_response_data['label'][]=$val[0];
            $app_response_data['value'][]=(int) $val[1];

			$rdata[] = [
                 'lable' =>$val[0],
				 'value' =>$val[1]
			];

        }

		$app_response_data['colour_code']=SELF::COLOUR_CODE;

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $data,'app_response_data' => $app_response_data,'data'=>$rdata), 200);

	}

	function get_monthly_sales_app_post()

	{

		$model=	self::RET_DAS_MODEL;


		if ($this->input->is_ajax_request()) {

			$fy_year	= $this->input->post('fin_year');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$fy_year	= $post['fin_year'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_monthly_sales_mobile($fy_year,$id_branch,$id_metal);

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $data), 200);

	}

	function get_financial_year_get()

	{

		$model=	self::RET_DAS_MODEL;

		$data=$this->$model->get_financial_year();

		$this->response(array('status'=> true,'responsedata' => $data), 200);

	}

	function get_branch_comparison_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$id_metal	= $this->input->post('id_metal');

		$data = $this->$model->get_store_sales($from_date, $to_date,$id_branch,$id_metal);


		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $data), 200);

	}

	function get_branch_compare_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		//print_r($_POST);exit;

		$data = $this->$model->get_store_sales($from_date, $to_date,$id_branch,$id_metal);

		foreach($data as $key=>$value){

			$response_data['branch'][]=array('name' =>$value['branch_name'],'short_code' =>$value['branch_short_name'],"id_branch"=>$value['id_branch']);

			$response_data['branch_sales'][]=array('value' =>$value['branch_sales'],"id_branch"=>$value['id_branch']);

			$response_data['colour_code'][]=$value['colour_code'];

         }


		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $response_data), 200);

	}

	function get_store_sales_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_store_sales($from_date, $to_date,$id_branch,$id_metal);


		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $data), 200);

	}

	function get_product_sales_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$id_metal	= $this->input->post('id_metal');

		$data = $this->$model->get_product_sales($from_date, $to_date,$id_branch,$id_metal);


		foreach($data as $key => $val){


			if(sizeof($response_data)< 10){

                $response_data[] = [$val['product_name'],(int) $val['product_sales']];

		    }else{

		    }

        }

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'chartdata' => $response_data,'data'=>$data), 200);

	}

	function get_branch_avg_va_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$group_by	= $this->input->post('group_by');

		$data = $this->$model->get_branch_wastage($from_date, $to_date,$id_branch,$group_by);

		foreach($data as $key => $val){

		    if($group_by == 1){

		         $response_data[] = [$val['product_name'],(float) $val['branch_wastage_va']];

		    }else if($group_by == 2){

		         $response_data[] = [$val['section_name'],(float) $val['branch_wastage_va']];

		    }else{

            $response_data[] = [$val['branch_name'],(float) $val['branch_wastage_va']];

		    }

        }
		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $response_data), 200);

	}

	function get_employee_sales_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_employee_sales($from_date, $to_date,$id_branch,$id_metal);

		foreach($data as $key => $val){

		    if(sizeof($response_data)< 10){

                $response_data[] = [$val['emp_name'],(int) $val['emp_sales']];

                if(sizeof($response_data)< 5){

                    $app_response_data['label'][]=$val['emp_name'];
                    $app_response_data['value'][]=(int) $val['emp_sales'];
        			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];

                }

		    }else{
		       // $response_data[10]=['Others',($response_data[10][1] + $val['emp_sales'])];
		    }

        }

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'chartdata' => $response_data,'app_response_data' =>$app_response_data,'data'=>$data), 200);

	}

	function get_section_sales_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_section_sales($from_date, $to_date,$id_branch, $id_metal);



		foreach($data as $key => $val){

			if(sizeof($response_data)< 10){

			   $response_data[] = [$val['section_name'],(int) $val['section_sales']];
			   $app_response_data['label'][]=$val['section_name'];
			   $app_response_data['value'][]=(int) $val['section_sales'];


			}else{

			   // var_dump($response_data[9][2] + 1);exit;

			   //$response_data[10]=['Others',($response_data[10][1] + $val['section_sales'])];
		   }

	   }

	   $app_response_data['colour_code']=SELF::COLOUR_CODE;

	   //print_r($this->db->last_query());exit;

		   $this->response(array('status'=> true,'chartdata' => $response_data,'app_response_data' =>$app_response_data,'data'=>$data), 200);

	}

	function get_karigar_sales_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$id_metal	= $this->input->post('id_metal');

		$data = $this->$model->get_karigar_sales($from_date, $to_date,$id_branch,$id_metal);


		foreach($data as $key => $val){

		     if(sizeof($response_data)< 10){

                   $response_data[] = [$val['karigar_name'],(int) $val['karigar_sales']];

		     }else{
		       // $response_data[10]=['Others',($response_data[10][1] + $val['emp_sales'])];
		      }

        }

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'chartdata' => $response_data,'data'=>$data), 200);

	}



	function get_product_stock_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$app_response_data =[];

		$app_data = [];

		if ($this->input->is_ajax_request()) {

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_product_stock($id_branch,$id_metal);


		foreach($data as $key => $val){

			if(sizeof($response_data)< 5){

            $response_data[] = [$val['product_name'],(int) $val['stock_wt']];
			$app_response_data['label'][]=$val['product_name'];
            $app_response_data['value'][]=(int) $val['stock_wt'];
			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];

			$app_data[] =$val;
			}

        }

		if ($this->input->is_ajax_request()) {

			$this->response(array('status'=> true,'chartdata' => $response_data,'data'=>$data), 200);

		}else{

			$this->response(array('status'=> true,'app_response_data' =>$app_response_data,'data'=>$app_data), 200);

		}

		//print_r($this->db->last_query());exit;



	}

	function get_section_stock_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$app_response_data =[];

		$app_data = [];

		// $from_date	= $this->input->post('from_date');

		// $to_date	= $this->input->post('to_date');

		if ($this->input->is_ajax_request()) {

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}


		$data = $this->$model->get_section_stock($id_branch, $id_metal);


		foreach($data as $key => $val){

			if(sizeof($response_data)< 5){

				$response_data[] = [$val['section_name'],(int) $val['stock_wt']];
				$app_response_data['label'][]=$val['section_name'];
				$app_response_data['value'][]=(int) $val['stock_wt'];
				$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];

				$app_data[] =$val;
			}



        }

		//print_r($this->db->last_query());exit;

		if ($this->input->is_ajax_request()) {

			$this->response(array('status'=> true,'chartdata' => $response_data,'data'=>$data), 200);

		}else{

			$this->response(array('status'=> true,'app_response_data' =>$app_response_data,'data'=>$app_data), 200);

		}

	}

	function get_karigar_stock_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$return_data=[];

		$app_response_data =[];

		$app_data = [];

		if ($this->input->is_ajax_request()) {

			$id_branch	= $this->input->post('id_branch');

			$id_metal	= $this->input->post('id_metal');

			$id_karigar	= $this->input->post('id_karigar');

			$group_by	= $this->input->post('group_by');

		}else{

			$post = $this->get_values();

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

			$id_karigar = '';

			$group_by = 1;

		}



		$data = $this->$model->get_karigar_stock($id_branch,$id_metal,$id_karigar,$group_by);


		foreach($data as $key => $val){



			if($group_by == 2){
				$return_data[$val['karigar_name']][$val['branch_name']][$val['product_name']]= $val;
				$response_data=[];
			}else{
				$return_data[] = $val;
				if(sizeof($response_data)< 5){

					$response_data[] = [$val['karigar_name'],(int) $val['stock_wt']];

					$app_response_data['label'][]=$val['section_name'];
					$app_response_data['value'][]=(int) $val['stock_wt'];
					$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];

					$app_data[] =$val;

				}
			}


        }

		if ($this->input->is_ajax_request()) {

			$this->response(array('status'=> true,'chartdata' => $response_data,'data'=>$return_data), 200);

		}else{

			$this->response(array('status'=> true,'app_response_data' =>$app_response_data,'data'=>$app_data), 200);

		}



	}

	function get_EstimationStatus_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_dashboard_estimation($from_date, $to_date,$id_branch);




		$count =  0;
		//print_r($this->db->last_query());exit;

		foreach($data as $key => $val){

            $count = $count + 1;

			$response_data['label'][]=array('name' =>$key,"id"=>$count);

			$response_data['value'][]=array('value' =>$val,"id"=>$count);

			$response_data['colour_code'][]=SELF::COLOUR_CODE[$count];;

        }

		$this->response(array('status'=> true,'response_data' => $response_data), 200);

	}

	function get_VitrualTag_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_dashboard_virturaltag_details(date('Y-m-d'), date('Y-m-d'),$id_branch);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}


	function get_SalesReturn_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_dashboard_salesreturn_det(date('Y-m-d'), date('Y-m-d'),$id_branch);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}

	function get_LotDetails_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_dashboard_lot_tag_details(date('Y-m-d'), date('Y-m-d'),$id_branch);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}

	function get_FinancialStatus_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

			$report_type = 1;

			$fin_year = 0;

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

			$report_type =$post['report_type'];

			$fin_year = $post['fin_year'];

		}

		$data = $this->$model->get_dashboard_breakeven_details($from_date, $to_date, $id_branch, $id_metal, $report_type, $fin_year);

		//$this->response(array('status'=> true,'response_data' => rand(10,100)), 200);
		$this->response(array('status' => true, 'response_data' => $data), 200);

	}


	function get_CoverUpReport_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_cover_up_report(date('Y-m-d'), date('Y-m-d'),$id_branch,$id_metal);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}

// PURACHASE INWARDS

	function get_purchase_inwards_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_purchase_inwards($from_date, $to_date,$id_branch,$id_metal);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}


	function get_vendor_payment_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_vendor_payment($from_date, $to_date,$id_branch);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}


	function get_outward_details_post()
	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_outward_details($from_date, $to_date,$id_branch,$id_metal);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}

	function getMetalwiseApprovalTransaction_post()
	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->getMetalwiseApprovalTransactionList($from_date, $to_date,$id_branch);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}


	function get_crdr_details_post()
	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_crdr_details($from_date, $to_date,$id_branch);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}

	function get_qc_details_post()
	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_qc_details($from_date, $to_date,$id_branch,$id_metal);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}


	function getActiveMetals_get(){

		$model=	self::RET_CAT_MODEL;

		$this->load->model('ret_catalog_model');

		$data = $this->$model->getActiveMetals();

		$this->response(array('status'=> true,'response_data' => $data), 200);


	}

	function get_weight_gain_loss_post(){

    	$model=	self::RET_REP_MODEL;
    	$this->load->model($model);



    	if ($this->input->is_ajax_request()) {

    		$post = $this->post();

    		$from_date	= $this->post('from_date');

    		$to_date	= $this->post('to_date');

    		$id_branch	= $this->post('id_branch');

    		$id_metal	= $this->post('id_metal');

    	}else{

    		$post = $this->get_values();

    		$from_date	= $post['from_date'];

    		$to_date	= $post['to_date'];

    		$id_branch	= $post['id_branch'];

    		$id_metal	= $post['id_metal'];

    	}



    		$summary['blc_pcs'] = 0;

    		$summary['blc_gwt'] = 0;

    		$summary['blc_nwt'] = 0;

    		$summary['blc_diawt'] = 0;


    		$po_details=$this->$model->getLotwiseTaggedVault($post);



    		foreach($po_details as $val)

    		{



    			$summary['blc_pcs']=number_format($summary['blc_pcs']+($val['lotpcs'] - $val['taggedpcs'] - $val['recpcs'] - $val['lmpcs'] ), 2, '.', '');

    			$summary['blc_gwt']=number_format( $summary['blc_gwt']+($val['lotgrswt'] - $val['taggrswt'] - $val['recgrswt'] - $val['lmgrswt'] ) , 2, '.', '');

    			$summary['blc_nwt']=number_format( $summary['blc_nwt']+($val['lotnetwt'] - $val['tagnetwt'] - $val['recnetwt'] - $val['lmnetwt'] ) , 2, '.', '');

    			$summary['blc_diawt']=number_format( $summary['blc_diawt']+($val['lotdiawt'] - $val['lotdiawt'] - $val['lotdiawt'] - $val['lotdiawt'] ) , 2, '.', '');



    		}

    		$data = array(
    							'summary'=> $summary
    						);

    	$this->response(array('status'=> true,'response_data' => $data), 200);


    }



	function get_rate_fixed_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_rate_fixed_details($from_date, $to_date,$id_branch);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}


	function get_rate_unfixed_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_rate_unfixing_details($from_date, $to_date,$id_branch,$id_metal);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}

	function get_accountstock_inwards_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->get_accountstock_inwards_details($from_date, $to_date,$id_branch,$id_metal);

		$this->response(array('status'=> true,'response_data' => $data), 200);

	}

	function get_supplier_crde_post()

	{

    	$model=	self::RET_REP_MODEL;
    	$this->load->model($model);

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$post =$this->post();

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->getSupplierTransactionList($post);

		$summary['Debit'] = 0;

		$summary['Credit'] = 0;

		$summary['Balance'] = 0;


		foreach($data as $val)

		{

			$summary['Debit'] += $val['Debit'];

			$summary['Credit'] +=$val['Credit'];

			$summary['Balance'] += $val['RunningBalance'];


		}



		$this->response(array('status'=> true,'response_data' => $summary), 200);

	}

	function get_supplier_transcation_post()

	{

    	$model=	self::RET_REP_MODEL;
    	$this->load->model($model);

		$response_data =[];

		if ($this->input->is_ajax_request()) {

			$post =$this->post();

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

		}

		$data = $this->$model->getSupplierTransactionList($post);

		$return_data = [];

		foreach($data as $val)

		{

			$return_data[] = ['Debit' =>$val['Debit'],'Credit' => $val['Credit'],'Balance' =>$val['balance'],'RunningBalance' =>$val['RunningBalance'],'Supplier' =>$val['firstname']];

		}



		$this->response(array('status'=> true,'response_data' => $return_data), 200);

	}




}

?>