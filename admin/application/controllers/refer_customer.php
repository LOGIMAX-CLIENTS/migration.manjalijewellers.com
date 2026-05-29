<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Refer_customer extends CI_Controller
{

	const CAT_MODEL	= "ret_catalog_model";
	const SETT_MOD	= "admin_settings_model";

	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::SETT_MOD);
		$this->load->model("admin_settings_model");
		$this->load->model("employee_model");
	}

	public function index() {}

	public function customer_feedback($type = "")
	{

		$model = 'admin_settings_model';
		switch ($type) {
			case 'list':
				$data['header'] = $this->$model->getFeedbackHeader('');
				$this->load->view('master/customer/feedback_form', $data);
				break;

			case 'save':

				$data = $_POST;

				$this->db->trans_begin();

				if ($data['is_existing_customer'] == 0) {

					$insert_data = array(
						"firstname" => ucfirst($data['cus_name']),
						"mobile" => $data['cus_mobile'],
						'email' => $data['cus_mail'],
						'date_of_birth' => ($data['cus_dob'] ? $data['cus_dob'] : null),
						'date_of_wed' => ($data['cus_doa'] ? $data['cus_doa'] : null),
						'date_add' => date("Y-m-d H:i:s")
					);

					$cus_insert_id = $this->$model->insertData($insert_data, "customer");

					if ($cus_insert_id) {
						$insert_addressdata  = array(
							"id_country" => '101',
							"id_state" => !empty($data['cus_state']) ? $data['cus_state'] : null,
							"id_city" => !empty($data['cus_city']) ? $data['cus_city'] : null,
							"id_customer" => $cus_insert_id,
							"address1" => $data['address'],
							'pincode' => $data['cus_pincode'],
							'date_add' => date("Y-m-d H:i:s")
						);
					}

					$cus_addressinsert_id = $this->$model->insertData($insert_addressdata, "address");
				}

				$customer = array(
					"branch_id"           	=> (!empty($data['branch_id']) ? intval($data['branch_id']) : null),
					"firstname"  			=> ucfirst($data['cus_name']),
					"mobile" 				=> $data['cus_mobile'],
					"mail_id"         		=> $data['cus_mail'],
					"cus_id"         		=> ($cus_insert_id ? $cus_insert_id : $data['cus_id']),
					"date_of_birth"        	=> ($data['cus_dob'] ? $data['cus_dob'] : null),
					"date_of_anniversary"	=> ($data['cus_doa'] ? $data['cus_doa'] : null),
					"pincode"  				=> $data['cus_pincode'],
					"city"          		=> $data['cus_city'],
					"state"          		=> $data['cus_state'],
					"source"      			=> $data['source'],
					"address"      			=> $data['address'],
					"purchase_occasion"     => (!empty($data['purchase_occasion']) ? $data['purchase_occasion'] : null),
					"reason_not_purchase"   => (!empty($data['reason_not_purchase']) ? $data['reason_not_purchase'] : null),
					"created_on"     		=> date('Y-m-d H:i:s'),
					"rating_ambiance"     	=> $data['rating_ambiance'],
					"rating_collection"     => $data['rating_collection'],
					"staff_id"              => (!empty($data['staff_id']) ? intval($data['staff_id']) : null),
					"rating_staff"          => (!empty($data['rating_staff']) ? intval($data['rating_staff']) : 0),
					"suggestions"           => (!empty($data['suggestions']) ? $data['suggestions'] : null),
				);
				$feedback_id = $this->$model->insertData($customer, "feedback");

				if ($feedback_id) {
					if (!empty($data['feedback']) && is_array($data['feedback'])) {
						foreach ($data['feedback'] as $header_id => $answer) {
							$answer_data = array(
								'feedback_id'   => $feedback_id,
								'header_id' 	=> $header_id,
								'answer'     	=> $answer
							);
							$this->db->insert('feedback_value', $answer_data);
						}
					}
				}

				if ($this->db->trans_status() == true) {
					$this->db->trans_commit();

					// Fetch branch-specific Google Review URL
					$google_review_url = null;
					$branch_id_val     = !empty($data['branch_id']) ? intval($data['branch_id']) : 0;
					if ($branch_id_val > 0) {
						$branch_row = $this->db->query(
							"SELECT google_review_url FROM branch WHERE id_branch = " . $branch_id_val
						)->row_array();
						if (!empty($branch_row['google_review_url'])) {
							// Re-validate from DB - defense in depth against pre-existing bad data
							$stored_url = $branch_row['google_review_url'];
							if (filter_var($stored_url, FILTER_VALIDATE_URL)
								&& preg_match('#^https://(www\.)?(maps\.)?google\.(com|co\.\w{2,3}|\w{2,3})|g\.page|search\.google\.com#i', $stored_url)) {
								$google_review_url = $stored_url;
							}
						}
					}

					$result = array(
						"status"            => true,
						"message"           => "Feedback Sent Successfully",
						"google_review_url" => $google_review_url
					);
				} else {
					$this->db->trans_rollback();
					$result = array("status" => false, "message" => "Failed");
				}

				echo json_encode($result);
				break;
		}
	}

	public function getCustomersBySearch()
	{
		$model = "admin_settings_model";
		$data = $this->$model->getAvailableCustomers($_POST['searchTxt']);
		echo json_encode($data);
	}

	
	// ─── Employee by Branch (for QR feedback form) ────────────────────────────
	function getEmployeeByBranch($idBranch = '')
	{
		if ($idBranch != '') {
			$query = $this->db->query("SELECT id_employee, CONCAT(firstname, ' - ', emp_code) as emp_data FROM employee WHERE login_branches IN ('0', '" . $idBranch . "') AND active = 1 ORDER BY firstname ASC");
		} else {
			$query = $this->db->query("SELECT id_employee, CONCAT(firstname, ' - ', emp_code) as emp_data FROM employee WHERE active = 1 ORDER BY firstname ASC");
		}
		$result = $query->result_array();
		return !empty($result) ? $result : [];
	}

	public function get_state()
	{
		if (isset($_POST['id_country'])) {
			$data = $this->admin_settings_model->get_state($_POST['id_country']);
			echo $data;
		}
	}

	public function get_city()
	{
		if (isset($_POST['id_state'])) {
			$data = $this->admin_settings_model->get_city($_POST['id_state']);
			echo $data;
		}
	}

	function get_country_code()
    {
        $sql=$this->db->query("SELECT * FROM country WHERE IFNULL(mob_code,'')!='' ");
        echo  json_encode($sql->result_array());
    }

	public function get_employees_by_branch()
	{
		$branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : '';
		$result    = $this->employee_model->getEmployeeByBranch($branch_id);
		echo json_encode($result);
	}

	public function get_branch_location()
	{
		$branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;
		if ($branch_id > 0) {
			$row = $this->db->query("SELECT id_state, id_city FROM branch WHERE id_branch = " . $branch_id)->row_array();
			echo json_encode($row ? $row : array());
		} else {
			echo json_encode(array());
		}
	}
}
