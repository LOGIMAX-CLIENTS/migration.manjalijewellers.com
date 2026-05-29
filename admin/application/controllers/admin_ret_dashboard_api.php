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
		$this->load->model('ret_purchase_approval_model');
		$this->load->model('email_model');

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


		foreach($data as $key => $val){

           // $response_data[] = [$val['karigar_name'],(int) $val['sales_bill_count']];
		   if($val[1] > 0){

            $app_response_data['label'][]=$val[0];
            $app_response_data['value'][]=(int) $val[1];
			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];



			}

			$rdata[] = [
                 'lable' =>$val[0],
				'value' =>$val[1],
				'colour_code' =>SELF::COLOUR_CODE[$key]
			];

        }

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'responsedata' => $data,'app_response_data' => $app_response_data,'data' => $rdata), 200);

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


                }

		    }else{
		       // $response_data[10]=['Others',($response_data[10][1] + $val['emp_sales'])];
		    }

			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];

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
			   $app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];

			}else{

			   // var_dump($response_data[9][2] + 1);exit;

			   //$response_data[10]=['Others',($response_data[10][1] + $val['section_sales'])];
		   }

	   }

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

	function get_design_stock_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$app_response_data =[];

		$newdata = [];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

			$id_product	= $this->post('id_product');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

			$id_product	=  $post['id_product'];

		}

		$data = $this->$model->get_design_stock($id_branch,$id_product);


		foreach($data as $key => &$val){

			if(sizeof($response_data)< 5){

            $response_data[] = [$val['design_name'],(int) $val['stock_wt']];
			$app_response_data['label'][]=$val['design_name'];
            $app_response_data['value'][]=(int) $val['stock_wt'];
			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];

			}
			$id_design = $val['id_design'];

			$val['sub_details'] = $this->$model->get_sub_design_stock($id_branch,$id_product,$id_design);

        }

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'chartdata' => $response_data,'app_response_data' =>$app_response_data,'data'=>$data), 200);

	}


	function get_sub_design_stock_post()

	{

		$model=	self::RET_DAS_MODEL;

		$response_data =[];

		$app_response_data =[];

		if ($this->input->is_ajax_request()) {

			$from_date	= $this->post('from_date');

			$to_date	= $this->post('to_date');

			$id_branch	= $this->post('id_branch');

			$id_metal	= $this->post('id_metal');

			$id_product	= $this->post('id_product');

			$id_design	= $this->post('id_design');

		}else{

			$post = $this->get_values();

			$from_date	= $post['from_date'];

			$to_date	= $post['to_date'];

			$id_branch	= $post['id_branch'];

			$id_metal	= $post['id_metal'];

			$id_product	=  $post['id_product'];

			$id_design	=  $post['id_design'];

		}

		$data = $this->$model->get_sub_design_stock($id_branch,$id_product,$id_design);


		foreach($data as $key => $val){

			if(sizeof($response_data)< 5){

            $response_data[] = [$val['sub_design_name'],(int) $val['stock_wt']];
			$app_response_data['label'][]=$val['sub_design_name'];
            $app_response_data['value'][]=(int) $val['stock_wt'];
			$app_response_data['colour_code'][]=SELF::COLOUR_CODE[$key];
			}

        }

		//print_r($this->db->last_query());exit;

		$this->response(array('status'=> true,'chartdata' => $response_data,'app_response_data' =>$app_response_data,'data'=>$data), 200);

	}

	function tag_details_post(){

		$model ="ret_reports_model";
		$this->load->model($model);
		if ($this->input->is_ajax_request()) {

			$post =$this->post();

			$id_product	= $this->post('id_product');

			$id_design	= $this->post('id_design');

			$id_branch	= $this->post('id_branch');

			$id_subdesign	= $this->post('id_sub_design');

		}else{

			$post = $this->get_values();

			$id_product	= $post['id_product'];

			$id_design	= $post['id_design'];

			$id_branch	= $post['id_branch'];

			$id_subdesign	= $post['id_sub_design'];

		}

		$return_data= $this->$model->getTaggeditems($post);

		$this->response(array('status'=> true,'response_data' => $return_data), 200);



	}

	function get_delayed_purchase_orders_post() {
		$model = self::RET_DAS_MODEL;
		if ($this->input->is_ajax_request()) {
			$from_date = $this->post('from_date');
			$to_date = $this->post('to_date');
			$id_branch = $this->post('id_branch');
			$id_metal = $this->post('id_metal');
		} else {
			$post = $this->get_values();
			$from_date = $post['from_date'];
			$to_date = $post['to_date'];
			$id_branch = $post['id_branch'];
			$id_metal = $post['id_metal'];
		}
		$data = $this->$model->get_delayed_purchase_orders($from_date, $to_date, $id_branch, $id_metal);
		$this->response(array('status' => true, 'response_data' => $data), 200);
	}

	function get_delayed_po_payments_post() {
		$model=	self::RET_DAS_MODEL;
		if ($this->input->is_ajax_request()) {
			$from_date	= $this->post('from_date');
			$to_date	= $this->post('to_date');
			$id_branch	= $this->post('id_branch');
			$id_metal	= $this->post('id_metal');
		} else {
			$post = $this->get_values();
			$from_date	= $post['from_date'];
			$to_date	= $post['to_date'];
			$id_branch	= $post['id_branch'];
			$id_metal	= $post['id_metal'];
		}

		$data = $this->$model->get_delayed_po_payments($from_date, $to_date, $id_branch, $id_metal);
		$this->response(array('status'=> true,'response_data' => $data), 200);
	}

	function get_today_delivery_po_payments_post() {
		$model=	self::RET_DAS_MODEL;
		if ($this->input->is_ajax_request()) {
			$from_date	= $this->post('from_date');
			$to_date	= $this->post('to_date');
			$id_branch	= $this->post('id_branch');
			$id_metal	= $this->post('id_metal');
		} else {
			$post = $this->get_values();
			$from_date	= $post['from_date'];
			$to_date	= $post['to_date'];
			$id_branch	= $post['id_branch'];
			$id_metal	= $post['id_metal'];
		}

		$data = $this->$model->get_today_delivery_po_payments($from_date, $to_date, $id_branch, $id_metal);
		$this->response(array('status'=> true,'response_data' => $data), 200);
	}

	function save_po_dashboard_comment_post() {
		$model = self::RET_DAS_MODEL;
		$po_id = $this->post('po_id');
		$comment = $this->post('comment');
		if (!$po_id) {
			$this->response(array('status' => false, 'msg' => 'Missing PO ID'), 200);
			return;
		}
		$result = $this->$model->save_po_dashboard_comment($po_id, $comment);
		if ($result !== false) {
			$this->response(array('status' => true, 'msg' => 'Comment saved successfully'), 200);
		} else {
			$this->response(array('status' => false, 'msg' => 'Failed to save comment'), 200);
		}
	}



    function get_rate_cut_profit_loss_post() {
        $model = self::RET_DAS_MODEL;
        if ($this->input->is_ajax_request()) {
            $from_date = $this->post('from_date');
            $to_date = $this->post('to_date');
            $id_branch = $this->post('id_branch');
        } else {
            $post = $this->get_values();
            $from_date = $post['from_date'];
            $to_date = $post['to_date'];
            $id_branch = $post['id_branch'];
        }

        // Default to last 7 days if no dates provided
        if(empty($from_date) || empty($to_date)) {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime('-7 days'));
        }

        $data = $this->$model->get_rate_cut_profit_loss($from_date, $to_date, $id_branch);
        $this->response(array('status' => true, 'response_data' => $data), 200);
    }

	// ========== MD Approval Dashboard API Methods ==========

	function get_md_pending_orders_post() {
		$model = self::RET_DAS_MODEL;
		$data = $this->$model->get_md_pending_orders();
		$this->response(array('status' => true, 'response_data' => $data), 200);
	}

	function md_approve_order_post() {
		$model = self::RET_DAS_MODEL;
		$id = $this->post('id_customerorder');
		if (!$id) {
			$this->response(array('status' => false, 'msg' => 'Missing order ID'), 200);
			return;
		}
		// Try sending email FIRST — only approve if email succeeds
		$email_result = $this->_send_vendor_email($id);
		if(!$email_result['email_status']) {
			$this->response(array('status' => false, 'msg' => $email_result['email_error']), 200);
			return;
		}
		$result = $this->$model->md_approve_order($id);
		if ($result) {
			$this->response(array('status' => true, 'msg' => 'Purchase Order approved and email sent to vendor'), 200);
		} else {
			$this->response(array('status' => false, 'msg' => 'Failed to approve order'), 200);
		}
	}

	function md_reject_order_post() {
		$model = self::RET_DAS_MODEL;
		$id = $this->post('id_customerorder');
		$reason = $this->post('reason');
		if (!$id || !$reason) {
			$this->response(array('status' => false, 'msg' => 'Missing order ID or reason'), 200);
			return;
		}
		$result = $this->$model->md_reject_order($id, $reason);
		if ($result) {
			$this->response(array('status' => true, 'msg' => 'Purchase Order rejected'), 200);
		} else {
			$this->response(array('status' => false, 'msg' => 'Failed to reject order'), 200);
		}
	}

	function md_bulk_approve_orders_post() {
		$model = self::RET_DAS_MODEL;
		$order_ids = $this->post('order_ids');
		if (!$order_ids || !is_array($order_ids)) {
			$this->response(array('status' => false, 'msg' => 'No orders selected'), 200);
			return;
		}
		// Try sending emails FIRST — only approve if all emails succeed
		foreach($order_ids as $oid) {
			$email_result = $this->_send_vendor_email($oid);
			if(!$email_result['email_status']) {
				$this->response(array('status' => false, 'msg' => $email_result['email_error']), 200);
				return;
			}
		}
		$count = $this->$model->md_bulk_approve_orders($order_ids);
		$this->response(array('status' => true, 'msg' => $count . ' order(s) approved and emails sent'), 200);
	}

	/**
	 * Private helper: Send vendor email after MD approval and log to ret_order_email_logs
	 */
	private function _send_vendor_email($id_customerorder)
	{
		$pur_model = 'ret_purchase_approval_model';

		// Get order details
		$order_row = $this->db->query("SELECT * FROM customerorder WHERE id_customerorder = ".$id_customerorder)->row_array();
		if(empty($order_row)) return array('email_status' => false, 'email_error' => 'Order not found');

		$karigar = $this->$pur_model->get_karigar_details($order_row['id_karigar']);
		if(empty($karigar) || empty($karigar['email'])) {
			// Log if email not available
			$this->$pur_model->save_email_log(array(
				'id_customerorder' => $id_customerorder,
				'id_karigar' => $order_row['id_karigar'],
				'email_id' => isset($karigar['email']) ? $karigar['email'] : NULL,
				'token' => '',
				'status' => 2,
				'error_msg' => 'Email ID not found for this supplier',
				'created_at' => date("Y-m-d H:i:s")
			));
			return array('email_status' => false, 'email_error' => 'Email ID not found for this supplier');
		}

		$token = md5($id_customerorder . time() . uniqid());
		$log_id = $this->$pur_model->save_email_log(array(
			'id_customerorder' => $id_customerorder,
			'id_karigar' => $order_row['id_karigar'],
			'email_id' => $karigar['email'],
			'token' => $token,
			'status' => 0,
			'created_at' => date("Y-m-d H:i:s")
		));

		if($log_id) {
			$accept_url = base_url("index.php/OrderAccept/index/" . $token);

			$orderDetails = $this->$pur_model->get_karigar_order_details($id_customerorder);
			$embeddings = array();

			// Embed logo
			$logo_path = FCPATH . 'assets/img/logo.png';
			if(file_exists($logo_path)) {
				$embeddings['logo_img'] = $logo_path;
				$logo_src = "cid:logo.png";
			} else {
				$logo_src = base_url('assets/img/logo.png');
			}

			if(!empty($orderDetails)) {
				foreach($orderDetails as $itemDet) {
					if(!empty($itemDet['images'])) {
						$img_name = $itemDet['images'][0]['image'];
						$img_path = FCPATH . 'assets/img/order/purchase_order/' . $img_name;
						if(file_exists($img_path)) {
							$embeddings[$img_name] = $img_path;
						} else {
							$img_path = FCPATH . 'assets/img/customer_order/' . $img_name;
							if(file_exists($img_path)) {
								$embeddings[$img_name] = $img_path;
							}
						}
					}
				}
			}

			$subject = "New Purchase Order - #" . $order_row['pur_no'];

			$viewData = array(
				'order' => $order_row,
				'karigar' => $karigar,
				'orderDetails' => $orderDetails,
				'accept_url' => $accept_url,
				'logo_src' => $logo_src
			);

			// Try to load the email template view, fallback to simple message
			if(file_exists(APPPATH . 'views/order/purchase_order_email_template.php')) {
				$message = $this->load->view('order/purchase_order_email_template', $viewData, true);
			} else {
				$karigar_name = isset($karigar['karigar_name']) ? $karigar['karigar_name'] : (isset($karigar['firstname']) ? $karigar['firstname'] : 'Vendor');
				$message = "
					<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
						<h2 style='color: #4f46e5;'>Purchase Order Approved</h2>
						<p>Hello <b>" . $karigar_name . "</b>,</p>
						<p>Your purchase order <b>#" . $order_row['pur_no'] . "</b> has been approved.</p>
						<p>Please click the button below to view the order details and confirm your delivery date:</p>
						<p style='text-align: center; margin: 30px 0;'>
							<a href='" . $accept_url . "' style='background-color: #6366f1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;'>View Order</a>
						</p>
						<p>If the button doesn't work, copy and paste this link into your browser:</p>
						<p><a href='" . $accept_url . "'>" . $accept_url . "</a></p>
						<hr style='border: 0; border-top: 1px solid #eee;'>
					</div>";
			}

			$mail_sent = $this->email_model->send_email($karigar['email'], $subject, $message, "", "", "", $embeddings);

			if($mail_sent) {
				$this->$pur_model->update_email_log($log_id, array('status' => 1));
				return array('email_status' => true, 'email_error' => '');
			} else {
				$error_msg = $this->email_model->last_error;
				// Truncate error message to prevent MySQL issues with large SMTP debug output
				$error_msg_db = substr($error_msg, 0, 500);
				// Check for common quota/limit errors
				if(stripos($error_msg, 'quota') !== false || stripos($error_msg, 'limit') !== false || stripos($error_msg, '550') !== false || stripos($error_msg, 'rate') !== false || stripos($error_msg, 'Daily') !== false) {
					$friendly_error = 'Daily email sending quota exceeded. Please try again later or contact administrator.';
				} else {
					$friendly_error = 'Email sending failed. Please check email configuration.';
				}
				$this->$pur_model->update_email_log($log_id, array('status' => 2, 'error_msg' => $error_msg_db));
				return array('email_status' => false, 'email_error' => $friendly_error);
			}
		}
		return array('email_status' => false, 'email_error' => 'Email log could not be created');
	}
}
?>