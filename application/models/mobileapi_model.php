<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Mobileapi_model extends CI_Model
{
    const TAB_ADD = "address";
    const TAB_ST = "state";
    const TAB_CY = "country";
    const TAB_CT = "city";
    const TAB_CUS = "customer";
    const TAB_ACC = "scheme_account";
    const TAB_SCH = "scheme";
    const TAB_PAY = "payment";
    const SCH_ENQ = "sch_enquiry";
    const API_MODEL = 'syncapi_model';
    //const TAB_KYC    = "kyc";
    const CUS_IMG_PATH = 'admin/assets/img/customer';
    const BRN_IMG_PATH = 'assets/img/branch';
    const CUS_ORD_IMG_PATH = 'admin/assets/img/orders';
    function __construct()
    {
        parent::__construct();
        $this->load->model('services_modal');
        $this->load->helper('metal_wgt_digit');
        $this->load->model("commonapi_model");     //lumpcanp
        $this->load->model(self::API_MODEL);
        // $this->load->helper('metal_wgt_digit');
        $this->sms_data = $this->services_modal->sms_info();
        $this->sms_chk = $this->services_modal->otp_smsavilable();
    }
    /**  Read data functions  **/
    function company_details()
    {
        $sql = " Select  cs.branch_settings,cs.is_branchwise_cus_reg,cs.maintenance_text,cs.maintenance_mode,cs.mob_code,cs.maintenance_text,cs.maintenance_mode,cs.mob_code as call_prefix,c.id_company,c.whatsapp_no,c.company_name,c.short_code,c.pincode,c.mobile,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,c.tollfree1 as tollfree,cy.name as country,cs.currency_symbol,cs.currency_name,c.phone1,c.mobile1,cs.allow_notification,c.whatsapp_no1, c.whatsapp_url, c.insta_url, c.fb_url, c.youtube_url, c.website as web_url
				from company c
					join chit_settings cs
					left join country cy on (c.id_country=cy.id_country)
					left join state s on (c.id_state=s.id_state)
					left join city ct on (c.id_city=ct.id_city) ";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    public function updData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? $id_value : 0);
    }
    /*function get_currency()
        {
            $sql = " Select c.company_name,cs.currency_symbol,cs.show_closed_list,cs.currency_name,cs.allow_notification,cs.reg_existing,cs.regExistingReqOtp,cs.useWalletForChit,allow_catlog ,
            cs.allow_referral,cs.has_lucky_draw
                    from company c
                        join chit_settings cs ";
            $data = $this->db->query($sql);	
            $result['currency']=$data->row_array();
            $filename = base_url().'api/rate.txt'; 
            $data = file_get_contents($filename);
            $result['metal_rates'] = (array) json_decode($data);
            return $result;
        }*/
    /*function get_currency()
    {
        $sql = " Select c.company_name,cs.currency_symbol,cs.show_closed_list,cs.currency_name,cs.allow_notification,cs.reg_existing,cs.regExistingReqOtp,cs.useWalletForChit,allow_catlog ,
        cs.allow_referral,cs.has_lucky_draw,cs.enableSilver_rateDisc,cs.enableGoldrateDisc,cs.is_branchwise_cus_reg,cs.branch_settings
                from company c
                    join chit_settings cs ";
        $sql1="SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,
                Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   FROM metal_rates m 
                WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m )";
        $data = $this->db->query($sql);	
        $result['currency']=$data->row_array();
        $rate = $this->db->query($sql1);	
        $result['metal_rates']=$rate->row_array();
        return $result;
    }*/
    // Code By Karthikai Kumaran for show social media links ON 16/10/2025
    function get_currency($id_branch)
    {
        $sql = "SELECT
                    cs.lock_metal,
                    cs.show_kyc_optional,
                    cs.showDirectpay,
                    cs.is_pin_required AS mpin_setting,
                    cs.enable_digi_gold AS show_digi,
                    cost_center,
                    block_pay_mins,
                    cs.is_kyc_required,
                    cs.auto_debit,
                    cs.auto_debit_allow_app_pay,
                    c.company_name,
                    cs.currency_symbol,
                    cs.rate_history,
                    cs.show_closed_list,
                    cs.currency_name,
                    cs.allow_notification,
                    cs.reg_existing,
                    cs.regExistingReqOtp,
                    cs.useWalletForChit,
                    allow_catlog,
                    c.tollfree1 AS tollfree,
                    cs.allow_referral,
                    cs.show_video_shop,
                    cs.show_customer_order,
                    cs.has_lucky_draw,
                    cs.enableSilver_rateDisc,
                    cs.enableGoldrateDisc,
                    cs.is_kyc_required,
                    cs.kyc_approval,
                    cs.block_kyc_by,
                    cs.pan_required_by,
                    cs.pan_req_amt,
                    cs.is_branchwise_cus_reg,
                    cs.branch_settings,
                    '₹' AS curr_symb_html,
                    enable_dth,
                    1 AS show_invite,
                    1 AS allow_shareonly,
                    allow_wallet,
                    cs.branchwise_scheme,
                    cs.enable_digi_gold AS show_digi,
                    cs.allow_our_stores,
                    co.show_amt_history,
                    co.show_wgt_history,
                    co.show_close,
                    co.show_language as showlanguage,
                    c.show_social_media,cs.metal_wgt_decimal,cs.metal_wgt_roundoff
                FROM
                    company c
                JOIN chit_settings cs
                JOIN configuration co";
        // print_r($sql);exit;
        $data = $this->get_chit_settings();
        if ($data['is_branchwise_rate'] == 1) {
            if ($id_branch != '' && $id_branch != 0)        // branch wise rate view. If you are logged in//HH
            {
                $sql1 = "SELECT  m.goldrate_18ct,m.market_gold_20ct, b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
                Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   
                FROM metal_rates m 
                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
                left join branch b on b.id_branch=br.id_branch
                " . ($id_branch != '' ? " WHERE br.id_branch=" . $id_branch . "" : '') . " ORDER by br.id_metalrate desc LIMIT 1";
                //print_r($sql1);exit;
            } else {
                $sql1 = "SELECT m.goldrate_18ct,m.market_gold_20ct,  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
                Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   
                FROM metal_rates m 
                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
                left join branch b on b.id_branch=br.id_branch
                WHERE br.id_branch=1 ORDER by br.id_metalrate desc LIMIT 1";     //1st branch rate view. If you are not logged in//HH
                //print_r($sql1);exit;
            }}
		else{
		// $sql1="SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
		// 		Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   FROM metal_rates m 
		// 		WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m )";

		$sql1 ="SELECT 
					m.mjdmagoldrate_22ct,
					m.goldrate_22ct,
					m.goldrate_24ct,
					m.silverrate_1gm,
					m.silverrate_1kg,
					m.mjdmasilverrate_1gm,
					m.platinum_1g,
					DATE_FORMAT(m.updatetime,'%d-%m-%Y %h:%i %p') AS updatetime,
					ROW_NUMBER() OVER (ORDER BY m.id_metalrates DESC) AS row_num
				FROM metal_rates m
				LIMIT 2";
        }
        $data = $this->db->query($sql);
        $result['currency'] = $data->row_array();
        $rate = $this->db->query($sql1);
        $result['metal_rates'] = $rate->result_array();
        if ($result['metal_rates'][0]['silverrate_1gm'] == 0) {
            $silver = "SELECT m.id_metalrates,m.silverrate_1gm FROM metal_rates m WHERE m.silverrate_1gm!='0.00' ORDER by m.id_metalrates DESC LIMIT 1";
            $silver_rate = $this->db->query($silver)->row_array();
            $result['metal_rates']['silverrate_1gm'] = $silver_rate['silverrate_1gm'];
        }
        if ($result['metal_rates'][0]['platinum_1g'] == 0) {
            $silver = "SELECT m.id_metalrates,m.platinum_1g FROM metal_rates m WHERE m.silverrate_1gm!='0.00' ORDER by m.id_metalrates DESC LIMIT 1";
            $silver_rate = $this->db->query($silver)->row_array();
            $result['metal_rates']['platinum_1g'] = $silver_rate['platinum_1g'];
        }
		$diff = $this->metalRateDiffCalculation($result['metal_rates']);
		$result['metal_rates']['gold_22kt_8grm']=	$result['metal_rates'][0]['goldrate_22ct'] * 8;
		$result['metal_rates'] = $result['metal_rates'][0];
		$result['metal_rates']['diff'] = $diff;
        $result['reg_custom_fields'] = $this->config->item('custom_fields');
        $result['digi_custom_fields'] = $this->config->item('digi_custom_fields');
        $result['currency']['searchbyaccno'] = $this->config->item('searchbyaccno');
		// print_r($result['metal_rates']);exit;
		//$result['reg_custom_fields'] = $this->config->item('custom_fields');
        return $result;
    }
    function get_chit_settings()
    {
        $sql = "select * from chit_settings";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    //get customer ID by mobile number	
    function get_customerID($mobile)
    {
        $this->db->select('id_customer');
        $this->db->where('mobile', $mobile);
        $customer = $this->db->get(self::TAB_CUS);
        return $customer->row_array();
    }
    //get last generated otp by mobile number
    function get_lastOTP($id_customer)
    {
        $this->db->select('id_customer,last_generated_otp,last_otp_expiry');
        $this->db->where('id_customer', $id_customer);
        $customer = $this->db->get(self::TAB_CUS);
        return $customer->row_array();
    }
    function get_customerByMobile($mobile)
    {
        $record = array();
        $sql = "Select if(c.pin_no IS NULL && cs.is_pin_required = 1 ,1,0) as is_pin_req,c.pin_no,c.id_customer,c.firstname,c.lastname,c.notification,c.mobile,c.email,c.id_branch,c.cus_img,c.kyc_status,
		cs.is_kyc_required,cs.is_pin_required 
		from customer c
		JOIN chit_settings cs
		where c.mobile=" . $mobile;
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            $row = $result->row_array();
            $file = self::CUS_IMG_PATH . '/' . $row['id_customer'] . '/customer.jpg';
            $img_path = ($row['cus_img'] != null ? (file_exists($file) ? $file : null) : null);
            $record = array('mobile' => $mobile, 'mpin_setting' => $row['is_pin_required'], 'id_customer' => $row['id_customer'], 'is_pin_req' => $row['is_pin_req'], 'kyc_status' => $row['kyc_status'], 'is_kyc_required' => $row['is_kyc_required'], 'email' => $row['email'], 'id_branch' => $row['id_branch'], 'lastname' => ucfirst($row['lastname']), 'firstname' => ucfirst($row['firstname']), 'cus_img' => $img_path);
        }
        //print_r($this->db->last_query());exit;
        return $record;
    }
    function get_customerProfile($id_customer)
    {
        $file = base_url() . 'admin/assets/img/customer/' . $id_customer . '/customer.jpg';
        $cus_img = (file_exists('admin/assets/img/customer/' . $id_customer . '/customer.jpg') ? $file . "?nocache=" . time() : null);
        $sql = "Select
		   c.id_customer,c.title,c.firstname,c.lastname,DATE_FORMAT(c.date_of_birth, '%d-%m-%Y') as date_of_birth,DATE_FORMAT(c.date_of_wed, '%d-%m-%Y') as date_of_wed,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,c.nominee_address1,c.nominee_address2,
		   ifnull('" . $cus_img . "',null)  as cus_img,c.pan as pan_old,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,
   	       c.comments,c.username,c.is_new,c.active,c.`date_add`,c.`date_upd`,cy.name as countryname,s.name as statename,ct.name as cityname,c.notification,k.number as pan
			From
			  customer c
			left join " . self::TAB_ADD . " a on(c.id_customer=a.id_customer)
			left join " . self::TAB_CY . " cy on (a.id_country=cy.id_country)
			left join " . self::TAB_ST . " s on (a.id_state=s.id_state)
			left join " . self::TAB_CT . " ct on (a.id_city=ct.id_city)
            left join kyc k on k.id_customer = c.id_customer and k.kyc_type = 2
			where c.id_customer='" . $id_customer . "'";
        $result = $this->db->query($sql)->row_array();
        $result['reg_custom_fields'] = $this->config->item('custom_fields');
        return $result;
    }
    //get customer by id
    function get_customerByID($id_customer)
    {
        $file = base_url() . 'admin/assets/img/customer/' . $id_customer . '/customer.jpg';
        $cus_img = (file_exists('admin/assets/img/customer/' . $id_customer . '/customer.jpg') ? $file . "?nocache=" . time() : null);
        $sql = "Select
		   c.id_customer,c.firstname,c.lastname,c.date_of_birth,c.id_branch,c.title,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,
		   ifnull('" . $cus_img . "',null)  as  cus_img,c.pan as pan_old,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,
   	c.comments,c.username,c.passwd,c.is_new,c.active,c.`date_add`,c.`date_upd`,cy.name as countryname,s.name as statename,ct.name as cityname,k.number as pan
			From
			  customer c
			left join " . self::TAB_ADD . " a on(c.id_customer=a.id_customer)
			left join " . self::TAB_CY . " cy on (a.id_country=cy.id_country)
			left join " . self::TAB_ST . " s on (a.id_state=s.id_state)
			left join " . self::TAB_CT . " ct on (a.id_city=ct.id_city)
            left join kyc k on k.id_customer = c.id_customer and k.kyc_type = 2 
			where c.id_customer='" . $id_customer . "'";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    function get_wallet_accounts($id_customer)
    {
        $sql = "Select
					  wa.id_wallet_account,
					  c.id_customer,
					  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,
					  c.mobile,
					  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
					  wa.wallet_acc_number,
					  Date_Format(wa.issued_date,'%d-%m-%Y') as issued_date,
					  wa.remark,
					  if(wa.active=1,'Active','Inactive') as active,
					  SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) as  issues,
					  SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END) as redeem,
					  (SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) -   SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END)) as balance,
					   cs.wallet_amt_per_points,cs.wallet_balance_type,cs.wallet_points
				From wallet_account wa
					Left Join customer c on (wa.id_customer=c.id_customer)
					Left Join employee e on (wa.id_employee=e.id_employee)
					Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account)
					join chit_settings cs
					Where c.id_customer='" . $id_customer . "'
					Group By wa.id_wallet_account";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    function get_wallet_transactions($id_customer, $id_wallet_trans)
    {
        $data = array();
        if ($id_wallet_trans == '') {
            $sql = "Select
				  wt.id_wallet_transaction,
				  wt.id_wallet_account,
				  c.id_customer,
				  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,
				  c.mobile,
				  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
				  wa.wallet_acc_number,
				  wa.issued_date,
				  Date_Format(wt.date_transaction,'%d-%m-%Y') as date_transaction,
				  wt.transaction_type,
				  wt.value,
				  wt.description,
				  wa.active,
                  wt.id_sch_ac as id_scheme_account
			From wallet_transaction wt
			Left Join wallet_account wa on (wt.id_wallet_account=wa.id_wallet_account)
			Left Join customer c on (wa.id_customer=c.id_customer)
			Left Join employee e on (wa.id_employee=e.id_employee)
			Where c.id_customer =" . $id_customer . "ORDER BY wt.id_wallet_transaction ASC";
        } else {
            $sql = "Select
				  wt.id_wallet_transaction,
				  wt.id_wallet_account,
				  c.id_customer,
				  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,
				  c.mobile,
				  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
				  wa.wallet_acc_number,
				  wa.issued_date,
				  Date_Format(wt.date_transaction,'%d-%m-%Y') as date_transaction,
				  wt.transaction_type,
				  wt.value,
				  wt.description,
				  wa.active,
                  wt.id_sch_ac as id_scheme_account
			From wallet_transaction wt
			Left Join wallet_account wa on (wt.id_wallet_account=wa.id_wallet_account)
			Left Join customer c on (wa.id_customer=c.id_customer)
			Left Join employee e on (wa.id_employee=e.id_employee)
			Where c.id_customer =" . $id_customer . " " . ($id_wallet_trans == 0 || $id_wallet_trans == '' ? ' order by id_wallet_transaction ASC limit 30' : ' and id_wallet_transaction < ' . $id_wallet_trans . ' order by id_wallet_transaction ASC limit 30');
        }
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            $data = $result->result_array();
            foreach ($data as &$d) {
                $record = $this->db->query("SELECT s.is_lucky_draw, sa.scheme_acc_number, sa.group_code as scheme_group_code, cs.schemeaccNo_displayFrmt,cs.scheme_wise_acc_no,sa.id_branch as acc_branch,s.code,sa.start_year 
		                                    FROM `scheme_account` sa 
		                                    LEFT JOIN scheme s on s.id_scheme = sa.id_scheme 
		                                    JOIN chit_settings cs 
		                                    WHERE sa.id_scheme_account = " . $d['id_scheme_account'])->row_array();
                $accNumData = array(
                    'is_lucky_draw' => $record['is_lucky_draw'],
                    'scheme_acc_number' => $record['scheme_acc_number'],
                    'scheme_group_code' => $record['scheme_group_code'],
                    'schemeaccNo_displayFrmt' => $record['schemeaccNo_displayFrmt'],
                    'scheme_wise_acc_no' => $record['scheme_wise_acc_no'],
                    'acc_branch' => $record['acc_branch'],
                    'code' => $record['code'],
                    'start_year' => $record['start_year'],
                    'id_scheme_account' => $d['id_scheme_account']
                );
                $d['scheme_acc_number'] = $this->getAccNoFormat($accNumData);
            }
        }
        return $data;
    }
    function get_weights($min_weight = 0, $max_weight = 0)
    {
        $this->db->select('*');
        $this->db->where('active', 1);
        if ($min_weight > 0 && $max_weight > 0) {
            $this->db->where('weight >=', (float)$min_weight);
            $this->db->where('weight <=', (float)$max_weight);
        }
        $this->db->order_by('weight', 'asc');
        return $this->db->get('weight')->result_array();
    }
    function get_country()
    {
        $this->db->select('*');
        return $this->db->get('country')->result_array();
    }
    function get_countryByID($id_country)
    {
        $this->db->select('*');
        $this->db->where('id_country', $id_country);
        return $this->db->get('country')->row_array();
    }
    function get_state($id_country)
    {
        $this->db->select('*');
        $this->db->where('id_country', $id_country);
        // 		return $this->db->get('state')->result_array();
        $states = $this->db->get('state');
        $default_state = $this->db->query("Select id_state from company LIMIT 1")->row();
        foreach ($states->result() as $state) {
            if ($default_state->id_state == $state->id_state) {
                $data[] = array(
                    'id_state' => $state->id_state,
                    'name' => $state->name,
                    'is_default' => 1
                );
            } else {
                $data[] = array(
                    'id_state' => $state->id_state,
                    'name' => $state->name,
                    'is_default' => 0
                );
            }
        }
        return $data;
    }
    function get_stateByID($id_state)
    {
        $this->db->select('*');
        $this->db->where('id_state', $id_state);
        return $this->db->get('state')->row_array();
    }
    function get_city($id_state)
    {
        $this->db->select('*');
        $this->db->where('id_state', $id_state);
        $cities = $this->db->get('city');
        // 		print_r($cities->result());exit;
        $default_city = $this->db->query("Select id_city from company LIMIT 1")->row();
        foreach ($cities->result() as $city) {
            if ($default_city->id_city == $city->id_city) {
                $data[] = array(
                    'id_city' => $city->id_city,
                    'name' => $city->name,
                    'is_default' => 1
                );
            } else {
                $data[] = array(
                    'id_city' => $city->id_city,
                    'name' => $city->name,
                    'is_default' => 0
                );
            }
        }
        return $data;
    }
    function get_cityByID($id_city)
    {
        $this->db->select('*');
        $this->db->where('id_city', $id_city);
        return $this->db->get('city')->row_array();
    }
    //get all locations
    function get_cityAll()
    {
        $this->db->select('*');
        return $this->db->get('city')->result_array();
    }
    function getMatchingCountry($char)
    {
        $sql = "Select * from country where name Like '$char%'";
        return $result = $this->db->query($sql)->result_array();
    }
    function getMatchingState($char, $id_country)
    {
        $sql = "Select * from state where name Like '$char%' And id_country='$id_country'";
        return $result = $this->db->query($sql)->result_array();
    }
    function getMatchingCity($char, $id_state)
    {
        $sql = "Select * from city where name Like '$char%' And id_state='$id_state'";
        return $result = $this->db->query($sql)->result_array();
    }
    function get_stateAll()
    {
        $this->db->select('*');
        return $this->db->get('state')->result_array();
    }
    //get Schemes
    function get_schemesAll()
    {
        $sql = "SELECT id_scheme,id_classification,scheme_name,max_weight,min_weight,amount,code,description,scheme_type,total_installments ,branch_settings,is_pan_required
            FROM scheme s
	    	join chit_settings where active=1";
        $scheme = $this->db->query($sql)->result_array();
        return $scheme;
    }
    //get Schemes
    function get_groups($id_scheme)
    {
        $sql = "SELECT s.id_scheme_group, s.id_scheme, s.group_code,sch.code as scheme_code, DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date, DATE_FORMAT(s.end_date,'%d-%m-%Y') as end_date FROM scheme_group s left join scheme sch on (sch.id_scheme=s.id_scheme)where s.id_scheme=" . $id_scheme;
        return $this->db->query($sql)->result_array();
    }
    /*function get_activeSchemes()
    {
        $sql = ('SELECT if(scheme_type = 1 ,if(max_weight=min_weight,1,0),"a") as type,id_scheme,min_amount,max_amount,classification_name as cls_name,s.id_classification,scheme_name,max_weight,min_weight,amount,code,s.description,scheme_type,total_installments,interest,interest_by,interest_value 
        FROM scheme s
        left join sch_classify cls on cls.id_classification = s.id_classification
        where s.active=1 and visible=1');
        return $this->db->query($sql)->result_array();
    }*/
    function get_activeSchemes($id_branch)
    {
        $data = $this->get_costcenter();
        $file_path = base_url() . "admin/assets/img/sch_image";
        $sql = ('SELECT is_enquiry,if(s.logo=null,s.logo ,concat("' . $file_path . '","/",s.logo) ) as sch_logo,if(scheme_type = 1 ,if(max_weight=min_weight,1,0),"a") as type,s.id_scheme,min_amount,max_amount,classification_name as cls_name,s.id_classification,scheme_name,max_weight,min_weight,amount,code,s.description,scheme_type,total_installments,interest,interest_by,interest_value,s.flx_denomintion,s.flexible_sch_type,s.one_time_premium, 
        s.closing_maturity_days,s.maturity_days,s.maturity_type,if(s.display_payable = 0, "Days", if(s.display_payable = 1 , "Weeks", if(s.display_payable = 2 , "Months",if(s.display_payable = 3 , "Month","-" ) ))) as ins_word,
		 if(s.display_payable=0, "Payable (In days)", if(s.display_payable=1,"Payable (In weeks)", if(s.display_payable = 2 , "Payable (Monthly)",if(s.display_payable = 3 , "Payable (One time)","-" )))) as payable_word
		FROM scheme s
		left join sch_classify cls on cls.id_classification = s.id_classification
		' . ($data['branchwise_scheme'] == 1 ? "left join scheme_branch sb on sb.id_scheme=s.id_scheme" : '') . '
		where s.active=1 and s.is_digi = 0 and visible=1 ' . ($id_branch != '' && $id_branch != 0 && $data['branchwise_scheme'] == 1 ? 'and sb.id_branch in (' . $id_branch . ')' : '') . ' group by s.id_scheme ORDER BY amount ASC');
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function get_scheme($id_scheme, $id_customer)
    {
        $sql = ('select s.is_digi,s.sch_approval,s.is_lumpSum,s.emp_refferal as show_referral,s.one_time_premium,s.id_scheme,s.min_amount,s.max_amount, s.is_digi,is_enquiry,flexible_sch_type,s.id_metal,s.is_lucky_draw,cls.description as terms,if(s.scheme_type = 1 ,if(s.max_weight=s.min_weight,1,0),"a") as type,s.id_scheme,cs.cusName_edit,s.scheme_name,s.min_amount,if(scheme_type=3 && max_amount!=0,max_amount,round(max_weight *(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1))) as oldmax_amount,
		if(s.is_digi = 1 && s.daily_pay_limit > 0 , s.daily_pay_limit ,(if(scheme_type=3 && max_amount!=0,max_amount,round(max_weight *(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)))))  as max_amount,
        if(s.is_digi = 1 && s.daily_pay_limit > 0 , s.daily_pay_limit ,(if(scheme_type=3 && min_amount!=0,min_amount,round(min_weight *(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)))))  as min_amount,
		s.max_weight,s.min_weight,s.amount,s.description,s.scheme_type,s.total_installments,s.interest,s.interest_by, s.interest_value,cs.branch_settings,if(c.cus_ref_code!="",c.cus_ref_code,"") as cus_ref_code,if(c.emp_ref_code!="",c.emp_ref_code,"") as emp_ref_code,is_pan_required,cs.cusbenefitscrt_type,cs.empbenefitscrt_type,s.flx_denomintion,s.get_amt_in_schjoin,s.maturity_type,maturity_days,
    	CONCAT(if(cs.has_lucky_draw = 1 && s.is_lucky_draw = 1,sg.group_code,s.code)) as code,
    	cs.is_branchwise_cus_reg,cs.branchwise_scheme,c.id_branch,0 as askBranch,s.agent_refferal,s.has_gift,
    	concat(c.firstname," ",c.lastname) as cus_name,
    	cs.kyc_approval,
        cs.is_kyc_required,
        s.is_pan_required,
        s.is_aadhaar_required,
        IFNULL(s.aadhaar_required_amt,0) as aadhaar_required_amt,
        c.aadharid as aadhaar_no,	
        c.aadhar_ImgName,
        IFNULL(s.pan_req_amt,0) as pan_req_amt,c.pan as pan_old,
        if(schacc.issue_self_giftBonus is null OR  schacc.issue_self_giftBonus = 2,s.show_bonus_gift,0) as show_bonus_gift,
        s.closing_maturity_days,s.maturity_days,s.maturity_type,if(s.display_payable = 0, "Days", if(s.display_payable = 1 , "Weeks", if(s.display_payable = 2 , "Months",if(s.display_payable = 3 , "Month","-" ) ))) as ins_word,
		 if(s.display_payable=0, "Payable (In days)", if(s.display_payable=1,"Payable (In weeks)", if(s.display_payable = 2 , "Payable (Monthly)",if(s.display_payable = 3 , "Payable (One time)","-" )))) as payable_word,k.number as pan,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable, 
         c.id_customer,cs.pan_required_by, ifnull(cs.pan_req_amt,0) as  pan_req_amt, (ifnull(s.amount,0) * s.total_installments) as total_amount,IFNULL((SELECT SUM(py.payment_amount) FROM payment py LEFT JOIN scheme_account sch_acc on (sch_acc.id_scheme_account = py.id_scheme_account) where sch_acc.active = 1 and sch_acc.is_closed = 0 and py.payment_status = 1 and sch_acc.id_customer = c.id_customer) ,0) as cus_overall_amount 
    	from scheme s
    	Left Join scheme_group sg On (s.id_scheme = sg.id_scheme )
    	left join sch_classify cls on cls.id_classification = s.id_classification
    	Left join (SELECT issue_self_giftBonus,id_scheme FROM scheme_account WHERE id_customer = ' . $id_customer . ' ) schacc on (schacc.id_scheme = s.id_scheme)
    	join chit_settings cs
    	join customer c	
        left join kyc k on k.id_customer = c.id_customer and k.kyc_type = 2
    	where s.id_scheme=' . $id_scheme . '  and s.active=1 and c.active=1 and c.id_customer=' . $id_customer . ' group by s.id_scheme');
        //echo $sql; exit;
        $result = $this->db->query($sql)->row_array();
        if ($result['branch_settings'] == 1) {
            if ($result['is_branchwise_cus_reg'] == 0) {
                if ($result['branchwise_scheme'] == 1) {
                    $sch_branch = $this->db->query("SELECT id_branch from scheme_branch where id_scheme = " . $id_scheme);
                    $schBrData = $sch_branch->result_array();
                    if ($sch_branch->num_rows() == 1) { // Donot ask the branch set the available branch
                        $result['id_branch'] = $schBrData[0]['id_branch'];
                    } else if ($sch_branch->num_rows() > 0) { // Ask the branch
                        $result['askBranch'] = 1;
                    }
                    $result['sch_branch'] = $schBrData;
                } else {
                    $result['askBranch'] = 1;
                }
            }
        }
        $result['active_accounts'] = 0;
        if ($result['is_digi'] == 1) {
            //get id_scheme_account and payment details...
            $sql = $this->db->query("SELECT sa.id_branch FROM scheme_account sa WHERE sa.id_scheme=" . $id_scheme . " AND sa.id_customer=" . $id_customer . " LIMIT 1");
            $pay = $sql->row_array();
            if ($sql->num_rows() > 0) {
                $result['askBranch'] = 0;
                $result['id_branch'] = $pay['id_branch'];
            }
        } else {
            //get id_scheme_account and payment details...
            $sql = $this->db->query("SELECT count(sa.id_scheme_account) as  active_accounts FROM scheme_account sa WHERE sa.active = 1 AND sa.is_closed = 0 AND sa.id_scheme=" . $id_scheme . " AND sa.id_customer=" . $id_customer . " ");
            $pay = $sql->row_array();
            if ($sql->num_rows() > 0) {
                $result['active_accounts'] = $pay['active_accounts'];
            }
        }
        /* PAN required chit_settings and scheme based - Esakkiraja_20260109 starts */
        $result['pan_required'] = $this->get_pan_required($result);
        /* PAN required chit_settings and scheme based - Esakkiraja_20260109 ends */
        return $result;
    }
    function branchesData()
    {
        $sql = "SELECT id_branch,name,short_name FROM branch b  where show_to_all != 3 and active = 1 order by sort ";
        $branch = $this->db->query($sql)->result_array();
        return $branch;
    }
    function get_schAccount_no($id_scheme)
    {
        //$sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account where id_scheme=".$id_scheme." ORDER BY id_scheme_account DESC";
        $sql = "SELECT max(TRIM(LEADING '0' FROM scheme_acc_number))  as lastSchAcc_no FROM scheme_account where id_scheme=" . $id_scheme . " ORDER BY id_scheme_account DESC ";
        return $this->db->query($sql)->row()->lastSchAcc_no;
    }
    // to check scheme_acc_number already exists
    /*function verify_existing($scheme_acc_number)
    {
        $this->db->select('id_scheme_account,id_customer');
        $this->db->where('scheme_acc_number',$scheme_acc_number);
        $schAcc = $this->db->get('scheme_account');
        if($schAcc->num_rows() > 0)
        {
            return TRUE;
        }	
    }*/
    function verify_existing($schAcc)
    {
        $this->db->select('id_scheme_account,id_customer');
        $this->db->where('scheme_acc_number', $schAcc['scheme_acc_number']);
        $schAcc = $this->db->get('scheme_account');
        if ($schAcc['group_code'] != NULL) {
            $this->db->where('group_code', $schAcc['group_code']);
        }
        if ($schAcc->num_rows() > 0) {
            return TRUE;
        }
    }
    function checkService($serviceID)
    {
        $email = 0;
        $sms = 0;
        $query = $this->db->get_where('services', array('id_services' => $serviceID));
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $email = $row->serv_email;
            $sms = $row->serv_sms;
        }
        $data = array("email" => $email, "sms" => $sms);
        return $data;
    }
    //get customer schemes_accounts
    function get_scheme_accounts($id_customer)
    {
        $sql = "Select sa.id_scheme_account,s.code,sa.scheme_acc_number,s.scheme_name,s.scheme_type,ref_no as client_id,sa.start_date,
       IF(s.scheme_type=0,s.amount,IF(s.scheme_type=1,s.max_weight,s.amount)) as installment,
       s.total_installments,
       COUNT(DISTINCT DATE_FORMAT(p.date_payment,'%Y%m')) as paid_installment,
       MAX(DISTINCT DATE_FORMAT(p.date_payment,'%Y/%m')) as last_paid_month,
       MAX(DISTINCT p.date_payment) as last_paid,
       FORMAT(IFNULL(SUM(p.metal_weight),0),3) as total_weight,
       FORMAT(IFNULL(SUM(p.payment_amount),0),2) as total_paid_amount,
       FORMAT(IFNULL(SUM(py.metal_weight),0),3) as cur_total_weight,
       FORMAT(IFNULL(s.max_weight,0) - IFNULL(SUM(py.metal_weight),0),3) as eligible_weight,
       FORMAT(IFNULL(SUM(py.payment_amount),0),2) as cur_total_paid,
       IF(COUNT(DISTINCT DATE_FORMAT(p.date_payment,'%Y%m')) <= s.total_installments,IF(s.scheme_type=0,(IF(IFNULL(SUM(py.payment_amount),0)=0,'Y','N')),IF(IFNULL(SUM(py.metal_weight),0) < s.max_weight,'Y','N')),'Completed') as pay_now
From scheme_account sa
Left join scheme s On (sa.id_scheme=s.id_scheme)
Left join payment p On (sa.id_scheme_account=p.id_scheme_account and p.payment_status=1)
Left join payment py On (sa.id_scheme_account=py.id_scheme_account and py.payment_status=1  and DATE_FORMAT(py.date_payment,'%Y%m')=DATE_FORMAT(now(),'%Y%m'))
Where sa.active=1 and s.active=1 and sa.id_customer='$id_customer'
Group By sa.id_scheme_account";
        return $result = $this->db->query($sql)->result_array();
    }
    function get_schemeaccount_detail($id_scheme_account)
    {
        $schemeAcc = array();
        $sql = "Select s.is_digi,s.gst,s.gst_type,p.payment_status,if(s.free_payment = 1 , s.allowSecondPay,0) as redirectToPay,approvalReqForFP,
				IF(s.firstPayDisc=1,s.firstPayDisc_value,0.00) as discount,s.firstPayDisc_by,s.firstPayDisc,sa.is_new,
			    sa.id_scheme_account,sa.id_branch as sch_join_branch,
			    s.id_scheme,cs.is_branchwise_rate,
			    c.id_customer,
			   sa.scheme_acc_number as chit_number,
			    IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
			    c.mobile,
			    s.scheme_type,
			    s.code,
			    IFNULL(s.min_chance,0) as min_chance,
			    IFNULL(s.max_chance,0) as max_chance,
			    Format(IFNULL(s.max_weight,0),3) as max_weight,
			    Format(IFNULL(s.min_weight,0),3) as min_weight,
			    Date_Format(sa.start_date,'%d-%m-%Y') as start_date,
			    IF(s.scheme_type=0,s.amount,IF(s.scheme_type=1,s.max_weight,s.amount)) as payable,
			    s.total_installments,
			   IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
  as paid_installments,s.get_amt_in_schjoin,sa.firstPayment_amt,sa.is_registered,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)
 as total_paid_weight,
  	round(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,
				if(s.scheme_type=3 && s.max_amount!='',s.max_amount,0)))) as max_amount,cs.firstPayamt_payable,s.firstPayamt_as_payamt,s.flexible_sch_type,sa.firstPayment_amt,
  if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments, 
(s.total_installments - COUNT(payment_amount)), 
ifnull((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) - SUM(p.no_of_dues),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) 
as totalunpaid_1, 
   if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,   (s.total_installments - if(sa.is_opening = 1,(COUNT(payment_amount)+sa.paid_installments),COUNT(payment_amount))),ifnull(((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))+1) - IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) as totalunpaid,  
IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_add,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = p.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='AD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_adv_paycount,
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='PD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_pend_paycount,
IF(s.scheme_type =1 and s.max_weight != s.min_weight,true,false) as is_flexible_wgt,
			    Format(IFNULL(cp.total_amount,0),2) as  current_total_amount,
			    Format(IFNULL(cp.total_weight,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_weight),0) ,3) as  current_total_weight,
			    IFNULL(cp.paid_installment,0)       as  current_paid_installments,
			    IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0) as  current_chances_used,
			    s.is_pan_required,
			    IFNULL(Date_Format(max(p.date_payment),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0))                 as last_paid_date,
					IFNULL(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(max(p.date_add),'%Y%m')),IF(sa.is_opening=1,PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(sa.last_paid_date,'%Y%m')),0)) as last_paid_duration,
				IF(sa.is_opening = 1 and s.scheme_type = 0 || s.scheme_type = 2,
				IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),false,true),
				true) AS previous_amount_eligible,
				count(pp.id_scheme_account) as cur_month_pdc,
				s.allow_unpaid,
				if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_months,
				s.allow_advance,
				if(s.allow_advance=1,s.advance_months,0) as advance_months,
				IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid,
				sa.disable_payment
			From scheme_account sa
			Left Join scheme s On (sa.id_scheme=s.id_scheme)
			Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and p.payment_status=1)
			Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
			Left Join
			(	Select
				  sa.id_scheme_account,
				  COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) as paid_installment,
				  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances,
				  SUM(p.payment_amount) as total_amount,
				  SUM(p.metal_weight) as total_weight
				From payment p
				Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
				Where p.payment_status=1 and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
				Group By sa.id_scheme_account
			) cp On (sa.id_scheme_account=cp.id_scheme_account)
			 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))
			 	JOIN chit_settings cs 
		Where sa.active=1 and sa.is_closed = 0 and sa.id_scheme_account='$id_scheme_account'";
        //	print_r($sql);exit;
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            foreach ($records->result() as $record) {
                $allowed_due = 0;
                $due_type = '';
                $metal_rates = $this->get_metalrate($record->sch_join_branch, $record->is_branchwise_rate); //For branchwise rate
                if ($record->paid_installments > 0 || $record->totalunpaid > 0) {
                    if ($record->currentmonthpaycount == 0) {  // current month not paid (allowed pending due + current due)
                        if ($record->allow_unpaid == 1) {
                            if ($record->allow_unpaid_months > 0 && ($record->total_installments - $record->paid_installments) >= $record->allow_unpaid_months && $record->totalunpaid > 0) {
                                if (($record->total_installments - $record->paid_installments) == $record->allow_unpaid_months) {
                                    $allowed_due = $record->allow_unpaid_months;
                                    $due_type = 'PD'; //  pending
                                } else {
                                    $allowed_due = $record->allow_unpaid_months + 1;
                                    $due_type = 'PN'; // normal and pending
                                }
                            } else {
                                $allowed_due = 1;
                                $due_type = 'ND'; // normal due
                            }
                        } else {
                            $allowed_due = 1;
                            $due_type = 'ND'; // normal due
                        }
                    } else {     //current month paid
                        if ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months) {
                            // can pay previous pending dues if attempts available 
                            if ($record->totalunpaid > $record->allow_unpaid_months) {
                                $allowed_due = $record->allow_unpaid_months;
                                $due_type = 'PD'; // pending due
                            } else {
                                $allowed_due = $record->totalunpaid;
                                $due_type = 'PD'; // pending due
                            }
                        } else {  // check allow advance
                            if ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonth_adv_paycount) < $record->advance_months) {
                                if (($record->advance_months + $record->paid_installments) <= $record->total_installments) {
                                    $allowed_due = ($record->advance_months - ($record->currentmonth_adv_paycount));
                                    $due_type = 'AD'; // advance due
                                } else {
                                    $allowed_due = ($record->total_installments - $record->paid_installments);
                                    $due_type = 'AD'; // advance due
                                }
                            } else { // have to check
                                $allowed_due = 0;
                                $due_type = ''; // normal due
                            }
                        }
                    }
                } else {  // check allow advance and add due with currect month (allowed advance due + current due)
                    if ($record->allow_advance == 1) { // check allow advance
                        if ($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)) {
                            if (($record->total_installments - $record->paid_installments) == $record->advance_months) {
                                $allowed_due = $record->advance_months;
                                $due_type = 'AN'; // advance and normal
                            } else {
                                $allowed_due = $record->advance_months + 1;
                                $due_type = 'AN'; // advance and normal
                            }
                        } else {
                            $allowed_due = 1;
                            $due_type = 'ND'; // normal due
                        }
                    } else {
                        $allowed_due = 1;
                        $due_type = 'ND'; // normal due
                    }
                }
                $schemeAcc[] = array(
                    'gst_type' => $record->gst_type,
                    'gst' => $record->gst,
                    'id_scheme_account' => $record->id_scheme_account,
                    'chit_number' => $record->chit_number,
                    'account_name' => $record->account_name,
                    'start_date' => $record->start_date,
                    'mobile' => $record->mobile,
                    'id_branch' => $record->sch_join_branch,
                    //'payable' 		=> $record->payable,
                    'payable' => ($record->is_digi == 1 && $record->paid_installments == 0 ? $record->firstPayment_amt : (($record->scheme_type == 3 && $record->max_amount != 0 && ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2) && $record->max_amount != '' ? ((($record->firstPayamt_payable == 1 || $record->firstPayamt_as_payamt == 1) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) || ($record->is_registered == 1)) ? round($record->firstPayment_amt) : round($record->max_amount - str_replace(',', '', $record->current_total_amount))) : ($record->scheme_type == 3 && ($record->max_weight != 0 || $record->max_weight != '') ? round(($record->max_weight - $record->current_total_weight) * $record->metal_rate) : $record->payable)))),
                    'metal_rate' => $metal_rates['goldrate_22ct'],
                    'code' => $record->code,
                    'scheme_type' => $record->scheme_type,
                    'total_installments' => $record->total_installments,
                    'paid_installments' => $record->paid_installments,
                    'total_paid_amount' => $record->total_paid_amount,
                    'total_paid_weight' => $record->total_paid_weight,
                    'current_total_amount' => $record->current_total_amount,
                    'current_paid_installments' => $record->current_paid_installments,
                    'current_chances_used' => $record->current_chances_used,
                    'eligible_weight' => ($record->max_weight - $record->current_total_weight),
                    'last_paid_date' => $record->last_paid_date,
                    'is_pan_required' => $record->is_pan_required,
                    'last_transaction' => $this->getLastTransaction($record->id_scheme_account),
                    'isPaymentExist' => $this->isPaymentExist($record->id_scheme_account),
                    'isPendingStatExist' => $this->isPendingStatExist($record->id_scheme_account),
                    'max_weight' => $record->max_weight,
                    'current_total_weight' => $record->current_total_weight,
                    'previous_amount_eligible' => $record->previous_amount_eligible,
                    'cur_month_pdc' => $record->cur_month_pdc,
                    'redirectToPay' => $record->redirectToPay,
                    'approvalReqForFP' => $record->approvalReqForFP,
                    'allow_pay' => ($record->disable_payment != 1 && ($record->payment_status != 2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ? ($record->is_flexible_wgt ? ($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ? 'N' : ($record->paid_installments == $record->total_installments && $record->currentmonthpaycount == 0 ? 'N' : 'Y')) : ($record->paid_installments < $record->total_installments ? ($record->allow_unpaid == 1 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months ? 'Y' : ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount - 1) < $record->advance_months ? 'Y' : ($record->currentmonthpaycount == 0 ? 'Y' : 'N'))) : 'N')) : 'N') : 'N') : 'N'),
                    'allowed_dues' => ($record->is_flexible_wgt == 1 ? 1 : $allowed_due),
                    'allowPayDisc' => ($record->is_new == 'Y' ? ($record->scheme_type == 0 ? ($record->paid_installments == 0 && $record->firstPayDisc == 1 ? 1 : 0) : ($record->current_chances_used == 0 && $record->paid_installments == 0 && $record->firstPayDisc == 1 ? 1 : 0)) : 0),
                    'firstPayDisc' => $record->firstPayDisc,
                    'firstPayDisc_by' => $record->firstPayDisc_by,
                    'discount' => $record->discount,
                    'due_type' => ($record->is_flexible_wgt == 1 ? 'ND' : $due_type),
                    'max_allowed_limit' => ($record->is_flexible_wgt == 1 ? 1 : $allowed_due),
                    'min_allowed_limit' => 1,
                    'sel_due' => 1,   //default selected due
                    'pdc_payments' => ($record->cur_month_pdc > 0 ? $this->get_postdated_payment($record->id_scheme_account) : NULL),
                    'max_amount' => round(($record->scheme_type == 3 && $record->max_amount != 0 && $record->max_amount != '' && ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2) ? (($record->firstPayamt_payable == 1 || $record->firstPayamt_as_payamt == 1) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) ? $record->firstPayment_amt : ($record->max_amount - str_replace(',', '', $record->current_total_amount))) : ($record->scheme_type == 3 && $record->max_weight != 0 && $record->max_weight != '' ? (($record->max_weight - $record->current_total_weight) * $metal_rates['goldrate_22ct']) : $record->max_amount))),
                    'is_digi' => $record->is_digi
                );
            }
            return $schemeAcc;
        }
    }
    //didn't show due Aftre Acc Apprvl in Admin chked //hh
    function get_payment_details($id_customer, $id_branch = '', $id_sch_acc_string = '')
    {
        $showGCodeInAcNo = $this->config->item('showGCodeInAcNo');
        $filename = base_url() . 'api/rate.txt';
        $data = file_get_contents($filename);
        $result['metal_rates'] = (array) json_decode($data);
        $id_scheme_account = $this->get_idSchemeAccounts($id_customer);
        $id_scheme_values = array_column($id_scheme_account, 'id_scheme_account');
        // Build properly formatted string
        if (!empty($id_scheme_values)) {
            // If IDs are integers (no quotes needed)
            $id_sch_acc_string = implode(",", $id_scheme_values);
            // OR if IDs need quotes
            // $id_sch_acc_string = "'" . implode("','", $id_scheme_values) . "'";
        } else {
            $id_sch_acc_string = '';
        }
        $schemeAcc = array();
        $where = !empty($id_sch_acc_string) ? " AND p.id_scheme_account in ({$id_sch_acc_string})" : '';
        $sql = "Select s.is_lumpSum,sa.lump_joined_weight,sa.lump_payable_weight,s.firstPayment_as_wgt,sa.firstpayment_wgt,s.allow_general_advance,s.adv_min_amt,s.adv_max_amt,s.adv_denomination,s.maturity_type,s.avg_calc_by,	
		s.unpaid_months,
		date_add(date(sa.start_date),interval + s.total_installments month) as daily_sch_allow_pay_till,
        IFNULL(SUM(CASE WHEN p.payment_status = 1 AND p.date_payment >= CURDATE() AND p.date_payment < CURDATE() + INTERVAL 1 DAY THEN 1 ELSE 0 END),0) as curday_total_paid_count,
        IFNULL(SUM(CASE WHEN p.payment_status = 1 AND p.due_type = 'AD' THEN 1 ELSE 0 END),0) as total_adv_paid,
        IFNULL(SUM(CASE WHEN p.payment_status = 1 AND p.due_type = 'PD' THEN 1 ELSE 0 END),0) as total_pend_paid,
        s.pay_duration,s.installment_cycle,s.grace_days,
		cs.restrict_lastPayment_days,IFNULL(sa.start_year,'') as start_year,br.short_name as acc_branch,s.code,
		                cs.schemeaccNo_displayFrmt,s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		s.get_amt_in_schjoin,s.firstPayamt_maxpayable,s.is_enquiry,s.is_digi,s.total_days_to_pay,DATEDIFF(CURDATE(),date(sa.start_date)) joined_date_diff, 
        IFNULL(SUM(CASE WHEN p.payment_status = 1 AND p.date_payment >= CURDATE() AND p.date_payment < CURDATE() + INTERVAL 1 DAY THEN p.payment_amount ELSE 0 END),0) as curday_total_paid,
		s.daily_pay_limit,IF(s.daily_pay_limit != 'NULL' , 1,0) as daily_payLimit_applicable,
		IF(s.daily_pay_limit != 'null',IFNULL(s.daily_pay_limit - IFNULL(SUM(CASE WHEN p.payment_status = 1 AND p.date_payment >= CURDATE() AND p.date_payment < CURDATE() + INTERVAL 1 DAY THEN p.payment_amount ELSE 0 END),0),0),'') as eligible_amt, 
        IFNULL(SUM(CASE WHEN p.payment_status = 1 AND p.due_type = 'AD' AND p.date_payment >= CURDATE() AND p.date_payment < CURDATE() + INTERVAL 1 DAY THEN 1 ELSE 0 END),0) as curday_advance_count,
        IFNULL(SUM(CASE WHEN p.payment_status = 1 AND p.due_type = 'AD' AND p.date_payment >= DATE_FORMAT(NOW() ,'%Y-%m-01') AND p.date_payment < DATE_FORMAT(NOW() + INTERVAL 1 MONTH ,'%Y-%m-01') THEN 1 ELSE 0 END), 0) as curmonth_advance_count,
		s.show_ins_type,s.set_as_min_from,s.set_as_max_from,s.no_of_dues as dues_count,s.rate_fix_by,s.rate_select,Date_Format(sa.start_date,'%Y-%m-%d') as join_date,
                date_format(CURRENT_DATE(),'%m') as cur_month,if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments, 
                (s.total_installments - COUNT(payment_amount)), 
                ifnull((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) - SUM(p.no_of_dues),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) 
                as missed_ins,sa.avg_payable,s.avg_calc_ins,p.payment_status,
                PERIOD_DIFF(Date_Format(CURRENT_DATE(),'%Y%m'),Date_Format(sa.start_date,'%Y%m')) as current_pay_ins, 
		    	PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')) as paid_ins,IFNULL(sa.maturity_date,DATE_ADD(date(sa.start_date), INTERVAL s.maturity_days DAY)) as maturity_date,
			    sg.group_code as scheme_group_code, UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw,otp_price_fixing,fixed_rate_on,
                s.allowSecondPay,s.free_payment,cs.firstPayamt_payable,sa.firstPayment_amt,sa.is_registered,
                CONCAT(if(" . $showGCodeInAcNo . "=1,if(has_lucky_draw = 1,sg.group_code,s.code),'') ,' ',ifnull(sa.scheme_acc_number,'Not Allocated')) as chit_number,
			    s.gst_type,s.gst,sa.id_scheme_account,
			    IF(s.discount=1,s.firstPayDisc_value,0.00) as discount_val,s.firstPayDisc_by,s.firstPayDisc,sa.is_new, 
			    s.id_scheme,br.id_branch, br.short_name, br.name as branch_name, 
			    c.id_customer,s.min_amount,s.max_amount,s.pay_duration,s.discount_type,s.discount_installment,s.discount,sa.id_branch as sch_join_branch,cs.is_branchwise_rate,
			    IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
			    c.mobile,
			    s.scheme_type,s.maturity_days,s.firstPayamt_as_payamt,s.flexible_sch_type,s.one_time_premium,s.is_enquiry,
			    s.fix_weight,sa.fixed_metal_rate,sa.fixed_wgt,
			    s.code,
			    IFNULL(s.min_chance,0) as min_chance,
			    IFNULL(s.max_chance,0) as max_chance,
			    Format(IFNULL(s.max_weight,0),3) as max_weight, IF(s.max_weight=s.min_weight,'1','0') as wgt_type,
			    Format(IFNULL(s.min_weight,0),3) as min_weight,s.wgt_convert,
			    Date_Format(sa.start_date,'%d-%m-%Y') as start_date,s.flx_denomintion,
			    
			    IF(s.scheme_type=0 OR s.scheme_type=2,TRIM(s.amount),IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,if(flexible_sch_type = 3 ,  s.max_weight,if(s.firstPayamt_as_payamt=1,sa.firstPayment_amt ,TRIM(s.min_amount))),0))) as payable,
				round(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,
				if(s.scheme_type=3 && s.max_amount!='',s.max_amount,0)))) as max_amount,
				 (SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)as metal_rate,
				s.total_installments,IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid,
 				IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as oldpaid_installments,
 cs.branch_settings,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
FORMAT(sum(if(p.gst > 0,if((p.gst_type = 1),0,p.payment_amount-(p.payment_amount*(100/(100+p.gst)))),0)),0) as paid_gst,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)
 as total_paid_weight,
  if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,   (s.total_installments - if(sa.is_opening = 1,(count(DISTINCT((Date_Format(p.date_payment,'%Y%m'))))+sa.paid_installments),count(DISTINCT((Date_Format(p.date_payment,'%Y%m')))))),ifnull(((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))+1) - IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) as totalunpaid,   
   IFNULL(if(Date_Format(max(p.date_payment),'%Y%m') = Date_Format(curdate(),'%Y%m'), 
   SUM(CASE WHEN Date_Format(p.date_payment,'%Y%m') = Date_Format(curdate(),'%Y%m') AND p.payment_status = 1 THEN p.no_of_dues ELSE 0 END),
   IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,  
  SUM(CASE WHEN p.due_type='AD' AND (p.payment_status=1 OR p.payment_status=2) THEN p.no_of_dues ELSE 0 END) as currentmonth_adv_paycount,
  SUM(CASE WHEN p.due_type='PD' AND (p.payment_status=1 OR p.payment_status=2) THEN p.no_of_dues ELSE 0 END) as currentmonth_pend_paycount,
IF(s.scheme_type =1 and s.max_weight!=s.min_weight,true,false) as is_flexible_wgt,  
			    round(IFNULL(cp.total_amount,0)) as  current_total_amount,
			    Format(IFNULL(cp.total_weight,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_weight),0) ,3) as  current_total_weight,
			    IFNULL(cp.paid_installment,0)       as  current_paid_installments,
			   			    IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0) as  current_chances_used,
							if(s.scheme_type=3 && s.pay_duration=0 ,IFNULL(sp.chance,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0),IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0)) as  current_chances_pay,
			    s.is_pan_required,
			    IFNULL(Date_Format(max(p.date_payment),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0))                 as last_paid_date,
					IFNULL(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(max(p.date_add),'%Y%m')),IF(sa.is_opening=1,PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(sa.last_paid_date,'%Y%m')),0)) as last_paid_duration,
			    month(max(p.date_payment)) as last_paid_month,
				IF(sa.is_opening = 1 and s.scheme_type = 0 || s.scheme_type = 2,
				IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),false,true),
				true) AS previous_amount_eligible,
				count(pp.id_scheme_account) as cur_month_pdc,
				s.allow_unpaid,ifnull(s.auto_debit_plan_type,0) as auto_debit_plan_type,
				if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_months,
				s.allow_advance,
				if(s.allow_advance=1,s.advance_months,0) as advance_months,
				if(s.allow_preclose=1,preclose_months,0) as allow_preclose_months,
				sa.disable_payment,s.charge,s.charge_type,s.charge_head,
				IFNULL(sa.auto_debit_status, 0) as auto_debit_status,ad.auth_link,
				cs.currency_name,
				cs.currency_symbol,s.firstPayamt_maxpayable,s.get_amt_in_schjoin,s.id_metal,s.id_purity,s.max_chance,cs.curr_symb_html,cls.classification_name,s.scheme_name,
				if(s.display_payable = 0, 'Days', if(s.display_payable = 1 , 'Weeks', if(s.display_payable = 2 , 'Months',if(s.display_payable = 3 , 'Month','-' ) ))) as ins_word,
		 if(s.display_payable=0, 'Payable (In days)', if(s.display_payable=1,'Payable (In weeks)', if(s.display_payable = 2 , 'Payable (Monthly)',if(s.display_payable = 3 , 'Payable (One time)','-' )))) as payable_word,
		 ifnull(sa.total_paid_ins,0) as paid_installments,s.allow_advance_in,s.allow_unpaid_in,
			DATE_FORMAT(
                        IF(
                            s.installment_cycle = 0,  
                            LAST_DAY(DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) MONTH)),
                            IF(
                                s.installment_cycle = 1,  
                                DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) DAY),
                                IF(
                                    s.installment_cycle = 2,  
                                    DATE_ADD(
                                        sa.start_date,
                                        INTERVAL ((s.ins_days_duration * s.total_installments) - 1) DAY
                                    ),
                                    NULL
                                )
                            )
                        ),
                        '%Y-%m-%d'
                    ) AS due_cycle_end, s.closing_maturity_days
			From scheme_account sa
			Left Join scheme s On (sa.id_scheme=s.id_scheme)
			left join sch_classify cls on (cls.id_classification = s.id_classification)
			Left Join branch br  On (br.id_branch=sa.id_branch)
			Left Join scheme_group sg On (sa.group_code = sg.group_code )
			Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=1 or p.payment_status=2 or p.payment_status=8) {$where})
			Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
			Left Join
			(	Select
				  sa.id_scheme_account,
				  COUNT(Date_Format(p.date_add,'%Y%m')) as paid_installment,
				  COUNT(Date_Format(p.date_add,'%Y%m')) as chances,
				  SUM(p.payment_amount) as total_amount,
				  SUM(p.metal_weight) as total_weight
				From payment p
				Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0 )
				Where (p.payment_status=1 or p.payment_status=2 ) and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_add,'%Y%m') {$where}
				Group By sa.id_scheme_account
			) cp On (sa.id_scheme_account=cp.id_scheme_account)	
			left join(Select sa.id_scheme_account, COUNT(Distinct Date_Format(p.date_add,'%d%m')) as paid_installment,
					COUNT(Date_Format(p.date_add,'%d%m')) as chance,
					SUM(p.payment_amount) as total_amount,
					SUM(p.metal_weight) as total_weight
					From payment p
					Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
					Where  (p.payment_status=2 or p.payment_status=1) and  Date_Format(Current_Date(),'%d%m')=Date_Format(p.date_add,'%d%m') {$where}
					Group By sa.id_scheme_account)sp on(sa.id_scheme_account=sp.id_scheme_account)
			 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))
			 LEFT JOIN auto_debit_subscription ad on ad.id_scheme_account = sa.id_scheme_account and ad.status=1
				JOIN chit_settings cs 
		Where   sa.active=1 and sa.is_closed = 0  and s.is_digi = 0 and c.id_customer='$id_customer' and s.is_enquiry=0
		" . (!empty($id_sch_acc_string) ? "and sa.id_scheme_account in ({$id_sch_acc_string})" : '') . "
			Group By sa.id_scheme_account order by sa.id_scheme_account desc";
        // 			echo $sql;exit;
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            //customer scheme account overall amount
            $overall_amt = 0;
            foreach ($records->result() as $record) {
                $overall_amt += $record->current_total_amount;
                $current_installments = ($record->current_paid_installments == 0 ? $record->paid_installments + 1 : $record->paid_installments);
                if ($record->set_as_min_from > 0 && $record->set_as_max_from > 0) {
                    if ($record->paid_installments > 1 && $record->min_amount != 0) {
                        if ($record->current_total_amount < $record->min_amount) {
                            $current_amt_min = 'Y';
                        } else {
                            $current_amt_min = 'N';
                        }
                    }
                    if ($current_installments >= $record->set_as_min_from && $record->get_amt_in_schjoin == 0) {
                        if ($record->firstPayment_flexible == 0) {
                            if ($record->paid_installments == 0) {
                                $record->min_amount = $record->min_amount;
                                $record->max_amount = $record->min_amount;
                            } else if ($record->paid_installments > 1 && $record->paid_installments <= $record->set_as_max_from) {
                                $record->min_amount = $record->min_amount;
                            }
                        } else if ($record->firstPayment_flexible == 1) {
                            if ($record->paid_installments == 0) {
                                $record->min_amount = $record->min_amount;
                                $record->max_amount = $record->max_amount;
                            } else if ($record->currentmonthpaycount == 0 && $current_installments >= $record->paid_installments && $record->paid_installments <= $record->set_as_max_from) {
                                $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
    			                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
    			                 where p.id_scheme_account = '" . $record->id_scheme_account . "' order by id_payment asc limit 1");
                                $payamount = $res->row_array();
                                $record->min_amount = $payamount['payment_amount'];
                            }
                        }
                    }
                    if ($current_installments >= $record->set_as_max_from) {
                        $record->min_amount = $record->min_amount;
                        $record->max_amount = $record->min_amount;
                    }
                    if ($record->dues_count > 0 && $current_installments + 1 >= $record->set_as_min_from && $current_installments + 1 <= $record->set_as_max_from) {
                        $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
    			                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
    			                 where p.payment_status=1 and p.id_scheme_account = '" . $record->id_scheme_account . "' order by id_payment asc limit 1");
                        $payamount = $res->row_array();
                        if ($payamount['payment_amount'] > 0) {
                            $record->min_amount = $record->dues_count * $payamount['payment_amount'];
                            $record->max_amount = $record->dues_count * $payamount['payment_amount'];
                        } else {
                            $record->min_amount = $record->min_amount;
                            $record->max_amount = $record->max_amount;
                        }
                    }
                }
                // Calculate max payable [Applicable only for No advance, No pending enabled schemes]
                if ((($record->scheme_type == 1 && $record->is_flexible_wgt == 1) || $record->scheme_type == 3) && $record->avg_calc_ins > 0) {
                    $current_installments = ($record->current_paid_installments == 0 ? $record->paid_installments + 1 : $record->paid_installments);
                    // Previous Ins == Average calc installment
                    if ($record->avg_calc_by == 0) {
                        if (($current_installments > $record->avg_calc_ins || $record->avg_payable > 0) && $record->avg_calc_ins > 0) {
                            //30-05-2023 #AB : avg payable not storing if already in 0.000
                            if ($record->avg_payable > 0 || ($record->avg_payable !== NULL && $record->avg_payable !== '0')) { // Already Average calculated, just set the value
                                if ($record->scheme_type == 1 && $record->is_flexible_wgt == 1) { // Weight - Flexible weight scheme
                                    // Set max payable
                                } else if ($record->scheme_type == 3) {
                                    if ($record->flexible_sch_type == 2) { // Flexible - Amount to weight [amount based]
                                        // Set max payable
                                        $record->max_amount = number_format($record->avg_payable, 2, '.', '');
                                        ;
                                        //echo $record->max_amount;exit;
                                        $record->payable = number_format($record->avg_payable, 2, '.', '');
                                        ;
                                    } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                        $record->max_weight = $record->avg_payable;
                                    }
                                }
                            } else { // Calculate Average , set the value and updte it in schemme_account table
                                $paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE payment_status=1 and id_scheme_account=" . $record->id_scheme_account . " GROUP BY YEAR(date_payment), MONTH(date_payment)");
                                $paid_wgt = 0;
                                $paid_amt = 0;
                                $paid = $paid_sql->result_array();
                                foreach ($paid as $p) {
                                    $paid_wgt = $paid_wgt + $p['paid_wgt'];
                                    $paid_amt = $paid_amt + $p['paid_amt'];
                                }
                                if ($record->scheme_type == 1 && $record->is_flexible_wgt == 1) { // Weight - Flexible weight scheme
                                    // Set max payable
                                } else if ($record->scheme_type == 3) {
                                    if ($record->flexible_sch_type == 2) { // Flexible - Amount to weight [amount based]
                                        // Set max payable
                                        $avg_payable = $paid_amt / $record->avg_calc_ins;
                                        $record->max_amount = number_format($avg_payable, 2, '.', '');
                                        ;
                                    } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                        $avg_payable = number_format($paid_wgt / $record->avg_calc_ins, 3);
                                        $record->max_weight = $avg_payable;
                                    }
                                }
                                $updData = array("avg_payable" => $avg_payable, "date_upd" => date("Y-m-d"));
                                $this->db->where('id_scheme_account', $record->id_scheme_account);
                                $this->db->update("scheme_account", $updData);
                                //	print_r($this->db->last_query());exit;
                            }
                        }
                    } else if ($record->avg_calc_by == 1 && $record->avg_calc_ins > 0) {
                        //calculate average by scheme joining date
                        $d1 = $record->start_date;
                        $d2 = date("d-m-Y");
                        $no_of_months = (int) abs((strtotime($d1) - strtotime($d2)) / (60 * 60 * 24 * 30));
                        $join_date = date('Y-m-d', strtotime($record->start_date));
                        $endDate = date('Y-m-d', strtotime("+" . $record->avg_calc_ins . " months", strtotime($join_date)));
                        //echo $no_of_months; exit;
                        if ($no_of_months >= $record->avg_calc_ins) {
                            if ($record->avg_payable > 0) { // Already Average calculated, just set the value
                                if ($record->scheme_type == 3) {
                                    if ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 1) { // Flexible - Amount to weight [amount based]
                                        // Set max payable
                                        $record->max_amount = number_format($record->avg_payable, 2, '.', '');
                                        ;
                                        //$record->min_amount =number_format($record->avg_payable, 2, '.', '');;
                                        $record->payable = number_format($record->avg_payable, 2, '.', '');
                                        ;
                                    } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                        $record->max_weight = $record->avg_payable;
                                    }
                                }
                            } else { // Calculate Average , set the value and updte it in schemme_account table
                                $paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE payment_status=1 and id_scheme_account=" . $record->id_scheme_account . " and date(date_payment) BETWEEN '" . $join_date . "' and '" . $endDate . "' GROUP BY YEAR(date_payment), MONTH(date_payment)");
                                $paid_wgt = 0;
                                $paid_amt = 0;
                                $paid = $paid_sql->result_array();
                                foreach ($paid as $p) {
                                    $paid_wgt = $paid_wgt + $p['paid_wgt'];
                                    $paid_amt = $paid_amt + $p['paid_amt'];
                                }
                                if ($record->scheme_type == 1 && $record->is_flexible_wgt == 1) { // Weight - Flexible weight scheme
                                    // Set max payable
                                } else if ($record->scheme_type == 3) {
                                    if ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 1) { // Flexible - Amount to weight [amount based]
                                        // Set max payable
                                        $avg = $paid_amt / $record->avg_calc_ins;
                                        //if average amount is less than 1st ins amt set 1st ins amount
                                        $sql = $this->db->query("SELECT payment_amount from payment where payment_status=1 and id_scheme_account=" . $record->id_scheme_account . " order by id_payment ASC limit 1");
                                        $payamt = $sql->row_array();
                                        if ($avg >= $payamt['payment_amount']) {
                                            $avg_payable = $avg;
                                        } else {
                                            $avg_payable = $payamt['payment_amount'];
                                        }
                                        $record->max_amount = $avg_payable;
                                    } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                        $avg_payable = number_format($paid_wgt / $record->avg_calc_ins, 3);
                                        $record->max_weight = $avg_payable;
                                    }
                                }
                                $updData = array("avg_payable" => $avg_payable, "date_upd" => date("Y-m-d"));
                                $this->db->where('id_scheme_account', $record->id_scheme_account);
                                $this->db->update("scheme_account", $updData);
                            }
                        }
                    }
                }
                $allowed_due = 0;
                $due_type = '';
                $checkDues = TRUE;
                $allowSecondPay = FALSE;
                //$metal_rates=$this->get_metalrate($record->sch_join_branch,$record->is_branchwise_rate);//For branchwise rate
                /*	if($record->rate_select == 0 || $record->rate_select == 1)
                    {
                        $metal_rates=$this->get_metalrate($record->sch_join_branch,$record->is_branchwise_rate);//For branchwise rate
                    }
                    else if($record->rate_select == 2 || $record->rate_select == 1)
                    {
                        $metal_rates = $this->get_ratesByJoin($record->join_date);
                    } */
                if ($record->has_lucky_draw == 1) {
                    if ($record->group_start_date == NULL && $record->paid_installments > 1) { // block 2nd payment if scheme_group_code is not updated 
                        $checkDues = FALSE;
                    } else if ($record->group_start_date != NULL) { // block before start date and payment after end date 
                        if ($record->group_end_date >= time() && $record->group_start_date <= time()) {
                            $checkDues = TRUE;
                        } else {
                            $checkDues = FALSE;
                        }
                    }
                }
                if ($record->maturity_days != null && $record->maturity_date != '') {
                    $current_date = date("Y-m-d");
                    $maturity_date = $record->maturity_date;
                    if (strtotime($current_date) <= strtotime($maturity_date)) {
                        $checkDues = TRUE;
                        if (($record->missed_ins + $record->paid_installments) <= $record->total_installments) {
                            $checkDues = TRUE;
                        } else {
                            $checkDues = FALSE;
                        }
                    } else {
                        $checkDues = FALSE;
                    }
                } else {
                    // print_r(1);exit;
                    // maturity date esakki 13-12
                    // $maturity_date = date('d-m-Y', strtotime("+" . $record->total_installments . " months", strtotime($record->start_date)));
                    $maturitydate = date('d-m-Y', strtotime("+" . $record->maturity_days . " days", strtotime($record->start_date)));
                }
                // print_r($record->maturity_date);exit;    
                if ($checkDues) {
                    if ($record->paid_installments > 0 || $record->totalunpaid > 0) {
                        if ($record->currentmonthpaycount == 0) {  // current month not paid (allowed pending due + current due)
                            if ($record->allow_unpaid == 1) {
                                if ($record->allow_unpaid_months > 0 && ($record->total_installments - $record->paid_installments) >= $record->allow_unpaid_months && $record->totalunpaid > 0) {
                                    if (($record->total_installments - $record->paid_installments) == $record->allow_unpaid_months) {
                                        $allowed_due = $record->allow_unpaid_months;
                                        $due_type = 'PD'; //  pending
                                    } else {
                                        $allowed_due = $record->allow_unpaid_months + 1;
                                        $due_type = 'PN'; // normal and pending
                                    }
                                } else {
                                    $allowed_due = 1;
                                    $due_type = 'ND'; // normal due
                                }
                            } else {
                                // current month not paid (allowed advance due + current due)
                                if ($record->allow_advance == 1) { // check allow advance
                                    if ($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)) {
                                        if (($record->total_installments - $record->paid_installments) == $record->advance_months) {
                                            $allowed_due = $record->advance_months;
                                            $due_type = 'AN'; // advance and normal
                                        } else {
                                            $allowed_due = $record->advance_months + 1;
                                            $due_type = 'AN'; // advance and normal
                                        }
                                    } else {
                                        $allowed_due = 1;
                                        $due_type = 'ND'; // normal due
                                    }
                                } else {
                                    $allowed_due = 1;
                                    $due_type = 'ND'; // normal due
                                }
                            }
                        } else {     //current month paid
                            if ($record->free_payment == 1 && $record->allowSecondPay == 1 && $record->paid_installments == 1) {
                                $allowed_due = 1;
                                $due_type = 'AD'; // adv due
                                $allowSecondPay = TRUE;
                            } else {
                                if ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months) {
                                    // can pay previous pending dues if attempts available 
                                    if ($record->totalunpaid > $record->allow_unpaid_months) {
                                        $allowed_due = $record->allow_unpaid_months;
                                        $due_type = 'PD'; // pending due
                                    } else {
                                        $allowed_due = $record->totalunpaid;
                                        $due_type = 'PD'; // pending due
                                    }
                                } else {  // check allow advance
                                    if ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonth_adv_paycount) < $record->advance_months) {
                                        if (($record->advance_months + $record->paid_installments) <= $record->total_installments) {
                                            $allowed_due = ($record->advance_months - ($record->currentmonth_adv_paycount));
                                            $due_type = 'AD'; // advance due
                                        } else {
                                            $allowed_due = ($record->total_installments - $record->paid_installments);
                                            $due_type = 'AD'; // advance due
                                        }
                                    } else { // have to check
                                        $allowed_due = 0;
                                        $due_type = ''; // normal due
                                    }
                                }
                            }
                        }
                    } else {  // check allow advance and add due with currect month (allowed advance due + current due)
                        if ($record->allow_advance == 1) { // check allow advance
                            if ($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)) {
                                if (($record->total_installments - $record->paid_installments) == $record->advance_months) {
                                    $allowed_due = $record->advance_months;
                                    $due_type = 'AN'; // advance and normal
                                } else {
                                    $allowed_due = $record->advance_months + 1;
                                    $due_type = 'AN'; // advance and normal
                                }
                            } else {
                                $allowed_due = 1;
                                $due_type = 'ND'; // normal due
                            }
                        } else {
                            $allowed_due = 1;
                            $due_type = 'ND'; // normal due
                        }
                    }
                }
                /*   if($this->config->item('defaulter_payment')==1)
                   {
                       $allowed_due=$record->total_installments-$record->current_pay_ins;
                       if($record->current_paid_installments==0 && $allowed_due>1)
                       {
                           $due_type='AN';
                       }
                       else if($record->current_paid_installments>0  && $allowed_due>1)
                       {
                            $due_type='AD';
                       }
                   }*/
                if (!empty($record->maturity_days) && $record->allow_unpaid == 0) // ** Advance Only. No Pending allowed. ** //
                {
                    if ($record->advance_months > 0) {
                        if ($record->current_paid_installments == 0)  // Current month not Paid (Current+Advance)
                        {
                            $allowed_due = $record->total_installments - $record->current_pay_installemnt;
                            $due_type = 'AN';
                        } else // Current month Paid (Advance)
                        {
                            $allowed_due = $record->total_installments - ($record->current_pay_installemnt + $record->current_paid_installments);
                            $due_type = 'AD';
                        }
                    }
                }
                //golden promise scheme    
                if ($record->set_as_min_from > 0 && $record->set_as_max_from > 0 && $record->paid_installments > 0) {
                    if ($record->dues_count > 0 && $record->paid_installments >= $record->set_as_min_from && $record->paid_installments <= $record->set_as_max_from) {
                        $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
    			                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
    			                 where p.payment_status=1 and p.id_scheme_account = '" . $record->id_scheme_account . "' order by id_payment asc limit 1");
                        $payamount = $res->row_array();
                        if ($payamount['payment_amount'] > 0) {
                            //$record->min_amount = $record->dues_count * $payamount['payment_amount'];
                            $record->max_amount = $record->dues_count * $payamount['payment_amount'];
                            $record->min_amount = $payamount['payment_amount'];
                            if ($record->currentmonthpaycount != 0) {
                                if ($record->paid_installments > 0 && $record->currentmonthpaycount == 1) {
                                    $record->current_total_amount = 0;
                                } else {
                                    $month_first_day = date('Y-m-01');
                                    $res1 = $this->db->query("select SUM(p.payment_amount) as payment_amount,sa.id_scheme_account from payment p 
        			                        left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
        			                        where p.payment_status=1 and p.due_type != 'ND' and date(p.date_payment) between '" . $month_first_day . "' and '" . $record->pay_date . "' and p.id_scheme_account = '" . $record->id_scheme_account . "' ");
                                    //echo $this->db->last_query();exit;
                                    $amt = $res1->row_array();
                                    $record->current_total_amount = $amt['payment_amount'];
                                }
                            } else {
                                $record->min_amount = $record->min_amount;
                                $record->max_amount = $record->dues_count * $payamount['payment_amount'];
                                $record->current_total_amount = 0;
                            }
                        } else {
                            $record->min_amount = $record->min_amount;
                            $record->max_amount = $record->max_amount;
                        }
                    }
                }
                //partly flexible scheme - NAC
                if ($record->scheme_type == 3 && ($record->flexible_sch_type == 6 || $record->flexible_sch_type == 6)) {
                    $result = "SELECT sa.id_scheme,sf.ins_from,sf.ins_to,sf.min_value,sf.max_value FROM scheme_flexi_settings sf
			        left join scheme_account sa on sf.id_scheme = sa.id_scheme
				    where sf.id_scheme = sa.id_scheme and sf.id_scheme = " . $record->id_scheme . " and sf.ins_from <= " . $current_installments . "  and  sf.ins_to >= " . $current_installments . " and sa.id_scheme_account = " . $id_scheme_account;
                    //echo $result;exit;
                    $flexi = $this->db->query($result)->row_array();
                    if ($record->flexible_sch_type == 6) {
                        if ($current_installments >= $flexi['ins_from']) {
                            $record->min_amount = $flexi['min_value'];
                            $record->max_amount = $flexi['max_value'];
                        } else if ($current_installments >= $flexi['ins_to']) {
                            $record->min_amount = $flexi['min_value'];
                            $record->max_amount = $flexi['max_value'];
                        }
                    } else if ($record->flexible_sch_type == 7) {
                        if ($current_installments >= $flexi['ins_from']) {
                            $record->min_weight = $flexi['min_value'];
                            $record->max_weight = $flexi['max_value'];
                        } else if ($current_installments >= $flexi['ins_to']) {
                            $record->min_weight = $flexi['min_value'];
                            $record->max_weight = $flexi['max_value'];
                        }
                    }
                }
                // Allow Pay
                // Allow Pay
                if ($record->scheme_type == 3) {
                    if ($record->one_time_premium == 0) {
                        if ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2) {
                            $allow_pay = ($record->disable_payment != 1 && $record->payment_status != 2 && (($record->is_flexible_wgt == 1 && $record->paid_installments <= $record->total_installments) || ($record->is_flexible_wgt == 0 && $record->paid_installments < $record->total_installments)) ? ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2 ? (($record->current_total_amount >= $record->max_amount || $record->current_chances_used >= $record->max_chance) && $record->paid_installments != 0 ? ($record->allow_unpaid == 1 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months ? 'Y' : ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount - 1) < $record->advance_months ? 'Y' : ($record->currentmonthpaycount == 0 ? 'Y' : 'N'))) : 'Y') : 'N') : 'N');
                        } elseif ($record->flexible_sch_type == 3 || $record->flexible_sch_type == 4) {
                            $allow_pay = ($record->disable_payment != 1 && $record->payment_status != 2 && $record->paid_installments <= $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ? ($record->allow_unpaid == 1 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months ? 'Y' : ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount - 1) < $record->advance_months ? 'Y' : ($record->currentmonthpaycount == 0 ? 'Y' : 'N'))) : 'Y') : 'N') : 'N');
                        } else {
                            $allow_pay = ($record->disable_payment != 1 && $record->payment_status != 2 && $record->paid_installments < $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ? ($record->allow_unpaid == 1 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months ? 'Y' : ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount - 1) < $record->advance_months ? 'Y' : ($record->currentmonthpaycount == 0 ? 'Y' : 'N'))) : 'Y') : 'N') : 'N');
                        }
                    } else {
                        $allow_pay = ($record->disable_payment != 1 && $record->payment_status != 2 && $record->paid_installments == 0 && $record->is_enquiry == 0 ? ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 4 || $record->flexible_sch_type == 5 ? ($record->current_total_amount >= $record->max_amount || $record->current_chances_used >= $record->max_chance ? 'N' : 'Y') : ($record->flexible_sch_type == 8 ? ($record->current_total_weight >= $record->max_weight && $record->paid_installments >= $record->total_installments ? 'N' : 'Y') : 'N')) : 'N');
                    }
                } else {
                    $allow_pay = ($record->disable_payment != 1 && ($record->payment_status != 2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments < $record->total_installments ? ($record->is_flexible_wgt ? ($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ? 'N' : 'Y') : ($record->paid_installments < $record->total_installments ? ($record->allow_unpaid == 1 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months ? 'Y' : ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount - 1) < $record->advance_months ? 'Y' : ($record->currentmonthpaycount == 0 ? 'Y' : 'N'))) : 'N')) : 'N') : 'N') : 'N');
                }
                //DGS-DCNM restrict payment by days allow pay settings....	
                if ($record->is_digi == 1) {
                    if ($record->daily_pay_limit != null) {
                        if ($record->curday_total_paid != 0 && $record->curday_total_paid >= $record->daily_pay_limit) {
                            $allow_pay = 'N';
                        } else {
                            $allow_pay = 'Y';
                        }
                    }
                    if ($record->restrict_payment == 1) {
                        if ($record->total_days_to_pay != null && ($record->joined_date_diff >= $record->total_days_to_pay)) {
                            $allow_pay = 'N';
                        } else {
                            $allow_pay = 'Y';
                        }
                    }
                }
                //DGS-DCNM end...
                /* Make allow pay No for payment restriction by hours if online payment is not directly success need to verify in gateway case starts ..... Dt: 7/8/2023,By:Abi */
                if ($record->restrict_lastPayment_days > 0 && $allow_pay == 'Y' && $record->paid_installments == ($record->total_installments - 1)) {
                    $cur_month = $this->db->query("SELECT  MONTH(p.date_payment),YEAR(p.date_payment), p.id_payment,date(p.date_payment) as date_payment,p.payment_status 
                                                    FROM payment p 
                                                    JOIN chit_settings cs
                                                    WHERE YEAR(CURDATE()) = YEAR(p.date_payment) AND MONTH(CURDATE()) = MONTH(p.date_payment)  
                                                    AND (p.payment_status=3 OR p.payment_status=4 OR p.payment_status=7)
                                                    AND TIMESTAMPDIFF(HOUR,p.date_payment,'" . date('Y-m-d H:i:s') . "')  < cs.restrict_lastPayment_days
                                                    AND p.added_by = 2
                                                    AND p.id_scheme_account =" . $record->id_scheme_account);
                    if ($cur_month->num_rows() > 0) {
                        $allow_pay = 'N';
                    } else {
                        $allow_pay = 'Y';
                    }
                }
                //gateway allow pay restriction ends
                //RHR schemes ends 	starts	
                // if ($record->installment_cycle == 2 || $record->installment_cycle == 1 || $record->installment_cycle == 0) {
                if ($record->installment_cycle == 3 || $record->installment_cycle == 2 || $record->installment_cycle == 1 || $record->installment_cycle == 0) {
                    //RHR schemes :starts 20-09-2023 #ABI...
                    /*
                    1. ALLOW PAY : (Y/N)
                    2. ALLOWED DUES : no of dues count can pay
                    3. DUE TYPE	:
                        allow pay for days duration wise instalment cycle starts...
                        ND- only normal due , allowed_due : 1
                        AD - only advance due, allowed_due : 1
                        PD - only pending due, allowed_due : 1
                        AN - advance + normal, allowed_due : no.of.adv + 1
                        PN - pending + normal, allowed_due : no.of.pend + 1
                        APN - Advance + pending + normal, allowed_due : no.of.adv + no.of.pend + 1
                        RHR schemes : Installment cycle type 2 (by days duration) by 30 days payment cycle with advance , pending flow.. 20-09-2023 #ABI...
                        RHR schemes : Installment cycle type 1 (daily)  daily payment scheme with advance and pending...updatedOn:12-10-2023, By:AB
                        worked for only single payment chance type till 12-10-2023
                        need to work for multiple payment chances type further
                    */
                    //due_type , allowed_dues
                    $allowed_due = 0;
                    $date = date('Y-m-d');
                    $paid_normal_due = 0;
                    $paid_advance_due = 0;
                    $paid_pending_due = 0;
                    $paid_due = 0;
                    $remaining_normal_due = 0;
                    $remaining_advance_due = 0;
                    $remaining_pending_due = 0;
                    $remaining_due = 0;
                    $paid_multiple_chance = 0;
                    $chances_allowed_due = 0;
                    $max_chance = $record->max_chance;
                    //get range for current date....
                    $range = $this->get_due_date('current_range', $date, $record->id_scheme_account);
                    //take the no of paid dues with due_type customer paid already...
                    /*$paid_dueData = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count 
                                            FROM payment where payment_status = 1 and id_scheme_account = " . $record->id_scheme_account . " 
                                            and date(date_payment) BETWEEN '" . $range[0]['due_date_from'] . "' AND '" . $range[0]['due_date_to'] . "'
                                            group by due_type")->result_array();
                // print_r($paid_dueData);exit;
                foreach ($paid_dueData as $due) {
                    if ($due['due_name'] == 'ND') {
                        $paid_normal_due = $due['dues_count'];
                    } else if ($due['due_name'] == 'AD') {
                        $paid_advance_due = $due['dues_count'];
                    } else if ($due['due_name'] == 'PD') {
                        $paid_pending_due = $due['dues_count'];
                    } else {
                        $paid_multiple_chance = $due['dues_count'];
                    }
                }*/
                    if (!empty($range)) {
                        //take the no of paid dues with due_type customer paid already...
                        $paid_dueData = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count 
		                                    FROM payment where payment_status = 1 and id_scheme_account = " . $record->id_scheme_account . " 
		                                    and date(date_payment) BETWEEN '" . $range[0]['due_date_from'] . "' AND '" . $range[0]['due_date_to'] . "'
		                                    group by due_type")->result_array();
                        // print_r($paid_dueData);exit;
                        foreach ($paid_dueData as $due) {
                            if ($due['due_name'] == 'ND') {
                                $paid_normal_due = $due['dues_count'];
                            } else if ($due['due_name'] == 'AD') {
                                $paid_advance_due = $due['dues_count'];
                            } else if ($due['due_name'] == 'PD') {
                                $paid_pending_due = $due['dues_count'];
                            } else {
                                $paid_multiple_chance = $due['dues_count'];
                            }
                        }
                    }
                    if ($record->maturity_type != 2 && (date('Y-m-d') > $record->maturity_date || date('Y-m-d') > $record->due_cycle_end)) {
                        $dueCounts = array_count_values(array_column($range, 'due_type'));
                        $paid_normal_due = $dueCounts['ND'] ?? 0;
                        $paid_advance_due = $dueCounts['AD'] ?? 0;
                        $paid_pending_due = $dueCounts['PD'] ?? 0;
                        /* multiple chance = all OTHER due types */
                        $paid_multiple_chance = 0;
                        foreach ($dueCounts as $type => $count) {
                            if (!in_array($type, ['ND', 'PD', 'AD'], true)) {
                                $paid_multiple_chance += $count;
                            }
                        }
                        $remaining_pending_due = $record->total_installments - $record->paid_installments;
                    } else {
                    //take the no of remaining dues with due_type customer want to pay..
                    $remaining_dueData = $this->get_due_date('allow_pay', $date, $record->id_scheme_account);
                    //print_r($this->db->last_query());exit;
                    foreach ($remaining_dueData as $due) {
                        if ($due['due_name'] == 'ND') {
                            $remaining_normal_due = $due['dues_count'];
                        } else if ($due['due_name'] == 'AD') {
                            $remaining_advance_due = $due['dues_count'];
                        } else if ($due['due_name'] == 'PD') {
                            $remaining_pending_due = $due['dues_count'];
                        } else {
                            $remaining_due = $due['dues_count'];
                            }
                        }
                    }
                    //calculate can pay advance due and pending dues...
                    $chances_allowed_due = $max_chance - ($paid_multiple_chance + $paid_normal_due);
                    $allow_advance_in = explode(',', $record->allow_advance_in);
                    $allow_unpaid_in = explode(',', $record->allow_unpaid_in);
                    //var_dump($record->allow_advance_in);exit;
                    //if ($chances_allowed_due <= 1 && (($record->allow_advance == 1 && $record->advance_months > 0 && (in_array('2', $allow_advance_in) || in_array('3', $allow_unpaid_in) || in_array('4', $allow_advance_in)) && $remaining_advance_due > 0) || ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && (in_array('2', $allow_unpaid_in) || in_array('3', $allow_unpaid_in) || in_array('4', $allow_unpaid_in)) && $remaining_pending_due > 0))) {
                    if (
                        ($chances_allowed_due <= 1 &&
                        (
                            ($record->allow_advance == 1 && $record->advance_months > 0 && (in_array('1', $allow_advance_in) || in_array('4', $allow_advance_in)) && $remaining_advance_due > 0)
                            ||
                            ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0
                                && (in_array('1', $allow_unpaid_in) || in_array('4', $allow_unpaid_in))
                                && $remaining_pending_due > 0
                            )
                            )) ||
                            (
                            $remaining_pending_due > 0 && $record->maturity_type != 2 && (date('Y-m-d') > $record->maturity_date || date('Y-m-d') > $record->due_cycle_end)
                        )
                    ) {
                        //advance..
                        $sch_advance = $record->advance_months;
                        $cur_advance = ($remaining_advance_due > 0 ? ($sch_advance < $remaining_advance_due && $remaining_advance_due > 0 && $paid_advance_due < $sch_advance ? $sch_advance : abs($sch_advance - $paid_advance_due)) : 0);
                        $canPay_advance = ($remaining_advance_due < $cur_advance ? $remaining_advance_due : $cur_advance);
                        //pending
                        if ((in_array('2', $allow_unpaid_in) || in_array('3', $allow_unpaid_in))) {
                            $record->allow_unpaid_months = $record->allow_unpaid_months;
                        } else {
                            $record->allow_unpaid_months = 0;
                        }
                        // $sch_unpaid = ($remaining_pending_due > 0 && $record->maturity_type != 2  && date('Y-m-d') > $record->maturity_date ? ($record->allow_unpaid_months + 1) : $record->allow_unpaid_months); 
                        $sch_unpaid = ($remaining_pending_due > 0 && $record->maturity_type != 2 && (date('Y-m-d') > $record->maturity_date || date('Y-m-d') > $record->due_cycle_end) ? ($record->allow_unpaid_months + 1) : $record->allow_unpaid_months);
                        $cur_unpaid = ($remaining_pending_due > 0 ? abs($sch_unpaid - $paid_pending_due) : 0);
                        $canPay_pending = ($remaining_pending_due > $cur_unpaid ? $cur_unpaid : $remaining_pending_due);
                        //print_r($remaining_normal_due);exit;
                        if ($remaining_normal_due == 0 && $canPay_pending > 0) {            //only pending
                            $due_type = 'PD';
                            $allowed_due = $canPay_pending;
                        } else if ($remaining_normal_due == 0 && $canPay_advance > 0) {        //only advance
                            $due_type = 'AD';
                            $allowed_due = $canPay_advance;
                        } else if ($remaining_normal_due > 0 && $canPay_pending > 0) {        //normal + pending
                            $due_type = 'PN';
                            $allowed_due = $remaining_normal_due + $canPay_pending;
                        } else if ($remaining_normal_due > 0 && $canPay_advance > 0) {        //normal + advance
                            $due_type = 'AN';
                            $allowed_due = $remaining_normal_due + $canPay_advance;
                        }
                    } else {
                        if ($chances_allowed_due != 0 && $paid_normal_due != 0 && $max_chance > 1 && sizeof($range) > 0 && $max_chance > $chances_allowed_due) {
                            $due_type = 'MND';
                            $allowed_due = $chances_allowed_due;
                        } else {
                            $due_type = 'ND';
                            $allowed_due = $remaining_normal_due;
                        }
                    }
                    //allow pay , discuss for APN and multiple payment chances
                    if ($allowed_due > 0 && $record->paid_installments < $record->total_installments && $record->disable_payment != 1) {
                        $allow_pay = 'Y';
                    } else {
                        $allow_pay = 'N';
                    }
                }
                if ($record->maturity_type == 2 && date('Y-m-d') > $record->maturity_date) {
                    $allow_pay = 'N';
                }
                //allow pay for daily scheme....
                /*if($record->installment_cycle == 1 && $record->pay_duration == 0 ){
                    if($record->curday_total_paid_count >= $record->max_chance && ($record->allow_advance == 1 && $record->total_adv_paid >= $record->advance_months) && ($record->allow_unpaid == 1 && $record->total_pend_paid >= $record->allow_unpaid_months)){
                        $allow_pay = 'N';
                        $allowed_dues = 0;
                        $due_type = '';
                    }else{
                        //due type && allowed dues count...
                        if($record->curday_total_paid_count == 0){
                            //normal + pending
                            if($record->allow_unpaid == 1 && $record->total_pend_paid < $record->allow_unpaid_months){
                                $due_type = 'PN';
                                $allowed_dues = ($record->allow_unpaid_months - $record->total_pend_paid) + 1 ;
                            }
                            //normal + advance
                            elseif($record->allow_advance == 1 && $record->total_adv_paid < $record->advance_months){
                                $due_type = 'AN';
                                $allowed_dues = ($record->advance_months - $record->total_adv_paid) + 1 ;
                            }
                            //only normal
                            else{
                                $due_type = 'ND';
                                $allowed_dues = 1;
                            }
                        }else{
                            //only pending
                            if($record->allow_unpaid == 1 && $record->total_pend_paid < $record->allow_unpaid_months){
                                $due_type = 'PD';
                                $allowed_dues = ($record->allow_unpaid_months - $record->total_pend_paid) ;
                            }
                            //only advance
                            elseif($record->allow_advance == 1 && $record->total_adv_paid < $record->advance_months){
                                $due_type = 'AD';
                                $allowed_dues = ($record->advance_months - $record->total_adv_paid) ;
                            }
                        }
                    }
                     if($allowed_due > 0 || $record->total_installments < $record->paid_installments){
                        $allow_pay = 'Y';
                    }else{
                        $allow_pay = 'N';
                    }
                }  */
                //RHR schemes ends 
                //allow pay for daily scheme....
                /*
                if($record->pay_duration == 0){
                    if($record->maturity_type == 1 && $record->paid_installments < $record->total_installments){
                        $allow_pay = 'Y';
                    }else{
                        if($record->curday_total_paid_count < $record->max_chance && date('Y-m-d') < $record->daily_sch_allow_pay_till  ){
                            $allow_pay = 'Y';
                        }else{
                            $allow_pay = 'N';
                        }
                    }
                }*/
                //echo '<pre>';print_r($record);exit;
                //allow pay daily scheme ends....  
                //For RHR Metal Purity wise rate	
                if ($record->rate_select == 0 || $record->rate_select == 1) {
                    $metal_rate = $this->get_metalrate_by_branch($record->id_branch, $record->id_metal, $record->id_purity, ''); //For branchwise rate
                } else if ($record->rate_select == 2 || $record->rate_select == 1) {
                    $metal_rate = $this->get_metalrate_by_branch($record->id_branch, $record->id_metal, $record->id_purity, $record->join_date); //For branchwise rate
                }
                //	print_r($record);exit;
                $elig_wgt = ($record->flexible_sch_type == 3 || $record->scheme_type == 1 ? ($record->min_weight == $record->max_weight ? $record->min_weight : ($record->max_weight - $record->current_total_weight)) : ($record->firstPayment_as_wgt == 1 && $record->paid_installments == 0 ? ($record->max_weight - $record->current_total_weight) : (($record->max_amount / $metal_rate) - $record->current_total_weight)));
                if ($record->get_amt_in_schjoin == 1 && ($record->firstPayamt_maxpayable == 1 || $record->firstPayamt_as_payamt == 1) && $record->firstPayment_amt > 0) {
                    $eligible_wgt = ($record->firstPayment_amt / $metal_rate);
                } else {
                    if ($record->scheme_type == 1 || $record->scheme_type == 3 && ($record->flexible_sch_type == 3 || $record->flexible_sch_type == 4 || $record->flexible_sch_type == 8)) {   //normal weight scheme,
                        $eligible_wgt = ($record->max_weight - $record->current_total_weight);
                    } elseif ($record->scheme_type == 2) {   //normal amt to wgt
                        $eligible_wgt = ($record->amount / $metal_rate);
                    } elseif ($record->flexible_sch_type == 5 || $record->flexible_sch_type == 2) {
                        $eligible_wgt = (($record->max_amount / $metal_rate) - $record->current_total_weight);
                    } else {
                        $eligible_wgt = $elig_wgt;
                    }
                }
                if ($record->scheme_type == 3 && $record->set_as_min_from > 0 && $record->set_as_max_from > 0 && $record->firstPayamt_as_payamt == 0) {
                    if ($current_installments <= $record->set_as_max_from) {
                        $show_payable = 'N';
                    }
                } else {
                    $show_payable = 'Y';
                }
                $dates = date('d-m-Y');
                //chit number and receipt number based on display format settings starts...
                $accNumData = array(
                    'is_lucky_draw' => $record->is_lucky_draw,
                    'scheme_acc_number' => $record->scheme_acc_number,
                    'scheme_group_code' => $record->scheme_group_code,
                    'schemeaccNo_displayFrmt' => $record->schemeaccNo_displayFrmt,
                    'scheme_wise_acc_no' => $record->scheme_wise_acc_no,
                    'acc_branch' => $record->acc_branch,
                    'code' => $record->code,
                    'start_year' => $record->start_year,
                    'id_scheme_account' => $record->id_scheme_account
                );
                //ends
                //allow pay for general advance settings if enabled //TKV 
                $payable = (int) (($record->scheme_type == 3 && $record->max_amount != 0 && ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2 || $record->flexible_sch_type == 5) && $record->max_amount != '' ? ((($record->firstPayamt_maxpayable == 1 || $record->firstPayamt_as_payamt == 1) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) || ($record->is_registered == 1)) ? round($record->firstPayment_amt) : round($record->min_amount)) : ($record->scheme_type == 3 && ($record->max_weight != 0 || $record->max_weight != '') ? ($record->firstPayment_as_wgt == 1 && $record->paid_installments > 0 && $record->firstpayment_wgt != null ? round($record->firstpayment_wgt * $metal_rate, 3) : ($record->firstPayment_as_wgt == 1 && $record->paid_installments == 0 ? round($record->min_weight * $metal_rate, 3) : round(($record->max_weight - $record->current_total_weight) * $metal_rate))) : $record->payable)));
                $eligible_wgt = ($record->flexible_sch_type == 3 || $record->scheme_type == 1 ? ($record->min_weight == $record->max_weight ? $record->min_weight : ($record->max_weight - $record->current_total_weight)) : ($record->firstPayment_as_wgt == 1 && $record->paid_installments == 0 ? ($record->max_weight - $record->current_total_weight) : $payable / $metal_rate));
                $d1 = $record->start_date;
                $d2 = date("d-m-Y");
                $no_of_months = (int) abs((strtotime($d1) - strtotime($d2)) / (60 * 60 * 24 * 30));
                if ($record->avg_payable > 0 && $record->avg_calc_ins > 0 && (($record->avg_calc_by == 1 && $no_of_months >= $record->avg_calc_ins) || ($record->avg_calc_by == 0 && $record->paid_installments >= $record->avg_calc_ins))) {
                    // $min_amount = $record->avg_payable;
                    $max_amount = number_format($record->avg_payable, 2, '.', '');
                    ;
                } else {
                    $max_amount = round(($record->scheme_type == 3 && $record->max_amount != 0 && $record->max_amount != '' ? (($record->firstPayamt_maxpayable == 1 || $record->firstPayamt_as_payamt == 1) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) ? $record->firstPayment_amt : ($record->max_amount - str_replace(',', '', $record->current_total_amount))) : ($record->scheme_type == 3 && $record->max_weight != 0 && $record->max_weight != '' ? (($record->max_weight - $record->current_total_weight) * $metal_rate) : $record->max_amount)));
                }
                /*  $min_amount = round(($record->scheme_type==3 && $record->min_amount!=0 && $record->min_amount!='' ? ((($record->firstPayamt_maxpayable==1 ||$record->firstPayamt_as_payamt==1) && $record->firstPayment_amt != NULL)&&($record->paid_installments>0 || $record->get_amt_in_schjoin==1) ? $record->firstPayment_amt:$record->min_amount):
                     ($record->scheme_type==3 && $record->min_weight!=0 && $record->min_weight!=''? (($record->min_weight)*$metal_rate) : $record->min_amount)));
                 */
                if ($record->scheme_type == 0 || $record->scheme_type == 1) {
                    $min_amount = $record->payable;
                } else {
                    $min_amount = round(($record->scheme_type == 3 && $record->min_amount != 0 && $record->min_amount != '' ? ((($record->firstPayamt_maxpayable == 1 || $record->firstPayamt_as_payamt == 1) && $record->firstPayment_amt != NULL) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) ? $record->firstPayment_amt : $record->min_amount) : ($record->scheme_type == 3 && $record->min_weight != 0 && $record->min_weight != '' ? (($record->min_weight) * $metal_rate) : $record->min_amount)));
                }
                $flx_denomintion = $record->flx_denomintion;
                $due_type = ($record->is_flexible_wgt == 1 ? 'ND' : $due_type);
                $allowed_due = ($record->is_flexible_wgt == 1 ? 1 : $allowed_due);
                if ($allow_pay == 'N' && $record->allow_general_advance == 1 && date('Y-m-d') < $record->daily_sch_allow_pay_till && (($record->installment_cycle == 0 && $record->totalunpaid == 0) || ($record->installment_cycle == 2 && $remaining_pending_due == 0)) && $record->disable_payment != 1) {
                    $genAdv_allow_pay = 'Y';
                    $allow_pay = 'Y';
                    $due_type = 'GA';
                    $allowed_due = 1;
                    $min_amount = round($record->adv_min_amt);
                    $max_amount = round($record->adv_max_amt);
                    $flx_denomintion = $record->adv_denomination;
                    $payable = round($record->adv_min_amt);
                } else {
                    $genAdv_allow_pay = 'N';
                }
                //TKV gen advance ends...
                //lump scheme starts...
                if ($record->is_lumpSum == 1 && $record->lump_joined_weight > 0 && $record->lump_payable_weight > 0 && $record->flexible_sch_type == 4 && $record->firstPayment_as_wgt == 1) {
                    $min_amount = number_format((float) round($record->lump_payable_weight * $metal_rate), 2, '.', '');
                    $max_amount = number_format((float) round($record->lump_payable_weight * $metal_rate), 2, '.', '');
                    $payable = number_format((float) round($record->lump_payable_weight * $metal_rate), 2, '.', '');
                    $record->min_weight = $record->lump_payable_weight;
                    $record->max_weight = $record->lump_payable_weight;
                    $eligible_wgt = $record->lump_payable_weight;
                }
                //lump scheme ends....
                //allow pay for advance settings...
                //allow pay for advance & pending settings...
                /*       $allowed_due = ($record->is_flexible_wgt == 1 ? 1:$allowed_due);
                       if($record->allow_advance == 1 && $record->advance_months > 0 &&  $allowed_due != 0 && ($allowed_due <= $record->advance_months) && $record->paid_installments < $record->total_installments)
                       {
                               $allow_advancePay = 'Y';
                       }
                       if($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 &&  $allowed_due != 0 && ($allowed_due <= $record->allow_unpaid_months) && $record->paid_installments < $record->total_installments)
                       {
                               $allow_pendingPay = 'Y';
                       }
                       if($allow_advancePay == 'Y' || $allow_pendingPay == 'Y'){
                           $allow_pay = 'Y';
                       }
                       if($record->allow_advance == 1 && $record->advance_months > 0  && ($due_type=='AN' or 'AD') && $record->paid_installments < $record-> total_installments ){
                                       // For daily payment 
                                       if($record->pay_duration==0 &&  $record->curday_advance_count < $record->advance_months){
                                               $allow_pay = 'Y';
                                       }else if($record->pay_duration==1 &&  $record->curmonth_advance_count < $record->advance_months){ // For monthy payment 
                                               $allow_pay = 'Y';
                                       }else{
                                           $allow_pay = 'N';
                                       }
                        }
                   //adv/pending allow_pay ends....
                   */
                if (!empty($record->maturity_date)) {
                    $maturity_date = $record->maturity_date;
                } else {
                    if ($record->maturity_type == 2) {
                        $maturity_date = date('d-m-Y', strtotime("+" . ($record->maturity_days + $record->closing_maturity_days) . " days", strtotime($record->start_date)));
                    } else {
                        $maturity_date = date('d-m-Y', strtotime("+" . $record->closing_maturity_days . " days", strtotime($record->start_date)));
                    }
                }
                //from commonapi model starts...
                // Check Discount
                $discountData = array(
                    "apply_disc" => 0,
                    "disc_by" => 0,
                    "discount_val" => 0
                );
                if ($record->discount == 1) {
                    $apply_disc = 0;
                    if ($record->discount_type == 0) { // Give discount for all installments
                        $apply_disc = 1;
                    } else {
                        if ($current_installments >= $record->disc_ins_from && $current_installments <= $record->disc_ins_from) { // Specific ins
                            $apply_disc = 1;
                        }
                    }
                    if ($apply_disc == 1) {
                        $discData = array(
                            "disc_days" => $record->disc_days,
                            "start_date" => $record->start_date,
                            "total_installment" => $record->total_installments,
                            "paid_ins" => $record->paid_installments,
                            "current_installment" => $current_installments
                        );
                        $allowDisc = $this->commonapi_model->checkAllowDisc($discData);
                        if ($allowDisc) {
                            $apply_disc = 1;
                        } else {
                            $apply_disc = 0;
                        }
                    }
                    $discountData = array(
                        "apply_disc" => $apply_disc,
                        "disc_by" => $record->firstPayDisc_by,
                        "discount_val" => $record->firstPayDisc_value
                    );
                }
                //allow pay , discuss for APN and multiple payment chances
                if ($allowed_due > 0 && $allow_pay == 'Y' && $record->paid_installments < $record->total_installments && $record->disable_payment != 1) {
                    $allow_pay = 'Y';
                } else {
                    $allow_pay = 'N';
                }
                //from commonapi model ends...

                $kyc_settings = $this->db->get('kyc_settings')->row_array();
                if (isset($kyc_settings['kyc_required']) && $kyc_settings['kyc_required'] == 1 &&
                    isset($kyc_settings['kyc_allow_type']) && $kyc_settings['kyc_allow_type'] == 1) {
                    
                    $this->db->where('id_customer', $record->id_customer);
                    $this->db->where_in('status', array(0, 1));
                    $pending_count = $this->db->count_all_results('kyc');
                    
                    if ($pending_count > 0) {
                        $kyc_mode = isset($kyc_settings['kyc_mode']) ? $kyc_settings['kyc_mode'] : 0;
                        $is_any_one_cust = (isset($kyc_settings['kyc_customer_rule_logic']) && $kyc_settings['kyc_customer_rule_logic'] == 1);
                        $is_any_one_sch = (isset($kyc_settings['kyc_scheme_rule_logic']) && $kyc_settings['kyc_scheme_rule_logic'] == 1);
                        $is_any_one = ($kyc_mode == 1) ? $is_any_one_cust : $is_any_one_sch;
                        
                        if ($is_any_one) {
                            $this->db->where('id_customer', $record->id_customer);
                            $this->db->where('status', 2);
                            $verified_count = $this->db->count_all_results('kyc');
                            if ($verified_count == 0) {
                                $allow_pay = 'N';
                            }
                        } else {
                            $allow_pay = 'N';
                        }
                    }
                }

                $schemeAcc[] = array(
                    'allow_general_advance' => $record->allow_general_advance,
                    'genAdv_allow_pay' => $genAdv_allow_pay,
                    'min_amount' => $min_amount,
                    'max_amount' => $max_amount,
                    'min_weight' => formatMetalWeight($record->min_weight),
                    'max_weight' => formatMetalWeight($record->max_weight),
                    'payable' => $payable,
                    'eligible_weight' => abs(formatMetalWeight($eligible_wgt)),
                    'firstPayment_as_wgt' => $record->firstPayment_as_wgt,
                    'is_lumpSum' => $record->is_lumpSum,
                    'discountData' => $discountData,
                    'flx_denomintion' => $flx_denomintion,
                    'due_type' => $due_type,
                    'allowed_dues' => ($record->is_flexible_wgt == 1 ? 1 : $allowed_due),
                    'allow_pay' => $allow_pay,
                    'firstpayment_wgt' => formatMetalWeight($record->firstpayment_wgt),
                    'gst_type' => $record->gst_type,
                    'pay_duration' => $record->pay_duration,
                    'branch_settings' => $record->branch_settings,
                    'min_chance' => $record->min_chance,
                    'max_chance' => $record->max_chance,
                    //'min_amount' 			    =>(($record->scheme_type==3 ) && ($record->firstPayamt_as_payamt==1 && $record->get_amt_in_schjoin==1) ?$record->firstPayment_amt :$record->min_amount),
                    //'min_amount'                => round(($record->scheme_type==3 && $record->min_amount!=0 && $record->min_amount!='' ? (($record->firstPayamt_payable==1 ||$record->firstPayamt_as_payamt==1 )&&($record->paid_installments>0 || $record->get_amt_in_schjoin==1) ? $record->firstPayment_amt:($record->min_amount - str_replace(',', '',$record->current_total_amount))):	($record->scheme_type==3 && $record->min_weight!=0 && $record->min_weight!=''? (($record->max_weight == $record->current_total_weight ? 0 :$record->min_weight)*$metal_rates['goldrate_22ct']) : $record->min_amount))),
                    'firstPayment_amt' => $record->firstPayment_amt,
                    'firstPayamt_payable' => $record->firstPayamt_payable,
                    //'flx_denomintion' 		        => $record->flx_denomintion,
                    'firstPayamt_as_payamt' => $record->firstPayamt_as_payamt,
                    'flexible_sch_type' => $record->flexible_sch_type,
                    'get_amt_in_schjoin' => $record->get_amt_in_schjoin,
                    'one_time_premium' => $record->one_time_premium,
                    'is_enquiry' => $record->is_enquiry,
                    'otp_price_fixing' => $record->otp_price_fixing,
                    'multiply_value' => 500,
                    'fixed_wgt' => formatMetalWeight($record->fixed_wgt),
                    'fixed_rate' => $record->fixed_metal_rate,
                    'maturity_date' => date('d-m-Y', strtotime($maturity_date)),
                    'fixed_metal_rate' => ($record->fixed_rate_on == NULL ? 'NO' : 'YES'),
                    //'max_amount'        => $record->max_amount,//
                    /*'max_amount'                => round(($record->scheme_type==3 && $record->max_amount!=0 && $record->max_amount!=''?($record->max_amount - str_replace(',', '',$record->current_total_amount)):
                    ($record->scheme_type==3 && $record->max_weight!=0 && $record->max_weight!=''? (($record->max_weight - $record->current_total_weight)*$record->metal_rate) : $record->payable))),*/
                    /*	'max_amount'                => round(($record->scheme_type==3 && $record->max_amount!=0 && $record->max_amount!=''? ((($record->firstPayamt_payable==1) && ($record->paid_installments>0))|| ($record->is_registered==1)?$record->firstPayment_amt :($record->max_amount - str_replace(',', '',$record->current_total_amount))):
                        ($record->scheme_type==3 && $record->max_weight!=0 && $record->max_weight!=''? (($record->max_weight - $record->current_total_weight)*$record->metal_rate) : $record->payable))),*/
                    /*'max_amount'             => round(($record->scheme_type==3 && $record->max_amount!=0 && $record->max_amount!='' && ($record->flexible_sch_type==1 ||$record->flexible_sch_type==2)? (($record->firstPayamt_payable==1 ||$record->firstPayamt_as_payamt==1 )&&($record->paid_installments>0 ||$record->get_amt_in_schjoin==1) ? $record->firstPayment_amt:($record->max_amount - str_replace(',', '',$record->current_total_amount))):
                     ($record->scheme_type==3 && $record->max_weight!=0 && $record->max_weight!=''? (($record->max_weight - $record->current_total_weight)*$metal_rates['goldrate_22ct']) : $record->max_amount))),*/
                    /*	'max_amount'             => round(($record->scheme_type==3 && $record->max_amount!=0 && $record->max_amount!='' ? (($record->firstPayamt_maxpayable==1 ||$record->firstPayamt_as_payamt==1 )&&($record->paid_installments>0 ||$record->get_amt_in_schjoin==1) ?  $record->firstPayment_amt:($record->max_amount - str_replace(',', '',$record->current_total_amount))):
                        ($record->scheme_type==3 && $record->max_weight!=0 && $record->max_weight!=''? (($record->max_weight - $record->current_total_weight)*$metal_rates['goldrate_22ct']) : $record->max_amount))),  */
                    'metal_rate' => $metal_rate,
                    'gst' => $record->gst,
                    'paid_gst' => (int) $record->paid_gst,
                    'id_branch' => $record->id_branch,
                    'short_name' => $record->short_name,
                    'branch_name' => $record->branch_name,
                    'currentmonthpaycount' => $record->currentmonthpaycount,
                    'totalunpaid' => $record->totalunpaid,
                    'id_scheme_account' => $record->id_scheme_account,
                    'max_wgt_rate' => ($record->is_flexible_wgt == 1 ? ($record->max_weight - $record->current_total_weight) : $record->max_weight) * $metal_rate,
                    'charge_head' => $record->charge_head,
                    'charge_type' => $record->charge_type,
                    'charge' => $record->charge,
                    'chit_number' => $this->getAccNoFormat($accNumData),
                    'account_name' => $record->account_name,
                    'start_date' => $record->start_date,
                    'mobile' => $record->mobile,
                    'is_flexible_wgt' => $record->is_flexible_wgt,
                    'currency_symbol' => $record->currency_symbol,
                    //'payable' => ($record->scheme_type==3?($record->max_amount-$record->current_total_amount):$record->payable),//
                    /*	'payable' => (($record->scheme_type==3 && $record->max_amount!=0  && $record->max_amount!=''?round($record->max_amount-str_replace(',', '',$record->current_total_amount)):($record->scheme_type==3 && ($record->max_weight!=0 || $record->max_weight!='')? round(($record->max_weight - $record->current_total_weight)*$record->metal_rate) : $record->payable))),*/
                    /*'payable' => (($record->scheme_type==3 && $record->max_amount!=0  && $record->max_amount!=''?((($record->firstPayamt_payable==1)&&($record->paid_installments>0)||($record->is_registered==1))?round($record->firstPayment_amt) :round($record->max_amount-str_replace(',', '',$record->current_total_amount))):($record->scheme_type==3 && ($record->max_weight!=0 || $record->max_weight!='')? round(($record->max_weight - $record->current_total_weight)*$record->metal_rate) : $record->payable))),*/
                    'code' => ($record->has_lucky_draw == 1 ? $record->scheme_group_code : $record->code),
                    'scheme_type' => $record->scheme_type,
                    'total_installments' => $record->total_installments,
                    'paid_installments' => $record->paid_installments,
                    'total_paid_amount' => $record->total_paid_amount,
                    'total_paid_weight' => formatMetalWeight($record->total_paid_weight),
                    'current_total_amount' => $record->current_total_amount,
                    'current_paid_installments' => $record->current_paid_installments,
                    'current_chances_used' => $record->current_chances_used,
                    'current_chances_pay' => $record->current_chances_pay,
                    //'eligible_weight' 		    => abs($record->max_weight - $record->current_total_weight),
                    'allow_unpaid_months' => $record->allow_unpaid_months,
                    'last_paid_duration' => $record->last_paid_duration,
                    'last_paid_date' => $record->last_paid_date,
                    'current_date' => $dates,
                    'last_paid_month' => ($record->last_paid_month != '' || $record->last_paid_month != NULL ? $record->last_paid_month : 0),
                    'is_pan_required' => $record->is_pan_required,
                    'wgt_convert' => $record->wgt_convert,
                    'last_transaction' => $this->getLastTransaction($record->id_scheme_account),
                    'isPaymentExist' => $this->isPaymentExist($record->id_scheme_account),
                    'isPendingStatExist' => $this->isPendingStatExist($record->id_scheme_account),
                    'current_total_weight' => formatMetalWeight($record->current_total_weight),
                    'previous_amount_eligible' => $record->previous_amount_eligible,
                    'cur_month_pdc' => $record->cur_month_pdc,
                    //'allow_pay'  => ($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':'Y'):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N'),
                    'allowPayDisc' => ($record->discount == 1 ? ($record->discount_type == 0 ? 'All' : $record->discount_installment) : 0),
                    'firstPayDisc' => $record->firstPayDisc,
                    'firstPayDisc_by' => $record->firstPayDisc_by,
                    'discount_val' => $record->discount_val,
                    'due_type' => ($record->is_flexible_wgt == 1 ? 'ND' : $due_type),
                    'max_allowed_limit' => ($record->is_flexible_wgt == 1 ? 1 : $allowed_due),
                    'sel_due' => 1,   //default selected due
                    'pdc_payments' => ($record->cur_month_pdc > 0 ? $this->get_postdated_payment($record->id_scheme_account) : NULL),
                    'rate_fix_by' => $record->rate_fix_by,
                    'rate_select' => $record->rate_select,
                    'set_as_min_from' => $record->set_as_min_from,
                    'set_as_max_from' => $record->set_as_max_from,
                    'show_payable' => $show_payable,
                    'id_scheme' => $record->id_scheme,
                    'id_customer' => $record->id_customer,
                    'eligible_amt' => $record->eligible_amt,
                    'daily_pay_limit' => $record->daily_pay_limit,
                    'daily_payLimit_applicable' => $record->daily_payLimit_applicable,
                    'classification_name' => $record->classification_name,
                    'scheme_name' => $record->scheme_name,
                    'ins_word' => $record->ins_word,
                    'payable_word' => $record->payable_word,
                    'show_ins_type'                     => $record->show_ins_type,
                    'id_metal'                     => $record->id_metal,
                    'auto_debit_plan_type'         => $record->auto_debit_plan_type,
                    'auto_debit_status'            => (int) $record->auto_debit_status,
                    'auth_link'                    => ($record->auto_debit_status == 1 && !empty($record->auth_link)) ? base_url().'index.php/cf_autodebit/authorize/'.$record->id_scheme_account : $record->auth_link
                );
            }
            return array('chits' => $schemeAcc, 'over_all_amount' => $overall_amt);
        }
    }
    function get_postdated_payment($id_scheme_account)
    {
        $sql = "Select
				   pp.id_scheme_account,
				   date(pp.date_payment) as date_payment,
				   pp.pay_mode,
				   pp.cheque_no,
				   pp.payee_acc_no,
				   b.bank_name,
				   b.short_code,
				   pp.payee_branch,
				   pp.payee_ifsc,
				   pp.amount
			From postdate_payment pp
			Left Join bank b On (pp.payee_bank=b.id_bank)
			Where (Date_Format(Current_Date(),'%Y%m')=Date_Format(pp.date_payment,'%Y%m'))
			       And (pp.payment_status = 2 or pp.payment_status=7)
			       And pp.id_scheme_account='" . $id_scheme_account . "'";
        return $this->db->query($sql)->row_array();
    }
    function get_metalrate($id_branch, $is_branchwise_rate)
    {
        if ($is_branchwise_rate == 1 && $id_branch != '' && $id_branch != NULL) {
            $sql = "select * from metal_rates m
	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
	   		where br.id_branch=" . $id_branch . " order by  br.id_metalrate desc limit 1";
        } else {
            $sql = "select * from metal_rates order by id_metalrates Desc LIMIT 1";
        }
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    function get_scheme_detail($id_scheme_account)
    {
        $sql = "select
				   sa.id_scheme_account,
				   s.id_scheme,
				   c.id_customer,
				   IF(sa.scheme_acc_number !='',CONCAT(s.code,' ',sa.scheme_acc_number),'') as chit_number,
				   s.scheme_name,
				   s.scheme_type,
				   s.code,
				   IF(s.scheme_type=0,s.amount,IF(s.scheme_type=1,s.max_weight,s.amount)) as payable,
				   IF(s.scheme_type=1,s.max_weight,0) as  eligible_weight
			from scheme_account sa
			Left Join scheme s On (sa.id_scheme = s.id_scheme)
			Left Join customer c On (sa.id_customer = c.id_customer)
			Where sa.id_scheme_account='$id_scheme_account'";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    //individual scheme and payment details
    function chit_scheme_detail($id_scheme_account)
    {
        $result = array();
        $generalAdvance_payArray = array();
        $showGCodeInAcNo = $this->config->item('showGCodeInAcNo');
        //scheme detail
        $sql_scheme = "Select s.is_lumpSum,sa.lump_joined_weight, sa.lump_payable_weight,s.show_paymentMonthwise,IFNULL(DATEDIFF(date(maturity_date),CURDATE()),0) as remaining_days_for_maturity,
		s.allow_general_advance,s.apply_adv_benefit,
		s.is_digi,IFNULL(sa.start_year,'') as start_year,br.short_name as acc_branch,s.code,
		                cs.schemeaccNo_displayFrmt,s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		IF(DATEDIFF(CURDATE(),date(sa.start_date)) > s.chit_detail_days,0,1) as show_chit_wallet,if(s.scheme_type=3 && s.pay_duration=0,'Daily','Monthly') as account_type, 
		s.restrict_payment,DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference,CURDATE() as cur_date, 
		DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,COUNT(p.id_payment) as pay_count,sa.id_branch as joined_branch,
	            	ifnull(p.old_metal_amount,0) as old_metal_amount,
					sa.fixed_wgt,sa.fixed_metal_rate,IF(rate_fixed_in = 1, 'Web App', IF(rate_fixed_in = 2, 'Mobile App', IF(rate_fixed_in = 3, 'Offline', '-'))) as rate_fixed_in,
					Date_Format(sa.fixed_rate_on,'%d-%m-%Y') as fixed_rate_on,IF(sa.fixed_rate_on is NULL,'NO','YES') as is_rate_fixed, 
				    sa.id_scheme_account,cs.firstPayamt_payable,sa.firstPayment_amt,sa.is_registered,sa.fixed_metal_rate,sa.fixed_wgt, ifnull(Date_Format(sa.maturity_date,'%d-%m-%Y'),'-') as maturity_date,s.flexible_sch_type,
				    s.id_scheme,s.one_time_premium,s.otp_price_fixing,s.firstPayamt_as_payamt,s.get_amt_in_schjoin,
				    c.id_customer,
				    IF(s.discount=1,s.firstPayDisc_value,0.00) as discount_val,s.discount,s.discount_type,s.discount_installment,
				    CONCAT(if(" . $showGCodeInAcNo . "=1,if(has_lucky_draw = 1,sg.group_code,s.code),'') ,' ',ifnull(sa.scheme_acc_number,'Not Allocated')) as chit_number,
				    IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
				    c.mobile,
				    s.scheme_name,s.gst_type,
				    s.scheme_type,s.show_ins_type,
				    s.code,
				    IFNULL(s.min_chance,0) as min_chance,
				    IFNULL(s.max_chance,0) as max_chance,
				    Format(IFNULL(s.max_weight,0),3) as max_weight,
				    Format(IFNULL(s.min_weight,0),3) as min_weight,
				    Date_Format(sa.start_date,'%d-%m-%Y')start_date,
				  Format(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,if((s.min_amount!='' && s.max_amount!='0'),s.max_amount,round((s.max_weight * (SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)))),0))),2) as payable,
				    s.total_installments,
	IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installment,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
			FORMAT(sum(if(p.gst > 0,if( p.gst_type = 1,0,p.payment_amount-(p.payment_amount*(100/(100+p.gst)))),0)),0) as gst,
			cp.paid_installmentss,
			IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as old_paid_installments,ifnull(sa.total_paid_ins,0) as paid_installments,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)
 as total_paid_weight,
				   Format(IFNULL(cp.total_amount,0),2) as  current_total_amount,
				    Format(IFNULL(cp.total_weight,0),3) as  current_total_weight,
				    IFNULL(cp.paid_installmentss,0)       as  current_paid_installments,
				    IFNULL(cp.chances,0)                as  current_chances_used,
				    s.is_pan_required,
				    max(p.date_payment)                 as last_paid_date,
					sa.active as chit_active,
					sa.is_closed as is_closed, cs.has_lucky_draw,sa.group_code as scheme_group_code,
						(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1) as metal_rate,
					IFNULL(sa.auto_debit_status, 0) as auto_debit_status
                        -- ,s.min_amount as payable
				From scheme_account sa
				Left Join scheme s On (sa.id_scheme=s.id_scheme)
                Left Join branch br On (br.id_branch=sa.id_branch)
				Left Join scheme_group sg On (sa.group_code = sg.group_code )
				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=1 or p.payment_status=2 or p.payment_status=8) )
				Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
				Left Join
					(	Select
						  sa.id_scheme_account,
						  COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) as paid_installmentss,
						  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances,
						  SUM(p.payment_amount) as total_amount,
						  SUM(p.metal_weight) as total_weight
						From payment p
						Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
						Where (p.payment_status=1 or p.payment_status=2 or p.payment_status=8) 
						Group By sa.id_scheme_account
					) cp On (sa.id_scheme_account=cp.id_scheme_account)
					JOIN chit_settings cs 
				Where sa.active=1 and sa.is_closed = 0 and sa.`id_scheme_account` ='$id_scheme_account'";
        $record = $this->db->query($sql_scheme)->row_array();
        $restrict_pay = $record['restrict_payment'];
        //DGS-DCNM -> digi gold allow pay settings...	
        $digi_allowpay = '';
        if ($record['is_digi'] == 1) {
            if ($record['restrict_payment'] == 1) {
                if ($record['total_days_to_pay'] != null && ($record['joined_date_diff'] >= $record['total_days_to_pay'])) {
                    $digi_allowpay = 'N';
                    $digi_allowpay_msg = 'Your scheme payment days has been expired. You can redeem the scheme at your nearest store.';
                } else {
                    $digi_allowpay = 'Y';
                    $digi_allowpay_msg = '';
                }
            }
        }
        //DGS-DCNM
        //chit number and receipt number based on display format settings starts...
        $accNumData = array(
            'is_lucky_draw' => $record['is_lucky_draw'],
            'scheme_acc_number' => $record['scheme_acc_number'],
            'scheme_group_code' => $record['scheme_group_code'],
            'schemeaccNo_displayFrmt' => $record['schemeaccNo_displayFrmt'],
            'scheme_wise_acc_no' => $record['scheme_wise_acc_no'],
            'acc_branch' => $record['acc_branch'],
            'code' => $record['code'],
            'start_year' => $record['start_year'],
            'id_scheme_account' => $record['id_scheme_account']
        );
        //end
        // print_r($record);exit;
        $payable = str_replace(',', '', ($record['scheme_type'] == 3 && ($record['firstPayamt_payable'] == 1 || $record['firstPayamt_as_payamt'] == 1) ? $record['firstPayment_amt'] : $record['payable']));
        //Gst taken from GST spitup table.....29-09-2023 #AB starts.....
        $gst = $this->db->query('SELECT splitup_name,percentage,status FROM `gst_splitup_detail` where id_scheme = ' . $record['id_scheme'] . ' and type is null and splitup_name = "GST" and status = 1')->row('percentage');
        //Gst taken from GST spitup table.....29-09-2023 #AB ends.....
        //tkv Chit General Advance settings (GA) block with separate benefit settings .... Dt Added : 09-11-2023, By: #AB
        $genAdv_show_bonus = $this->db->query("SELECT sg.installment_from, sg.installment_to,sg.interest_type,sg.interest_value, 
	                                DATE_ADD(date(sa.start_date), INTERVAL + (sg.installment_from - 1) month) as calculate_date_from, 
	                                DATE_ADD(date(sa.start_date), INTERVAL + (sg.installment_to - 1) month) as calculate_date_to 
	                                FROM scheme_account sa 
	                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme) 
	                                LEFT JOIN `scheme_general_advance_benefit_settings` sg ON (sg.id_scheme = s.id_scheme) 
	                                WHERE s.id_scheme = " . $record['id_scheme'] . " and sa.id_scheme_account = " . $record['id_scheme_account'] . "
	                                and EXTRACT( YEAR_MONTH FROM (DATE_ADD(date(sa.start_date), INTERVAL + sg.installment_to month))) <= EXTRACT( YEAR_MONTH FROM CURDATE())
                ")->result_array();
        //   print_r($this->db->last_query());exit;
        $tot_bonus_GA = '0';
        $bonus_GA = [];
        $tot_paid_GA_wgt = 0;
        $tot_paid_GA_amt = 0;
        if ($record['allow_general_advance'] == 1 && $record['apply_adv_benefit'] == 1 && sizeof($genAdv_show_bonus) > 0) {
            foreach ($genAdv_show_bonus as $sb) {
                $value = $sb['interest_value'];
                $type = $sb['interest_type'];
                $calc_date_from = $sb['calculate_date_from'];
                $calc_date_to = $sb['calculate_date_to'];
                $range = $sb['installment_from'] . ' - ' . $sb['installment_to'] . ' months';
                $ben_value = ($type == 0 ? $value . ' %' : 'INR ' . $value);
                $ga_data = $this->get_generalAdv_BonusData($record['id_scheme_account'], $value, $type, $calc_date_from, $calc_date_to);
                // echo '<pre>';print_r($ga_data);//exit;
                $tot_bonus_GA += $ga_data['adv_bonus'];
                $tot_bonus_GA_wgt += $ga_data['adv_bonus_wgt'];
                $tot_paid_GA_amt += $ga_data['tot_adv_amt'];
                $tot_paid_GA_wgt += $ga_data['tot_adv_wgt'];
                // $bonus_GA[] = array('range' => $range , 'bonus' => $ga_data, 'value' => $ben_value);
                $bonus_GA[] = array('range' => $range, 'bonus' => $ga_data['adv_bonus'], 'bonus_wgt' => $ga_data['adv_bonus_wgt'], 'value' => $ben_value, 'bonus_paid_amt' => $ga_data['tot_adv_amt'], 'bonus_paid_wgt' => $ga_data['tot_adv_wgt']);
            }
            //  echo '<pre>';print_r($ga_data);exit;
        }
        //Tkv - Chit GA ends
        // maturity date esakki 13-12
        if ($record['maturity_date'] == '-') {
            $maturitydate = date('d-m-Y', strtotime("+" . $record['total_installments'] . " months", strtotime($record['start_date'])));
        } else {
            $maturitydate = $record['maturity_date'];
            if (!empty($record['maturity_date'])) {
                $maturitydate = $record['maturity_date'];
            } else {
                if ($record['maturity_type'] == 2) {
                    $maturitydate = date('d-m-Y', strtotime("+" . ($record['maturity_days'] + $record['closing_maturity_days']) . " days", strtotime($record['start_date'])));
                } else {
                    $maturitydate = date('d-m-Y', strtotime("+" . $record['closing_maturity_days'] . " days", strtotime($record['start_date'])));
                }
            }
        }
            $result['chit'] = array(
                //tkv starts...
                'allow_general_advance' => $record['allow_general_advance'],
                'apply_adv_benefit' => $record['apply_adv_benefit'],
                'gen_adv_bonus' => $bonus_GA,
                'tot_gen_adv_bonus' => round($tot_bonus_GA),
                'tot_gen_adv_bonus_wgt' => $tot_bonus_GA_wgt,
                'gst' => (empty($gst) ? '0.00' : $gst),
                'gst_type' => $record['gst_type'],
                'tot_bonus_paid_amt' => $tot_paid_GA_amt,
                'tot_bonus_paid_wgt' => formatMetalWeight($tot_paid_GA_wgt),
                'grand_total_amount' => number_format(($record['total_paid_amount'] + $tot_paid_GA_amt + $tot_bonus_GA), 2),
                'grand_total_weight' => formatMetalWeight(($record['total_paid_weight'] + $tot_paid_GA_wgt + $tot_bonus_GA_wgt)),
                'remaining_days_for_maturity' => $record['remaining_days_for_maturity'],
                //tkv ends...       
                //lump scheme
                'lump_joined_weight' => $record['lump_joined_weight'],
                'lump_payable_weight' => $record['lump_payable_weight'],
                'is_lumpSum' => (int) $record['is_lumpSum'],
                'account_name' => $record['account_name'],
                'chit_active' => $record['chit_active'],
                'chit_number' => $this->getAccNoFormat($accNumData),
                'code' => $record['has_lucky_draw'] = 1 ? $record['scheme_group_code'] : $record['code'],
                'current_chances_used' => $record['current_chances_used'],
                'current_paid_installments' => $record['current_paid_installments'],
                'current_total_amount' => $record['current_total_amount'],
                'current_total_weight' => formatMetalWeight($record['current_total_weight']),
                'id_customer' => $record['id_customer'],
                'id_scheme' => $record['id_scheme'],
                'paid_installments' => $record['paid_installments'],
                'id_scheme_account' => $record['id_scheme_account'],
                'is_closed' => $record['is_closed'],
                'is_pan_required' => $record['is_pan_required'],
                'last_paid_date' => $record['last_paid_date'],
                'max_chance' => $record['max_chance'],
                'max_weight' => formatMetalWeight($record['max_weight']),
                'min_chance' => $record['min_chance'],
                'min_weight' => formatMetalWeight($record['min_weight']),
                'mobile' => $record['mobile'],
                'paid_installment' => $record['paid_installment'],
                // 'payable' => (($record['scheme_type']==3 && $record['firstPayamt_payable']==1 &&$record['paid_installments']>0)||($record['is_registered']==1) ?$record['firstPayment_amt'] :$record['payable']),//
                'payable' => number_format($payable, 0, '.', ''),
                'scheme_name' => $record['scheme_name'],
                'scheme_type' => $record['scheme_type'],
                'flexible_sch_type' => $record['flexible_sch_type'],
                'start_date' => $record['start_date'],
                'total_installments' => $record['total_installments'],
                'total_paid_amount' => $record['total_paid_amount'] + $record['old_metal_amount'],
                'total_paid_weight' => formatMetalWeight($record['total_paid_weight']),
                'maturity_date' => $maturitydate,
                'fixed_metal_rate' => $record['fixed_metal_rate'],
                'fixed_wgt' => formatMetalWeight($record['fixed_wgt']),
                'one_time_premium' => $record['one_time_premium'],
                'otp_price_fixing' => $record['otp_price_fixing'],
                'firstPayment_amt' => $record['firstPayment_amt'],
                'is_rate_fixed' => $record['is_rate_fixed'],
                'fixed_wgt' => $record['fixed_wgt'],
                'fixed_metal_rate' => $record['fixed_metal_rate'],
                'fixed_rate_on' => $record['fixed_rate_on'],
                'rate_fixed_in' => $record['rate_fixed_in'],
                'discount_val' => $record['discount_val'],
                'allowPayDisc' => ($record['discount'] == 1 ? ($record['discount_type'] == 0 ? 'All' : $record['discount_installment']) : 0),
            'show_ins_type'                     => $record['show_ins_type'],
                'gst_type' => $record['gst_type'],
                'allow_pay_till' => $record['allow_pay_till'],
                'pay_count' => $record['pay_count'],
                'current_date' => $record['cur_date'],
                'interest_value' => $walletArr['interest'],
                'total_benefit' => $walletArr['total_benefit'],
                'date_diff' => $record['date_difference'],
                'show_chit_wallet' => ($record['is_digi'] == 1 ? $record['show_chit_wallet'] : 0),    //changes
                'chit_wallet_pdf' => ($record['is_digi'] == 1 ? base_url() . 'index.php/paymt/chit_detail_report/' . $id_scheme_account : ''),
                'metal_rate' => $record['metal_rate'],
                'restrict_payment' => $restrict_pay,
                'account_type' => $record['account_type'],
                'id_branch' => $record['joined_branch'],
                'digi_allowpay' => $digi_allowpay,
                'digi_allowpay_msg' => $digi_allowpay_msg,
                'daily_payLimit_applicable' => $record['daily_payLimit_applicable'],
                'eligible_amt' => $record['eligible_amt'],
                'daily_pay_limit' => $record['daily_pay_limit'],
                'min_amount' => $record['min_amount'],
                'max_amount' => $record['max_amount'],
                'payable' => $record['payable'],
                //DGS-DCNM
                'show_paymentMonthwise' => $record['show_paymentMonthwise'],
                'auto_debit_status' => (int) $record['auto_debit_status']
            );
            // Derive allow_pay from get_payment_details (same logic used by customerSchemes API)
            $allow_pay = 'N';
            $paymentDetails = $this->get_payment_details($record['id_customer'], '', '');
            if (!empty($paymentDetails['chits'])) {
                foreach ($paymentDetails['chits'] as $chit) {
                    if ($chit['id_scheme_account'] == $id_scheme_account) {
                        $allow_pay = $chit['allow_pay'];
                        break;
                    }
                }
            }
            $result['chit']['allow_pay'] = $allow_pay;
            //payments
            //$interest = ($walletArr['interest'] != '' ? $walletArr['interest'] : '0');
            //  (DATEDIFF(CURDATE(),date(p.date_payment)) -1) as days_diff,	ROUND((p.metal_weight)*(".$interest."/100)*((DATEDIFF(CURDATE(),date(p.date_payment)) -1)/364),3) as pay_int,
            $sql_payments = "Select @a:=@a+1 as sno,month(p.date_payment) as payment_month,s.show_paymentMonthwise, if(s.show_paymentMonthwise=1,SUM(p.payment_amount),p.payment_amount) as total_payment_amount,IFNULL(p.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = p.id_branch) as payment_branch,
				                    s.code,cs.receiptNo_displayFrmt,ifnull(p.receipt_no,'') as receipt_no,cs.scheme_wise_receipt,
                                    p.payment_amount,p.old_metal_amount,if(s.scheme_type=3 && s.pay_duration=0,'Daily','Monthly') as account_type,
                                    p.metal_weight as esti_wgt,
                                    p.id_payment,
                                    IFNULL(p.id_transaction,'') as id_transaction,p.old_metal_amount,
                                    IFNULL(p.payu_id,'') as payu_id,
                                    Date_Format(p.date_payment,'%d-%m-%Y') as trans_date ,
                                    IFNULL(round(p.payment_amount,0),'0.00') as amount,
                                    IFNULL(p.metal_weight,'0.00') as weight,
                                    if(p.payment_mode is null,'',if(p.payment_mode = 'FP','Free',p.payment_mode )) as payment_mode,
                                    IFNULL(p.bank_name,'') as bank_name,
                                    IFNULL(p.bank_branch,'') as  branch_name,
                                    IFNULL(p.card_no,'')    as card_no,
                                    IFNULL(p.payment_ref_number,'') as approval_no,
                                    IFNULL(p.receipt_no,'') as oldreceipt_jil,
                                    IFNULL(p.id_scheme_account,'') as id_scheme_account,
                                    IFNULL(p.metal_rate,'') as rate,
                                    IFNULL(p.remark,'') as remark,
                                    IF(p.payment_status = 2,'Awaiting',psm.payment_status) as pay_status,
                                    p.payment_status as id_status,
                                    psm.color as status_color,
                                    concat(if(p.due_month=1,'JAN',if(p.due_month=2,'FEB',if(p.due_month=3,'MAR',if(p.due_month=4,'APR',if(p.due_month=5,'MAY',if(p.due_month=6,'JUN',if(p.due_month=7,'JULY',if(p.due_month=8,'AUG',if(p.due_month=9,'SEP',if(p.due_month=10,'OCT',if(p.due_month=11,'NOV',if(p.due_month=12,'DEC','')))))))))))),'-',p.due_year) as due_month
    							 From payment p
    							 Left Join payment_mode pm on (p.payment_mode=pm.id_mode)
    							 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
    							 left join scheme s on s.id_scheme=sa.id_scheme
    			                 Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
    			                 join chit_settings cs
    							 Where (p.payment_status=1 or p.payment_status=2 or p.payment_status=8) and p.id_scheme_account='$id_scheme_account' ";
            if ($result['chit']['show_paymentMonthwise'] == 1) {
                $sql_payments .= "group by date_format(date_payment,'%Y%m') order by UNIX_TIMESTAMP(p.date_payment) ASC";
            } else {
                $sql_payments .= "group by id_payment order by UNIX_TIMESTAMP(p.date_payment) ASC";
            }
            $paydata = $this->db->query($sql_payments)->result_array();
            foreach ($paydata as $payments) {
                $rcptNumData = array(
                    'receipt_year' => $payments['receipt_year'],
                    'payment_branch' => $payments['payment_branch'],
                    'receiptNo_displayFrmt' => $payments['receiptNo_displayFrmt'],
                    'scheme_wise_receipt' => $payments['scheme_wise_receipt'],
                    'receipt_no' => $payments['receipt_no'],
                    'id_payment' => $payments['id_payment'],
                    'id_scheme_account' => $payments['id_scheme_account']
                );
                $payArray[] = array(
                    'gst' => (empty($gst) ? '0.00' : $gst),
                    'gst_type' => $record['gst_type'],
                    'receipt_jil' => $this->getRcptNoFormat($rcptNumData),
                    'payment_amount' => $payments['payment_amount'],
                    'old_metal_amount' => $payments['old_metal_amount'],
                    'account_type' => $payments['account_type'],
                    'esti_wgt' => $payments['esti_wgt'],
                    'id_payment' => $payments['id_payment'],
                    'id_transaction' => $payments['id_transaction'],
                    'payu_id' => $payments['payu_id'],
                    'trans_date' => $payments['trans_date'],
                    'amount' => $payments['amount'],
                    'weight' => $payments['weight'],
                    'bank_name' => $payments['bank_name'],
                    'branch_name' => $payments['branch_name'],
                    'card_no' => $payments['card_no'],
                    'approval_no' => $payments['approval_no'],
                    'id_scheme_account' => $payments['id_scheme_account'],
                    'rate' => $payments['rate'],
                    'remark' => $payments['remark'],
                    'pay_status' => $payments['pay_status'],
                    'id_status' => $payments['id_status'],
                    'due_month' => $payments['due_month'],
                    'status_color' => $payments['status_color'],
                    'payment_mode' => $payments['payment_mode'],
                    'month_payments' => $payments['show_paymentMonthwise'] == 1 ? $this->getMonthwiseRecords($payments['payment_month'], $payments['id_scheme_account']) : '',
                    'installment_no' => $payments['sno'],
                    'pdf_url' => base_url() . 'index.php/mobile_api/generateInvoice/' . $payments['id_payment'] . '/' . $payments['id_scheme_account']
                );
            }
            $result['payments']['installments'] = $payArray;
            /* GENERAL ADVANCE TRANSACTION STARTS */
            $generalAdvance = $this->db->query("Select IFNULL(p.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = p.id_branch) as payment_branch, s.code,cs.receiptNo_displayFrmt,ifnull(p.receipt_no,'') as receipt_no,cs.scheme_wise_receipt, p.payment_amount,if(s.scheme_type=3 && s.pay_duration=0,'Daily','Monthly') as account_type, p.metal_weight as esti_wgt, p.id_adv_payment as id_payment, IFNULL(p.id_transaction,'') as id_transaction, IFNULL(p.payu_id,'') as payu_id, Date_Format(p.date_payment,'%d-%m-%Y') as trans_date , IFNULL(round(p.payment_amount,0),'0.00') as amount, IFNULL(p.metal_weight,'0.00') as weight, if(p.payment_mode is null,'',if(p.payment_mode = 'FP','Free',p.payment_mode )) as payment_mode, IFNULL(p.bank_name,'') as bank_name, IFNULL(p.bank_branch,'') as branch_name, IFNULL(p.card_no,'') as card_no, IFNULL(p.payment_ref_number,'') as approval_no, IFNULL(p.receipt_no,'') as oldreceipt_jil, IFNULL(p.id_scheme_account,'') as id_scheme_account, IFNULL(p.metal_rate,'') as rate, IFNULL(p.remark,'') as remark, IF(p.payment_status = 2,'Awaiting',psm.payment_status) as pay_status, p.payment_status as id_status, psm.color as status_color
                                        From general_advance_payment p 
                                        Left Join payment_mode pm on (p.payment_mode=pm.id_mode) 
                                        left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account 
                                        left join scheme s on s.id_scheme=sa.id_scheme 
                                        Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
                                        join chit_settings cs 
                                        Where (p.payment_status=1 or p.payment_status=2 or p.payment_status=8) and p.id_scheme_account='5' 
                                        order by UNIX_TIMESTAMP(p.date_payment) DESC;")->result_array();
            if (sizeof($generalAdvance) > 0) {
                foreach ($generalAdvance as $ga) {
                    $rcptNumData = array(
                        'receipt_year' => $ga['receipt_year'],
                        'payment_branch' => $ga['payment_branch'],
                        'receiptNo_displayFrmt' => $ga['receiptNo_displayFrmt'],
                        'scheme_wise_receipt' => $ga['scheme_wise_receipt'],
                        'receipt_no' => $ga['receipt_no'],
                        'id_payment' => $ga['id_payment'],
                        'id_scheme_account' => $ga['id_scheme_account']
                    );
                    $generalAdvance_payArray[] = array(
                        'gst' => (empty($gst) ? '0.00' : $gst),
                        'gst_type' => $record['gst_type'],
                        'receipt_jil' => $this->getRcptNoFormat($rcptNumData),
                        'payment_amount' => $ga['payment_amount'],
                        'account_type' => $ga['account_type'],
                        'esti_wgt' => $ga['esti_wgt'],
                        'id_payment' => $ga['id_payment'],
                        'id_transaction' => $ga['id_transaction'],
                        'payu_id' => $ga['payu_id'],
                        'trans_date' => $ga['trans_date'],
                        'amount' => $ga['amount'],
                        'weight' => $ga['weight'],
                        'bank_name' => $ga['bank_name'],
                        'branch_name' => $ga['branch_name'],
                        'card_no' => $ga['card_no'],
                        'approval_no' => $ga['approval_no'],
                        'id_scheme_account' => $ga['id_scheme_account'],
                        'rate' => $ga['rate'],
                        'remark' => $ga['remark'],
                        'pay_status' => $ga['pay_status'],
                        'id_status' => $ga['id_status'],
                        'due_month' => $ga['due_month'],
                        'status_color' => $ga['status_color'],
                        'payment_mode' => $ga['payment_mode']
                    );
                }
            }
            $result['payments']['general_advance_payments'] = $generalAdvance_payArray;
            /* GENERAL ADVANCE TRANSACTION ENDS */
            $result['gift'] = $this->db->query("SELECT CONCAT(if(" . $showGCodeInAcNo . "=1,if(is_lucky_draw = 1,sg.group_code,s.code),'') ,' ',ifnull(sa.scheme_acc_number,'Not Allocated')) as chit_number, date(g.date_issued) as gift_issued_date,g.gift_desc
		                           FROM gift_issued g
		                           LEFT JOIN scheme_account sa ON (sa.id_scheme_account = g.id_scheme_account)
		                           LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
		                           LEFT JOIN scheme_group sg ON (sg.id_scheme = s.id_scheme)
		                           WHERE g.id_scheme_account = '$id_scheme_account'
		                           GROUP BY g.id_gift_issued ORDER BY g.date_issued DESC")->result_array();
            return $result;
        
    }
    function get_settings()
    {
        $result = array();
        $this->db->select('allow_notification,delete_unpaid,reg_existing,show_closed_list,branch_settings,is_branchwise_rate,regExistingReqOtp,metal_wgt_decimal,metal_wgt_roundoff');
        $result = $this->db->get('chit_settings');
        return $result->row_array();
    }
    function get_customer_dashboard($id_customer)
    {
        $result = array();
        $result['delete_customer'] = TRUE;
        $result['total_accounts'] = $this->countSchemes($id_customer);
        $result['wallets'] = $this->countWallets($id_customer);
        $wb = $this->wallet_balance($id_customer);
        // print_r($wb);exit;
        $result['wallet_balance'] = (!empty($wb['wal_balance1']) ? $wb['wal_balance1'] : 0);
        $result['giftItems_enable'] = $this->get_giftItems();
        $scheme_types = $this->totalAmtWgt($id_customer);    // closed a/c Amt is Reduced in Dashboard page//HH
        $result['amount']['total_amount'] = $scheme_types['amount'];
        $result['weight']['total_weight'] = $scheme_types['weight'];
        $result['payments'] = $this->countPayments($id_customer);
        $result['customer'] = $this->get_customerByID($id_customer);
        $result['dues'] = $this->countDues($id_customer);
        $result['closed_acc'] = $this->total_closed_acc($id_customer);
        $allAccountsCount = $this->countAllSchemes($id_customer);
        if ($allAccountsCount > 0) {
            $sql1 = " Select sa.active, sa.is_closed 
			From scheme_account  sa
			left join scheme s on s.id_scheme = sa.id_scheme
			Where sa.id_customer ='$id_customer'";
            $result1 = $this->db->query($sql1);
            $values = $result1->result_array();
            // print_r($value);exit;
            foreach ($values as $val) {
                if ($val['active'] == 1) {
                    $result['delete_cus_msg'] = "You have a active financial transaction. Contact admin for account deletion.";
                    break;
                } else if ($val['active'] == 0 && $val['is_closed'] == 0) {
                    $result['delete_cus_msg'] = "Your account will be in active, contact admin for active account";
                    break;
                } else {
                    $result['delete_cus_msg'] = "You have a closed financial transaction. Contact admin for account deletion.";
                    break;
                }
            }
            $result['delete_customer'] = FALSE;
        } else {
            $result['delete_customer'] = true;
        }
        return $result;
    }
    /*	function totalAmtWgt($id_customer){
            $sql="select sum(ifnull(payment_amount,0)) as amount,sum(ifnull(metal_weight,0)) as weight
                From payment p
                     Left Join scheme_account sa  on (sa.id_scheme_account = p.id_scheme_account)
                      where sa.active=1 and p.payment_status = 1 and id_customer = '".$id_customer."'";
                      //print_r($sql);exit;
            return $this->db->query($sql)->row_array();
        }  */
    function totalAmtWgt($id_customer)
    {
        /*$sql="select sum(ifnull(payment_amount,0)) as amount,sum(ifnull(metal_weight,0)) as weight
            From payment p
                 Left Join scheme_account sa  on (sa.id_scheme_account = p.id_scheme_account and (p.payment_status=1 ))
                  where sa.active=1 and id_customer = '".$id_customer."'";*/
        /* 22-10
       IF(s.scheme_type = 0 OR (s.scheme_type = 3 AND s.flexible_sch_type = 1) ,sum(ifnull(payment_amount,0)),'0.00') as amount,
IF((s.scheme_type = 1 OR s.scheme_type = 2 OR s.scheme_type = 3) AND (s.flexible_sch_type = 2 OR s.flexible_sch_type = 3 OR s.flexible_sch_type = 4 OR s.flexible_sch_type = 5 OR s.flexible_sch_type = 6) ,sum(ifnull(metal_weight,0)),0.00) as weight
       */
        $sql = "SELECT s.scheme_type as sch_typ,s.flexible_sch_type as flexible_sch_type,s.wgt_convert as wgt_convert,
                   sum(ifnull(payment_amount,0.00)) as amount,sum(ifnull(metal_weight,0.00)) as weight 
			From payment p
         		Left Join scheme_account sa  on (sa.id_scheme_account = p.id_scheme_account and p.payment_status=1)
         		LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
         		 where s.is_digi = 0 and sa.active=1 and id_customer = '" . $id_customer . "' group by sa.id_scheme_account";
        //print_r($sql);exit;
        $res = $this->db->query($sql)->result_array();
        $amount = 0;
        $wgt = 0;
        foreach ($res as $row) {
            if ($row['sch_typ'] == 0 || $row['sch_typ'] == 3 && ($row['flexible_sch_type'] == 1 || $row['flexible_sch_type'] == 6 || ($row['flexible_sch_type'] == 2 && $row['wgt_convert'] == 2))) {
                $is_weight = 0;
            } elseif ($row['sch_typ'] == 1 || $row['sch_typ'] == 2 || ($row['sch_typ'] == 3 && (($row['flexible_sch_type'] == 2 && ($row['wgt_convert'] == 0 || $row['wgt_convert'] == 1)) || $row['flexible_sch_type'] == 3 || $row['flexible_sch_type'] == 4 || $row['flexible_sch_type'] == 5 || $row['flexible_sch_type'] == 7 || $row['flexible_sch_type'] == 8))) {
                if (($row['flexible_sch_type'] == 2 && ($row['wgt_convert'] == 0 || $row['wgt_convert'] == 1)) || $row['flexible_sch_type'] != 2) {
                    $is_weight = 1;
                } elseif ($row['flexible_sch_type'] == 2 && $row['wgt_convert'] == 2) {
                    $is_weight = 0;
                } else {
                    $is_weight = 1;
                }
            }
            if ($is_weight == 0) {
                $amount += $row['amount'];
            } else if ($is_weight == 1) {
                $wgt += $row['weight'];
            }
            //  $amount += $row['amount'];
            //  $wgt += $row['weight'];
        }
        $settings = $this->get_settings();
		// print_r($settings['metal_wgt_roundoff']);exit;
		$data = array('amount' => $amount, 'weight' => formatMetalWeight($wgt));
        return $data;
    }
    function total_closed_acc($id_customer)
    {
        $sql = "Select count(id_scheme_account) as closed_acc 
				From scheme_account 
				Where is_closed=1 and active=0 and id_customer='" . $id_customer . "'";
        return $this->db->query($sql)->row()->closed_acc;
    }
    function countDues($id_customer)
    {
        $dues = 0;
        $this->load->model('payment_modal');
        $id_scheme_account = $this->get_idSchemeAccounts($id_customer);
        $id_scheme_values = array_column($id_scheme_account, 'id_scheme_account');
        // Build properly formatted string
        if (!empty($id_scheme_values)) {
            // If IDs are integers (no quotes needed)
            $id_sch_acc_string = implode(",", $id_scheme_values);
            // OR if IDs need quotes
            // $id_sch_acc_string = "'" . implode("','", $id_scheme_values) . "'";
        } else {
            $id_sch_acc_string = '';
        }
        $payrec = $this->get_payment_details($id_customer, '', $id_sch_acc_string);
        if (isset($payrec)) {
            foreach ($payrec['chits'] as $pay) {
                if ($pay['allow_pay'] == 'Y') {
                    $dues++;
                }
            }
        }
        return $dues;
    }
    function schemeType_wise($id_customer)
    {
        $sql = "Select scheme_type,count(distinct sa.id_scheme_account) as schemes,
					sum(ifnull(payment_amount,0)) as amount,sum(ifnull(metal_weight,0)) as weight,
					count(distinct  p.id_payment) as payments
					From scheme_account sa
					Left Join scheme s On (sa.id_scheme = s.id_scheme)
					Left Join payment p  on (sa.id_scheme_account = p.id_scheme_account and (p.payment_status=2 or p.payment_status=1 ))
					Where sa.active=1 and sa.id_customer ='$id_customer'
					group by s.scheme_type";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    //count no schemes by the customer
    function countSchemes($id_customer)
    {
        $sql = " Select count(sa.id_scheme) as schemes 
		         From scheme_account  sa
		         left join scheme s on s.id_scheme = sa.id_scheme
		         Where sa.active=1 and s.is_digi = 0 and is_enquiry = 0 and sa.id_customer ='$id_customer'";
        $result = $this->db->query($sql);
        return $result->row()->schemes;
    }
    function countAllSchemes($id_customer)
    {
        $sql = " Select count(sa.id_scheme_account) as schemes 
		         From scheme_account  sa
		         left join scheme s on s.id_scheme = sa.id_scheme
		         Where sa.id_customer ='$id_customer'";
        $result = $this->db->query($sql);
        return $result->row()->schemes;
    }
    //count no wallet by the customer
    function countWallets($id_customer)
    {
        $sql = " Select count(id_wallet_account) as wallets
		         From wallet_account 
		         Where active=1 and id_customer ='$id_customer'";
        $result = $this->db->query($sql);
        return $result->row()->wallets;
    }
    function wallet_balance($id_customer)
    {
        $data = array();
        $sql = "Select
								  wa.id_wallet_account,
								  c.id_customer,
								  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,
								  c.mobile,
								  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
								  wa.wallet_acc_number,
								  Date_Format(wa.issued_date,'%d-%m-%Y') as issued_date,
								  wa.remark,
								  wa.active,
								  SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) as  issues,
								  SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END) as redeem,
								  (SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) -   SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END)) as balance,
								   cs.wallet_amt_per_points,cs.wallet_balance_type,cs.wallet_points
							From wallet_account wa
								Left Join customer c on (wa.id_customer=c.id_customer)
								Left Join employee e on (wa.id_employee=e.id_employee)
								Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account)
								join chit_settings cs
								where c.id_customer =" . $id_customer;
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            // Code updated by Karthikai kumaran 05/11/2025 for redeem percent fetch
            // This is comment due to we dont use a redeem percent otherwise need to put one dummy entry on wallet_category_settings
            // $sql1 = "SELECT w.redeem_percent FROM wallet_category_settings w where active=1 and w.id_category=" . $this->config->item('wallet_cat_id');
            // $record = $this->db->query($sql1);
            // if ($record->num_rows() > 0) {
            // print_r($result->row());exit;
            $balance = ($result->row()->wallet_balance_type == 1 ? (($result->row()->balance / $result->row()->wallet_points) * $result->row()->wallet_amt_per_points) : $result->row()->balance);
            $balance = is_nan($balance) ? 0 : $balance;
            //$data = array('redeem_percent'=>$record->row()->redeem_percent,'wal_balance1'=>$result->row()->balance,'wal_balance'=>$balance,'wallet_balance_type'=>$result->row()->wallet_balance_type,'wallet_points'=>$result->row()->wallet_points,'wallet_amt_per_points'=>$result->row()->wallet_amt_per_points);
            $data = array('redeem_percent' => '', 'wal_balance1' => $balance, 'wal_balance' => $result->row()->balance, 'wallet_balance_type' => $result->row()->wallet_balance_type, 'wallet_points' => $result->row()->wallet_points, 'wallet_amt_per_points' => $result->row()->wallet_amt_per_points);
            // }
        }
        return $data;
    }
    /*function countWalletsblc($id_customer)
    {
        $sql = " Select count(id_wallet_account) as wallet
                 From wallet_account 
                 Where active=1 and id_customer ='$id_customer'
                 GROUP BY id_customer";
        $result = $this->db->query($sql);	
        return $result->row()->wallets;
    }*/
    function countPayments($id_customer)
    {
        $sql = "Select count(distinct  p.id_payment) as payments
				From scheme_account sa
				Left Join payment p  on (sa.id_scheme_account = p.id_scheme_account and (p.payment_status=2 or p.payment_status=1 ))
         		Where sa.active=1 and sa.id_customer ='$id_customer'";
        $result = $this->db->query($sql);
        return $result->row()->payments;
    }
    //Check whether the customer has same scheme
    function sameSchemeExist($id_customer, $id_scheme)
    {
        $sql = " Select count(id_scheme) as schemes 
		         From scheme_account 
		         Where active=1  And id_customer ='$id_customer' And id_scheme='$id_scheme'";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    //to count number of  payments made by customer on particular scheme
    function countSchemePayments($id_customer, $id_scheme)
    {
        $sql = "Select count(id_payment) as sch_payments
				From scheme_account sa
				Left Join payment p On(sa.id_scheme_account = p.id_scheme_account)
				Where sa.active=1 And p.paymentstatus=1 and sa.id_customer='$id_customer' and sa.id_scheme='$id_scheme'";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    //to check any scheme accounts without payment
    function notPaidAccounts($id_customer)
    {
        $result = '';
        $sql = "Select sa.id_scheme_account,c.id_customer,sa.ref_no as client_id,s.id_scheme,s.code,sa.scheme_acc_number,count(id_payment) as sch_payments
			From scheme_account sa
			Left Join payment p On(sa.id_scheme_account = p.id_scheme_account)
			Left Join scheme s On (sa.id_scheme = s.id_scheme)
			Left Join customer c On (sa.id_customer = c.id_customer)
			Where sa.active =1 and p.payment_status = 1 and sa.id_customer='$id_customer' 
			Group By sa.id_scheme_account
			Having sch_payments=0;";
        $account = $this->db->query($sql);
        if ($account->num_rows() > 0) {
            $result = array('not_paid_acc' => TRUE, 'data' => $account->result_array());
        } else {
            $result = array('not_paid' => FALSE, 'data' => '');
        }
        return $result;
    }
    function isAddressExist($id_customer)
    {
        $sql = "Select * from address Where id_customer='" . $id_customer . "' ";
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            return TRUE;
        }
    }
    //to check whether customer has payment entry
    function isPaymentExist($id_scheme_account)
    {
        $sql = "Select
					  sa.id_scheme_account,c.mobile
				From payment p
				Left Join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)
				Left Join customer c on (sa.id_customer = c.id_customer)
				Where (p.payment_status = 2 or p.payment_status = 1) And sa.id_scheme_account= '" . $id_scheme_account . "' ";
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    //to check whether customer has pending status payment entry
    function isPendingStatExist($id_scheme_account)
    {
        $sql = "Select
					  sa.id_scheme_account,c.mobile
				From payment p
				Left Join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)
				Left Join customer c on (sa.id_customer = c.id_customer)
				Where (p.payment_status = 7) And sa.id_scheme_account= '" . $id_scheme_account . "' ";
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    //get last paid entry
    function getLastTransaction($id_scheme_account)
    {
        $sql = "Select no_of_dues,payment_amount,due_type,act_amount,payment_status from payment			
			  Where (payment_status=1 Or payment_status=2 Or payment_status=7)	
			         And id_scheme_account='$id_scheme_account'";
        return $this->db->query($sql)->row_array();
    }
    /** Customer functions  **/
    function insert_customer($data)
    {
        $status = $this->db->insert(self::TAB_CUS, $data['info']);
        $insertID = $this->db->insert_id();
        if ($insertID) {
            $data['address']['id_customer'] = $insertID;
            $res = $this->db->insert(self::TAB_ADD, $data['address']);
            if ($res) {
                $id_address = $this->db->insert_id();
                $address = array('id_address' => $id_address);
                $this->db->where('id_customer', $insertID);
                $this->db->update(self::TAB_CUS, $address);
                $status = array("status" => true, "insertID" => $insertID);
            } else {
                $status = array("status" => false, "insertID" => '');
            }
        } else {
            $status = array("status" => false, "insertID" => '');
        }
        return $status;
    }
    function update_customer($data, $id)
    {
        $this->db->where('id_customer', $id);
        return $this->db->update(self::TAB_CUS, $data);
    }
    function insert_customerAdd($data)
    {
        $status = $this->db->insert(self::TAB_ADD, $data);
        return array('status' => $status, 'insertID' => $this->db->insert_id());
    }
    function insCusFeedback($data)
    {
        $status = $this->db->insert('cust_enquiry', $data);
        return array('status' => $status, 'insertID' => $this->db->insert_id());
    }
    function update_customerAdd($data, $id)
    {
        $this->db->where('id_customer', $id);
        return $this->db->update(self::TAB_ADD, $data);
    }
    function update_customerByMobile($data, $mobile)
    {
        $this->db->where('mobile', $mobile);
        return $this->db->update(self::TAB_CUS, $data);
    }
    /** Scheme Account functions  **/
    function update_schemeAcc($data, $id)
    {
        $this->db->where('id_scheme_account', $id);
        return $this->db->update(self::TAB_ACC, $data);
    }
    /** payment functions  **/
    function insert_schemeAcc($data)
    {
        /* Coded by ARVK*/
        $sql_scheme = $this->db->query("select  s.approvalReqForFP,cs.receipt_no_set,s.free_payment, s.amount, s.scheme_type, s.min_weight, s.max_weight, c.company_name, c.short_code  ,s.gst,s.gst_type,cs.cusbenefitscrt_type,cs.empbenefitscrt_type
			  										from scheme s join company c
                                                    join chit_settings cs		 
			  										where s.id_scheme=" . $data['id_scheme']);
        $sch_data = $sql_scheme->row_array();
        /* / Coded by ARVK*/
        if ($sch_data) {
            $cus_single = $sch_data['cusbenefitscrt_type'];
            $emp_single = $sch_data['empbenefitscrt_type'];
            if ($data['referal_code'] != '' && ($data['is_refferal_by'] == 0 && $cus_single == 0) || ($data['is_refferal_by'] == 1 && $emp_single == 0)) {
                if ($data['is_refferal_by'] == 0) {
                    $cus_data = array(
                        'referal_code' => $data['referal_code'],
                        'id_customer' => $data['id_customer'],
                        'is_refferal_by' => $data['is_refferal_by'],
                        'cus_single' => $cus_single
                    );
                } else {
                    $cus_data = array(
                        'referal_code' => $data['referal_code'],
                        'id_customer' => $data['id_customer'],
                        'is_refferal_by' => $data['is_refferal_by'],
                        'emp_single' => $emp_single
                    );
                }
                $this->available_refcode($cus_data);
            } else if ($data['referal_code'] != '' && ($data['is_refferal_by'] == 0 && $cus_single == 1) || ($data['is_refferal_by'] == 1 && $emp_single == 1)) {
                $cus_data = array(
                    'id_customer' => $data['id_customer'],
                    'cus_single' => $cus_single,
                    'emp_single' => $emp_single,
                    'is_refferal_by' => $data['is_refferal_by'],
                    'emp_ref_code' => NULL,
                    'cus_ref_code' => NULL
                );
            }
        }
        $status = $this->db->insert(self::TAB_ACC, $data);
        return array('status' => $status, 'sch_data' => $sch_data, 'insertID' => $this->db->insert_id());
    }
    function insert_gift($data)
    {
        $status = $this->db->insert('gift_issued', $data);
        return array('status' => $status, 'gift_data' => $data, 'insertID' => $this->db->insert_id());
    }
    /*function available_refcode($data){  
     $query=$this->db->query("SELECT c.referal_code 
                FROM customer c 
                where c.id_customer=".$data['id_customer']." and c.referal_code='".$data['referal_code']."'");
       if($query->num_rows()>0){
           return TRUE;		   
       }else{
           $this->db->where('id_customer',$data['id_customer']); 		  
           $updaterefcode =  $this->db->update('customer',array('referal_code'=>$data['referal_code']));
          return TRUE;	
       }
    } */
    function available_refcode($data)
    {
        if ($data['is_refferal_by'] == 0 && $data['cus_single'] == 0) {
            $query = $this->db->query("SELECT c.cus_ref_code 
				FROM customer c 
				where c.id_customer=" . $data['id_customer'] . " and c.cus_ref_code='" . $data['referal_code'] . "'");
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                $this->db->where('id_customer', $data['id_customer']);
                $updaterefcode = $this->db->update('customer', array('cus_ref_code' => $data['referal_code']));
                return TRUE;
            }
        } else if ($data['is_refferal_by'] == 1 && $data['emp_single'] == 0) {
            $query = $this->db->query("SELECT c.emp_ref_code 
				FROM customer c 
				where c.id_customer=" . $data['id_customer'] . " and c.emp_ref_code='" . $data['referal_code'] . "'");
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                $this->db->where('id_customer', $data['id_customer']);
                $updaterefcode = $this->db->update('customer', array('emp_ref_code' => $data['referal_code']));
                return TRUE;
            }
        } else if ($data['is_refferal_by'] == 0 || $data['is_refferal_by'] == 1 && ($data['cus_single'] == 1 && $data['emp_single'] == 1)) {
            $this->db->where('id_customer', $data['id_customer']);
            $updaterefcode = $this->db->update('customer', array('cus_ref_code' => $data['referal_code'], 'emp_ref_code' => $data['referal_code']));
            return TRUE;
        }
    }
    /* Coded by ARVK*/
    function free_payment_data($sch_data, $sch_acc_id)
    {
        $metal_rate = $this->getMetalRate();
        $gold_rate = number_format((float) $metal_rate['goldrate_22ct'], 2, '.', '');
        $gst_amt = 0;
        if ($sch_data['gst'] > 0) {
            $gst_amt = $sch_data['amount'] * ($sch_data['gst'] / 100);
            if ($sch_data['gst_type'] == 0) {
                $converted_wgt = number_format((float) (($sch_data['amount'] - $gst_amt) / $gold_rate), 3, '.', '');
            } else {
                $converted_wgt = number_format((float) ($sch_data['amount'] / $gold_rate), 3, '.', '');
            }
        } else {
            $converted_wgt = number_format((float) ($sch_data['amount'] / $gold_rate), 3, '.', '');
        }
        $fxd_wgt = $sch_data['max_weight'];
        $insertData = array(
            "id_scheme_account" => $sch_acc_id,
            "gst" => $sch_data['gst'],
            "gst_type" => $sch_data['gst_type'],
            "id_employee" => NULL,
            "date_payment" => date('Y-m-d H:i:s'),
            "payment_type" => "Cost free payment",
            "payment_mode" => "FP",
            "act_amount" => $sch_data['amount'],
            "payment_amount" => $sch_data['amount'],
            "due_type" => 'D',
            "no_of_dues" => '1',
            "metal_rate" => $gold_rate,
            "metal_weight" => ($sch_data['scheme_type'] == 2 ? $converted_wgt : ($sch_data['scheme_type'] == 1 ? $fxd_wgt : 0.000)),
            "remark" => "Paid by " . $sch_data['company_name'],
            "installment" => 1, // only for 1st ins free
            "payment_status" => ($sch_data['approvalReqForFP'] == 1 ? 2 : 1)
        );
        return $insertData;
    }
    function getMetalRate()
    {
        $filename = base_url() . 'api/rate.txt';
        $data = file_get_contents($filename);
        $metalrates = (array) json_decode($data);
        return $metalrates;
    }
    /* / Coded by ARVK*/
    function insert_payment($data)
    {
        $status = $this->db->insert(self::TAB_PAY, $data);
        return array('status' => $status, 'insertID' => $this->db->insert_id());
    }
    function update_payment($data, $id)
    {
        $this->db->where('id_payment', $id);
        return $this->db->update(self::TAB_PAY, $data);
    }
    /*** General Functions ***/
    //Checking the customer mobile already registered
    function isMobileExists($mobile)
    {
        $this->db->select('mobile');
        $this->db->where('mobile', $mobile);
        $customer = $this->db->get(self::TAB_CUS);
        if ($customer->num_rows() > 0) {
            return TRUE;
        }
    }
    //validate user login
    function isValidLogin($mobile, $passwd)
    {
        //selected all fields, return in array format
        $this->db->select('*');
        $this->db->where('mobile', $mobile);
        $this->db->where('passwd', $passwd);
        //$this->db->where('active',1);
        $login = $this->db->get(self::TAB_CUS);
        if ($login->num_rows() > 0) {
            //return TRUE;
            return $login->row_array();
        }
    }
    function generateOTP()
    {
        $data['otp'] = mt_rand(100000, 999999);
        $data['expiry'] = date("Y-m-d H:i:s", strtotime('+1 hour'));
        return $data;
    }
    //Promotion sms and otp setting
    function send_sms($mobile, $message, $type = '', $dlt_te_id)
    {
        $url = $this->sms_data['sms_url'];
        $senderid = $this->sms_data['sms_sender_id'];
        if ($this->sms_chk['debit_sms'] != 0) {
            $arr = array("@customer_mobile@" => $mobiles, "@message@" => urlencode(str_replace(array("\n", "\r"), '', $message)), "@senderid@" => $senderid, "@dlt_te_id@" => $dlt_te_id);
            $user_sms_url = strtr($url, $arr);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $user_sms_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $result = curl_exec($ch);
            curl_close($ch);
            unset($ch);
            $status = $this->update_otp();
            if ($status == 1) {
                return TRUE;
            }
            return FALSE;
        } else {
            return FALSE;
        }
    }
    function update_otp()
    {
        $query_validate = $this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1 
				WHERE id_sms_api =1 and debit_sms > 0');
        if ($query_validate > 0) {
            return true;
        } else {
            return false;
        }
    }
    //Promotion sms and otp setting
    //update gateway response
    function updateGatewayResponse($data, $txnid)
    {
        $this->db->where('id_transaction', $txnid);
        $status = $this->db->update(self::TAB_PAY, $data);
        $result = array(
            'status' => $status,
            'id_payment' => $this->get_lastUpdateID($txnid)
        );
        return $result;
    }
    function get_lastUpdateID($txnid)
    {
        $this->db->select('id_payment');
        $this->db->where('id_transaction', $txnid);
        $payid = $this->db->get('payment');
        return $payid->row()->id_payment;
    }
    function get_schemeByChit($id_scheme_account)
    {
        $sql = "select 
		s.id_scheme,s.code,s.scheme_type,s.amount,s.maturity_days
		from scheme s
		left join scheme_account sa on sa.id_scheme=s.id_scheme
		where sa.id_scheme_account='$id_scheme_account'";
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            return $result->row_array();
        }
    }
    function get_invoiceData($payment_no)
    {
        $records = array();
        $query_invoice = $this->db->query("SELECT pay.id_scheme_account as id_scheme_account, sch_acc.ref_no as scheme_acc_number, DATE_FORMAT(pay.date_payment,'%d-%m-%Y') as date_payment, sch.scheme_name as scheme_name, pay.payment_amount as payment_amount,cus.firstname as firstname, cus.lastname as lastname, cus.mobile, addr.address1 as address1,email,if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',if(payment_mode='OP','Other',''))))) as payment_mode,id_transaction,payment_ref_number,pay.receipt_no,bank_name,bank_acc_no,bank_branch,metal_weight,scheme_type
							FROM payment as pay
							LEFT JOIN scheme_account sch_acc ON sch_acc.id_scheme_account = pay.id_scheme_account
							LEFT JOIN scheme sch ON sch.id_scheme = sch_acc.id_scheme
							LEFT JOIN customer as cus ON cus.id_customer = sch_acc.id_customer
							LEFT JOIN address as addr ON addr.id_customer = cus.id_customer WHERE id_payment = '" . $payment_no . "' AND mobile='" . $this->session->userdata('username') . "'");
        if ($query_invoice->num_rows() > 0) {
            foreach ($query_invoice->result() as $row) {
                $records[] = array('id_scheme_account' => $row->id_scheme_account, 'scheme_acc_number' => $row->scheme_acc_number, 'date_payment' => $row->date_payment, 'scheme_name' => $row->scheme_name, 'payment_amount' => $row->payment_amount, 'firstname' => $row->firstname, 'lastname' => $row->lastname, 'id_payment' => $payment_no, 'address1' => $row->address1, 'email' => $row->email, 'mobile' => $row->mobile, 'payment_mode' => $row->payment_mode, 'id_transaction' => $row->id_transaction, 'payment_ref_number' => $row->payment_ref_number, 'receipt_no' => $row->receipt_no, 'bank_name' => $row->bank_name, 'bank_acc_no' => $row->bank_acc_no, 'bank_branch' => $row->bank_branch, 'metal_weight' => $row->metal_weight, 'scheme_type' => $row->scheme_type);
            }
        }
        return $records;
    }
    function get_branchWiseLogin()
    {
        $sql = "select cs.branchWiseLogin from chit_settings cs";
        $records = $this->db->query($sql)->row()->branchWiseLogin;
        return $records;
    }
    // closed a/c pay entries removed in pay histy page//hh
    function get_paymenthistory($mobile, $branchWiseLogin, $is_digi = 0)
    {
        $showGCodeInAcNo = $this->config->item('showGCodeInAcNo');
        $records = array();
        if ($is_digi == 1) {
            $scheme_filter = ($id_scheme != '' ? " and sch.id_scheme = " . $id_scheme . " " : "");
        } else {
            $scheme_filter = "";
        }
        if ($branchWiseLogin == 1) {
            $query_scheme = $this->db->query(" select sch.wgt_store_as,IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,sch.code,
		                cs.schemeaccNo_displayFrmt,sch.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		                IFNULL(pay.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = pay.id_branch) as payment_branch, sch.code,cs.receiptNo_displayFrmt,ifnull(pay.receipt_no,'') as receipt_no,cs.scheme_wise_receipt,sa.id_scheme_account,
		                 id_payment, DATE_FORMAT(date_payment,'%d-%m-%Y') AS date_payment, metal_rate, payment_amount, metal_weight,pay.receipt_no,pay.add_charges,if(pay.payment_type = 'Payu checkout',(payment_amount+ifnull(add_charges,0)), payment_amount) as total_amt,sch.charge_head,pay.gst,pay.gst_type,br.id_branch, br.short_name, br.name as branch_name,cs.branch_settings,pay.old_metal_amount,sch.flexible_sch_type,
										 if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking', 
										  if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',
										  if(payment_mode='OP','Other',if(payment_mode='DC','Debit Card', if(payment_mode='FP','Enrollment Offer',payment_mode) )))))) as payment_mode,IFNULL(id_transaction,'-') as id_transaction, if(payment_status = 1, 'Paid',if(payment_status = 2, 'Awaiting',if(payment_status = 5, 'Returned',if(payment_status = 6, 'Refund',if(payment_status = 7, 'Pending',if(payment_status = 3, 'Failed',if(payment_status = 4, 'Cancelled',if(payment_status = 9, 'User Dropped',''))))))))
										  as payment_status ,sch.code , 
										  CONCAT(if(" . $showGCodeInAcNo . "=1,if(has_lucky_draw = 1,sg.group_code,sch.code),'') ,' ',ifnull(concat(ifnull(concat(sa.start_year,'-'),''),sa.scheme_acc_number),'Not Allocated')) as oldscheme_acc_number,
										  ref_no AS client_id, scheme_name,cs.currency_symbol,pay.payment_type,
											if(scheme_type = 0,'Amount Scheme',IF(scheme_type=1,'Weight Scheme','Amt to Wgt scheme')) as scheme_type,
											scheme_type as sch_type,
											sa.group_code as scheme_group_code,cs.has_lucky_draw,pay.saved_benefits
											FROM payment as pay
											left join scheme_account AS sa on sa.id_scheme_account = pay.id_scheme_account
											Left Join branch br  On (pay.id_branch=br.id_branch)
											left join scheme as sch on sch.id_scheme = sa.id_scheme
											Left Join scheme_group sg On (sa.group_code = sg.group_code )
											left join customer as cus on  cus.id_customer = sa.id_customer
											left join payment_mode pm on pay.payment_mode=pm.short_code
											join chit_settings cs
											WHERE sch.is_digi = " . $is_digi . " and pay.payment_status in (1,2) and cus.mobile='" . $mobile . "'$scheme_filter and sa.active=1 ORDER By id_payment DESC");
        } else {
            $query_scheme = $this->db->query("select sch.wgt_store_as,IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,sch.code,
		                cs.schemeaccNo_displayFrmt,sch.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		                IFNULL(pay.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = pay.id_branch) as payment_branch, sch.code,cs.receiptNo_displayFrmt,ifnull(pay.receipt_no,'') as receipt_no,cs.scheme_wise_receipt,sa.id_scheme_account,
		                id_payment, DATE_FORMAT(date_payment,'%d-%m-%Y') AS date_payment, metal_rate, payment_amount, metal_weight,pay.receipt_no,pay.add_charges,if(pay.payment_type = 'Payu checkout',(payment_amount+ifnull(add_charges,0)), payment_amount) as total_amt,sch.charge_head,pay.gst,pay.gst_type,br.id_branch, br.short_name, br.name as branch_name,cs.branch_settings,pay.old_metal_amount,sch.flexible_sch_type,
										 if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking', 
										  if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',
										  if(payment_mode='OP','Other',if(payment_mode='DC','Debit Card', if(payment_mode='FP','Enrollment Offer',payment_mode) )))))) as payment_mode,IFNULL(id_transaction,'-') as id_transaction, if(payment_status = 1, 'Paid',if(payment_status = 2, 'Awaiting',if(payment_status = 5, 'Returned',if(payment_status = 6, 'Refund',if(payment_status = 7, 'Pending',if(payment_status = 3, 'Failed',if(payment_status = 4, 'Cancelled','')))))))
										  as payment_status ,sch.code , 
										  CONCAT(if(" . $showGCodeInAcNo . "=1,if(has_lucky_draw = 1,sg.group_code,sch.code),'') ,' ',ifnull(concat(ifnull(concat(sa.start_year,'-'),''),sa.scheme_acc_number),'Not Allocated')) as oldscheme_acc_number,
										  ref_no AS client_id, scheme_name,cs.currency_symbol,pay.payment_type,
											if(scheme_type = 0,'Amount Scheme',IF(scheme_type=1,'Weight Scheme','Amt to Wgt scheme')) as scheme_type,
											scheme_type as sch_type,
											sa.group_code as scheme_group_code,cs.has_lucky_draw,pay.saved_benefits
											FROM payment as pay
											left join scheme_account AS sa on sa.id_scheme_account = pay.id_scheme_account
											Left Join branch br  On (br.id_branch=sa.id_branch)
											left join scheme as sch on sch.id_scheme = sa.id_scheme
											Left Join scheme_group sg On (sa.group_code = sg.group_code )
											left join customer as cus on  cus.id_customer = sa.id_customer
											left join payment_mode pm on pay.payment_mode=pm.short_code
											join chit_settings cs
											WHERE sch.is_digi = " . $is_digi . " and pay.payment_status in (1,2) and cus.mobile='" . $mobile . " ' $scheme_filter and sa.active=1 ORDER By id_payment DESC");
        }
        //echo $this->db->_error_message();exit;
        if ($query_scheme->num_rows() > 0) {
            foreach ($query_scheme->result() as $row) {
                /*Add GST GST Amount = ( Original Cost * GST% ) / 100 Net Price = Original Cost + GST Amount
                Remove GST GST Amount = Original Cost - ( Original Cost * ( 100 / ( 100 + GST% ) ) ) Net Price = Original Cost - GST Amount */
                $paid_gst = 0.00;
                $add_gst = 0.00;
                if ($row->gst > 0) {
                    if ($row->gst_type == 1) {
                        $paid_gst = $row->payment_amount * ($row->gst / 100);
                        $add_gst = $paid_gst;
                    } else {
                        $paid_gst = $row->payment_amount - ($row->payment_amount * (100 / (100 + $row->gst)));
                    }
                }
                //chit number and receipt number based on display format settings starts...
                $accNumData = array(
                    'is_lucky_draw' => $row->is_lucky_draw,
                    'scheme_acc_number' => $row->scheme_acc_number,
                    'scheme_group_code' => $row->scheme_group_code,
                    'schemeaccNo_displayFrmt' => $row->schemeaccNo_displayFrmt,
                    'scheme_wise_acc_no' => $row->scheme_wise_acc_no,
                    'acc_branch' => $row->acc_branch,
                    'code' => $row->code,
                    'start_year' => $row->start_year,
                    'id_scheme_account' => $row->id_scheme_account
                );
                $rcptNumData = array(
                    'receipt_year' => $row->receipt_year,
                    'payment_branch' => $row->payment_branch,
                    'receiptNo_displayFrmt' => $row->receiptNo_displayFrmt,
                    'scheme_wise_receipt' => $row->scheme_wise_receipt,
                    'receipt_no' => $row->receipt_no,
                    'id_payment' => $row->id_payment,
                    'id_scheme_account' => $row->id_scheme_account
                );
                //ends
                //display value...
                /*	Amount Schemes:
                scheme_type = 0
                scheme_type = 3 && flexible_scheme_type = 1,6
                scheme_type = 3 && flexible_scheme_type = 2 && wgt_convert = 2
                scheme_type = 3 && flexible_scheme_type = 5 && wgt_store_as = 0
                Weight Schemes:
                scheme_type = 1 , 2
                scheme_type = 3 && flexible_scheme_type =3,4,7,8
                scheme_type = 3 && flexible_scheme_type = 2 && wgt_convert != 2
                scheme_type = 3 && flexible_scheme_type = 5 && wgt_store_as  = 1  */
                if ($row->sch_type == 0 || ($row->sch_type == 3 && ($row->flexible_sch_type == 1 || $row->flexible_sch_type == 6 || ($row->flexible_sch_type == 2 && $row->wgt_convert == 2) || ($row->flexible_sch_type == 5 && $row->wgt_store_as == 0)))) {
                    $display_value = 'Amount';
                } else {
                    $display_value = 'Weight';
                }
                $records[] = array(
					'saved_benefits' => formatMetalWeight($row->saved_benefits),
                    'display_value' => $display_value,
                    'sch_type' => $row->sch_type,
                    'flexible_sch_type' => $row->flexible_sch_type,
                    'old_metal_amount' => $row->old_metal_amount,
                    'id_payment' => $row->id_payment,
                    'date_payment' => $row->date_payment,
                    'receipt_no' => $this->getRcptNoFormat($rcptNumData),
                    'metal_rate' => $row->metal_rate,
                    'payment_amount' => $row->payment_amount,
                    'metal_weight' => formatMetalWeight($row->metal_weight),
                    'payment_mode' => $row->payment_mode,
                    'id_branch' => $row->id_branch,
                    'short_name' => $row->short_name,
                    'branch_name' => $row->branch_name,
                    'branch_settings' => $row->branch_settings,
                    'id_transaction' => $row->id_transaction,
                    'payment_status' => $row->payment_status,
                    //'scheme_acc_number' =>($row->chit_number==' Not Allocated' ?$this->config->item('default_acno_label'):$row->chit_number) ,
                    'scheme_acc_number' => $this->getAccNoFormat($accNumData),
                    'client_id' => $row->client_id,
                    'scheme_name' => $row->scheme_name,
                    'scheme_type' => $row->scheme_type,
                    'currency_symbol' => $row->currency_symbol,
                    'add_charges' => $row->add_charges,
                    'payment_type' => $row->payment_type,
                    'total_amt' => number_format(($row->total_amt + $add_gst), '0', '.', ''),
                    'charge_head' => $row->charge_head,
                    'gst' => $row->gst,
                    'gst_type' => $row->gst_type,
                    'paid_gst' => number_format($paid_gst, '2', '.', ''),
                    'pdf_url' => base_url() . 'index.php/mobile_api/generateInvoice/' . $row->id_payment . '/' . $row->id_scheme_account
                );
            }
        }
        return $records;
    }
    function get_offers()
    {
        $this->db->select('*');
        $this->db->where('active', 1);
        $this->db->where('type', 0);
        $offers = $this->db->get('offers');
        return $offers->result_array();
    }
    function get_banners()
    {
        $this->db->select('*');
        $this->db->where('active', 1);
        $this->db->where('type', 1);
        $offers = $this->db->get('offers');
        return $offers->result_array();
    }
    function get_offersAndBanners()
    {
        $this->db->select('*');
        $this->db->where('active', 1);
        $this->db->where('type', 1);
        //$this->db->or_where('type',0);
        $offers = $this->db->get('offers');
        return $offers->result_array();
    }
    function get_new_arrivals()
    {
        /* $this->db->select('*'); 
        $this->db->where('active',1); 
        $new_arrivals = $this->db->get('new_arrivals');	
        return $new_arrivals->result_array(); */
        $sql = "SELECT n.id_new_arrivals, n.name, n.new_arrivals_content, n.new_arrivals_img_path FROM new_arrivals n
    	            where active=1  and (show_rate=1 or show_rate=0)  and new_type=1 and  (DATEDIFF(date(n.expiry_date),date(curdate()))) >= 0";
        return $this->db->query($sql)->result_array();
    }
    //Price show based on sett//hh
    function get_newArrdetail($id)
    {
        /* $this->db->select('*'); 
        $this->db->where('id_new_arrivals',$id); 
        $new_arrivals = $this->db->get('new_arrivals');	
        return $new_arrivals->row_array(); */
        $sql = "SELECT * from new_arrivals where active=1 
				AND  new_type=1 and (show_rate=1 or show_rate=0) AND id_new_arrivals=" . $id;
        return $this->db->query($sql)->row_array();
    }
    // gift_artical_detail
    function get_gift_items()
    {
        $sql = "SELECT n.id_new_arrivals,n.price,if(Date_Format(n.date_add,'%Y-%m-%d') = CURDATE(),1,0) as is_new, n.name, n.new_arrivals_content, n.new_arrivals_img_path,show_rate,gift_type FROM new_arrivals n
		where active=1 and gift_type=1";
        return $this->db->query($sql)->result_array();
    }
    function get_giftItemDetail($id)
    {
        $sql = "SELECT * FROM new_arrivals where active=1 AND gift_type=1 AND id_new_arrivals=" . $id;
        return $this->db->query($sql)->row_array();
    }
    function get_giftItems()
    {
        $sql = "SELECT * FROM new_arrivals where active=1 AND gift_type=1";
        $gift = $this->db->query($sql);
        if ($gift->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    // gift_artical_detail
    function get_offerdetail($id)
    {
        $this->db->select('*');
        $this->db->where('id_offer', $id);
        $offers = $this->db->get('offers');
        return $offers->row_array();
    }
    function getClassification($id_scheme = '')
    {
        /*$this->db->select('*'); 
        $this->db->where('active','1'); 
        $offers = $this->db->get('sch_classify');	*/
        $CLSFY_IMG_PATH = base_url().'admin/assets/img/sch_classify/';
        if(empty($id_scheme)){
            $where = 'where sc.active = 1 and s.visible=1';
        }else{
            $where = 'where sc.active = 1 and s.visible=1 and s.id_scheme='.$id_scheme;
        }
        $sql = $this->db->query("SELECT sc.id_classification,sc.classification_name,sc.description,sc.active,concat('".$CLSFY_IMG_PATH."','',sc.logo) as logo FROM sch_classify sc
		left join scheme s on s.id_classification = sc.id_classification
		$where GROUP BY sc.id_classification");
        return $sql->result_array();
    }
    function get_cus_noti_settings($id_customer)
    {
        $sql = "Select notification
				From customer 
         		Where active=1 and id_customer ='$id_customer'";
        $result = $this->db->query($sql);
        return $result->row()->notification;
    }
    function update_cusnotisettings($noti)
    {
        $status = false;
        if ($noti['notification'] == 0) {
            /*echo 1;*/
            $this->db->where("id_customer", $noti['id_customer']);
            $stat = $this->db->delete("registered_devices");
            if ($stat) {
                $this->db->where('id_customer', $noti['id_customer']);
                $status = $this->db->update('customer', $noti);
            }
        } else {
            $this->db->where('id_customer', $noti['id_customer']);
            $status = $this->db->update('customer', $noti);
        }
        return $status;
    }
    /* Device registration functions */

    /**
     * Upsert ONE ROW PER DEVICE, keyed on customer + device.
     *
     * The device key is `uuid` when supplied, else `token`. Keying on
     * id_customer alone overwrote every row for a customer, so a second device
     * (an iPhone after an Android) silently killed the first; and the plain
     * insert path created duplicate rows for the same handset.
     *
     * @param  array $data token / uuid / device_type / id_customer
     * @return array array('status' => bool, 'insertID' => int, 'action' => string)
     */
    function upsert_deviceData($data)
    {
        $token       = isset($data['token']) ? trim((string) $data['token']) : '';
        $uuid        = isset($data['uuid']) ? trim((string) $data['uuid']) : '';
        $id_customer = isset($data['id_customer']) ? (int) $data['id_customer'] : 0;
        $now         = date('Y-m-d H:i:s');

        // A blank token can never be delivered to and pollutes every broadcast query.
        if ($token === '' || strtolower($token) === 'null' || $id_customer <= 0) {
            return array('status' => FALSE, 'insertID' => 0, 'action' => 'rejected');
        }

        $row = array(
            'token'       => $token,
            'uuid'        => $uuid,
            'device_type' => (isset($data['device_type']) && $data['device_type'] !== '') ? $data['device_type'] : NULL,
            'id_customer' => $id_customer,
            'updated_on'  => $now,
        );

        // A handed-down handset: the same token under a DIFFERENT customer must go.
        $this->db->where('token', $token);
        $this->db->where('id_customer !=', $id_customer);
        $this->db->delete('registered_devices');

        // Locate this customer's row for THIS device (uuid when we have one, else token).
        $this->db->where('id_customer', $id_customer);
        if ($uuid !== '') {
            $this->db->where('uuid', $uuid);
        } else {
            $this->db->where('token', $token);
        }
        $existing = $this->db->get('registered_devices');

        if ($existing->num_rows() > 0) {
            $id_device = $existing->row()->id_device_detail;
            $this->db->where('id_device_detail', $id_device);
            $status = $this->db->update('registered_devices', $row);
            return array('status' => $status, 'insertID' => $id_device, 'action' => 'updated');
        }

        $row['created_on'] = $now;
        $status = $this->db->insert('registered_devices', $row);
        return array('status' => $status, 'insertID' => ($status ? $this->db->insert_id() : 0), 'action' => 'inserted');
    }

    /**
     * Wrapper kept for existing call sites -- original return shape preserved.
     *
     * @param  array $data
     * @return array array('status' => bool, 'insertID' => int)
     */
    function insert_deviceData($data)
    {
        $res = $this->upsert_deviceData($data);
        return array('status' => $res['status'], 'insertID' => $res['insertID']);
    }

    /**
     * Wrapper kept for existing call sites -- original return shape (bool) preserved.
     *
     * @param  array $data
     * @param  int   $id id_customer
     * @return bool
     */
    function update_deviceData($data, $id)
    {
        $data['id_customer'] = $id;
        $res = $this->upsert_deviceData($data);
        return $res['status'];
    }
    //to get closed account by customer
    function get_closed_account($id_cus)
    {
        $sql = "select sc.show_ins_type,Ifnull(b.name,'-') as closed_branch,s.closing_amount,s.start_year,s.id_branch as acc_branch,cs.scheme_wise_acc_no,cs.schemeaccNo_displayFrmt,sc.is_lucky_draw,s.is_closed,sc.flexible_sch_type,s.id_scheme_account,s.scheme_acc_number,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.ref_no,s.account_name,Date_Format(s.start_date,'%d-%m-%Y') as start_date,Date_Format(s.closing_date,'%d-%m-%Y') as closing_date,s.closing_balance,sc.one_time_premium,
							 sc.max_weight , s.closing_weight,sc.scheme_name,sc.code,sc.scheme_type,sc.total_installments,sc.max_chance,sc.amount,c.mobile,s.group_code as scheme_group_code, cs.has_lucky_draw,s.is_closed,date_format(s.maturity_date,'%d-%m-%Y') as maturity_date,
                             sum(p.payment_amount) as max_amount,IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 and sc.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments
							from
							  scheme_account s
							left join customer c on (s.id_customer=c.id_customer)
                            left join payment p on (p.id_scheme_account=s.id_scheme_account and p.payment_status=1)
							left join scheme sc on (sc.id_scheme=s.id_scheme)
							left join branch b on (b.id_branch = s.Closing_id_branch)
							join chit_settings cs
							where ((s.active=0 and s.is_closed=1) or  (sc.maturity_days > 0 and DATE_FORMAT(s.maturity_date,'%Y%m%d')) > (DATE_FORMAT(CURRENT_DATE(),'%Y%m%d'))) and s.id_customer='$id_cus'  
							 group by s.id_scheme_account";
        //echo $sql;exit;
        $accounts = $this->db->query($sql);
        $records = $accounts->result_array();
        $result = array();
        foreach ($records as $record) {
            if ($record['is_closed'] == 1) {
                //chit number and receipt number based on display format settings starts...
                $accNumData = array(
                    'is_lucky_draw' => $record['is_lucky_draw'],
                    'scheme_acc_number' => $record['scheme_acc_number'],
                    'scheme_group_code' => $record['scheme_group_code'],
                    'schemeaccNo_displayFrmt' => $record['schemeaccNo_displayFrmt'],
                    'scheme_wise_acc_no' => $record['scheme_wise_acc_no'],
                    'acc_branch' => $record['acc_branch'],
                    'code' => $record['code'],
                    'start_year' => $record['start_year'],
                    'id_scheme_account' => $record['id_scheme_account']
                );
                //ends
                $result[] = array(
                    'closed_branch' => $record['closed_branch'],
                    'account_name' => $record['account_name'],
                    'amount' => $record['amount'],
                    'closing_balance' => formatMetalWeight($record['closing_balance']),
                    'closing_weight' => formatMetalWeight($record['closing_weight']),
                    'closing_date' => $record['closing_date'],
                    'code' => ($record['has_lucky_draw'] == 0 ? $record['code'] : $record['scheme_group_code']),
                    'id_scheme_account' => $record['id_scheme_account'],
                    'max_chance' => $record['max_chance'],
                    'mobile' => $record['mobile'],
                    'name' => $record['name'],
                    'ref_no' => $record['ref_no'],
                    'scheme_acc_number' => $this->getAccNoFormat($accNumData),
                    'scheme_name' => $record['scheme_name'],
                    'scheme_type' => $record['scheme_type'],
                    'start_date' => $record['start_date'],
                    'total_installments' => $record['total_installments'],
                    'flexible_sch_type' => $record['flexible_sch_type'],
                    'is_closed' => $record['is_closed'],
                    'maturity_date' => $record['maturity_date'],
                    "max_weight" => formatMetalWeight($record['max_weight']),
                    "one_time_premium" => $record['one_time_premium'],
                    "max_amount" => $record['max_amount'],
                    "paid_installments" => $record['paid_installments'],
                    "closing_amount" => $record['closing_amount'],
                    "show_ins_type"    => $record['show_ins_type'],
                );
            }
        }
        return $result;
    }
    function get_matured_account($id_cus)
    {
        $sql = "select sc.flexible_sch_type,s.id_scheme_account,s.scheme_acc_number,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.ref_no,s.account_name,Date_Format(s.start_date,'%d-%m-%Y') as start_date,Date_Format(s.closing_date,'%d-%m-%Y') as closing_date,s.closing_balance,
							 s.closing_weight,sc.scheme_name,sc.code,sc.scheme_type,sc.total_installments,sc.max_chance,sc.amount,c.mobile,s.group_code as scheme_group_code, cs.has_lucky_draw,s.is_closed,date_format(s.maturity_date,'%d-%m-%Y') as maturity_date
							from
							  scheme_account s
							left join customer c on (s.id_customer=c.id_customer)
							left join scheme sc on (sc.id_scheme=s.id_scheme)
							join chit_settings cs
							where s.active=0  and (DATE_FORMAT(s.maturity_date,'%Y%m%d')) < (DATE_FORMAT(CURRENT_DATE(),'%Y%m%d')) and s.id_customer='$id_cus'";
        //	echo $sql;exit;
        $accounts = $this->db->query($sql);
        $records = $accounts->result_array();
    }
    function clientEmail($id)
    {
        $resultset = $this->db->query("select email from customer where email='" . $id . "'");
        return ($resultset->num_rows() > 0 ? TRUE : FALSE);
    }
    function update_account($data, $id)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->update('scheme_account', $data);
        return $status;
    }
    function getActivecardBrands($type)
    {
        $this->db->select('*');
        $this->db->where('active', 1);
        $this->db->where('card_type', $type);
        $res = $this->db->get('card_brand');
        return $res->result_array();
    }
    function get_payment_gateway($idPGSettings)
    {
        $sql = "SELECT
			      id_gateway,
			      `key`,
			      salt,
			      api_url,param_1,m_code,
			      if(type=0,'Demo','Real') as type,
			      is_default
			 FROM gateway
			 WHERE is_default=1 and pg_settings_id=" . $idPGSettings;
        return $this->db->query($sql)->row_array();
    }
    // functions to register existing scheme with validation
    function isAccExist_sktm($data)
    {
        if ($this->session->userdata('branch_settings') == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = NULL;
        }
        if (isset($data['mobile'])) {
            $resultset = $this->db->query("select is_acc_registered,mobile_no from chit_customer where mobile_no='" . $data['mobile'] . "' " . ($id_branch != NULL ? ' and BRANCH =' . $id_branch : '') . " ");
        } else {
            $resultset = $this->db->query("select is_acc_registered,mobile_no from chit_customer where group_cus_no='" . $data['scheme_acc_number'] . "' and group_name='" . $data['group_name'] . "'");
        }
        if ($resultset->num_rows() > 0) {
            if ($resultset->row()->mobile_no != NULL && $resultset->row()->is_acc_registered == 0) {
                return array('status' => TRUE, 'mobile' => $resultset->row()->mobile_no, 'msg' => 'We will send OTP to mobile number associated with this account');
            } elseif ($resultset->row()->is_acc_registered == 1) {
                return array('status' => FALSE, 'msg' => 'Account already registered');
            } else
                return array('status' => FALSE, 'msg' => 'Update mobile number in our branch');
        } else {
            return array('status' => FALSE, 'msg' => 'Enter valid details');
        }
    }
    function insertExisAccData_sktm($data)
    {
        $acc_data = array();
        //$branch_code = $this->get_branchid($data['id_branch']);
        if (isset($data['mobile'])) {
            $resultset = $this->db->query("select * from chit_customer where mobile_no='" . $data['mobile'] . "'");
        } else {
            $resultset = $this->db->query("select * from chit_customer where group_cus_no='" . $data['scheme_acc_number'] . "' and group_name='" . $data['group_name'] . "'");
        }
        if ($resultset->num_rows() > 0) {
            $records = array();
            foreach ($resultset->result() as $row) {
                if ($this->session->userdata('branch_settings') == 1) {
                    $id_branch = $this->input->post('id_branch');
                } else {
                    $id_branch = NULL;
                }
                $data['scheme_type'] = $row->scheme_type;
                $data['amount'] = $row->AMOUNT;
                $data['group_name'] = $row->GROUP_NAME;
                $records = array(
                    'id_customer' => $data['id_customer'],
                    'id_scheme' => $this->getschId($data),
                    'scheme_acc_number' => $row->GROUP_CUS_NO,
                    'account_name' => $row->NAME,
                    'start_date' => $row->ENTRYDATE,
                    'is_new' => $row->is_new,
                    'date_add' => date("Y-m-d H:i:s"),
                    'is_registered' => 1,
                    'active' => 1,
                    'is_opening' => $row->is_opening,
                    'balance_amount' => $row->balance_amount,
                    'balance_weight' => $row->balance_weight,
                    'last_paid_weight' => $row->last_paid_weight,
                    'last_paid_chances' => $row->last_paid_chances,
                    'last_paid_date' => $row->last_paid_date,
                    'paid_installments' => $row->paid_installments,
                    'id_branch' => $row->id_branch,
                    'added_by' => 1
                );
                $addData = $this->get_cityData($row->city_name);
                $address = array(
                    'id_customer' => $data['id_customer'],
                    'address1' => $row->ADDRESS1,
                    'address2' => $row->ADDRESS2,
                    'address3' => $row->ADDRESS3,
                    'id_city' => $addData['id_city'],
                    'id_state' => $addData['id_state']
                );
                $sql = $this->db->query("select * from address where id_customer=" . $data['id_customer']);
                if ($sql->num_rows() > 0) {
                    $this->db->where('id_customer', $data['id_customer']);
                    $updateCus = $this->db->update('address', $address);
                } else {
                    $updateCus = $this->db->insert('address', $address);
                }
                if ($updateCus) {
                    $status = $this->db->insert('scheme_account', $records);
                    $acc_data[] = array(
                        'group_cus_no' => $row->GROUP_CUS_NO,
                        'group_name' => $row->GROUP_NAME,
                        'id_scheme_account' => $this->db->insert_id()
                    );
                } else {
                    return array('status' => FALSE, 'msg' => 'Unable to proceed your request,try again later or contact customer care');
                }
            }
            return array('status' => TRUE, 'data' => $acc_data);
        } else {
            return array('status' => FALSE, 'msg' => 'Unable to proceed your request,try again later or contact customer care');
        }
    }
    function getschId_sktm($data)
    {
        if ($data['scheme_type'] == 1) {
            $result = $this->db->query("SELECT id_scheme FROM scheme s where s.code='" . $data['group_name'] . "' and scheme_type='" . $data['scheme_type'] . "'");
        } else {
            $result = $this->db->query("SELECT id_scheme FROM scheme s where s.amount='" . $data['amount'] . "' and scheme_type='" . $data['scheme_type'] . "'");
        }
        if ($result->num_rows() > 0) {
            return $result->row()->id_scheme;
        } else {
            return '';
        }
    }
    function insert_paymentData_sktm($data)
    {
        foreach ($data as $rowData) {
            $resultset = $this->db->query("select * from chit_transaction c where group_cus_no='" . $rowData['group_cus_no'] . "' and group_name='" . $rowData['group_name'] . "'");
            if ($resultset->num_rows() > 0) {
                $records = array();
                $dues = 1;
                foreach ($resultset->result() as $row) {
                    $records = array(
                        'id_scheme_account' => $rowData['id_scheme_account'],
                        'metal_rate' => $row->GOLD_RATE,
                        'receipt_no' => $row->RECEIPT_NO,
                        'metal_weight' => $row->WEIGHT,
                        'payment_amount' => $row->AMOUNT,
                        'date_payment' => $row->RECEIPT_DATE,
                        'payment_status' => 1,
                        'date_add' => $row->RECEIPT_DATE,
                        'payment_mode' => ($row->is_free_payment == 1 ? 'FP' : $row->CASH_TYPE),
                        'dues' => $row->NO_OF_INSTAL,
                        'payment_type' => 'Manual',
                        'is_offline' => 1,
                        'due_type' => $row->due_type,
                        'added_by' => 0, //admin
                        'is_offline' => 1,
                        'discountAmt' => $row->discountAmt
                    );
                    $status = $this->db->insert('payment', $records);
                    if (!$status) {
                        return array('status' => FALSE);
                    }
                    $dues++;
                }
            } else {
                return array('status' => FALSE);
            }
        }
        return array('status' => $status);
    }
    function updateOfflineData($data)
    {
        foreach ($data as $rowData) {
            $arrdata = array("is_acc_registered" => 1);
            $this->db->where('group_name', $rowData['group_name']);
            $this->db->where('group_cus_no', $rowData['group_cus_no']);
            $status = $this->db->update('chit_customer', $arrdata);
            if (!$status) {
                return array('status' => FALSE);
            }
        }
        /*	echo $this->db->last_query()	
            echo $status;exit;*/
        return $status;
    }
    // END OF -- functions to register existing scheme with validation
    function get_branch()
    {
        $sql = "SELECT * FROM branch b  where show_to_all = 1 or show_to_all = 2 order by sort ";
        $branch = $this->db->query($sql)->result_array();
        return $branch;
    }
    function insChitwallet($id_wal_ac, $mobile, $id_customer)
    {
        $redeem_updated = [];
        $sql = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,iwt.bill_no,category_code,trans_type from inter_wallet_trans	 iwt
		LEFT JOIN  inter_wallet_trans_detail iwd on iwd.id_inter_wallet_trans = iwt.id_inter_wallet_trans
		where mobile=" . $mobile);
        if ($sql->num_rows() > 0) {
            foreach ($sql->result_array() as $record) {
                $b_date = date_create($record['bill_date']);
                $bill_date = date_format($b_date, "Y-m-d H:i:s");
                if ($record['actual_redeemed'] > 0) {
                    $debitdata = array(
                        'id_wallet_account' => $id_wal_ac,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_transaction' => $bill_date,
                        'transaction_type' => 1, // debit
                        'value' => $record['actual_redeemed'],
                        'ref_no' => $record['bill_no'] . '-' . $record['category_code'],
                        'description' => 'Debited for bill no ' . $record['bill_no'] . ' on ' . $record['bill_date'],
                    );
                    if (sizeof($redeem_updated) > 0) {
                        $alreadyUpdated = 0;
                        foreach ($redeem_updated as $k => $v) {
                            if ($k == $record['bill_no']) {
                                $alreadyUpdated = 1;
                            }
                        }
                        if ($alreadyUpdated == 0) {
                            $this->db->insert('wallet_transaction', $debitdata);
                            $redeem_updated[$record['bill_no']] = 1;
                        }
                    } else {
                        $this->db->insert('wallet_transaction', $debitdata);
                        $redeem_updated[$record['bill_no']] = 1;
                    }
                }
                if ($record['trans_type'] == 1 && $record['trans_points'] > 0) {
                    $data = array(
                        'id_wallet_account' => $id_wal_ac,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_transaction' => $bill_date,
                        'transaction_type' => ($record['trans_type'] == 1 ? 0 : 1),
                        'value' => $record['trans_points'],
                        'ref_no' => $record['bill_no'] . '-' . $record['category_code'],
                        'description' => 'Credited for bill no. ' . $record['bill_no'] . ' on ' . $record['bill_date'],
                    );
                    $status = $this->db->insert('wallet_transaction', $data);
                }
                // Update Customer ID in inter_wallet_account
                $this->db->where('mobile', $mobile);
                $this->db->update('inter_wallet_account', array('id_customer' => $id_customer));
            }
        }
        $sql->free_result();
        $tmp_redeem_updated_1 = [];
        // To insert data from temp table
        $tmp_table_1 = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,category_code,iwt.bill_no,trans_type 
		from inter_wallet_trans_tmp_2	 iwt
		LEFT JOIN  inter_walTransDetail_tmp_1 iwd on iwd.id_inter_wallet_trans = iwt.id_inter_waltrans_tmp
		where mobile=" . $mobile);
        if ($tmp_table_1->num_rows() > 0) {
            foreach ($tmp_table_1->result_array() as $record) {
                $b_date = date_create($record['bill_date']);
                $bill_date = date_format($b_date, "Y-m-d H:i:s");
                if ($record['actual_redeemed'] > 0) {
                    $debitdata = array(
                        'id_wallet_account' => $id_wal_ac,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_transaction' => $bill_date,
                        'transaction_type' => 1, // debit
                        'value' => $record['actual_redeemed'],
                        'ref_no' => $record['bill_no'] . '-' . $record['category_code'],
                        'description' => 'Debited for bill no ' . $record['bill_no'] . ' on ' . $record['bill_date'],
                    );
                    if (sizeof($tmp_redeem_updated_1) > 0) {
                        $talreadyUpdated = 0;
                        foreach ($tmp_redeem_updated_1 as $k => $v) {
                            if ($k == $record['bill_no']) {
                                $talreadyUpdated = 1;
                            }
                        }
                        if ($talreadyUpdated == 0) {
                            $this->db->insert('wallet_transaction', $debitdata);
                            $tmp_redeem_updated_1[$record['bill_no']] = 1;
                        }
                    } else {
                        $this->db->insert('wallet_transaction', $debitdata);
                        $tmp_redeem_updated_1[$record['bill_no']] = 1;
                    }
                }
                if ($record['trans_type'] == 1 && $record['trans_points'] > 0) {
                    $data = array(
                        'id_wallet_account' => $id_wal_ac,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_transaction' => $bill_date,
                        'transaction_type' => ($record['trans_type'] == 1 ? 0 : 1),
                        'value' => $record['trans_points'],
                        'ref_no' => $record['bill_no'] . '-' . $record['category_code'],
                        'description' => 'Credited for bill no. ' . $record['bill_no'] . ' on ' . $record['bill_date'],
                    );
                    $status = $this->db->insert('wallet_transaction', $data);
                }
                // Update Customer ID in inter_wallet_account
                $this->db->where('mobile', $mobile);
                $this->db->update('inter_wallet_account', array('id_customer' => $id_customer));
            }
        }
        $tmp_table_1->free_result();
        $tmp_redeem_updated = [];
        // To insert data from temp table
        $tmp_table = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,category_code,iwt.bill_no,trans_type from inter_wallet_trans_tmp	 iwt
		LEFT JOIN  inter_walTransDetail_tmp iwd on iwd.id_inter_wallet_trans = iwt.id_inter_waltrans_tmp
		where mobile=" . $mobile);
        if ($tmp_table->num_rows() > 0) {
            foreach ($tmp_table->result_array() as $record) {
                $b_date = date_create($record['bill_date']);
                $bill_date = date_format($b_date, "Y-m-d H:i:s");
                if ($record['actual_redeemed'] > 0) {
                    $debitdata = array(
                        'id_wallet_account' => $id_wal_ac,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_transaction' => $bill_date,
                        'transaction_type' => 1, // debit
                        'value' => $record['actual_redeemed'],
                        'ref_no' => $record['bill_no'] . '-' . $record['category_code'],
                        'description' => 'Debited for bill no ' . $record['bill_no'] . ' on ' . $record['bill_date'],
                    );
                    if (sizeof($tmp_redeem_updated) > 0) {
                        $talreadyUpdated = 0;
                        foreach ($tmp_redeem_updated as $k => $v) {
                            if ($k == $record['bill_no']) {
                                $talreadyUpdated = 1;
                            }
                        }
                        if ($talreadyUpdated == 0) {
                            $this->db->insert('wallet_transaction', $debitdata);
                            $tmp_redeem_updated[$record['bill_no']] = 1;
                        }
                    } else {
                        $this->db->insert('wallet_transaction', $debitdata);
                        $tmp_redeem_updated[$record['bill_no']] = 1;
                    }
                }
                if ($record['trans_type'] == 1 && $record['trans_points'] > 0) {
                    $data = array(
                        'id_wallet_account' => $id_wal_ac,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_transaction' => $bill_date,
                        'transaction_type' => ($record['trans_type'] == 1 ? 0 : 1),
                        'value' => $record['trans_points'],
                        'ref_no' => $record['bill_no'] . '-' . $record['category_code'],
                        'description' => 'Credited for bill no. ' . $record['bill_no'] . ' on ' . $record['bill_date'],
                    );
                    $status = $this->db->insert('wallet_transaction', $data);
                }
                // Update Customer ID in inter_wallet_account
                $this->db->where('mobile', $mobile);
                $this->db->update('inter_wallet_account', array('id_customer' => $id_customer));
            }
        }
        return TRUE;
    }
    function get_exisRegReq($id_cus)
    {
        $query = $this->db->query("SELECT if(remark = '','-',remark) as remark,id_customer,
        if(cs.has_lucky_draw=1,concat(ifnull(sg.group_code,''),' ',IFNULL(schReg.scheme_acc_number,'Not Allocated')),concat(ifnull(sch.code,''),' ',IFNULL(schReg.scheme_acc_number,'Not Allocated')))as scheme_acc_number,
		sch.code as group_code,
        schReg.ac_name,DATE_FORMAT(schReg.date_add,'%d-%m-%Y') AS date_add,schReg.id_scheme,schReg.ac_name AS ac_name,if(schReg.status=1,'A/c Created',if(schReg.status=2,'Rejected',if(schReg.status=0,'processing','')))as status 
        from scheme_reg_request schReg
        LEFT JOIN scheme AS sch ON sch.id_scheme = schReg.id_scheme	
        LEFT JOIN scheme_group as sg on  sg.id_scheme_group=schReg.id_scheme_group	
        join chit_settings cs
        WHERE id_customer = " . $id_cus . " order by id_reg_request desc");
        //LEFT JOIN branch AS br ON br.id_branch = schReg.id_branch	
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }
    //scheme_ acc_ no setting//
    function accno_generatorset()
    {
        $resultset = $this->db->query("SELECT c.schemeacc_no_set FROM chit_settings c");
        if ($resultset->row()->schemeacc_no_set == 0) {
            return array('status' => TRUE, 'schemeacc_no_set' => $resultset->row()->schemeacc_no_set);
        } else {
            return array('status' => FALSE, 'schemeacc_no_set' => $resultset->row()->schemeacc_no_set);
        }
    }
    function getAllPG()
    {
        $record = array();
        $sql = "Select active,creditCard, debitCard,description,id_pg_settings,is_primary_gateway,netBanking,pg_code,pg_icon,pg_name,saveCard,sort from payment_gateway order by sort asc";
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $file = base_url() . 'admin/assets/img/gateway/' . $row['pg_icon'];
                $img_path = ($row['pg_icon'] != null ? (file_exists('admin/assets/img/gateway/' . $row['pg_icon']) ? $file : null) : null);
                $record[] = array('pg_name' => $row['pg_name'], 'pg_code' => $row['pg_code'], 'netBanking' => $row['netBanking'], 'is_primary_gateway' => $row['is_primary_gateway'], 'active' => $row['active'], 'description' => $row['description'], 'id_pg_settings' => $row['id_pg_settings'], 'saveCard' => $row['saveCard'], 'creditCard' => $row['creditCard'], 'debitCard' => $row['debitCard'], 'pg_icon' => $img_path);
            }
        }
        return $record;
    }
    function forgetUser($mobile)
    {
        $sql = "select firstname ,email from customer where mobile=" . $mobile;
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    function getPopup()
    {
        $this->db->select('offer_img_path');
        $this->db->where('type', 2);
        $this->db->where('active', 1);
        $res = $this->db->get('offers');
        return $res->row('offer_img_path');
    }
    /* function getNotifications($id_cus,$id_sent_noti)
     {
         $data = array();
         $sql="select id_sent_noti,noti_service,sn.id_customer,noti_title,noti_subtitle,noti_content,targetUrl,noti_img,sn.date_add,c.date_add from sent_notifications  sn
         Left join customer c on c.id_customer=".$id_cus." 
         where unix_timestamp(sn.date_add) >= unix_timestamp(c.date_add) and (sn.id_customer=0 or sn.id_customer=".$id_cus.") ".($id_sent_noti ==0 ? ' order by id_sent_noti desc limit 10':' and id_sent_noti < '.$id_sent_noti.' order by id_sent_noti desc limit 10');    
          //echo $sql;exit;
          $result = $this->db->query($sql);	 
          if($result->num_rows() > 0){
            $data =  $result->result_array();
          } 
          return $data;
     }  */
    function getNotifications($id_cus, $id_sent_noti)
    {
        $data = array();
        $sql = "select id_sent_noti,noti_service,sn.id_customer,noti_title,noti_subtitle,noti_content,targetUrl,noti_img,sn.date_add,c.date_add from sent_notifications  sn
		Left join customer c on c.id_customer=" . $id_cus . " 
		where unix_timestamp(sn.date_add) >= unix_timestamp(c.date_add) and (sn.id_customer=0 or sn.id_customer=" . $id_cus . ") 
		and NOT EXISTS (
                SELECT dn.id_notification
                FROM delete_notifications dn
                WHERE dn.id_notification = sn.id_sent_noti and dn.id_customer = c.id_customer
            )
		" . ($id_sent_noti == 0 ? ' order by id_sent_noti desc limit 10' : ' and id_sent_noti < ' . $id_sent_noti . ' order by id_sent_noti desc limit 10');
        ///	echo $sql;exit;
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            $data = $result->result_array();
        }
        return $data;
    }
    function is_branchwise_cus_reg()
    {
        $sql = "select cs.is_branchwise_cus_reg,cs.branchWiseLogin,cs.branch_settings from chit_settings cs";
        $records = $this->db->query($sql)->row_array();
        return $records;
    }
    function getBranchGateways($branch_id, $id_metal = '')
    {
        //$sql="SELECT * from gateway_branchwise where is_default=1 and id_branch=".$branch_id."";
        $data = $this->get_costcenter();
        $cost_center = isset($data['cost_center']) ? $data['cost_center'] : 1;

        // Branch-wise filter: apply for cost_center 2 or 3
        $branch_filter = ($branch_id != '' && ($cost_center == 2 || $cost_center == 3))
            ? " AND id_branch=" . (int)$branch_id
            : '';

        // Commodity filter: apply for cost_center 4 (Multi-Cost Center) when id_metal is provided
        $metal_filter = ($cost_center == 4 && $id_metal != '')
            ? " AND id_metal=" . (int)$id_metal
            : '';

        $sql = "SELECT id_pg,id_branch,pg_name,pg_code,param_1,param_2,param_3,param_4,api_url,type,type,pg_icon,pg_icon,saveCard,saveCard,debitCard,netBanking,creditCard,date_add,is_primary_gateway,description,active
                FROM gateway
                WHERE active=1 AND is_default=1" . $branch_filter . $metal_filter . " ";

        //print_r($sql);exit;
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $file = base_url() . 'admin/assets/img/gateway/' . $row['pg_icon'];
                $img_path = ($row['pg_icon'] != null ? (file_exists('admin/assets/img/gateway/' . $row['pg_icon']) ? $file : null) : null);
                $record[] = array(
                    'pg_name'            => $row['pg_name'],
                    'pg_code'            => $row['pg_code'],
                    'netBanking'         => $row['netBanking'],
                    'is_primary_gateway' => $row['is_primary_gateway'],
                    'active'             => $row['active'],
                    'description'        => $row['description'],
                    'id_pg'              => $row['id_pg'],
                    'saveCard'           => $row['saveCard'],
                    'creditCard'         => $row['creditCard'],
                    'debitCard'          => $row['debitCard'],
                    'pg_icon'            => $img_path,
                    'id_metal'           => isset($row['id_metal']) ? $row['id_metal'] : null,
                );
            }
        }
        //echo"<pre>"; print_r($record);exit; echo"<pre>";
        return isset($record) ? $record : array();
    }
    function getBranchGatewayData($branch_id, $pg_id)
    {
        $data = $this->get_costcenter();
        $sql = "SELECT param_1,param_2,param_3,param_4,pg_code,api_url,pg_name,type from gateway where 
   		id_pg=" . $pg_id . " " . ($branch_id != '' && $data['cost_center'] != 1 ? "and id_branch=" . $branch_id . "" : '') . "";
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function get_costcenter()
    {
        $sql = "SELECT * from  chit_settings";
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function get_products($id_product)
    {
        $sql = "select * from products where id_product=" . $id_product . "";
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function insProduct_enquiry($data)
    {
        $status = $this->db->insert('product_enquiry', $data);
        return array('status' => $status, 'insertID' => $this->db->insert_id());
    }
    function getModules()
    {
        $sql2 = "SELECT  id_module,m_code,m_app,m_active from modules ";
        $module = $this->db->query($sql2);
        return $module->result_array();
    }
    function get_giftsAccwise($id_customer)
    {
        $file_path = base_url() . "admin/assets/img/sch_image";
        $sql = "select if(s.logo=null,s.logo ,concat('" . $file_path . "','/',s.logo) ) as logo,s.scheme_name,sa.id_scheme_account,if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),'  ',ifnull(sa.scheme_acc_number,'Not Allocated')),concat(s.code,'  ',IFNULL(sa.scheme_acc_number,'Not Allocated')))as scheme_acc_number,(select count(id_gift_issued) from gift_issued gi where gi.id_scheme_account=sa.id_scheme_account) as gift_issued 
		from  scheme_account sa
left join gift_issued g on sa.id_scheme_account = g.id_scheme_account
left join scheme s on s.id_scheme = sa.id_scheme 
left join customer c on c.id_customer = c.id_customer
join chit_settings cs
where sa.id_customer=" . $id_customer . " GROUP by sa.id_scheme_account";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    function get_giftsListAccwise($id_scheme_ac)
    {
        $sql = "select * from gift_issued  where id_scheme_account=" . $id_scheme_ac;
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    function genTicketNo()
    {
        $sql = "select max(ticket_no) as last_ticket_no from cust_enquiry";
        $result = $this->db->query($sql);
        $last_ticket_no = $result->row('last_ticket_no');
        if ($last_ticket_no == NULL) {
            $ticket_no = 1;
            return str_pad($ticket_no, 6, '0', STR_PAD_LEFT);
        } else {
            $ticket_no = $last_ticket_no + 1;
            return str_pad($ticket_no, 6, '0', STR_PAD_LEFT);
        }
    }
    // User complaint listing //hh
    function get_custComplaints($id_cus)
    {
        $sql = "select  comments,id_enquiry,date_format(date_add,'%d-%m-%Y') as date_add,ticket_no, IF(status = 0, 'blue', IF(status = 1, 'yellow', IF(status = 2, 'green', 'stable')))  as color,IF(status = 0, 'Open', IF(status = 1, 'In Follow Up', IF(status = 2, 'Closed', ''))) as status FROM cust_enquiry 
       where type=3 and id_customer=" . $id_cus . "  order by date_add DESC";
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function get_custComplaintStatus($id)  //last added complaint showed at the top/HH
    {
        $sql = "select id_cusenq_status, IF(enq_status = 0, 'blue', IF(enq_status = 1, 'yellow', IF(enq_status = 2, 'green', 'stable')))  as color,IF(enq_status = 0, 'Open', IF(enq_status = 1, 'In Follow Up', IF(enq_status = 2, 'Closed', '')))  as status, `enq_description`, date_format(ces.date_add,'%d-%m-%Y') as date_add
       FROM cust_enquiry_status ces 
       Where id_enquiry=" . $id . " order by date_add DESC";
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    // User complaint listing //hh
    function get_custDTHRequests($id_cus)
    {
        $sql = "select  comments,type,id_enquiry,date_format(date_add,'%d-%m-%Y') as date_add,ticket_no, IF(status = 0, 'blue', IF(status = 1, 'yellow', IF(status = 2, 'green', 'stable')))  as color,IF(status = 0, 'Open', IF(status = 1, 'In Follow Up', IF(status = 2, 'Closed', ''))) as status FROM cust_enquiry where (type=5 or type=6) and id_customer=" . $id_cus;
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function ej_rate_history($id_branch, $from_date, $To_date, $branch_settings, $type)
    {
        if ($type == 'lmx') {
            if ($branch_settings == 1 && $id_branch != null) {
                $sql = "select date_format(updatetime,'%d-%m-%Y %H:%i:%s') as updatetime,goldrate_22ct,silverrate_1gm,platinum_1g from metal_rates m
    	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
    	   		where br.id_branch=" . $id_branch . " and date(updatetime) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($To_date)) . "' order by  br.id_metalrate desc ";
            }
            /*	else if($branch_settings==1)
                {
                    $sql="select * from metal_rates 
                    left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
                    where br.status=1 and date(updatetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."'";
                }*/ else if ($branch_settings == 1) {
                $sql = "SELECT  platinum_1g,m.goldrate_22ct,m.silverrate_1gm,Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime
				FROM metal_rates m 
    			where date(updatetime) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($To_date)) . "'";
                //print_r($sql);exit;
            } else {
                return array();
            }
        } else {
            if ($id_branch != null) {
                $sql = "select TRANSDATE as updatetime,if(m.Metaltype='Gold',m.RATE,'0.00')as goldrate_22ct,if(m.Metaltype='Silver',m.Rate,'0.00')as silverrate_1gm,
	                     if(m.Metaltype='Platinum',m.RATE,'0.00')as platinum_1g 
	                     from ej_metalratehistory m
	                     where (date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($To_date)) . "' " . ($id_branch != '' ? " and m.id_branch=" . $id_branch . "" : '') . " ORDER by TRANSDATE ASC";
            } else {
                return array();
            }
            // echo $sql;exit;
        }
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    function get_rate_history($id_branch, $from, $to, $branch_settings)
    {
        $result = [];
        if ($branch_settings == 0) {
            $sql = "SELECT  platinum_1g,m.goldrate_22ct,m.silverrate_1gm,Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime
				FROM metal_rates m 
				WHERE (date(updatetime) between '" . $from . "' and '" . $to . "') ORDER by id_metalrates desc";
        } else if (!empty($id_branch) && $id_branch != 0 && $branch_settings == 1) {
            $sql = "SELECT  platinum_1g,m.goldrate_22ct,m.silverrate_1gm,Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime
				FROM metal_rates m
				LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
				WHERE (date(m.updatetime) between '" . $from . "' and '" . $to . "') and  br.id_branch=" . $id_branch . " ORDER by br.id_metalrate desc";
        } else {
            return $result;
        }
        $rate = $this->db->query($sql);
        $result = $rate->result_array();
        return $result;
    }
    //kyc Insert & Read Api// hh  
    function insert_kyc($data)
    {
        $status = $this->db->insert('kyc', $data);
        return array('status' => $status, 'insertID' => $this->db->insert_id());
    }
    function get_kycstatus($id_cus)
    {
        $sql = "select bank_branch,name,IF(status = 0,'Pending',IF(status = 1,'In Progress',IF(status = 2,'Verified',IF(status = 3,'Rejected','')))) as status,IF(status = 0,'medium',IF(status = 1,'warning',IF(status = 2,'success',IF(status = 3,'danger','')))) as color,dob,id_kyc,date_format(k.date_add,'%d-%m-%Y') as date_add,number, 
       bank_ifsc,IF(kyc_type = 1, 'Bank Account', IF(kyc_type = 2, 'PAN Card', IF(kyc_type = 3, 'Aadhaar', ''))) as kyc_type_name,type,
       kyc_type,IF(emp_verified_by = 0, 'Pending', IF(emp_verified_by = 1, 'In Progress', IF(emp_verified_by = 2, 'Verified',IF(emp_verified_by = 3, 'Rejected', '')))) as emp_verified_by,IF(kyc_type=3,'https://uidai.gov.in/','') as aadhaarlink,k.nominee_mobile,
       k.status as kyc_status,
       k.img_url as front_img_url, k.img_url as front_img, k.img_url as aadhar_img_front_url, k.img_url as pan_front_img_url,
       k.back_img_url as back_img_url, k.back_img_url as back_img, k.back_img_url as aadhar_img_back_url, k.back_img_url as pan_back_img_url,
       k.document_url, k.document_url as document, k.document_url as aadhar_doc_url, k.document_url as pan_doc_url
       FROM kyc k
       left join customer c on c.id_customer=k.id_customer
       where k.id_customer=" . $id_cus;
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    //kyc Insert & Read Api// hh    
    // User DTH listing And Status //hh
    function get_custDth($id_cus)
    {
        $sql = "select  comments,id_enquiry,date_format(date_add,'%d-%m-%Y') as date_add,title,type,IF(status = 0, 'blue', IF(status = 1, 'yellow', IF(status = 2, 'green', 'stable')))  as color,IF(status = 0, 'Open', IF(status = 1, 'In Follow Up', IF(status = 2, 'Closed', ''))) as status FROM cust_enquiry where (type=5 or type=6) and id_customer=" . $id_cus;
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function get_custDthStatus($id)
    {
        $sql = "select  IF(enq_status = 0, 'blue', IF(enq_status = 1, 'yellow', IF(enq_status = 2, 'green', 'stable')))  as color,IF(enq_status = 0, 'Open', IF(enq_status = 1, 'In Follow Up', IF(enq_status = 2, 'Closed', '')))  as status, `enq_description`, date_format(ces.date_add,'%d-%m-%Y') as date_add
       FROM cust_enquiry_status ces 
       Where id_enquiry=" . $id;
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    // User DTH listing And Status //hh   
    // For Store locator listing
    function branch_details()
    {
        $branches = array();
        $sql = " Select  cs.mob_code as call_prefix,b.id_branch,b.id_branch, b.name, address1, address2, b.phone, b.mobile,b.email,b.phone as whatsapp_no,logo,map_url,note,
        b.cusromercare, b.pincode, b.metal_rate_type,is_ho,
        ct.name as city,s.name as state,cy.name as country
				from branch b
					left join country cy on (b.id_country=cy.id_country)
					left join state s on (b.id_state=s.id_state)
					left join city ct on (b.id_city=ct.id_city)
					join chit_settings cs
        where b.active=1 and (show_to_all = 1 or show_to_all = 2)";
        $result = $this->db->query($sql);
        //echo base_url().self::BRN_IMG_PATH;exit;
        if ($result->num_rows() > 0) {
            $branches = $result->result_array();
            foreach ($branches as $key => $val) {
                $file = base_url() . 'admin/' . self::BRN_IMG_PATH . '/' . $val['logo'];
                $img_path = ($val['logo'] != null ? (file_exists($file) ? $file : $file) : null);
                $branches[$key]['img'] = $img_path;
            }
        }
        return $branches;
    }
    function insert_sch_enquiry($data)
    {
        $status = $this->db->insert(self::SCH_ENQ, $data);
        if ($status) {
            return array('status' => $status, 'insertID' => $this->db->insert_id(), 'message' => 'Enquiry Submitted Successfully');
        } else {
            return array('status' => 'false', 'message' => 'Unable to Proceed your Request');
        }
    }
    function getGold22ct($is_branchwise_rate, $id_branch)
    {
        if ($is_branchwise_rate == 1 && $id_branch != '' && $id_branch != 0) {
            $sql = "SELECT m.goldrate_22ct FROM metal_rates m 
			LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
				  " . ($id_branch != '' ? " WHERE br.id_branch=" . $id_branch . "" : '') . " ORDER by br.id_metalrate desc LIMIT 1";
        } else {
            $sql = "SELECT m.goldrate_22ct FROM metal_rates m 
			WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m )";
        }
        //print_r($sql);exit;
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            return $data->row()->goldrate_22ct;
        } else {
            return 0;
        }
    }
    function updFixedRate($data, $id_sch_ac)
    {
        $this->db->where('id_scheme_account', $id_sch_ac);
        $status = $this->db->update("scheme_account", $data);
        //	print_r($this->db->last_query());exit;
        return $status; //($this->db->affected_rows() >0 ?TRUE:FALSE);
    }
    function getMyGifts($id_cus, $mobile)
    {
        $sql = "SELECT gc.id_gift_card,code,amount,DATE_FORMAT(valid_from,'%d-%m-%Y') as valid_from,DATE_FORMAT(valid_to,'%d %M %Y') as valid_to,
       trans_to_mobile, gct.message as description,if(ifnull(trans_from,0) > 0 ,1,0) as is_shared, if(purchased_by=" . $id_cus . ",1,0) as purchased, purchased_by,
       trans_from,
       if(redeem_type = 1,'Redeem At Store','') as redeem_at
       FROM `gift_card` gc 
       LEFT JOIN gift_card_trans gct on (gct.id_gift_card = gc.id_gift_card) 
       WHERE gct.trans_to_mobile = " . $mobile . " or (gc.purchased_by=" . $id_cus . " and trans_to_mobile is NULL)";
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function getGiftedCards($id_cus, $mobile)
    {
        $sql = "SELECT gc.id_gift_card,code,amount,DATE_FORMAT(valid_from,'%d-%m-%Y') as valid_from,DATE_FORMAT(valid_to,'%d %M %Y') as valid_to,
       trans_to_mobile,trans_to_email,gct.message as description,if(purchased_by=" . $id_cus . ",1,0) as purchased,
       if(redeem_type = 1,'Redeem At Store','') as redeem_at
       FROM `gift_card` gc 
       LEFT JOIN gift_card_trans gct on (gct.id_gift_card = gc.id_gift_card) 
       WHERE gct.trans_from=" . $id_cus;
        return $this->db->query($sql)->result_array();
    }
    function getGiftcardstatus($id_cus)
    {
        $result = $this->db->query("SELECT amount,payment_mode,payment_status from gift_card_payment where id_customer = " . $id_cus);
        foreach ($result->result_array() as $row) {
            $records[] = array(
                'amount' => $row['amount'],
                'payment_mode' => $row['payment_mode'],
                'payment_status' => $row['payment_status']
            );
        }
        ;
        return $records;
        //print_r($this->db->last_query($sql)->res
    }
    function checkCusKYC($id_cus)
    {
        $sql = $this->db->query("SELECT kyc_type from kyc where status=2 and id_customer = " . $id_cus);
        if ($sql->num_rows() > 0) {
            $res = $sql->result_array();
            $bank = FALSE;
            $anyoneKYC = FALSE;
            foreach ($res as $r) {
                if ($r['kyc_type'] == 1) {
                    $bank = TRUE;
                } else if ($r['kyc_type'] == 2 || $r['kyc_type'] == 3 || $r['kyc_type'] == 4) {
                    $anyoneKYC = TRUE;
                }
                // echo $bank;
            }
            if ($bank == TRUE && $anyoneKYC == TRUE) {
                $this->db->where('id_customer', $id_cus);
                $data = array("kyc_status" => 1);
                $status = $this->db->update("customer", $data);
            } else {
                $status = FALSE;
            }
            if ($status) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    function get_terms_and_conditions()
    {
        $sql = "select * from general";
        return $this->db->query($sql)->result_array();
    }
    //Branch Wise Show Scheme Classify //
    /* function getVisClass($id_branch)
     {
     $data = $this->get_costcenter();
     $CLSFY_IMG_PATH = base_url().'admin/assets/img/sch_classify/';
     $sql = "SELECT  id_classification, classification_name, description,concat('".$CLSFY_IMG_PATH."','',sc.logo) as logo
     FROM sch_classify sc
     WHERE EXISTS (SELECT id_classification
     FROM scheme s ".($data['branchwise_scheme']==1 ? 'left join scheme_branch sb on sb.id_scheme=s.id_scheme' :'')."
     WHERE ".($id_branch!='' && $data['branchwise_scheme']==1 ?' sb.id_branch='.$id_branch  :'')." AND sc.id_classification = s.id_classification and s.active=1 and s.visible=1) ";
     $classifications = $this->db->query($sql)->result_array();
     return $classifications;
    // echo $sql;exit;
     return $this->db->query($sql)->result_array();
     }*/
    function getMatchingVillage($char)
    {
        $sql = "Select * from village where village_name Like '$char%' ";
        //print_r($sql);exit;
        return $result = $this->db->query($sql)->result_array();
    }
    function isRateFixed($id_sch_ac)
    {
        $sql = $this->db->query("Select firstPayment_amt,fixed_wgt from scheme_account where id_scheme_account=" . $id_sch_ac);
        $res = array(
            "status" => $sql->row()->fixed_wgt > 0 ? 1 : 0,
            "firstPayment_amt" => $sql->row()->firstPayment_amt
        );
        return $res;
    }
    function getCusData($id)
    {
        $sql = "Select * from customer where id_customer = " . $id;
        return $result = $this->db->query($sql)->row_array();
    }
    function getCusBranch($id_customer)
    {
        $sql = "select id_branch from customer where id_customer=" . $id_customer;
        $result = $this->db->query($sql);
        if ($result->num_rows() == 1) {
            return $result->row()->id_branch;
        } else {
            return NULL;
        }
    }
    function get_ratesByJoin($start_date)
    {
        $today = date('Y-m-d H:i:s');
        $sql = ("SELECT mjdmagoldrate_22ct,goldrate_22ct,goldrate_24ct,silverrate_1gm,silverrate_1kg,mjdmasilverrate_1gm,platinum_1g,
                Date_format(updatetime,'%d-%m%-%Y %h:%i %p')as updatetime FROM `metal_rates` WHERE date(add_date) BETWEEN '" . $start_date . "' AND '" . $today . "' ORDER BY goldrate_22ct ASC LIMIT 1");
        return $this->db->query($sql)->row_array();
    }
    function get_customer_order($id_customer)
    {
        $responseData = array("order_details" => array(), "payment_details" => array());
        $sql = $this->db->query("SELECT d.id_orderdetails,c.id_customerorder,c.order_no,date_format(c.order_date,'%d-%m-%Y') as order_date,pro.product_name,des.design_name,subDes.sub_design_name,IFNULL(d.totalitems,0) as order_pcs,
        IFNULL(d.weight,0) as order_weight,m.order_status,c.order_to,m.color,
        IFNULL(t.net_wt,0) as delivered_weight,'' as image,IFNULL(d.description,'') as description
        FROM customerorderdetails d 
        LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
        LEFT JOIN ret_product_master pro ON pro.pro_id = d.id_product
        LEFT JOIN ret_design_master des ON des.design_no = d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = d.id_sub_design
        LEFT JOIN order_status_message m ON m.id_order_msg  = d.orderstatus
        LEFT JOIN ret_taging t ON t.id_orderdetails = d.id_orderdetails
        WHERE order_type = 2 and c.order_to = " . $id_customer . " 
        ORDER BY d.id_orderdetails DESC");
        //print_r($this->db->last_query());exit;
        $result1 = $sql->result_array();
        foreach ($result1 as $items) {
            $images = $this->get_order_images($items['id_orderdetails']);
            if (sizeof($images) > 0) {
                foreach ($images as $img) {
                    $file = base_url() . self::CUS_ORD_IMG_PATH . '/' . $img['image'];
                    $items['images'][] = $file;
                }
                $items['image'] = base_url() . self::CUS_ORD_IMG_PATH . '/' . $images[0]['image'];
            } else {
                $items['images'] = [];
            }
            $responseData['order_details'][] = $items;
        }
        $payment_sql = $this->db->query("SELECT b.bill_no,a.advance_amount,date_format(b.bill_date,'%d-%m-%Y') as bill_date,a.id_customerorder,c.order_to
        FROM ret_billing b 
        LEFT JOIN ret_billing_advance a ON a.bill_id = b.bill_id
        LEFT JOIN customerorder c ON c.id_customerorder = a.id_customerorder
        WHERE b.bill_status = 1 AND a.id_customerorder IS NOT NULL AND b.bill_type = 5 AND c.order_to = " . $id_customer . "");
        //print_r($this->db->last_query());exit;
        $result2 = $payment_sql->result_array();
        foreach ($result2 as $items) {
            $responseData['payment_details'][] = $items;
        }
        return $responseData;
    }
    function get_order_images($id_orderdetails)
    {
        $sql = $this->db->query("SELECT * FROM `customer_order_image` where id_orderdetails=" . $id_orderdetails . "");
        return $sql->result_array();
    }
    function insertData($data, $table)
    {
        $insert_flag = 0;
        $insert_flag = $this->db->insert($table, $data);
        //	print_r($this->db->last_query());die;
        return ($insert_flag == 1 ? $this->db->insert_id() : 0);
    }
    function verifyAgentCode($agent_code)
    {
        $status = $this->db->query("SELECT id_agent,agent_code FROM agent WHERE agent_code='" . $agent_code . "'");
        if ($status->num_rows() > 0) {
            return array("status" => TRUE, 'agent' => $status->row_array());
        }
        return array("status" => FALSE);
    }
    function getGiftData()
    {
        $sql = $this->db->query("SELECT id_gift,gift_name from gifts where status=1");
        return $sql->result_array();
    }
    function getdigidata($data)
    {
        $sql = $this->db->query("SELECT s.is_digi,sa.id_scheme_account,sa.id_scheme,IF(DATEDIFF(CURDATE(),date(sa.start_date)) > s.chit_detail_days,0,1) as show_chit_wallet,
		s.restrict_payment,DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference,CURDATE() as cur_date, 
		DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,COUNT(p.id_payment) as pay_count,sa.id_branch as joined_branch,s.gst_type
                               FROM scheme_account sa
                                LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                                 LEFT JOIN payment p ON (p.id_scheme_account = sa.id_scheme_account)
                               WHERE sa.id_customer =" . $data['id_customer'] . "  AND s.is_digi = 1 AND sa.is_closed = 0 AND sa.active = 1
                                GROUP BY p.id_scheme_account");
        $res = $sql->row_array();
        $sql1 = $this->db->query("SELECT s.id_scheme 
                                FROM scheme s
                                WHERE is_digi = 1");
        $is_digi = $sql1->row_array();
        if ($sql1->num_rows() > 0) {
            $sch = $this->get_scheme($is_digi['id_scheme'], $data['id_customer']);
        }
        /* if($sql->num_rows() == 0 || $sql1->num_rows() == 0){
            //get scheme details
            return array('status' => FALSE,'scheme' => $sch, 'chit' => []);
        }else{
           //get scheme account details
            $sch_acc = $this->chit_scheme_detail($res['id_scheme_account']);
           return array('status' => TRUE, 'scheme' => $sch, 'chit' => $sch_acc);
        }  
        */
        if ($sql->num_rows() == 0) {
            //get scheme details
            return array('status' => FALSE, 'scheme' => $sch, 'chit' => [], 'digiwallet' => []);
        } else {
            //get scheme account details
            $sch_acc = $this->chit_scheme_detail($res['id_scheme_account']);
            // print_r($sch_acc);exit;
            if ($sch_acc['chit']['show_chit_wallet'] == 1) {
                $sql_int = $this->db->query("SELECT interest_type,interest_value, IF(interest_type = 0,'%','INR') as int_symbol 
				FROM `scheme_benefit_deduct_settings` 
				" . ($res['id_scheme'] != '' && $res['id_scheme'] != null ?
                    ($res['restrict_payment'] = 1 ? 'WHERE (' . $res['date_difference'] . ' BETWEEN installment_from AND installment_to) AND id_scheme=' . $res['id_scheme'] : 'WHERE id_scheme=' . $res['id_scheme'])
                    : ('WHERE id_scheme=' . $res['id_scheme'])) . "
    			");
                $int = $sql_int->row_array();
                $sql_debit = $this->db->query("SELECT deduction_type ,deduction_value,installment_to FROM `scheme_debit_settings` 
	            where " . ($res['is_digi'] == 1 ? "(" . $res['date_difference'] . " BETWEEN installment_from AND installment_to)" : "(installment_from =" . $res['paid_installments'] . " or installment_to =" . $res['paid_installments'] . ")") . "
				and id_scheme=" . $res['id_scheme']);
                $debit = $sql_debit->row_array();
                //print_r($this->db->last_query());exit;
                if ($sql_int->num_rows > 0) {
                    $sql_tot = $this->db->query("SELECT SUM(p.payment_amount) as total_paid,SUM(p.metal_weight) as saved_wgt,
                    SUM(ROUND((p.metal_weight)*(" . $int['interest_value'] . "/100)*(DATEDIFF(CURDATE(),date(p.date_payment))/365),3)) as total_benefit,
                    CURDATE() as cur_date, CONCAT(" . $int['interest_value'] . ",' %') as interest,COUNT(id_payment) as pay_count,date(sa.start_date) as join_date,
                    DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,DATEDIFF(DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY),date(p.date_payment)) as date_difference,
                    '' as wallet_text
        			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
                    $digiwallet = $sql_tot->row_array();
                } else {
                    $sql_tot = $this->db->query("SELECT SUM(p.payment_amount) as total_paid,SUM(p.metal_weight) as saved_wgt,
                    '' as total_benefit,CURDATE() as cur_date, '' as interest,COUNT(id_payment) as pay_count,date(sa.start_date) as join_date,
                    DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,DATEDIFF(CURDATE(),date(p.date_payment)) as date_difference,
                    '' as wallet_text
        			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
                    $digiwallet = $sql_tot->row_array();
                }
                if ($sql_debit->num_rows > 0) {
                    $sql_tot = $this->db->query("SELECT SUM(ROUND((p.metal_weight)*(" . $debit['deduction_value'] . "/100)*(DATEDIFF(CURDATE(),date(p.date_payment))/365),3)) as preclose_benefit,
                     CONCAT(" . $debit['deduction_value'] . ",' %') as preclose_interest,
                     CONCAT(date(sa.start_date),' to ',(DATE_ADD(date(sa.start_date), INTERVAL " . $debit['installment_to'] . " DAY))) AS preclose_date
        			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
                    $digiwallet['preclose_interest'] = $sql_tot->row()->preclose_interest;
                    $digiwallet['preclose_benefit'] = $sql_tot->row()->preclose_benefit;
                    $digiwallet['preclose_date'] = $sql_tot->row()->preclose_date;
                } else {
                    $sql_tot = $this->db->query("SELECT '' as preclose_interest, '' as preclose_benefit, '' as preclose_date
        			FROM `payment` p  
        			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
        			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)			
        			WHERE sa.id_scheme_account = " . $res['id_scheme_account'] . " and p.payment_status = 1");
                    $digiwallet['preclose_interest'] = $sql_tot->row()->preclose_interest;
                    $digiwallet['preclose_benefit'] = $sql_tot->row()->preclose_benefit;
                    $digiwallet['preclose_date'] = $sql_tot->row()->preclose_date;
                }
            } else {
                $digiwallet = [];
            }
            return array('status' => TRUE, 'scheme' => $sch, 'chit' => $sch_acc, 'digiwallet' => $digiwallet);
        }
    }
    function isValidPin($pin_no, $id_customer, $mobile)
    {
        $this->db->select('pin_no');
        $this->db->where('pin_no', $pin_no);
        $this->db->where('id_customer', $id_customer);
        $this->db->where('mobile', $mobile);
        $login = $this->db->get(self::TAB_CUS);
        //echo $this->db->last_query();exit;
        if ($login->num_rows() > 0) {
            return TRUE;
        }
    }
    function get_allvillage($id_city)
    {
        $sql = $this->db->query("SELECT ip.idpincode as id_village, ip.officeName as name, ip.pincode, ip.taluk, ip.districtName, ip.stateName,ct.id_city
                                FROM indianpostal ip
                                left join city ct on (ct.name = ip.taluk)
                                WHERE ct.id_city =" . $id_city);
        $res = $sql->result_array();
        return $res;
    }
    function inActiveCustomer($cus_id)
    {
        $updData = array("active" => 0, "date_upd" => date("Y-m-d"));
        $this->db->where('id_customer', $cus_id);
        $res = $this->db->update("customer", $updData);
        if ($res) {
            return array("status" => $res, "msg" => 'Your account deleted successfully');
        } else {
            return array("status" => $res, "msg" => 'Unable to Proceed your request');
        }
    }
    function getConfigData()
    {
        $sql = $this->db->query("SELECT * from configuration");
        return $sql->row_array();
    }
    function getAccNoFormat($record)
    {
        $acc = $this->get_acc_Data($record['id_scheme_account']);
        //scheme acc number format... 
        if ($this->config->item('sch_AccNo_padding') > 0) {
            $record['scheme_acc_number'] = str_pad($acc['sch_AccNo'], $this->config->item('sch_AccNo_padding'), '0', STR_PAD_LEFT);
        }
        if ($record['is_lucky_draw'] == 1) {
            $record['chit_number'] = $record['scheme_group_code'] . '-' . $record['scheme_acc_number'];
        } else {
            if ($record['schemeaccNo_displayFrmt'] == 0) {   //only acc num
                $record['chit_number'] = $record['scheme_acc_number'];
            } else if ($record['schemeaccNo_displayFrmt'] == 1) { //based on acc number generation setting
                if ($record['scheme_wise_acc_no'] == 0) {
                    $record['chit_number'] = $record['scheme_acc_number'];
                } else if ($record['scheme_wise_acc_no'] == 1) {
                    $record['chit_number'] = $record['acc_branch'] . '-' . $record['scheme_acc_number'];
                } else if ($record['scheme_wise_acc_no'] == 2) {
                    $record['chit_number'] = $record['code'] . '-' . $record['scheme_acc_number'];
                } else if ($record['scheme_wise_acc_no'] == 3) {
                    $record['chit_number'] = $record['code'] . $record['acc_branch'] . '-' . $record['scheme_acc_number'];
                } else if ($record['scheme_wise_acc_no'] == 4) {
                    $record['chit_number'] = $record['start_year'] . '-' . $record['scheme_acc_number'];
                } else if ($record['scheme_wise_acc_no'] == 5) {
                    $record['chit_number'] = $record['start_year'] . $record['code'] . $record['scheme_acc_number'];
                } else if ($record['scheme_wise_acc_no'] == 6) {
                    $record['chit_number'] = $record['start_year'] . $record['code'] . $record['acc_branch'] . '-' . $record['scheme_acc_number'];
                }
            } else if ($record['schemeaccNo_displayFrmt'] == 2) {
                //customised
                // $record['chit_number'] =  $record['scheme_acc_number'];
                $id = $record['id_scheme_account'];
                $acc_frmt_fromdb = $this->getFormatFromDB();
                $acc = $this->get_acc_Data($id);
                $frmt_short_code = [];
                if ($acc_frmt_fromdb['custom_AccDisplayFrmt'] != '' && $acc_frmt_fromdb['custom_AccDisplayFrmt'] != null) {
                    $field_name = explode('@@', $acc_frmt_fromdb['custom_AccDisplayFrmt']);
                    for ($i = 1; $i < count($field_name); $i += 2) {
                        $frmt_short_code[] = $field_name[$i];
                    }
                    $record['chit_number'] = $this->getFormatedNumber($frmt_short_code, $acc);
                } else {
                    $record['chit_number'] = $acc['sch_AccNo'];
                }
            }
        }
        return $record['chit_number'];
    }
    function getRcptNoFormat($payments)
    {
        $acc = $this->get_acc_Data($payments['id_scheme_account']);
        if ($this->config->item('sch_AccNo_padding') > 0) {
            $payments['scheme_acc_number'] = str_pad($acc['sch_AccNo'], $this->config->item('sch_AccNo_padding'), '0', STR_PAD_LEFT);
        }
        if ($payments['receipt_no'] == '' || $payments['receipt_no'] == NULL) {
            $receipt_no = '-';
        } else {
            if ($payments['receiptNo_displayFrmt'] == 0) {   //only acc num
                $receipt_no = $payments['receipt_no'];
            } else if ($payments['receiptNo_displayFrmt'] == 1) { //based on acc number generation setting
                if ($payments['scheme_wise_receipt'] == 1) {
                    $receipt_no = $payments['receipt_no'];
                } else if ($payments['scheme_wise_receipt'] == 2) {
                    $receipt_no = $payments['payment_branch'] . '-' . $payments['receipt_no'];
                } else if ($payments['scheme_wise_receipt'] == 3) {
                    $receipt_no = $payments['code'] . '-' . $payments['receipt_no'];
                } else if ($payments['scheme_wise_receipt'] == 4) {
                    $receipt_no = $payments['code'] . '' . $payments['payment_branch'] . '-' . $payments['receipt_no'];
                } else if ($payments['scheme_wise_receipt'] == 5) {
                    $receipt_no = $payments['start_year'] . '-' . $payments['receipt_no'];
                } else if ($payments['scheme_wise_receipt'] == 6) {
                    $receipt_no = $payments['start_year'] . '' . $payments['code'] . '' . $payments['payment_branch'] . '-' . $payments['receipt_no'];
                } else if ($payments['scheme_wise_receipt'] == 7) {   //7-Financial Year with Branch wise
                    $receipt_no = $payments['start_year'] . '' . $payments['payment_branch'] . '-' . $payments['receipt_no'];
                }
            } else if ($payments['receiptNo_displayFrmt'] == 2) {  //customised
                //$receipt_no = $payments['receipt_no'];
                $rcpt_frmt_fromdb = $this->getFormatFromDB();
                $id = $payments['id_payment'];
                $rcpt = $this->get_receipt_Data($id);
                $frmt_short_code = [];
                if ($rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt'] != '' && $rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt'] != null) {
                    if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] != 0 && $rcpt['receipt_no'] != '-') {
                        $field_name = explode('@@', $rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt']);
                        for ($i = 1; $i < count($field_name); $i += 2) {
                            $frmt_short_code[] = $field_name[$i];
                        }
                        $rcptFrmt = $this->getFormatedNumber($frmt_short_code, $rcpt);
                    } else {
                        $rcptFrmt = '-';
                    }
                } else {
                    if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] != 0 && $rcpt['receipt_no'] != '-') {
                        $rcptFrmt = $rcpt['receipt_no'];
                    } else {
                        $rcptFrmt = '-';
                    }
                }
                $receipt_no = $rcptFrmt;
            }
        }
        return $receipt_no;
    }
    function get_customer_acc($id_scheme_acc)
    {
        $accounts = $this->db->query("select
							  sc.id_scheme,maturity_type,s.id_scheme_account,s.id_branch as branch,c.id_branch as cus_reg_branch,sc.code as group_code,sc.sync_scheme_code,
							  if(cs.has_lucky_draw=1 && sc.is_lucky_draw = 1,concat(ifnull(s.group_code,''),'',ifnull(s.scheme_acc_number,'Not allocated')),concat(ifnull(sc.code,''),' ',ifnull(s.scheme_acc_number,'Not allocated'))) as scheme_acc_number,
								  IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,c.firstname,ifnull(s.account_name,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,s.start_date,c.is_new,c.email,sc.min_amount, sc.max_amount,  							  
							  sc.scheme_type, flexible_sch_type,
							  sc.scheme_name,s.is_new,sc.code,sc.total_installments,sc.max_chance,sc.payment_chances,sc.max_weight,sc.min_weight,
							  sc.amount,c.mobile,if(s.active =1,'Active','Inactive') as active,s.date_add,cs.currency_name,cs.currency_symbol,cs.custom_entry_date,cs.edit_custom_entry_date,s.pan_no 
							from scheme_account s
							left join customer c on (s.id_customer=c.id_customer)
							left join scheme sc on (sc.id_scheme=s.id_scheme)
							join chit_settings cs 
							where  s.is_closed=0 and s.id_scheme_account =" . $id_scheme_acc);
        return $accounts->row_array();
    }
    function getFormatFromDB()
    {
        $sql = "SELECT custom_AccDisplayFrmt,custom_ReceiptDisplayFrmt,receiptNo_displayFrmt,schemeaccNo_displayFrmt from chit_settings where id_chit_settings=1";
        return $this->db->query($sql)->row_array();
    }
    function get_acc_Data($id)
    {
        /* get necessary data of scheme account by id... */
        $sql = "SELECT IFNULL(sa.scheme_acc_number,'Not Allocated') as sch_AccNo,IFNULL(sa.start_year,'') as start_year,b.short_name as branch_code,s.code,IFNULL(sa.group_code,'') as group_code,
	                if(s.is_lucky_draw = 1, CONCAT(s.code,'(',ifnull(sa.group_code,''),')') ,s.code) as scheme_code
                FROM scheme_account sa
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
                WHERE sa.id_scheme_account = " . $id;
        return $this->db->query($sql)->row_array();
    }
    function get_receipt_Data($id)
    {
        /* get necessary data of payment by id... */
        $sql = "SELECT IFNULL(p.receipt_no,'-') as receipt_no,IFNULL(p.receipt_year,'') as receipt_year,b.short_name as branch_code,s.code,IFNULL(sa.group_code,'') as group_code,
	                if(s.is_lucky_draw = 1 && cs.group_wise_receipt = 1 , CONCAT(s.code,'(',ifnull(sa.group_code,''),')') ,s.code) as scheme_code
                FROM payment p
                LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = p.id_branch)
                JOIN chit_settings cs 
                WHERE p.id_payment = " . $id;
        return $this->db->query($sql)->row_array();
    }
    function getFormatedNumber($frmt_short_code, $acc)
    {
        if ($this->config->item('receipt_no_padding') > 0) {
            $acc['receipt_no'] = str_pad($acc['receipt_no'], $this->config->item('receipt_no_padding'), '0', STR_PAD_LEFT);
        }
        if ($this->config->item('sch_AccNo_padding') > 0) {
            $acc['sch_AccNo'] = str_pad($acc['sch_AccNo'], $this->config->item('sch_AccNo_padding'), '0', STR_PAD_LEFT);
        }
        if ($frmt_short_code != '' && $frmt_short_code != null) {
            $finalFormat = '';
            foreach ($frmt_short_code as $code) {
                switch ($code) {
                    case 'br_code':
                        $finalFormat .= $acc['branch_code'];
                        break;
                    case 'acc_num':
                        $finalFormat .= $acc['sch_AccNo'];
                        break;
                    case 'sch_code':
                        $finalFormat .= $acc['scheme_code'];
                        break;
                    case 'grp_code':
                        $finalFormat .= $acc['group_code'];
                        break;
                    case 'fin_yr':
                        $finalFormat .= $acc['start_year'];
                        break;
                    case 'rcpt_yr':
                        $finalFormat .= $acc['receipt_year'];
                        break;
                    case 'rcpt_num':
                        $finalFormat .= $acc['receipt_no'];
                        break;
                    case 'hyphen':
                        $finalFormat .= '-';
                        break;
                    case 'space':
                        $finalFormat .= ' ';
                        break;
                }
            }
        }
        //$finalFormat=substr($finalFormat, 1);
        return $finalFormat;
    }
    function isBranchWiseReg()
    {
        $sql = $this->db->query("Select cs.is_branchwise_cus_reg,cs.branch_settings from chit_settings cs");
        $row = $sql->row();
        if ($row->branch_settings == 1 && $row->is_branchwise_cus_reg == 1) {
            return true;
        } else {
            return false;
        }
    }
    function getVisClass($id_branch)
    {
        $result = [];
        $data = $this->get_costcenter();
        $CLSFY_IMG_PATH = base_url() . 'admin/assets/img/sch_classify/';
        $sql = "SELECT  id_classification, classification_name, description,concat('" . $CLSFY_IMG_PATH . "','',sc.logo) as logo
	    FROM sch_classify sc
	    WHERE active =1";
        //print_r($sql);exit;
        $classifications = $this->db->query($sql)->result_array();
        foreach ($classifications as $clsfy) {
            if ($data['branchwise_scheme'] == 1) {
                $sch_sql = $this->db->query("SELECT id_classification,sb.id_branch,b.name
										    FROM scheme s 
											    left join scheme_branch sb on sb.id_scheme=s.id_scheme and scheme_active=1
											    LEFT JOIN branch b on b.id_branch = sb.id_branch
		    								WHERE " . ($id_branch != '' && $data['is_branchwise_cus_reg'] == 1 ? ' sb.id_branch=' . $id_branch . ' and' : '') . "  s.active=1 and s.visible=1 and id_classification=" . $clsfy['id_classification'] . " GROUP BY id_classification,id_branch");
            } else {
                $sch_sql = $this->db->query("SELECT id_classification,null as id_branch
											    FROM scheme s 
											    WHERE s.is_digi=0 and s.active=1 and s.visible=1 and id_classification=" . $clsfy['id_classification'] . " GROUP BY id_classification");
            }
            //print_r($this->db->last_query());exit;
            $schemes = $sch_sql->result_array();
            foreach ($schemes as $sch) {
                $result[] = array(
                    "id_classification" => $clsfy['id_classification'],
                    "id_branch" => $sch['id_branch'],
                    "classification_name" => $clsfy['classification_name'],
                    "description" => $clsfy['description'],
                    "logo" => $clsfy['logo']
                );
            }
        }
        return $result;
    }
    function getVisClass_withoutbranch()
    {
        $classifications = [];
        $data = $this->get_costcenter();
        $CLSFY_IMG_PATH = base_url() . 'admin/assets/img/sch_classify/';
        $sql = $this->db->query("SELECT  id_classification, classification_name, description,concat('" . $CLSFY_IMG_PATH . "','',sc.logo) as logo
         FROM sch_classify sc WHERE active =1");
        //print_r($this->db->last_query());exit;
        foreach ($sql->result_array() as $clsfy) {
            $classifications[] = array(
                "id_classification" => $clsfy['id_classification'],
                "id_metal" => isset($clsfy['id_metal']) ? $clsfy['id_metal'] : NULL,
                "metal" => isset($clsfy['metal']) ? $clsfy['metal'] : NULL,
                "classification_name" => $clsfy['classification_name'],
                "description" => $clsfy['description'],
                "logo" => $clsfy['logo']
            );
        }
        return $classifications;
    }
    function get_metalrate_by_branch($id_branch, $id_metal, $id_purity, $start_date = '')
    {
        $today = date('Y-m-d H:i:s');
        $rate_field = '';
        if ($id_purity > 0) {
            $rf_sql = $this->db->query("SELECT rate_field,market_rate_field FROM `ret_metal_purity_rate` where id_metal=" . $id_metal . " and id_purity=" . $id_purity . "");
            if ($rf_sql->num_rows() > 0) {
                $rate_field = $rf_sql->row("rate_field");
            }
        }
        if (!empty($rate_field) && $id_purity > 0) {
            $rate_field = $rf_sql->row("rate_field");
        } else if ($id_metal > 0) {
            $rate_field = ($id_metal == 2 ? "silverrate_1gm" : "goldrate_22ct");
        }
        // print_r($rate_field);exit;
        if ($rate_field != '') {
            $data = $this->get_settings();
            //print_r($data);exit;
            if ($data['is_branchwise_rate'] == 1 && $id_branch != '' && $id_branch != NULL) {
                // $sql = "select " . $rate_field . " from metal_rates m
                // left join branch_rate br on m.id_metalrates=br.id_metalrate 
                // where br.id_branch=" . $id_branch . " 
                // '.($start_date!=NULL?' date(m.add_date) BETWEEN '" . $start_date . "' AND '" . $today . "' ' :'').'
                // order by  br.id_metalrate desc limit 1";
                $sql = "SELECT $rate_field FROM metal_rates m 
                        LEFT JOIN branch_rate br ON m.id_metalrates = br.id_metalrate
                        WHERE br.id_branch = $id_branch
                        " . (!empty($start_date) ? " AND DATE(m.add_date) BETWEEN '$start_date' AND '$today' " : "") . "
                        ORDER BY br.id_metalrate DESC
                        LIMIT 1";
                //echo $sql;exit;
            } else if ($data['is_branchwise_rate'] == 1) {
                $sql = "SELECT $rate_field FROM metal_rates
    			LEFT JOIN branch_rate br ON br.id_metalrate = metal_rates.id_metalrates
    			WHERE br.status = 1
    	   		" . (!empty($start_date) ? " AND DATE(metal_rates.add_date) BETWEEN '$start_date' AND '$today' " : "") . "
    			ORDER BY metal_rates.id_metalrates DESC
    			LIMIT 1";
            } else {
                $sql = "select " . $rate_field . " from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates
    			" . ($start_date != NULL ? " where date(metal_rates.add_date) BETWEEN '" . $start_date . "' AND '" . $today . "'" : "") . "
    			order by id_metalrates desc limit 1";
            }
            //	echo $sql;exit;
            $result = $this->db->query($sql);
            if ($result->num_rows() > 0) {
                return $result->row($rate_field);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    //RHR schemes : by 30 days payment cycle with advance , pending flow.. also has benefits for pre closed and one month bonus maturity closed ....20-09-2023 #AB....
    function get_due_date($due_type, $date_payment, $id_scheme_account)
    {
        $result = [];
        // print_r($due_type);exit;
        $where = '';
        $sch = $this->get_scheme_details($id_scheme_account);
        $now = date('Y-m-d');
        $date_payment = date('Y-m-d', strtotime($date_payment));
        $first_payment_date = (!empty($sch['first_payment_date']) ? $sch['first_payment_date'] : $date_payment);
        if ($sch['maturity_type'] != 2 && ($now > $sch['maturity_date'] || $now > $sch['due_cycle_end']) && $due_type == 'current_range') {
            $sch['total_installments'] = $sch['till_days_ins'] - 1;
        }
        $c_wh = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account) limit 1";
        if ($due_type == 'ND' || $due_type == '') {
            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to " . $c_wh . " ";
        } else if ($due_type == 'AD') {
            $where = "and dt.due_date_from >= date('" . $date_payment . "')  " . $c_wh . " ";
        } else if ($due_type == 'PD') {
            $where = "and dt.due_date_from <= date('" . $date_payment . "') " . $c_wh . " ";
        } else if ($due_type == 'allow_pay') {
            $where = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account)";
        } else if ($due_type == 'MND') {
            $where = "and '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to ";
        } else if ($sch['maturity_type'] != 2 && $now <= $sch['maturity_date'] && $due_type == 'current_range') {
            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to";
        } else if ($sch['maturity_type'] != 2 && $now > $sch['maturity_date'] && $due_type == 'current_range') {
            if($sch['installment_cycle'] == 1 || $sch['installment_cycle'] == 3 || $sch['installment_cycle'] == 2){
                $date_condition = "DATE_FORMAT(p.date_payment,'%Y-%m-%d') = date_format('" . $date_payment . "','%Y-%m-%d')";
            }else if($sch['installment_cycle'] == 0){
                $date_condition = "DATE_FORMAT(p.date_payment,'%Y-%m') = date_format('" . $date_payment . "','%Y-%m')";
            }else{
                $date_condition = "1";
            }
            $where = "AND EXISTS (
                                SELECT 1
                                FROM payment p
                                WHERE p.payment_status = 1
                                  AND p.due_date IS NOT NULL
                                  AND p.id_scheme_account = sa.id_scheme_account
                                  AND ".$date_condition."
                                  AND p.due_date BETWEEN dt.due_date_from AND dt.due_date_to
                            )";
        } else {
            $where = $c_wh;
        }
        if ($sch['installment_cycle'] == 2) {  //by days duration cycle
            $days_duration = $sch['ins_days_duration'] - 1;
            $grace_days = $sch['grace_days'] - 1;
            $sql = "SELECT 		if(
    			dt.due_date_to < '" . $date_payment . "' ,
    			'PD',
    			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 
    				'ND',
    				if(
                       dt.due_date_to >= '" . $date_payment . "'
                        ,'AD','-'
                    )
    			) 
    		) as due_type,
    		dt.installment,dt.due_date_from,dt.due_date_to,dt.grace_date,
    		if((('" . $due_type . "' = 'ND' OR '" . $due_type . "' = '') AND date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.grace_date) OR ('" . $due_type . "' = 'AD')  , '0','1') as is_limit_exceed	
    		FROM scheme_account sa
    		JOIN (SELECT @sno := @sno + 1 as installment,
    			@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL " . $sch['ins_days_duration'] . " day )) as due_date_from,
    			@due_Date_to := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $days_duration . " day ),date_add(@due_Date_from,INTERVAL " . $days_duration . " day )) as due_date_to,  
    			@grace_date := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $grace_days . " day ),date_add(@due_Date_from,INTERVAL " . $grace_days . " day )) as grace_date,
    			@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
    			FROM access
    			join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
    			limit " . $sch['total_installments'] . "
    		) as dt
    		WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        //daily payment cycle
        // else if ($sch['installment_cycle'] == 1) {
        else if ($sch['installment_cycle'] == 1 || $sch['installment_cycle'] == 3) {
            $sql = "SELECT 		if(
        		dt.due_date_to < '" . $date_payment . "' ,
        		'PD',
        		if( '" . $date_payment . "' = dt.due_date_from  , 
        			'ND',
        			if(
                       dt.due_date_to > '" . $date_payment . "'
                        ,'AD','-'
                    )
        		) 
        	) as due_type,
        	dt.installment,dt.due_date_from,dt.due_date_to
        	FROM scheme_account sa
        	JOIN (SELECT @sno := @sno + 1 as installment,
        		@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL 1 day )) as due_date_from,
        		@due_Date_to := @due_Date_from  as due_date_to,  
        		@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
        		FROM access
        		join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
        		limit " . $sch['total_installments'] . "
        	) as dt
        	WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        //monthly cycle    
        else if ($sch['installment_cycle'] == 0) {
            $sql = "SELECT 		if(
            			dt.due_date_to < '" . $date_payment . "' ,
            			'PD',
            			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 
            				'ND',
            				if(
                               dt.due_date_to >= '" . $date_payment . "'
                                ,'AD','-'
                            )
            			) 
            		) as due_type,
                    dt.installment,
                    dt.due_date_from,
                    dt.due_date_to 
                    FROM scheme_account sa 
                    JOIN (SELECT @sno := @sno + 1 as installment, 
                          @due_Date_from := if(@sno = 1, date_format('" . $first_payment_date . "','%Y-%m-01'),date_add(@pay_date ,INTERVAL 1 month )) as due_date_from, 
                          @due_Date_to := LAST_DAY(@due_Date_from) as due_date_to, 
                          @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from) as due_pay_date 
                          FROM access 
                          join (SELECT @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from), @sno := 0 ) as t limit " . $sch['total_installments'] . " 
                         ) as dt 
                    WHERE sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        // print_r($this->db->last_query());exit;
        if ($due_type == 'allow_pay') {
            foreach ($pay as $p) {
                $grouped_dues[$p['due_type']][] = $p;
            }
            foreach ($grouped_dues as $key => $gd) {
                $result[] = array('due_name' => $key, 'dues_count' => sizeof($gd));
            }
        } else {
            $result = $pay;
        }
        return $result;
    }
    function getSchemeData($id_sch_acc)
    {
        $sql = $this->db->query("SELECT s.maturity_type,s.maturity_days,s.interest,s.apply_benefit_by_chart,sa.id_scheme,
            Date_format(sa.start_date,'%Y-%m%-%d') as start_date,sa.maturity_date,
            			date_add(Date_format(sa.start_date,'%Y-%m%-%d'),INTERVAL s.maturity_days DAY) as calc_maturity_date
            from scheme_account sa
            left join scheme s on s.id_scheme=sa.id_scheme
            where sa.id_scheme_account=" . $id_sch_acc);
        return $sql->row_array();
    }
    function get_scheme_details($id_scheme_account)
    {
        $sql = $this->db->query("SELECT s.total_installments,s.grace_days,s.installment_cycle,s.ins_days_duration,
		(SELECT MIN(date(p.date_payment)) FROM payment p WHERE p.payment_status = 1 and p.id_scheme_account = sa.id_scheme_account) as first_payment_date,	
	    s.scheme_name,c.firstname as cus_name,IFNULL(c.lastname,'') as lastname,c.mobile,sa.account_name,concat(s.code,'-',sa.scheme_acc_number) as scheme_acc_number,sa.firstPayment_amt,sa.received_wgt,sa.fixed_metal_rate,sa.fixed_wgt,sa.fixed_rate_on,sa.maturity_date,s.otp_price_fix_type,s.one_time_premium,
	    date_format(sa.start_date,'%d-%m-%Y') as start_date,date_format(sa.fixed_rate_on,'%d-%m-%Y') as fixed_rate_on,IFNULL(s.description,'') as description,
	    s.emp_refferal,s.emp_incentive_closing,s.ref_benifitadd_ins_type,s.ref_benifitadd_ins,sa.id_employee,s.firstPayamt_as_payamt,
	    s.emp_refferal_value,s.maturity_type,
        IFNULL(IF(sa.is_opening=1,IFNULL(sa.balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0) as total_paid_amount,
        if(s.installment_cycle = 0 || s.installment_cycle = 3,
                               PERIOD_DIFF(date_format(CURDATE(),'%Y%m'),date_format(sa.start_date,'%Y%m'))+1,
                               if(s.installment_cycle = 1,
                                  DATEDIFF(date_format(CURDATE(),'%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1,
                                  if(s.installment_cycle = 2,
                                  CEIL((DATEDIFF(date_format(CURDATE(),'%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1) /s.ins_days_duration),'-'
                                    )
                                 )
                              ) 
                              as till_days_ins,
                              	DATE_FORMAT(
                        IF(
                            s.installment_cycle = 0,  
                            LAST_DAY(DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) MONTH)),
                            IF(
                                s.installment_cycle = 1,  
                                DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) DAY),
                                IF(
                                    s.installment_cycle = 2,  
                                    DATE_ADD(
                                        sa.start_date,
                                        INTERVAL ((s.ins_days_duration * s.total_installments) - 1) DAY
                                    ),
                                    NULL
                                )
                            )
                        ),
                        '%Y-%m-%d'
                    ) AS due_cycle_end
        FROM scheme_account sa 
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account  AND p.payment_status IN (1,2)
        LEFT JOIN customer c ON c.id_customer=sa.id_customer
        WHERE sa.id_scheme_account=" . $id_scheme_account . "");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
    }
    //RHR schemes ends
    function get_generalAdv_BonusData($id_scheme_account, $value, $type, $calc_date_from, $calc_date_to)
    {
        //calculate based on type : 1 - amount , 0 - percent
        if ($type == 0) {
            $formula_amt = "ROUND(( (SUM(ap.payment_amount) / s.total_installments) * (" . $value . "/100) ),2) ";
            $formula_wgt = "ROUND(( (SUM(ap.metal_weight) / s.total_installments) * (" . $value . "/100) ),3) ";
        } else {
            $formula_amt = "ROUND((SUM(ap.payment_amount) + " . $value . "),2) ";
            $formula_wgt = "ROUND((SUM(ap.metal_weight) + " . $value . "),3) ";
        }
        $bonus = $this->db->query("SELECT SUM(ap.payment_amount) as tot_adv_amt, ROUND(SUM(ap.metal_weight),3) as tot_adv_wgt,
                                " . $formula_amt . " as adv_bonus , " . $formula_wgt . " as adv_bonus_wgt
                                FROM `general_advance_payment` ap
                                left join scheme_account sa on (sa.id_scheme_account = ap.id_scheme_account)
                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                                WHERE ap.id_scheme_account = " . $id_scheme_account . " and EXTRACT( YEAR_MONTH FROM date(ap.date_payment)) BETWEEN EXTRACT( YEAR_MONTH FROM '" . $calc_date_from . "') AND EXTRACT( YEAR_MONTH FROM '" . $calc_date_to . "')
                    ")->row_array();
        //   print_r($this->db->last_query());exit;          
        return $bonus;
    }
    function get_entrydate($id)
    {
        $sql = "SELECT entry_date as custom_entry_date,cs.edit_custom_entry_date FROM ret_day_closing 
		join chit_settings cs 
		" . ($id != '' ? "where id_branch=" . $id . " " : '') . "";
        //print_r($sql);exit;
        return $this->db->query($sql)->row_array();
    }
    function getAreabypincode($pincode)
    {
        $sql = $this->db->query("SELECT  village_name as name,village_name,id_village,pincode from village where pincode=" . $pincode);
        return $sql->result_array();
    }
    function inActiveNotifications($id_notification)
    {
        $sql = $this->db->query("UPDATE sent_notifications SET active=0 where id_sent_noti='" . $id_notification . "'");
        return $sql;
    }
    function getMonthwiseRecords($month, $id_scheme_account)
    {
        $sql = $this->db->query("SELECT id_payment,payment_amount,Date_Format(date_payment,'%d-%m-%Y') as pay_date,receipt_no,payment_mode,IF(p.payment_status = 2,'Awaiting',psm.payment_status) as pay_status,p.metal_rate from 
	    payment p
	    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
	    where p.payment_status=1 and id_scheme_account='" . $id_scheme_account . "' and month(date_payment)='" . $month . "'");
        //echo $this->db->last_query();exit;
        return $sql->result_array();
    }
    function getPgdataBycode($pg_code, $id_payGateway)
    {
        $sql = "SELECT id_pg,api_url,param_1,param_2,param_3,if(type=0,'Demo','Real') as type,is_default FROM gateway WHERE is_default=1 and active=1 and pg_code=" . $pg_code . " 
	    " . ($id_payGateway > 0 ? "and id_pg=" . $id_payGateway : '') . "
	    ";
        //echo $sql;exit;
        return $this->db->query($sql)->row_array();
    }
    function get_ReceiptPrintData($payment_no, $id_scheme_account)
    {
        $records = array();
        $query_invoice = $this->db->query("SELECT IFNULL(m.metal,'Metal') as metal_name,pay.id_payment,sch_acc.id_scheme_account,IFNULL(sch_acc.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sch_acc.id_branch) as acc_branch,sch.code,c.schemeaccNo_displayFrmt,sch.is_lucky_draw,ifnull(sch_acc.scheme_acc_number,'Not Allocated') as scheme_acc_number,c.scheme_wise_acc_no,
		                    IFNULL(pay.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = pay.id_branch) as payment_branch, sch.code,c.receiptNo_displayFrmt,ifnull(pay.receipt_no,'') as receipt_no,c.scheme_wise_receipt,
		                    pay.id_scheme_account as id_scheme_account, sch_acc.account_name,
                            if(c.has_lucky_draw=1 && sch.is_lucky_draw = 1,concat(ifnull(sch_acc.group_code,''),'  ',ifnull(sch_acc.scheme_acc_number,'Not Allocated')),
							if(c.scheme_wise_acc_no=3,concat(b.short_name,sch.code,'-',IFNULL(sch_acc.scheme_acc_number,'Not Allocated')),concat(sch.code,'  ',IFNULL(sch_acc.scheme_acc_number,'Not Allocated'))))as oldscheme_acc_number,
							gst_amount,flexible_sch_type,DATE_FORMAT(sch_acc.maturity_date,'%d-%m-%Y') as maturity_date,DATE_FORMAT(pay.date_payment,'%d-%m-%Y %h:%i %p') as date_payment, sch.scheme_name as scheme_name, sch_acc.id_branch,avg_calc_ins,avg_payable,
							sch.code as sch_code,sch.hsn_code,pay.payment_amount as payment_amount,
							IF(cus.lastname is null,cus.firstname,concat(cus.firstname,cus.lastname)) as name,
							cus.firstname,cus.lastname,pay.is_print_taken,pay.discountAmt,
							 if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,	
							 IF(payment_mode = 'MULTI', (SELECT group_concat(concat(payment_mode,'-',payment_amount)) as multi_mode FROM `payment_mode_details` where id_payment=" . $payment_no . " and is_active=1),'' )as multi_modes,
							addr.address1 as address1,addr.address2 as address2,addr.address3 as address3,ct.name as city,
							addr.pincode,cus.email,if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',if(payment_mode='FP','Free Payment',pm.mode_name))))) as payment_mode,id_transaction,payment_ref_number,
							if((c.receipt_no_set= 1 && pay.payment_status =1 && pay.receipt_no is null ),pay.receipt_no,if((c.receipt_no_set=1 && pay.payment_status =1 && pay.receipt_no!=''),pay.receipt_no,pay.receipt_no)) as oldreceipt_no,bank_name,bank_acc_no,bank_branch,if(sch.scheme_type=0,'-',metal_weight) as metal_weight,if(sch.scheme_type=0,'-',metal_rate) as metal_rate,scheme_type,
							IF(sch_acc.is_opening=1,IFNULL(sch_acc.paid_installments,0)+ COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')))  as installment,
							DATE_FORMAT(Date_add(date_payment,Interval 1 month),'%b %Y') as next_due,pay.id_transaction as trans_id,cus.mobile, pay.due_type,ifnull(pay.add_charges,0.00) as add_charges,pay.payment_type,sch.charge_head,sch.id_scheme,pay.gst,pay.gst_type,pay.date_add,addr.id_state,s.name as state,con.name as country,
							(select SUM(pa.metal_weight)  from payment pa left
join scheme_account sch_acc on sch_acc.id_scheme_account=pa.id_scheme_account
left join scheme sch on sch.id_scheme=sch_acc.id_scheme
where pa.id_payment<='" . $payment_no . "' and pa.id_scheme_account='" . $id_scheme_account . "' and  pa.payment_status=1)as total_weight,
							(select IFNULL(IF(sch_acc.is_opening=1,IFNULL(sch_acc.paid_installments,0)+ IFNULL(if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight or sch.scheme_type=3 , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay left
 join scheme_account sch_acc on sch_acc.id_scheme_account=pay.id_scheme_account
 left join scheme sch on sch.id_scheme=sch_acc.id_scheme
 where pay.id_payment<='" . $payment_no . "' and pay.id_scheme_account='" . $id_scheme_account . "' and  pay.payment_status=1)
  as paid_installments,sch.total_installments,IFNULL(v.village_name,'') as village_name,IFNULL(pay.old_metal_amount,0) as old_metal_amount,
  '' as login_employee
								   FROM payment as pay								
									   JOIN  chit_settings c
									   	Left Join employee e On (e.id_employee=pay.id_employee)
										LEFT JOIN scheme_account sch_acc ON sch_acc.id_scheme_account = pay.id_scheme_account
										LEFT JOIN scheme sch ON sch.id_scheme = sch_acc.id_scheme
                                        left join branch b on sch_acc.id_branch=b.id_branch
										LEFT JOIN customer as cus ON cus.id_customer = sch_acc.id_customer
										LEFT JOIN address as addr ON addr.id_customer = cus.id_customer 
										LEFT JOIN city as ct ON addr.id_city = ct.id_city
										LEFT JOIN state as s ON addr.id_state = s.id_state 
							            LEFT JOIN country as con ON addr.id_country = con.id_country 
							            LEFT JOIN village v on v.id_village=cus.id_village
							            LEFT JOIN metal m on m.id_metal = sch.id_metal
										LEFT JOIN payment_mode pm ON (pay.payment_mode = pm.short_code)
									    WHERE pay.payment_status=1 AND id_payment = '" . $payment_no . "'");
        if ($query_invoice->num_rows() > 0) {
            foreach ($query_invoice->result() as $row) {
                $records[] = array(
                    'schemeaccNo_displayFrmt' => $row->schemeaccNo_displayFrmt,
                    'scheme_wise_acc_no' => $row->scheme_wise_acc_no,
                    'scheme_acc_number' => $row->scheme_acc_number,
                    'acc_branch' => $row->acc_branch,
                    'code' => $row->code,
                    'start_year' => $row->start_year,
                    'receiptNo_displayFrmt' => $row->receiptNo_displayFrmt,
                    'receipt_no' => $row->receipt_no,
                    'payment_branch' => $row->payment_branch,
                    'receipt_year' => $row->receipt_year,
                    'scheme_wise_receipt' => $row->scheme_wise_receipt,
                    'id_scheme_account' => $row->id_scheme_account,
                    'date_payment' => $row->date_payment,
                    'scheme_name' =>
                        $row->scheme_name,
                    'sch_code' => $row->sch_code,
                    'payment_amount' =>
                        $row->payment_amount,
                    'name' => $row->name,
                    'village_name' => $row->village_name,
                    $row->payment_amount,
                    'firstname' => $row->firstname,
                    'avg_calc_ins' => $row->avg_calc_ins,
                    'avg_payable' => $row->avg_payable,
                    $row->payment_amount,
                    'lastname' => $row->lastname,
                    'multi_modes' => $row->multi_modes,
                    'id_payment' => $payment_no,
                    'address1' => $row->address1,
                    'address2' => $row->address2,
                    'address3' => $row->address3,
                    'city' => $row->city,
                    'pincode' => $row->pincode,
                    'email' => $row->email,
                    'payment_mode' => $row->payment_mode,
                    'id_transaction' => $row->id_transaction,
                    'payment_ref_number' => $row->payment_ref_number,
                    'bank_name' => $row->bank_name,
                    'bank_acc_no' => $row->bank_acc_no,
                    'id_branch' => $row->id_branch,
                    'bank_branch' => $row->bank_branch,
                    'metal_weight' => $row->metal_weight,
                    'metal_rate' => $row->metal_rate,
                    'scheme_type' => $row->scheme_type,
                    'installment' => $row->installment,
                    'next_due' => $row->next_due,
                    'trans_id' => $row->trans_id,
                    'account_name' => $row->account_name,
                    'mobile' => $row->mobile,
                    'due_type' => $row->due_type,
                    'add_charges' => $row->add_charges,
                    'charge_head' => $row->charge_head,
                    'payment_type' => $row->payment_type,
                    'id_scheme' => $row->id_scheme,
                    'gst_type' => $row->gst_type,
                    'gst' => $row->gst,
                    'hsn_code' => $row->hsn_code,
                    'date_add' => $row->date_add,
                    'id_state' => $row->id_state,
                    'is_print_taken ' => $row->is_print_taken,
                    'state' => $row->state,
                    'country' => $row->country,
                    'paid_installments' => $row->paid_installments,
                    'total_installments' => $row->total_installments,
                    'total_weight' => $row->total_weight,
                    'employee' => $row->employee,
                    'discountAmt' => $row->discountAmt,
                    'old_metal_amount' => $row->old_metal_amount,
                    'login_employee' => $row->login_employee,
                    'metal_name' => ucfirst(strtolower($row->metal_name))
                );
            }
        }
        return $records;
    }
    function checkreferral_code($empcode)
    {
        $sql = "SELECT * from employee where  emp_code=" . $empcode;
        //echo $sql;exit;
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            $sts = array("status" => true, 'message' => 'Referrel code valid');
        } else {
            $sts = array("status" => false, 'message' => 'Referrel code Invalid');
        }
        return $sts;
    }
    function getCusSchejoinedbranch($id)
    {
        $sql = "SELECT DISTINCT(id_branch) FROM scheme_account WHERE id_customer = " . $id . " AND id_branch IS NOT NULL AND id_branch != ''";
        $result = $this->db->query($sql);
        $branch_list = array();
        foreach ($result->result_array() as $row) {
            $branch_list[] = $row['id_branch'];
        }
        $branch = implode(',', $branch_list);
        return $branch;
    }
    function getBranchByOrderId($order_id)
    {
        $sql = "SELECT id_branch from payment where ref_trans_id='" . $order_id . "'";
        return $this->db->query($sql)->row_array();
    }
    //Rahul
    function check_has_digi_account($id_customer, $id_scheme)
    {
        $sql = "select sa.* from scheme_account sa
		left join scheme s on s.id_scheme = sa.id_scheme
		where s.is_digi = 1 and sa.active = 1 and s.visible = 1 and sa.id_customer = " . $id_customer . " and s.id_scheme = $id_scheme";
        $result = $this->db->query($sql);
        return $result;
    }
    function get_rate_name($id_metal, $id_purity)
    {
        $this->db->select('rate_field');
        $this->db->from('ret_metal_purity_rate');
        $this->db->where('id_metal', $id_metal);
        $this->db->where('id_purity', $id_purity);
        $query = $this->db->get();
        return $query->row_array();
    }

    function commodityForDigi(){
        $this->db->select('m.id_metal, m.metal, s.visible, s.active');
        $this->db->from('metal m');
        $this->db->join('scheme s', 's.id_metal = m.id_metal');
        $this->db->where('s.is_digi', 1);
        $query = $this->db->get();
        return $result = $query->result_array();
    }
    function get_chit_metals()
    {
        $this->db->select('id_metal, metal, display_name');
        $this->db->where('show_chit', 1);
        $this->db->where('metal_status', 1);
        $query = $this->db->get('metal');
        return $query->result_array();
    }
    function get_idSchemeAccounts($id_customer)
    {
        $sql = "select id_scheme_account from scheme_account where active = 1 and is_closed = 0 and id_customer in ({$id_customer})";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    /* PAN required chit_settings and scheme based - Esakkiraja_20260109 starts */
    function get_pan_required($data)
    {
        $kyc_pan_number = '';
        $get_all_kycData = $this->db->query("SELECT number,kyc_type FROM `kyc` where id_customer = " . $data['id_customer'])->result_array();
        if (sizeof($get_all_kycData) > 0) {
            foreach ($get_all_kycData as $kyc) {
                //for other types can add code here
                if ($kyc['kyc_type'] == 2) {
                    $kyc_pan_number = $kyc['number'];
                }
            }
        }
		// print_r($data);exit;
        // if (!empty($data['pan_required_by']) && !empty($data['pan_req_amt']) && empty($kyc_pan_number)) {
        if (empty($kyc_pan_number)) {

            // if (($data['pan_required_by'] == 2 && $data['total_amount'] >= $data['pan_req_amt']) || ($data['pan_required_by'] == 1 && $data['cus_overall_amount'] >= $data['pan_req_amt'])) {
            //     return true;
            // }/* else if($data['scheme_type'] == 3 && ($data['get_amt_in_schjoin'] && $data['firstPayamt_as_payamt'])){
            //    return true;
        //    } */
        // } else if (!empty($data['is_pan_required']) && empty($kyc_pan_number)) {
            // if (($data['is_pan_required'] == 1 && ($data['total_amount'] >= $data['sch_pan_req_amt'])) || ($data['is_pan_required'] == 2 && ($data['cus_overall_amount'] >= $data['sch_pan_req_amt']))) {
            //     return true;
            // }/* else if($data['scheme_type'] == 3 && ($data['get_amt_in_schjoin'] && $data['firstPayamt_as_payamt'])){
				//    } */
				if($data['is_pan_required'] == 2 || $data['is_pan_required'] == 1){

					return true;
				}
        } else {
            return false;
        }
    }
    /* PAN required chit_settings and scheme based - Esakkiraja_20260109 ends */
    	function metalRateDiffCalculation($metal_rates){

		foreach($metal_rates as $key => $value){
			$diff['goldrate_22ct'] = $value['goldrate_22ct'] - $metal_rates[$key+1]['goldrate_22ct'];
			$diff['silverrate_1gm'] = $value['silverrate_1gm'] - $metal_rates[$key+1]['silverrate_1gm'];
			$diff['goldrate_22ct_diff_status'] = ($diff['goldrate_22ct'] > 0) ? 1 : 0;
			$diff['silverrate_1gm_diff_status'] = ($diff['silverrate_1gm'] > 0) ? 1 : 0;
			$diff['goldrate_22ct'] = abs($diff['goldrate_22ct']);
			$diff['silverrate_1gm'] = abs($diff['silverrate_1gm']);
			break;
		}
		// print_r($diff);exit;
		return $diff;
	}

    function insert_common_data($id_payment)
	{
		$model = self::API_MODEL;

		$this->load->model($model);

		//getting payment detail

		$pay_data = $this->$model->getPaymentByID($id_payment);

		//storing temp values
		$ref_no = $pay_data[0]['ref_no'];
		$id_scheme_account = $pay_data[0]['id_scheme_account']; 

		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account,$ref_no);

		$reg = $this->$model->getCustomerByID($id_scheme_account);

		$reg_1 = $this->$model->getCustomerDet($id_scheme_account);

		$reg[0]['record_to']= 1 ;

		$reg[0]['is_registered_online']= 2 ;  // 2 - online record

		$reg[0]['ref_no']		= $ref_no;

        // Skip clientid if gent_clientid is disabled
        $chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
		
        if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
            $reg[0]['clientid'] = '';
        }
		
		if(!$isCusRegExists['status']) {
            
			//insert customer registration detail
			$status = $this->$model->insert_CustomerReg($reg[0]);

			$runDirectAPI = true;

		} 
       /*  elseif($isCusRegExists['status'] && $isCusRegExists['clientid'] == null && $isCusRegExists['is_transferred'] == 'N' && $chit_settings['gent_clientid'] == 1) {

			$reg_data = array(
				'clientid' => "ON-".$id_scheme_account
			);
			$this->$model->update_CustomerReg($reg_data, $isCusRegExists['id_customer_reg']);

			$runDirectAPI = true;

		}  */
        elseif ($isCusRegExists['status'] && $isCusRegExists['is_transferred'] == 'N') {

			$runDirectAPI = true;
		} else {

			$runDirectAPI = false; // for all other cases
		}

		if($runDirectAPI && $this->config->item('directAPI') == '1') {

            if (!empty($reg[0]['maturity_date'])) {

                $maturitydate = $reg[0]['maturity_date'];
            } else {

                $total_installment = $reg_1[0]['total_installment'];
                    $maturitydate = date('Y-m-d', strtotime("+" . $reg_1[0]['maturity_days'] . " days" , strtotime($reg[0]['reg_date'])));

                $maturitydate = date('Y-m-d', strtotime("+" . $total_installment . " months", strtotime($reg[0]['reg_date']))); 
                if($reg_1[0]['maturity_type'] == 2){
                    $maturitydate = date('Y-m-d', strtotime("+" . ($reg_1[0]['maturity_days'] + $reg_1[0]['closing_maturity_days']) . " days", strtotime($reg[0]['reg_date'])));

                }
                else{
                    $maturitydate = date('Y-m-d', strtotime("+" .$reg_1[0]['closing_maturity_days'] . " days", strtotime($reg[0]['reg_date'])));
                } 
            }
		
			$account = array(
				'customer' => array(
					'customerid'    => $reg_1[0]['id_customer'],
					'customerName'  => $reg[0]['firstname'],
					'mobileNo'      => $reg[0]['mobile'],
					'branch'        => (int) $reg[0]['id_branch'],
					'insert_update' => 1,
					'cardnumber'    => $reg[0]['mobile'],
					'nomineeMobile' => $reg_1[0]['nomineeMobile'],
					'doorNo'        => 'NA',
					'street'        => 'NA',
					'area'          => $reg_1[0]['area'],
					'taluk'         => 'NA',
					'city'          => $reg_1[0]['city'],
					'pinCode'       => $reg_1[0]['pincode'],
					'state'         => $reg_1[0]['state']
				),
				'joining' => array(
					'clientid'     => $reg[0]['clientid'],
                    'schemeCode'   => $reg[0]['sync_scheme_code'],
					'schemeid'     => (int) $reg_1[0]['id_scheme'],
					'schemerefid'  => (int) $id_scheme_account,
					'schemeamount' => (int) $pay_data[0]['amount'],
					'cardnumber'   => $reg[0]['mobile'],
					'groupname'    => $reg[0]['sync_scheme_code'],
					'startdate'    => $reg[0]['reg_date'],
					'enddate'      => $maturitydate,
					'branch'       => (int) $reg[0]['id_branch'],
					'digi'         => (int) $reg[0]['is_digi']
				)
			);
            
			$response = $this->sendtoDirectApi('/scheme-joining-insertion/insert',$account);

			if($response->success == true) {
				$cus_reg_data = $this->$model->getCustomerRegbyID($id_scheme_account);
				
				$acc_data = array(
					'scheme_acc_number' => $response->data->softwareJoinNo,
					'ref_no'            => $response->data->clientid,
					'date_upd'          => date("Y-m-d H:i:s")
				);

				if (!empty($response->data->softwareJoinNo) && !empty($cus_reg_data[0]['id_customer_reg'])) {
					$acc_status = $this->$model->update_account(
						$acc_data,
						$cus_reg_data[0]['id_scheme_account'],
						$cus_reg_data[0]['id_customer_reg']
					);
				}
				
			}

			// Log response
			if (!is_dir($this->log_dir.'/directAPI')) {
				mkdir($this->log_dir.'/directAPI', 0777, true);
			}

			$log_path = $this->log_dir.'/directAPI/response'.date("Y-m-d").'.txt';
			$ldata = "\n".date('d-m-Y H:i:s')
				." \n Acc Postdata : ".json_encode($account,true)
				." \n Acc Response :".json_encode($response,true);

			file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
		}

		$isTranExists = $this->$model->checkTransExists($ref_no);
	
		$payID_data = $this->$model->getPayIDdet($id_payment);

        $pay_data[0]['client_id'] = $payID_data[0]['clientid'] ;
        

		if(!$isTranExists['status'])
		{

			//insert payment detail

			$pay_data[0]['record_to'] = 1;	

			$pay_data[0]['payment_type'] = 1;	// 1 - online 

			$status =	$this->$model->insert_transaction($pay_data[0]); 

			if(!empty($payID_data[0]['scheme_acc_number'])){
			    $runPayDirect = true;
            }

		} 
        /* elseif ($isTranExists['status'] && ($isTranExists['clientid'] == null || $isTranExists['clientid'] == '') && $chit_settings['gent_clientid'] == 1) {
			$trans_data = array(
				'client_id' => $pay_data[0]['client_id']
			);
			$this->$model->update_transaction($trans_data,$isTranExists['id_transaction']);

			$runPayDirect = true;
		}  */
        else if ($isTranExists['status'] && $isTranExists['is_transferred'] == 'N' && !empty($payID_data[0]['scheme_acc_number'])) {

			$runPayDirect = true;

		}else {
			
			$runPayDirect = false;
		}

			//For online payments Send in direct API and update receipt no , ref no 

		if ($runPayDirect && $this->config->item('directAPI') == '1') {
				$payment = array(
                    "payment" => array(
                        "paymentbranch" => (int)$pay_data[0]['id_branch'],
                        "schemeid" => (int)$payID_data[0]['id_scheme'],
                        "schemename" => $payID_data[0]['scheme_name'],
                        "schemeamount" => (float)$pay_data[0]['amount'],
                        "groupno" => ($payID_data[0]['scheme_acc_number'] ? $payID_data[0]['scheme_acc_number'] : ''),
                        "groupname" => ($payID_data[0]['group_code'] != "" ? $payID_data[0]['group_code'] : ''),
                        "customermobile" => (int) $pay_data[0]['mobile'],
                        "cardnumber" => $pay_data[0]['mobile'],
                        "customerid" => (int)$payID_data[0]['id_customer'],
                        "monthyear" => $pay_data[0]['payment_date'],
                        "saved_weight" => (float)$pay_data[0]['weight'],
                        "saved_benefitswt" => (float)$pay_data[0]['saved_benefits_wgt'],
                        "saved_benefit_amt" => (float)$pay_data[0]['saved_benefit_amt'],
                        "installment" => (int) $payID_data[0]['installment'],
                        "benefit_value" => $pay_data[0]['benefit_value'],
                        "benefit_type" => (int) $pay_data[0]['benefit_type'],
                        "is_digi" => (int) $pay_data[0]['is_digi'],
                        "goldrate" => (int)$pay_data[0]['rate'],
                        "customername" => $payID_data[0]['customername'],
                        "onlinepaymentrefid" => ($pay_data[0]['pay_trans_id'] ? $pay_data[0]['pay_trans_id'] : ''),
                        "onlinepayment" => ($payID_data[0]['added_by'] == 2 || $payID_data[0]['added_by'] == 4) ? 1 : 0,
                        "onlineamount" => (float)$pay_data[0]['amount'],
                        "clientid" => ($payID_data[0]['clientid'] ? $payID_data[0]['clientid'] : ''),
                        "schemerefid" => (string) $id_payment
                    )
			);
				
				$response = $this->sendtoDirectApi('/scheme-payment-entry-insertion/insert',$payment);
						
				if ($response->success == true) {
					$isClientID =  $this->$model->checkClientID($pay_data[0]['id_scheme_account'],$response->data->clientid);

					if (!empty($response->data->softwareVchNo)) {
						if ($isClientID['status']) {
							$pay_array = array(
								'receipt_no' => $response->data->softwareVchNo,
								'date_upd'	 => date("Y-m-d H:i:s")
							);

							$pay_status = $this->$model->updatedirPayment($pay_array,$pay_data[0]['ref_no']);

						}
					}
					
				}

				if (!is_dir($this->log_dir.'/directAPI')) 
				{
					mkdir($this->log_dir.'/directAPI', 0777, true);
				}
				$log_path = $this->log_dir.'/directAPI/response'.date("Y-m-d").'.txt';
				$ldata = "\n".date('d-m-Y H:i:s')." \n Postdata : ".json_encode($payment,true)." \n Response :".json_encode($response,true);
				file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
		}
		
		//echo $this->db->last_query();exit;

		return true;

	}

    function sendtoDirectApi($api,$postData)
	{
		$this->load->helper('directapi');

		// Host was unreachable a moment ago - don't make this request wait on it again.
		if (directapi_is_down()) {
			return false;
		}

		$url = $this->config->item('directAPIurl').$api;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CONNECTTIMEOUT => directapi_config('directAPI_connect_timeout', 5),
			CURLOPT_TIMEOUT => directapi_config('directAPI_timeout', 30),
			CURLOPT_NOSIGNAL => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($postData),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		//print_r($response);exit;

		$errno = curl_errno($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			if (directapi_is_connection_error($errno)) {
				directapi_mark_down($err);
			}
			return false;
		} else {
			directapi_mark_up();
			return json_decode($response);
		}
	}

}
