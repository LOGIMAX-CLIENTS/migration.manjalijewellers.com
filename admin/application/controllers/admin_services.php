<?php

if (! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_services extends CI_Controller

{

	const MODEL	= "admin_usersms_model";

	const CUS_MODEL	= "customer_model";

	const VIEW = 'sms/';

	const GRP = 'sms/group/';

	const SMS_VIEW = 'sms/smsService/';

	const NOTI_VIEW = 'notification/';

	const SET_MODEL = 'admin_settings_model';

	const EMAIL_MODEL = 'email_model';

	const ADM_MODEL = 'chitadmin_model';

	const PAY_MODEL = 'payment_model';

	const PAY_VIEW  = "payment/";

	const SET_VIEW  = "scheme/settlement/";

	const API_MODEL = 'chitapi_model';

	const ACC_MODEL = 'account_model';

	const SMS_MODEL = 'admin_usersms_model';

	const LOG_MODEL = "log_model";

	const MAIL_MODEL = "email_model";

	const SERV_MODEL = 'services_model';

	const TEST_VIEW = "settings/";

	const SYN_MODEL = "syncapi_model";

	const OFF_DATA_FILE_PATH = "assets/offline_data/2021-03-08/cus-reg/";

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::MODEL);

		$this->load->model(self::SERV_MODEL);

		$this->load->model(self::SET_MODEL);

		$this->load->model(self::CUS_MODEL);

		$this->load->model(self::EMAIL_MODEL);

		$this->load->model(self::ADM_MODEL);

		$this->load->model(self::PAY_MODEL);

		$this->load->model(self::ACC_MODEL);

		$this->load->model(self::SMS_MODEL);

		$this->load->model(self::LOG_MODEL);

		$this->load->model(self::MAIL_MODEL);

		$this->load->model("sms_model");

		$this->load->model("sktm_syncapi_model");
		$this->load->model("chit_transaction_model");

		$this->load->model(self::SYN_MODEL);

		$this->employee =  $this->session->userdata('uid');

		$this->company = $this->admin_settings_model->get_company();

		$this->chit_set = $this->admin_settings_model->get_settings();

		$this->log_dir = 'log/';

		if (!is_dir($this->log_dir)) {

			mkdir($this->log_dir, 0777, TRUE);
		}
	}

	function check_expiry()

	{

		$model = self::SET_MODEL;

		$this->db->trans_begin();

		$expired_date_data = $this->$model->getExpiredData();

		/*  echo "<pre>";print_r($expired_date_data);echo "</pre>";exit;*/

		foreach ($expired_date_data as $expired_data) {

			if ($expired_data['active'] == 1) {

				$data = array('active' => 0);

				$data = $this->$model->update_new_arrivals($data, $expired_data['id_new_arrivals']);

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();
				} else {

					$this->db->trans_rollback();

					echo 'Something went wrong';
				}
			}
		}
	}

	function set_image($filename)

	{

		$data = array();

		$model = self::SET_MODEL;

		if ($_FILES['notification_img']['name']) {

			$path = 'assets/img/notification/';

			if (!is_dir($path)) {

				mkdir($path, 0777, TRUE);
			}

			/*else{

			$file = $path.$id.".jpg" ;

			chmod($path,0777);

	        unlink($file);

		}*/

			$img = $_FILES['notification_img']['tmp_name'];

			$imgpath = 'assets/img/notification/' . $filename;

			$upload = $this->upload_img('noti_img', $imgpath, $img);
		}
	}

	function upload_img($outputImage, $dst, $img)

	{

		if (($img_info = getimagesize($img)) === FALSE) {

			// die("Image not found or not an image");

			return false;
		}

		$width = $img_info[0];

		$height = $img_info[1];

		switch ($img_info[2]) {

			case IMAGETYPE_GIF:
				$src = imagecreatefromgif($img);

				$tmp = imagecreatetruecolor($width, $height);

				$kek = imagecolorallocate($tmp, 255, 255, 255);

				imagefill($tmp, 0, 0, $kek);

				break;

			case IMAGETYPE_JPEG:
				$src = imagecreatefromjpeg($img);

				$tmp = imagecreatetruecolor($width, $height);

				break;

			case IMAGETYPE_PNG:
				$src = imagecreatefrompng($img);

				$tmp = imagecreatetruecolor($width, $height);

				$kek = imagecolorallocate($tmp, 255, 255, 255);

				imagefill($tmp, 0, 0, $kek);

				break;

			default: //die("Unknown filetype");	

				return false;
		}

		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);

		imagejpeg($tmp, $dst, 60);
	}

	// to send lowest rate notification 

	function send_lowestrate_noti()
	{

		$model = self::MODEL;

		$result = array();

		$send_notif = $this->$model->check_noti_settings();

		if ($send_notif == 1) {

			//send lowest rate notification

			$data = $this->$model->getnotiData('9');

			//print_r($data);exit;

			$a = 0;

			foreach ($data['data']  as $r) {

				if (sizeof($r['token']) > 0) {

					$arraycontent = array(
						'token' => $r['token'],

						'notification_service' => 9,

						'header' => $data['header'],

						'message' => $r['message'],

						'mobile' => $r['mobile'],

						'footer' => $data['footer']

					);

					$rate = $this->send_singlealert_notification($arraycontent);

					$result['rate'][$a] = $rate;

					$a++;
				}
			}
		}

		return $result;
	}

	// to send rate notification 

	function send_rate_noti()
	{

		$model = self::MODEL;

		$result = array();

		$send_notif = $this->$model->check_noti_settings();

		if ($send_notif == 1) {

			//send rate notification

			$data = $this->$model->get_cusnotiData('1');

			$a = 0;

			foreach ($data['data']  as $r) {

				if (sizeof($r['token']) > 0) {

					$arraycontent = array(
						'token' => $r['token'],

						'notification_service' => 1,

						'header' => $data['header'],

						'message' => $r['message'],

						'mobile' => $r['mobile'],

						'footer' => $data['footer']

					);

					$rate = $this->send_singlealert_notification($arraycontent);

					$result['rate'][$a] = $rate;

					$a++;
				}
			}
		}

		return $result;
	}

	// send due,bday,wday notification by service call //

	function send_notification()
	{

		$model = self::MODEL;

		$result = array();

		$send_notif = $this->$model->check_noti_settings();

		if ($send_notif == 1) {

			//send today due notification

			$noti_settings = $this->$model->get_noti_settings(4);

			$sendDueAlert = 0;

			if (date('d') >= $noti_settings['send_daily_from'] && $noti_settings['send_daily_from'] != 0) {

				// Ex: send_daily_from = 25 , then notification will be sent daily from 25 th //HH

				$sendDueAlert = 1;
			} else {

				// Send Due alert Multiple Dates//HH

				$dates = explode(',', $noti_settings['send_notif_on']);

				foreach ($dates as $date) {

					if ($date == date('d')) {

						$sendDueAlert = 1;
					}
				}
			}

			if ($sendDueAlert == 1) {

				$data = $this->$model->get_cusnotiData('4');

				//	echo "<pre>";print_r($data);echo "</pre>";exit;

				$i = 0;

				$arraycontent = array();

				$targetUrl = '#/app/paydues';

				foreach ($data['data']  as $row) {

					if (((sizeof($row['token']) > 0) || sizeof($row['mobile'] > 0))) {

						$arraycontent = array(
							'token' => $row['token'],

							'notification_service' => 4,

							'header' => $data['header'],

							'message' => $row['message'],

							'mobile' => $row['mobile'],

							'footer' => $data['footer'],

							'id_customer' => $row['id_customer'],

							'targetUrl' => $targetUrl,

							'currentpaycount' => $row['currentpaycount']

						);

						//echo "<pre>";print_r($arraycontent);echo "</pre>";

						if ($row['currentpaycount'] == 0) {

							if ($this->config->item('sms_gateway') == '1') {

								$this->sms_model->sendSMS_MSG91($row['mobile'], $row['message']);
							} elseif ($this->config->item('sms_gateway') == '2') {

								$this->sms_model->sendSMS_Nettyfish($row['mobile'], $row['message'], 'trans');
							} elseif ($this->config->item('sms_gateway') == '3') {

								$this->sms_model->sendSMS_SpearUC($row['mobile'], $row['message'], '', '');
							} elseif ($this->config->item('sms_gateway') == '4') {

								$this->sms_model->sendSMS_Asterixt($row['mobile'], $row['message'], '', '');
							} elseif ($this->config->item('sms_gateway') == '5') {

								$this->sms_model->sendSMS_Qikberry($row['mobile'], $row['message'], '', '');
							}

							$res = $this->send_singlealert_notification($arraycontent);

							$r = json_decode($res);

							if ($r->recipients > 0) {

								$status = $this->$model->insert_sent_notification($arraycontent);
							}

							$result['res'][$i] = $res;

							if ($status) {

								$alertcount = $alertcount + 1;
							}
						}
					}

					$i++;
				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$msg = $alertcount . ' Customer(s) Due ALert Notification sent successfully.';

					echo  $msg;
				} else {

					$this->db->trans_rollback();

					echo "Did't Due ALert Notification sent.";
				}
			}

			//send birthday wish

			$data = $this->$model->get_cusnotiData('7');

			$a = 0;

			$otp_promotion = 1;

			//$arraycontent=array();

			$targetUrl = '#/app/notification';

			foreach ($data['data']  as $bday) {

				if (((sizeof($bday['token']) > 0) || sizeof($bday['mobile'] > 0))) {

					$arraycontent = array(
						'token' => $bday['token'],

						'notification_service' => 7,

						'header' => $data['header'],

						'message' => $bday['message'],

						'mobile' => $bday['mobile'],

						'footer' => $data['footer'],

						'id_customer' => $bday['id_customer'],

						'targetUrl' => $targetUrl

					);

					if ($this->config->item('sms_gateway') == '1') {

						$this->sms_model->sendSMS_MSG91($bday['mobile'], $bday['message'], 1);
					} elseif ($this->config->item('sms_gateway') == '2') {

						$this->sms_model->sendSMS_Nettyfish($bday['mobile'], $bday['message'], 'trans');
					} elseif ($this->config->item('sms_gateway') == '3') {

						$this->sms_model->sendSMS_SpearUC($bday['mobile'], $bday['message'], '', '');
					} elseif ($this->config->item('sms_gateway') == '4') {

						$this->sms_model->sendSMS_Asterixt($bday['mobile'], $bday['message'], '', '');
					} elseif ($this->config->item('sms_gateway') == '5') {

						$this->sms_model->sendSMS_Qikberry($bday['mobile'], $bday['message'], '', '');
					}

					$bdaywish = $this->send_singlealert_notification($arraycontent);

					$res = json_decode($bdaywish);

					if ($res->recipients > 0) {

						$this->$model->insert_sent_notification($arraycontent);
					}

					$result['bdaywish'][$a] = $bdaywish;
				}

				$a++;
			}

			//send wedding day wish

			$res = $this->$model->get_cusnotiData('8');

			$b = 0;

			$otp_promotion = 1;

			//$array_content=array();

			$targetUrl = '#/app/notification';

			foreach ($res['data']  as $wedwish) {

				if (((sizeof($wedwish['token']) > 0) || sizeof($wedwish['mobile'] > 0))) {

					$array_content = array(
						'token' => $wedwish['token'],

						'notification_service' => 8,

						'header' => $res['header'],

						'message' => $wedwish['message'],

						'mobile' => $wedwish['mobile'],

						'footer' => $res['footer'],

						'id_customer' => $wedwish['id_customer'],

						'targetUrl' => $targetUrl

					);

					//echo "<pre>";print_r($array_content);echo "</pre>"; 

					if ($this->config->item('sms_gateway') == '1') {

						$this->sms_model->sendSMS_MSG91($wedwish['mobile'], $wedwish['message'], 1);
					} elseif ($this->config->item('sms_gateway') == '2') {

						$this->sms_model->sendSMS_Nettyfish($wedwish['mobile'], $wedwish['message'], 'trans');
					} elseif ($this->config->item('sms_gateway') == '3') {

						$this->sms_model->sendSMS_SpearUC($wedwish['mobile'], $wedwish['message'], '', '');
					} elseif ($this->config->item('sms_gateway') == '4') {

						$this->sms_model->sendSMS_Asterixt($wedwish['mobile'], $wedwish['message'], '', '');
					} elseif ($this->config->item('sms_gateway') == '5') {

						$this->sms_model->sendSMS_Qikberry($wedwish['mobile'], $wedwish['message'], '', '');
					}

					$wedwish = $this->send_singlealert_notification($array_content);

					$res = json_decode($wedwish);

					if ($res->recipients > 0) {

						$this->$model->insert_sent_notification($array_content);
					}

					$result['wedwish'][$b] = $wedwish;
				}

				$b++;
			}
		}

		//print_r($result);

		return $result;
	}

	// send due sms single  & multipl continus date by service call //HH  

	function send_smsdue()
	{

		$model = self::MODEL;

		$result = array();

		$sendSMS = $this->$model->get_sms_settings(22);

		//	echo "<pre>";print_r($sendSMS);echo "</pre>";exit;

		if ($sendSMS) {

			//send today due sms

			$sendDueAlert = 0;

			if (date('d') >= $sendSMS['send_daily_from'] && $sendSMS['send_daily_from'] != 0) {

				// Ex: send_daily_from = 25 , then sms will be sent daily from 25 th

				$sendDueAlert = 1;
			} else {

				// Send Due sms alert Multiple Dates

				$dates = explode(',', $sendSMS['send_sms_on']);

				foreach ($dates as $date) {

					// echo "<pre>";print_r($date);exit;

					if ($date == date('d')) {

						$sendDueAlert = 1;
					}
				}
			}

			if ($sendDueAlert == 1) {

				$data = $this->$model->get_SMS_due('22');

				//	echo "<pre>";print_r($data);echo "</pre>";exit;

				$arraycontent = array();

				foreach ($data['data']  as $row) {

					if ((sizeof($row['mobile'] > 0))) {

						$arraycontent = array(
							'id_services' => 22,

							'message' => $row['message'],

							'mobile' => $row['mobile'],

							'footer' => $data['footer'],

							'id_customer' => $row['id_customer'],

							'currentpaycount' => $row['currentpaycount']

						);

						//print_r($arraycontent);exit;

						if ($row['currentpaycount'] == 0) {

							if ($this->config->item('sms_gateway') == '1') {

								$status = $this->sms_model->sendSMS_MSG91($row['mobile'], $row['message'], $row['dlt_te_id']);

								//print_r($status);exit;  

							} elseif ($this->config->item('sms_gateway') == '2') {

								$this->sms_model->sendSMS_Nettyfish($row['mobile'], $row['message'], 'promo');
							} elseif ($this->config->item('sms_gateway') == '3') {

								$status = $this->sms_model->sendSMS_SpearUC($row['mobile'], $row['message'], '', $row['dlt_te_id']);
							} elseif ($this->config->item('sms_gateway') == '4') {

								$status = $this->sms_model->sendSMS_Asterixt($row['mobile'], $row['message'], '', $row['dlt_te_id']);
							} elseif ($this->config->item('sms_gateway') == '5') {

								$status = $this->sms_model->sendSMS_Qikberry($row['mobile'], $row['message'], '', $row['dlt_te_id']);
							}

							if ($status) {

								$alertcount = $alertcount + 1;

								// Log

								$sub_log_path = $this->log_dir . 'duesms/';

								$log_path = $sub_log_path . date("Y-m-d") . '.txt';

								if (!is_dir($sub_log_path)) {

									mkdir($sub_log_path, 0777, true);
								}

								$data = "\n ----- \n due sms Services " . date("Y-m-d H:i:s") . ": " . json_encode($arraycontent);

								// print_r($data);exit;

								file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
							} else {

								// Log

								$sub_log_path = $this->log_dir . 'duesms/';

								$log_path = $sub_log_path . date("Y-m-d") . '.txt';

								if (!is_dir($sub_log_path)) {

									mkdir($sub_log_path, 0777, true);
								}

								$data = "\n ----- \n due sms Services " . date("Y-m-d H:i:s") . ": " . json_encode($arraycontent);

								// print_r($data);exit;

								file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
							}
						}
					}
				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$msg = $alertcount . ' Customer(s) Due ALert sms sent successfully.';

					echo  $msg;
				} else {

					$this->db->trans_rollback();

					echo "Did't Due ALert sms sent.";
				}
			}

			//return true;	

		}
	}  	// send due sms by service call//   

	function send_singlealert_notification($alertdetails = array())

	{

		$registrationIds = array();

		$registrationIds[0] = $alertdetails['token'];

		$content = array(

			"en" => $alertdetails['message']

		);

		if ($alertdetails['notification_service'] == '2') {

			$targetUrl = '#/app/offers';
		} else if ($alertdetails['notification_service'] == '3') {

			$targetUrl = '#/app/newarrivals';
		} else if ($alertdetails['notification_service'] == '4' || $alertdetails['notification_service'] == '5' || $alertdetails['notification_service'] == '6') {

			$targetUrl = '#/app/paydues';
		} else {

			$targetUrl = '#/app/notification';
		}

		$fields = array(

			'app_id' => $this->config->item('app_id'),

			'include_player_ids' => $registrationIds,

			'contents' => $content,

			'headings' => array("en" => $alertdetails['header']),

			'subtitle' => array("en" => $alertdetails['footer']),

			'data' => array('targetUrl' => $targetUrl, 'noti_service' => $alertdetails['notification_service'], 'mobile' => $alertdetails['mobile']),

			'big_picture' => (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : " ")

		);

		$auth_key = $this->config->item('authentication_key');

		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',

			'Authorization: Basic ' . $auth_key
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);

		curl_close($ch);

		//print_r($response);

		return $response;
	}

	public function getmetal_content($noti_data)

	{

		$model = self::MODEL;

		$send_notif = $this->$model->check_noti_settings();

		$metalrate['message'] = $this->$model->get_noticontent($noti_data);

		$noti = $this->$model->get_noti($noti_data['notification_service']);

		if ($send_notif == 1) {

			$data['token'] = $this->$model->getDevicetokens();

			$i = 1;

			foreach ($data['token']  as $row) {

				if (sizeof($row['token']) > 0) {

					$metalcontent = array(
						'token' => $row['token'],

						'notification_service' => $noti['id_notification'],

						'header' => $noti['noti_name'],

						'message' => $metalrate['message'],

						'mobile' => $row['mobile'],

						'footer' => $noti['noti_footer'],

					);

					//echo "<pre>";print_r($metalrate['message']); 

					$res = $this->send_singlealert_notification($metalcontent);

					$result['noti'][$i] = $res;

					$i++;
				}
			}
		}

		return $result;
	}

	public function lowest_goldrate_noti()

	{

		if (date('d') > 10) {

			$model = self::MODEL;

			//$lowmatelrate=$this->$model->metalrate_gold('ALL');

			$lowmatelrate_month = $this->$model->metalrate_gold('TM');

			//$lowmatelrate_yesterday=$this->$model->metalrate_gold('Y');

			$goldrate_today = $this->$model->metalrate_gold('T');

			/*if(isset($goldrate_today['goldrate_22ct'])<=isset($lowmatelrate['goldrate_22ct']))

		{

			$data=array(

							'tgoldrate_22ct'=>$goldrate_today['goldrate_22ct'],

							'ygoldrate_22ct'=>$lowmatelrate['goldrate_22ct'],

							'notification_service'=>11

							);

			$this->getmetal_content($data);	

		}*/

			if (isset($goldrate_today['goldrate_22ct']) <= isset($lowmatelrate_month['goldrate_22ct'])) {

				$data = array(

					'tgoldrate_22ct' => $goldrate_today['goldrate_22ct'],

					'ygoldrate_22ct' => $lowmatelrate_month['goldrate_22ct'],

					'notification_service' => 9

				);

				$res = $this->getmetal_content($data);
			}

			/*	elseif(isset($goldrate_today['goldrate_22ct'])<=isset($lowmatelrate_yesterday['goldrate_22ct']))

		{

							$data=array(

							'tgoldrate_22ct'=>$goldrate_today['goldrate_22ct'],

							'ygoldrate_22ct'=>$lowmatelrate_yesterday['goldrate_22ct'],

							'notification_service'=>9

							);

			$this->getmetal_content($data);

		} */
		}

		return true;
	}

	// scheme free payments installments

	function scheme_freepayment()

	{

		$model = self::PAY_MODEL;

		$freepaycust = $this->$model->get_freepaycust();

		$insertCount = 0;

		$status = array();

		$this->db->trans_begin();

		foreach ($freepaycust as $data) {

			$free_ins = explode(',', $data['free_payInstallments']);

			foreach ($free_ins as $ins) {

				if ($data['paid_installments'] + 1 == $ins && $data['has_free_ins'] == 1 && $data['last_paid_month'] != date('m-Y')) {

					$insertData = $this->getFreePayData($data);

					$status = $this->$model->paymentDB("insert", "", $insertData);

					if ($status['insertID']) {

						$insertCount = $insertCount + 1;
					}
				}
			}

			if ($status) {

				$pay_status_array = array(

					'id_payment'		=> (isset($status['insertID']) ? $status['insertID'] : NULL),

					'id_status_msg' 	=>  'success',

					'charges' 			=>  '',

					'id_employee' 		=>  $this->session->userdata('uid'),

					'date_upd'			=>  date('Y-m-d H:i:s')

				);

				$ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);

				//send sms/mail to Customer 

				$payData =  $this->$model->getPpayment_data($status['insertID']);

				$mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";

				$mailtype = 2;

				$this->sendSMSMail('3', $payData, $mailSubject, $mailtype, $status['insertID']);

				//send sms/mail to Customer 

				$payData =  $this->$model->getPpayment_data($status['insertID']);

				$mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";

				$mailtype = 3;

				$this->sendSMSMail('7', $payData, $mailSubject, $mailtype, $status['insertID']);
			}
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$msg = $insertCount . ' payment(s) credited successfully.';

			echo  $msg;
		} else {

			$this->db->trans_rollback();

			echo "Something went wrong";
		}
	}

	function getFreePayData($sch_data)

	{

		$model		 = self::PAY_MODEL;

		$metal_rate  = $this->payment_model->getMetalRate();

		$gold_rate   = number_format((float)$metal_rate['goldrate_22ct'], 2, '.', '');

		$converted_wgt = number_format((float)($sch_data['amount'] / $gold_rate), 3, '.', '');

		$fxd_wgt 	= $sch_data['max_weight'];

		if ($sch_data['receipt_no_set'] == 1) {

			$receipt_no = $this->generate_receipt_no();
		}

		if ($sch_data['gst_type'] == 0) {

			$gst_amt = $sch_data['amount'] - ($sch_data['amount'] * (100 / (100 + $sch_data['gst'])));

			$cal_amt = $sch_data['amount'] - $gst_amt;
		}

		if ($sch_data['gst_type'] == 1) {

			$gst_amt = $sch_data['amount'] * $sch_data['gst'] / 100;

			$cal_amt = $sch_data['amount'] + $gst_amt;
		}

		$insertData = array(

			"id_scheme_account"	 => $sch_data['id_scheme_account'],

			"date_payment" 		 => date('Y-m-d H:i:s'),

			"payment_type" 	     => "Cost free payment",

			"payment_mode" 	     => "FP",

			"act_amount" 	     => NULL,

			"payment_amount" 	 => $sch_data['amount'],

			"due_type" 	         => 'D',

			"no_of_dues" 	     => '1',

			"metal_rate"         => $gold_rate,

			"metal_weight"       => ($sch_data['scheme_type'] == 2 ? $converted_wgt : ($sch_data['scheme_type'] == 1 ? $fxd_wgt : 0.000)),

			"remark"             => "Paid by " . $sch_data['company_name'],

			"payment_status"     => '1',

			"receipt_no"     	 => $receipt_no,

			"gst"				=> $sch_data['gst'],

			"gst_type"			=> $sch_data['gst_type']

		);

		//echo "<pre>";print_r($insertData);echo "</pre>";exit;

		return 	$insertData;
	}

	function generate_receipt_no($id_scheme, $branch)

	{

		$model =	self::PAY_MODEL;

		$rcpt_no = "";

		$rcpt = $this->$model->get_receipt_no($id_scheme, $branch);

		if ($rcpt != NULL) {

			if ($this->config->item('receipTcode') != '') {          // based on the config settings to removed comp shortcode front of recp num //HH

				$temp = explode($this->company['short_code'], $rcpt);

				if (isset($temp)) {

					$number = (int) $temp[1];

					$number++;

					$rcpt_no = $this->company['short_code'] . str_pad($number, 7, '0', STR_PAD_LEFT);

					//print_r($rcpt_no);exit;

				}
			} else {

				$number = (int) $rcpt;

				$number++;

				$rcpt_no = str_pad($number, 7, '0', STR_PAD_LEFT);

				//print_r($rcpt_no);exit;

			}
		} else {

			if ($this->config->item('receipTcode') != '') {

				$rcpt_no = $this->company['short_code'] . "000001";
			} else {

				$rcpt_no = "000001";
			}
		}

		//print_r($rcpt_no);exit;

		return $rcpt_no;
	}

	// scheme free payments installments

	function sendSMSMail($serviceID, $data, $subject, $type, $id)

	{

		$ser_model = self::SET_MODEL;

		$mail_model = self::MAIL_MODEL;

		$service = $this->$ser_model->get_service($serviceID);

		$email	=  $data['email'];

		$sms_model = self::SMS_MODEL;

		if ($service['serv_email'] == 1  && $email != '') {

			$data['payData'] = $data;

			$data['company_details'] = $this->company;

			$data['type'] = $type;

			$to = $email;

			$message = $this->load->view('include/emailPayment', $data, true);

			$sendEmail = $this->$mail_model->send_email($to, $subject, $message);
		}

		if ($service['serv_sms'] == 1) {

			$data = $this->$sms_model->get_SMS_data($serviceID, $id);

			$mobile_number = $data['mobile'];

			$message = $data['message'];

			if ($this->config->item('sms_gateway') == '1') {

				$this->sms_model->sendSMS_MSG91($mobile_number, $message, 1);
			} elseif ($this->config->item('sms_gateway') == '2') {

				$this->sms_model->sendSMS_Nettyfish($mobile_number, $message, 'promo', $data['dlt_te_id']);
			} elseif ($this->config->item('sms_gateway') == '3') {

				$this->sms_model->sendSMS_SpearUC($mobile_number, $message, '', $data['dlt_te_id']);
			} elseif ($this->config->item('sms_gateway') == '4') {

				$this->sms_model->sendSMS_Asterixt($mobile_number, $message, '', $data['dlt_te_id']);
			} elseif ($this->config->item('sms_gateway') == '5') {

				$this->sms_model->sendSMS_Qikberry($mobile_number, $message, '', $data['dlt_te_id']);
			}
		}

		return true;
	}

	function send_sms($mobile, $message, $otp_prom = "")

	{

		$model = self::ADM_MODEL;

		// '0 - promotion sms , 1 - otp	

		if ($otp_prom != '') {

			$otp_promotion = $otp_prom;
		} else {

			$otp_promotion = 0;
		}

		if ($this->config->item('sms_gateway') == '1') {

			$this->sms_model->sendSMS_MSG91($mobile, $message, $otp_promotion);
		} elseif ($this->config->item('sms_gateway') == '2') {

			$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'promo');
		} elseif ($this->config->item('sms_gateway') == '3') {

			$this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
		} elseif ($this->config->item('sms_gateway') == '4') {

			$this->sms_model->sendSMS_Asterixt($mobile, $message, '', '');
		} elseif ($this->config->item('sms_gateway') == '5') {

			$this->sms_model->sendSMS_Qikberry($mobile, $message, '', '');
		}

		return true;
	}

	//  MJDMA rate notification 

	function send_mjdmarate_notification()

	{

		$model = self::SET_MODEL;

		$chitsettings = $this->$model->settingsDB("get", 1, "");

		/* 1 -> Rate updated & notification sent  2 -> Rate updated & didn\'t send notification ' */

		if ($chitsettings['allow_notification'] == 1) {

			if ($chitsettings['is_ratenoti_sent'] == 2) {

				$sendnoti = $this->sendmjdmarate_noti($chitsettings);

				if ($sendnoti) {

					$upd = $this->$model->settingsDB("update", 1, array('is_ratenoti_sent' => 1));
				}

				print_r($sendnoti);
			} elseif ($chitsettings['is_ratenoti_sent'] == 1) {

				echo 'Notification already sent';
			} else {

				echo 'Something went wrong!!!';
			}
		} else {

			echo 'Notification disabled.';
		}
	}

	// this func rate notifi//

	function sendmjdmarate_noti($chitsettings)
	{

		$model = self::MODEL;

		$result = array();

		//send rate notification

		if ($chitsettings['is_branchwise_rate'] == 1) {

			if ($chitsettings['is_branchwise_cus_reg'] == 1) {

				$branches = $this->$model->getBranches();

				foreach ($branches as $branch) {

					$cusData = $this->$model->get_cusBranchRate($branch, "1,2"); // 1-Automatic,2-partial 

					if (count($cusData) > 0) {

						foreach ($cusData as $cus) {

							if ($cus['metal_rate_type'] == 1 || $cus['metal_rate_type'] == 2) { // 1-Automatic,2-partial

								$noti_msg  = '';

								$resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification =1");

								foreach ($resultset->result() as $row) {

									$noti_msg = $row->noti_msg;

									$noti_footer = $row->noti_footer;

									$noti_header = $row->noti_name;
								}

								$resultset->free_result();

								//Generating Message content

								$field_name = explode('@@', $noti_msg);

								for ($i = 1; $i < count($field_name); $i += 2) {

									$field =  $field_name[$i];

									if (isset($cus[$field])) {

										$noti_msg = str_replace("@@" . $field . "@@", $cus[$field], $noti_msg);
									}
								}

								$field_name_footer = explode('@@', $noti_footer);

								for ($i = 1; $i < count($field_name_footer); $i += 2) {

									if (isset($cus->$field_name_footer[$i])) {

										$noti_footer = str_replace("@@" . $field_name_footer[$i] . "@@", $cus[$field_name_footer[$i]], $noti_footer);
									}
								}

								$arraycontent = array(

									'notification_service'  => 1,

									'header'                => 'Daliy Rate',

									'message'               => $noti_msg,

									'token'                 => $cus['token']

								);

								$result = $this->send_singlealert_rate_notification($arraycontent);
							}
						}
					}
				}
			} else {

				$account = $this->$model->get_account('');

				/*echo "<pre>";print_r($account);

    		    echo $this->db->last_query();exit;*/

				if (count($account) > 0) {

					foreach ($account as $acc) {

						$rate = $this->$model->get_metal_rateby_branch($acc['id_customer']);

						$noti_msg  = '';

						$resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification =1");

						if ($rate['metal_rate_type'] == 1 || $rate['metal_rate_type'] == 2) { // 1-Automatic,2-partial

							foreach ($resultset->result() as $row) {

								$noti_msg = $row->noti_msg;

								$noti_footer = $row->noti_footer;

								$noti_header = $row->noti_name;
							}

							$resultset->free_result();

							foreach ($rate as $cusData) {

								//$msg.="Today ".$rates['name']." Gold Rate Rs.".$rates['goldrate_22ct']."/Gm (22 kt),Silver Rate Rs.".$rates['silverrate_1gm']." Gm at ".$rates['updatetime']." .";

								//Generating Message content

								$field_name = explode('@@', $noti_msg);

								for ($i = 1; $i < count($field_name); $i += 2) {

									$field =  $field_name[$i];

									if (isset($cusData[$field])) {

										$noti_msg = str_replace("@@" . $field . "@@", $cusData[$field], $noti_msg);
									}
								}

								$field_name_footer = explode('@@', $noti_footer);

								for ($i = 1; $i < count($field_name_footer); $i += 2) {

									if (isset($cusData->$field_name_footer[$i])) {

										$noti_footer = str_replace("@@" . $field_name_footer[$i] . "@@", $cusData[$field_name_footer[$i]], $noti_footer);
									}
								}

								$msg .= $noti_msg;
							}

							$arraycontent = array(

								'notification_service'  => 1,

								'header'                => 'Daliy Rate',

								'message'               => $msg,

								'token'                 => $rate[0]['token']

							);

							$result = $this->send_singlealert_rate_notification($arraycontent);
						}
					}
				}
			}
		} else {

			$data = $this->$model->get_cusnotiData('1');

			//print_r($data);exit;

			foreach ($data['data']  as $r) {

				$arraycontent = array(
					'notification_service' => 1,

					'header' => $data['header'],

					'message' => $r['message'],

					'footer' => $data['footer']

				);

				$send = $this->onesignalNotificationToAll($arraycontent);

				$result['rate_noti'] = $send;

				//print_r($send);exit;

			}
		}

		return $result;
	}

	function onesignalNotificationToAll($alertdetails = array())

	{

		$content = array(

			"en" => $alertdetails['message']

		);

		$targetUrl = '#/app/notification';

		$fields = array(

			'app_id' => $this->config->item('app_id'),

			'included_segments' => array(

				'All'

			),

			'contents' => $content,

			'headings' => array("en" => $alertdetails['header']),

			'subtitle' => array("en" => $alertdetails['footer']),

			'data' => array('targetUrl' => $targetUrl, 'noti_service' => $alertdetails['notification_service'], 'mobile' => ''),

			'big_picture' => (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : " ")

		);

		$auth_key = $this->config->item('authentication_key');

		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',

			'Authorization: Basic ' . $auth_key
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);

		curl_close($ch);

		return $response;
	}

	function add_daily_collection() // NOTE : Daily collection service is executed on 2 AM of next day

	{

		$model =	self::SERV_MODEL;

		$previousDay = date('Y-m-d', strtotime("-2 days"));

		$branch = $this->$model->allBranches();

		$this->db->trans_begin();

		//echo $previousDay;

		foreach ($branch as $br) {

			$ydaytoday = $this->$model->daily_collection('get', $previousDay, '', $br['id_branch']);

			if (sizeof($ydaytoday) == 0) {

				$ydaytoday['closing_balance_amt'] = 0;

				$ydaytoday['closing_balance_wgt'] = 0;

				$ydaytoday['closing_weight'] = 0;
			}

			$today = $this->$model->getTodaySummaryBranchWise(date('Y-m-d', strtotime("-1 days")), $br['id_branch']);

			if (sizeof($today['canceled']) == 0) {

				$today['canceled']['today_cancelled_amt'] = 0;

				$today['canceled']['today_cancelled_wgt'] = 0;

				$today['canceled']['weight_cancelled'] = 0;
			}

			if (sizeof($today['collection']) == 0) {

				$today['collection']['today_collection_amt'] = 0;

				$today['collection']['today_collection_wgt'] = 0;

				$today['collection']['today_weight'] = 0;
			}

			if (sizeof($today['closed']) == 0) {

				$today['closed']['amtSchClosedAmt'] = 0;

				$today['closed']['wgtSchClosedAmt'] = 0;

				$today['closed']['wgtSchClosedWgt'] = 0;
			}

			//echo $ydaytoday['closing_balance_amt'];

			$closing_balance_amt =  $ydaytoday['closing_balance_amt'] + $today['collection']['today_collection_amt'] - $today['closed']['amtSchClosedAmt'] - $today['canceled']['today_cancelled_amt'];

			$closing_balance_wgt =  $ydaytoday['closing_balance_wgt'] + $today['collection']['today_collection_wgt'] - $today['closed']['wgtSchClosedAmt'] - $today['canceled']['today_cancelled_wgt'];

			$closing_weight =  $ydaytoday['closing_weight'] + $today['collection']['today_weight'] - $today['closed']['wgtSchClosedWgt'] - $today['canceled']['weight_cancelled'];

			$instoday = array(
				'date' 			=>  date('Y-m-d', strtotime("-1 days")),

				'today_collection_amt' 	=> $today['collection']['today_collection_amt'],

				'today_collection_wgt' 	=> $today['collection']['today_collection_wgt'],

				'today_weight' 			=> $today['collection']['today_weight'],

				'amtSchClosedAmt'		=> $today['closed']['amtSchClosedAmt'],

				'wgtSchClosedAmt'		=> $today['closed']['wgtSchClosedAmt'],

				'wgtSchClosedWgt'		=> $today['closed']['wgtSchClosedWgt'],

				'today_cancelled_amt'	=> $today['canceled']['today_cancelled_amt'],

				'today_cancelled_wgt'	=> $today['canceled']['today_cancelled_wgt'],

				'weight_cancelled'		=> $today['canceled']['weight_cancelled'],

				'closing_balance_amt'	=> number_format($closing_balance_amt, 2, '.', ''),

				'closing_balance_wgt'	=> number_format($closing_balance_wgt, 2, '.', ''),

				'closing_weight'		=> number_format($closing_weight, 2, '.', ''),

				'date_add'				=> date('Y-m-d H:i:s'),

				'id_branch'             => $br['id_branch']

			);

			echo print_r($instoday);

			$this->$model->daily_collection('insert', '', $instoday);
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			echo "Executed successfully.Inserted successfully";
		} else {

			$this->db->trans_rollback();

			echo "Executed successfully.Unable to insert.";
			echo $this->db->_error_message();
		}
	}

	public function ajax_interWallet_trans()

	{

		$id_branch = $_GET['id_branch'];

		$date = $_GET['date'];

		$model_name = self::SET_MODEL;

		$data['trans'] = $this->$model_name->get_interWallet_trans_temp($id_branch, $date);

		echo "Bills : " . sizeof($data['trans']);

		echo "<pre>";

		print_r($data['trans']);
	}

	function loadTempToMainView()
	{

		$model = self::SERV_MODEL;

		$data['main_content'] = self::TEST_VIEW . 'tempTomain';

		$data['tempData'] = $this->$model->getTempData();

		$this->load->view('layout/template', $data);
	}

	function ajaxtempTotest()
	{

		$id_branch = $_POST['id_branch'];

		$entry_date = $_POST['entry_date'];

		$tmp_wal_trans = [1, 2, 3];

		$del_trans_ids = [1, 2, 3];

		$till_update = $_POST['till_updated'];

		$success_ids = [];

		$failed_ids = [];

		$log_path = '../api/logTempToMain.txt';

		$data = "\n ---- \n Branch : " . $id_branch . " Entry Date : " . $entry_date . " \n Till Updated : " . (sizeof($success_ids) + $till_update) . " \n No of records fetched : " . sizeof($tmp_wal_trans) . "\n Success : " . json_encode($success_ids) . "\n Failed : " . json_encode($failed_ids) . "\n Deleted :" . json_encode($del_trans_ids);

		file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);

		echo 0;
	}

	function ajaxtempToMain()
	{

		$model = self::SERV_MODEL;

		$id_branch = $_POST['id_branch'];

		$entry_date = $_POST['entry_date'];

		$till_update = $_POST['till_updated'];

		$del_trans_ids = [];

		$success_ids = [];

		$failed_ids = [];

		$tmp_wal_trans = $this->$model->getIwalTrans_temp($id_branch, $entry_date);

		if (sizeof($tmp_wal_trans) > 0) {

			foreach ($tmp_wal_trans as $t) {

				$main_wal_trans = $this->$model->getIwalTrans_main($t);

				$tmp_wal_details = $this->$model->getIwalTranDetail_tmp($t['id_inter_waltrans_tmp']);

				$this->db->trans_begin();

				if ($main_wal_trans['action'] == 'Add') {

					$insertTran = $this->$model->insertTransinMain($t);

					if (sizeof($tmp_wal_details) > 0 && $insertTran['status']) {

						foreach ($tmp_wal_details as $d) {

							$insertDetail = $this->$model->insertTransDetailInMain($d, $insertTran['id_inter_wallet_trans']);
						}
					}
				} else if ($main_wal_trans['action'] == 'Update') {

					$main_trans = $main_wal_trans['trans'];

					$uTData  = array(
						'actual_redeemed' => $main_trans['actual_redeemed'] + $t['actual_redeemed'],

						'redeem_req_pts' => $main_trans['redeem_req_pts'] + $t['redeem_req_pts']

					);

					$updTran = $this->$model->updTransin_main($uTData, $main_trans['id_inter_wallet_trans']);

					$main_detailData = $this->$model->getIwalTranDetail_main($main_trans['id_inter_wallet_trans']);

					/*echo "<pre> Main :";

					print_r($main_detailData['trans']);

					echo "<br/>";

					echo "<pre> Temp :";

					print_r($tmp_wal_details);

					echo "<br/>";*/

					if (sizeof($main_detailData['trans']) > 0) {

						foreach ($main_detailData['trans'] as $main) {

							$upd = $this->updateTransDetailInmain($main, $tmp_wal_details);

							$tmp_wal_details = $upd['tmp_detail'];
						}
					}

					/*echo "<pre>A del Temp :";

					print_r($tmp_wal_details);

					echo "<br/>";*/

					if (sizeof($tmp_wal_details) > 0) {

						foreach ($tmp_wal_details as $tmp) {

							$ins = $this->$model->insertTransDetailInMain($tmp, $main_trans['id_inter_wallet_trans']);
						}
					}
				}

				// Detete data in temp tables

				$deleteTrans = $this->$model->delTransAndDetail($t['id_inter_waltrans_tmp']);

				if ($deleteTrans) {

					$del_trans_ids[] = $t['id_inter_waltrans_tmp'];
				}

				if ($this->db->trans_status() == TRUE) {

					$this->db->trans_commit();

					$success_ids[] = $t['id_inter_waltrans_tmp'];
				} else {

					$this->db->trans_rollback();

					$failed_ids[] = $t['id_inter_waltrans_tmp'];
				}
			}

			// Log

			$log_path = '../api/logTempToMain.txt';

			$data = "\n ---------------------------------------------- \n Branch : " . $id_branch . " Entry Date : " . $entry_date . " \n No of records fetched : " . sizeof($tmp_wal_trans) . "\n Success : " . json_encode($success_ids) . "\n Failed : " . json_encode($failed_ids) . "\n Deleted :" . json_encode($del_trans_ids) . " \n Executed records : " . (sizeof($success_ids) + $till_update);

			file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);

			// End of log 

			echo sizeof($success_ids);
		} else {

			echo "0";
		}
	}

	/*function tempToMain(){

		$model = self::SERV_MODEL;

		$id_branch = $_GET['id_branch'];

		$entry_date = $_GET['entry_date'];

		$del_trans_ids = [];	

		$tmp_wal_trans = $this->$model->getIwalTrans_temp($id_branch,$entry_date);

		if(sizeof($tmp_wal_trans) > 0){

			foreach($tmp_wal_trans as $t){

				$main_wal_trans = $this->$model->getIwalTrans_main($t);

				$tmp_wal_details = $this->$model->getIwalTranDetail_tmp($t['id_inter_waltrans_tmp']);

				$this->db->trans_begin();

				if($main_wal_trans['action'] == 'Add'){

					$insertTran = $this->$model->insertTransinMain($t);

					if(sizeof($tmp_wal_details) > 0 && $insertTran['status']){

						foreach($tmp_wal_details as $d){ 

							$insertDetail = $this->$model->insertTransDetailInMain($d,$insertTran['id_inter_wallet_trans']);

						}

					}				

				}else if($main_wal_trans['action'] == 'Update'){

					$main_trans = $main_wal_trans['trans']; 

					$uTData  = array('actual_redeemed' => $main_trans['actual_redeemed']+$t['actual_redeemed'],

									'redeem_req_pts' => $main_trans['redeem_req_pts']+$t['redeem_req_pts']

								);

					$updTran = $this->$model->updTransin_main($uTData,$main_trans['id_inter_wallet_trans']);

					$main_detailData = $this->$model->getIwalTranDetail_main($main_trans['id_inter_wallet_trans']); 

					if(sizeof($main_detailData['trans']) > 0){

						foreach($main_detailData['trans'] as $main){ 						

							$upd = $this->updateTransDetailInmain($main,$tmp_wal_details);

							$tmp_wal_details = $upd['tmp_detail'];

						}

					} 

					if(sizeof($tmp_wal_details) > 0){ 

						foreach($tmp_wal_details as $tmp){  

							$ins = $this->$model->insertTransDetailInMain($tmp,$main_trans['id_inter_wallet_trans']);

						}

					} 

				}

				// Detete data in temp tables

				$deleteTrans = $this->$model->delTransAndDetail($t['id_inter_waltrans_tmp']);

				if($deleteTrans){

					$del_trans_ids[]=$t['id_inter_waltrans_tmp'];

				} 

				if($this->db->trans_status() == TRUE){

					$this->db->trans_commit();

					echo "Success : ".$t['id_inter_waltrans_tmp']; 

					echo "############################### <br/>";

				}else{

					$this->db->trans_rollback(); 

					echo "Failed : ".$t['id_inter_waltrans_tmp']." Rolled back <br/>";

					echo "############################### <br/>";

				}

			}

			echo "No of records fetched : ".sizeof($tmp_wal_trans)."<br/>";

			echo "<pre> Deleted : ";

			echo print_r($del_trans_ids);

		}else{

			echo "No records to proceed";

		}

	}*/

	function updateTransDetailInmain($main_detail, $tmp_detail)
	{

		$model = self::SERV_MODEL;

		$i = 0;

		foreach ($tmp_detail as $tmp) {

			if ($tmp['category_code'] == $main_detail['category_code']) {

				$d = array(
					'trans_points' 	=> $tmp['trans_points'] + $main_detail['trans_points'],

					'amount'			=>	$tmp['amount'] + $main_detail['amount'],

					'last_update'	=>	date('Y-m-d H:i:s')

				);

				$res = $this->$model->updateTransDetail_main($d, $main_detail['id_inter_waltransdetail']);

				/*echo "<pre>D Temp :";

				print_r($tmp_detail);

				echo "<br/>"; */

				$tmp_detail = $this->removeElementWithValue($tmp_detail, 'id_tmp_waldetails', $tmp['id_tmp_waldetails']);

				return array('status' => $res, 'tmp_detail' => $tmp_detail);
			}

			$i++;
		}

		return array('tmp_detail' => $tmp_detail);
	}

	function removeElementWithValue($array, $key, $value)
	{

		foreach ($array as $subKey => $subArray) {

			if ($subArray[$key] == $value) {

				unset($array[$subKey]);
			}
		}

		return $array;
	}

	// Reset Wallet Transaction	

	/*function insertWalletTR(){

		$model = self::SERV_MODEL;

		$updated_walAc = []; 

		$id_wallet_account = ($this->session->userdata('id_wallet_account')?$this->session->userdata('id_wallet_account'):NULL);

		$wal_accounts = $this->$model->getWalletAccounts($id_wallet_account);

		if(sizeof($wal_accounts) > 0){

			foreach($wal_accounts as $ac){

				if($ac['mobile'] != NULL){

					$this->db->trans_begin();

					$this->$model->insChitwallet($ac['id_wallet_account'],$ac['mobile'],$ac['id_customer']);

					if($this->db->trans_status() == TRUE){

						$this->db->trans_commit();

						$updated_walAc[] = 	$ac['id_wallet_account'];

						$this->session->set_userdata('id_wallet_account',$ac['id_wallet_account']);

					}else{

						$this->db->trans_rollback();	

					}	

				}			

			}

			echo "No of records fetched : ".sizeof($wal_accounts)."<br/>";

			echo "Session id_wallet_account : ".$this->session->userdata('id_wallet_account')."<br/>";

			echo "<pre> Updated : ";

			echo print_r($updated_walAc);	

		}else{

			echo "No records to proceed";

		}

	}*/

	function send_queue_sms()
	{

		$fields = array(

			'mobile' => array(0 => 8526737799, 1 => 7010198473, 2 => 8489957773, 3 => 6383032800, 4 => 9790100539, 5 => 909529309, 6 => 9688550514, 7 => 9488577633, 8 => 9965473800, 9 => 9080802153, 10 => 9080130172, 11 => 9095563265),

			'message' => "Queue Message"

		);

		$postData = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://nammauzhavan.com/api/v1/smjtvm_sendsms");

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(

			'Content-Type: application/json; charset=utf-8',

			'Authorization: Basic ' . base64_encode("lmx@uzhavan:lmx@2018")

		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);

		curl_close($ch);

		$data['response'] = $response;

		print_r($data);
	}

	function postRequest($url, $_data)
	{

		// convert variables array to string:

		$data = array();

		while (list($n, $v) = each($_data)) {

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

		echo "<pre> DATA : ";
		print_r($data);

		echo "<pre> HOST : ";
		print_r($host);

		echo "<pre> PATH ";
		print_r($path);

		// open a socket connection on port 80

		$fp = fsockopen($host, 80);

		// send the request headers:

		fputs($fp, "POST $path HTTP/1.1\r\n");

		fputs($fp, "Host: $host\r\n");

		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");

		fputs($fp, "Content-length: " . strlen($data) . "\r\n");

		fputs($fp, "Connection: close\r\n\r\n");

		fputs($fp, $data);

		$result = '';

		while (!feof($fp)) {

			// receive the results of the request

			$result .= fgets($fp, 128);
		}

		// close the socket connection:

		fclose($fp);

		// split the result header from the content

		$result = explode("\r\n\r\n", $result, 2);

		$header = isset($result[0]) ? $result[0] : '';

		$content = isset($result[1]) ? $result[1] : '';

		// return as array:

		return array($header, $content);
	}

	function Nettyfish_smsGateway()
	{

		$data = array(

			'apikey' => "wC/ePE0tehvWCcC2IqqUXrv5usSBx8s+hEppxZBbNQ8=",

			'clientid' => "e7126180-afd4-4c50-9865-a85248a87398",

			'msisdn' => "8526737799",

			'sid' => "JWLONE",

			'msg' => "Test Message",

			'fl' => "0",

			"gwid" => 2

		);

		list($header, $content) = $this->postRequest(
			"http://45.127.102.185:6005/api/v2/SendSMS",

			$data

		);

		echo "\n Response : " . $content;
	}

	// Service to Update Maturity Date in scheme_account table (if maturity date is flexible)

	/*function updMaturityDate(){

        $execute = date("Y-m-t", strtotime(date("Y-m-d")));

        if($execute == date("Y-m-d")){

            $this->services_model->updMaturityDate();

        }

        return true;

    }*/

	function syncExistingCusData($last_id_cus)
	{

		$sql = $this->db->query("select mobile,id_customer from customer where id_customer >=" . $last_id_cus . " limit 100");

		$customers = $sql->result_array();

		foreach ($customers as $cus) {

			$syncData = $this->sync_existing_data($cus['mobile'], $cus['id_customer'], 1);

			echo "<pre>";
			print_r($syncData);

			echo $cus['id_customer'];
		}
	}

	function createExistingCus($id_branch)
	{

		$sql = $this->db->query("select * from customer_reg where is_transferred='N' and id_branch=" . $id_branch . " group by mobile limit 20000");

		$customers = $sql->result_array();

		//	echo "<pre>";print_r($customers);exit; 

		$cus_model = self::CUS_MODEL;

		foreach ($customers as $cus) {

			$id_customer = $this->$cus_model->isCustomerExist($cus['mobile'], $cus['id_branch']);

			if ($id_customer == 0) {

				$id_state = $this->$cus_model->get_state_id($cus['state']);

				$id_city = $this->$cus_model->get_city_id($cus['city']);

				$id_village = $this->$cus_model->get_village_id($cus['area']);

				// create customer

				$cus_data = array(

					'info' => array(

						'firstname'			=> (isset($cus['firstname']) ? ucfirst($cus['firstname']) : NULL),

						'lastname' 			=> (isset($cus['lastname']) ? ucfirst($cus['lastname']) : NULL),

						'id_branch'	    	=> (isset($cus['id_branch']) ? $cus['id_branch'] : NULL),

						'date_of_birth'		=> (isset($cus['date_of_birth']) && $cus['date_of_birth'] != '' ? $cus['date_of_birth'] : NULL),

						'date_of_wed'		=> (isset($cus['date_of_wed']) && $cus['date_of_wed'] != '' ? $cus['date_of_wed'] : NULL),

						'email'				=> (isset($cus['email']) ? $cus['email'] : $cus['firstname'] . '_' . rand(0000, 9999) . '@gmail.com'),

						'mobile'			=> (isset($cus['mobile']) ? $cus['mobile'] : NULL),

						'phone'				=> (isset($cus['phone']) ? $cus['phone'] : NULL),

						'passwd'			=>	$this->$cus_model->__encrypt($cus['mobile']),

						'active'			=> (isset($cus['active']) ? $cus['active'] : 1),

						'date_add'			=>  date("Y-m-d H:i:s"),

						'custom_entry_date' => (isset($cus['custom_entry_date']) ? ucfirst($cus['custom_entry_date']) : NULL),

						'added_by'			=>  6,   // import

						'id_village'        => $id_village

					),

					'address' => array(

						'id_country'		=>	101,

						'id_state' 			=>	$id_state,

						'id_city'			=>	$id_city,

						'address1'			=> (isset($cus['address1']) ? $cus['address1'] : NULL),

						'address2'			=> (isset($cus['address2']) ? $cus['address2'] : NULL),

						'address3'			=> (isset($cus['address3']) ? $cus['address3'] : NULL),

						'pincode'			=> (isset($cus['pincode']) ? $cus['pincode'] : NULL),

						'active'			=>	1,

						'date_add'			=>	date("Y-m-d H:i:s")

					)

				);

				echo "<pre>";
				print_r($cus_data);
				echo "</pre>";

				$this->db->trans_begin();

				$cus_id =  $this->$cus_model->insert_customer($cus_data);

				if ($this->db->trans_status() === TRUE && $cus_id > 0) {

					$this->db->trans_commit();

					$syncData = $this->sync_existing_data($cus['mobile'], $cus_id, $id_branch);

					echo "<pre>";
					print_r($syncData);
				} else {

					$this->db->trans_rollback();

					echo "Rolled back";

					echo $this->db->last_query();

					echo $this->db->_error_message();
				}
			} else {

				echo "Customer already exist";

				$syncData = $this->sync_existing_data($cus['mobile'], $id_customer, $id_branch);

				echo "<pre>";
				print_r($syncData);
			}
		}
	}

	function sync_existing_data($mobile, $id_customer, $id_branch)

	{

		$this->load->model("customer_model");

		$data['id_customer'] = $id_customer;

		$data['id_branch'] = $id_branch;

		$data['branchWise'] = 0;

		$data['mobile'] = $mobile;

		$res = $this->customer_model->insExisAcByMobile($data);

		//echo $this->db->last_query();echo $this->db->_error_message();exit;

		if (sizeof($res) > 0) {

			$this->db->trans_begin();

			$payData = $this->customer_model->syncPayData($res);

			//echo $this->db->last_query();echo $this->db->_error_message();exit;

			if (sizeof($payData['succeedIds']) > 0 || $payData['no_records'] > 0) {

				$status = $this->customer_model->updateInterTableStatus($res, $payData['succeedIds']);

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					return array("status" => TRUE, "msg" => "Purchase Plan registered successfully");
				} else {

					echo $this->db->last_query();

					echo $this->db->_error_message();
					exit;

					$this->db->trans_rollback();

					return array("status" => FALSE, "msg" => "Error in updating intermediate tables");
				}
			} else {

				return array("status" => FALSE, "msg" => "Error in updating payment tables");
			}
		} else {

			echo $this->db->last_query();

			return array("status" => FALSE, "msg" => "No records to update in scheme account tables");
		}
	}

	function send_singlealert_rate_notification($alertdetails = array())

	{

		$registrationIds = array();

		$registrationIds[0] = $alertdetails['token'];

		$content = array(

			"en" => $alertdetails['message']

		);

		$targetUrl = '#/app/notification';

		$fields = array(

			'app_id' => $this->config->item('app_id'),

			'include_player_ids' => $registrationIds,

			'contents' => $content,

			'headings' => array("en" => $alertdetails['header']),

			'data' => array('targetUrl' => $targetUrl, 'noti_service' => $alertdetails['notification_service']),

			'big_picture' => (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : " ")

		);

		$auth_key = $this->config->item('authentication_key');

		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',

			'Authorization: Basic ' . $auth_key
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);

		//print_r($response);exit;

		curl_close($ch);

		return $response;
	}

	/*public function sendmjdmarate_noti()

	{

		$model=self::MODEL;		

		$noti=$this->$model->get_metalnotiContent(1);

		$notidata = $noti['data'][0];		

		$data['token']=$this->$model->getDevicetokens();

			//echo "<pre>";print_r($data);echo "</pre>";exit;

			$i =1;

			$token = array();

			foreach ($data['token']  as $row){

				$token[]=$row['token'];

			}	

//			foreach ($data['token']  as $row){

				if(sizeof($token)>0){

					$metalcontent=array('token'					=> $token,

										'notification_service'	=> 1,

										'header'				=> $noti['header'],

										'message'				=> $notidata['message'],

										'mobile'				=> '',

										'footer'				=> $noti['footer'],

					);

					$res = $this->send_bulk_notification($metalcontent);

					$result['noti'][$i]=$res;

					$i++;							

				}

//		}

		return $result;

	}*/

	/*function send_bulk_notification($alertdetails = array()) 

	{

		$regIdChunk = array_chunk($alertdetails['token'],1000);

		foreach($regIdChunk as $RegId){

            $registrationIds = $RegId;

            $content = array(

            "en" => $alertdetails['message']

            );

        	if($alertdetails['notification_service']== '2'){

				$targetUrl='#/app/offers';

			}

			else if($alertdetails['notification_service']== '3'){

				$targetUrl='#/app/newarrivals';

			}

			else if($alertdetails['notification_service']== '4' || $alertdetails['notification_service']== '5' || $alertdetails['notification_service']== '6'){

				$targetUrl='#/app/paydues';

			}

			else{

				$targetUrl='#/app/notification';

			}

            $fields = array(

            'app_id' => $this->config->item('app_id'),

            'include_player_ids' => $registrationIds,

            'contents' => $content,

            'headings' => array("en" => $alertdetails['header']),

            'subtitle' => array("en" => $alertdetails['footer']),

            'data' => array('targetUrl'=>$targetUrl,'noti_service'=>$alertdetails['notification_service'],'mobile'=>$alertdetails['mobile']),

            'big_picture' =>(isset($alertdetails['noti_img'])?$alertdetails['noti_img']:" ")

            );

            $auth_key = $this->config->item('authentication_key');

            $fields = json_encode($fields);

             $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',

              'Authorization: Basic '.$auth_key));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            curl_setopt($ch, CURLOPT_POST, TRUE);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);

            curl_close($ch);

        }

	return $response;

	}*/

	// For SKTM Group (SCM,TKTM only)

	function syncInterData()
	{

		$api_model = "sktm_syncapi_model";

		$acc_model = "account_model";

		$record_to = 2; // 2 - Online 

		$branch_id = null;

		$acc_id = "";

		$acc_rec = 0;

		$trans_rec = 0;

		$records = 0;

		$pay_id = "";

		$rejected_pay_id = "";

		$rejected_acc_id = "";

		$cus_reg_data = $this->$api_model->getcustomerByStatus('N', $branch_id, $record_to);

		if ($cus_reg_data) {

			$records += count($cus_reg_data);

			foreach ($cus_reg_data as $client) {

				// is_registered_online -> 0 - No, 1- Yes , 2 - online record

				if ($client['is_modified'] == 1 && $client['is_registered_online'] >= 1) {

					$this->db->trans_begin();

					// $isClientID =  $this->$api_model->checkClientID($client['id_scheme_account'],"");

					$acc_data = array(

						'closed_by'         => $client['closed_by'],

						'closing_date'      => $client['closing_date'],

						'closing_balance'   => $client['closing_amount'],

						'closing_weight'    => $client['closing_weight'],

						'closing_add_chgs'  => $client['closing_add_chgs'],

						//	'additional_benefits'=> $client['additional_benefits'],

						'remark_close'      => $client['remark_close'],

						'is_closed'         => $client['is_closed'],

						'active'            => ($client['is_closed'] == 1 ? 0 : 1),

						'scheme_acc_number' => $client['scheme_ac_no'],

						'ref_no'            => $client['clientid'],

						'date_upd'	        => date("Y-m-d H:i:s")

					);

					if ($client['id_scheme_account'] != null) {

						$acc_status = $this->$api_model->update_account($acc_data, $client['id_scheme_account'], $client['id_customer_reg']);

						$id = "ID" . '' . $client['id_scheme_account']; // Online record

					} else {

						$acc_status = $this->$api_model->update_accountByClientId($acc_data, $client['clientid'], $client['id_customer_reg']);

						$id = "CID" . '' . $client['clientid']; // Offline record

					}

					if ($this->db->trans_status() == TRUE) {

						$this->db->trans_commit();

						$acc_rec += 1;

						$acc_id .= $id . '|';
					} else {

						$this->db->trans_rollback();

						$rejected_acc_id .= $id . '|';
					}
				}
			}
		}

		//  echo $this->db->last_query();exit;

		$trans_data = $this->$api_model->getTransactionByStatus('N', $branch_id, $record_to);

		if ($trans_data) {

			$records += count($trans_data);

			foreach ($trans_data as $trans) {

				// payment_type -> 1- Online , 2 - Offine

				if ($trans['payment_type'] == 1) {

					// to update online record

					// check whether scheme a/c data updated

					$isClientID =  $this->$api_model->checkClientID($trans['id_scheme_account'], "");

					if ($isClientID['status'] &&  $trans['is_modified'] == 1 && $trans['payment_status'] == 1) {

						$this->db->trans_begin();

						$trans_data = array(
							'receipt_no' =>  $trans['receipt_no'],

							'payment_ref_number' =>  $trans['ref_no'],

							"payment_status" 	=> 1,

							'date_upd'	 => date("Y-m-d H:i:s")
						);

						$updPayment  = $this->$api_model->updatePayment($trans_data, $trans['payment_type'], $trans['id_scheme_account'], $trans['payment_date']);

						$trans_rec += 1;

						if ($this->db->trans_status() == TRUE) {

							$this->db->trans_commit();

							$trans_rec += 1;

							$pay_id .= $trans['ref_no'] . '|';
						} else {

							$this->db->trans_rollback();

							$rejected_pay_id .= $trans['ref_no'] . '|';
						}
					}
				} else if ($trans['payment_type'] == 2 && ($trans['client_id'] != null || $trans['client_id'] != '')) {

					// to update offline record

					$isClientID =  $this->$api_model->checkClientID("", $trans['client_id']);

					if ($isClientID['status']) {

						$this->db->trans_begin();

						if ($trans['payment_status'] == 1) {

							$id_branch = $this->$api_model->get_branchid($trans['branch_code']);

							$pay_array = array(
								"id_scheme_account" => $isClientID['id_scheme_account'],

								"date_payment" 		=> $trans['payment_date'],

								//	"id_metal" 			=> $trans['metal'],

								"metal_rate" 		=> $trans['rate'],

								"payment_amount"	=> $trans['amount'],

								"metal_weight" 		=> $trans['weight'],

								"payment_mode" 		=> $trans['payment_mode'],

								"payment_status" 	=> 1,

								"payment_type" 		=> "Offline",

								"due_type"          => $trans['due_type'],

								"installment" 		=> $trans['installment_no'],

								"receipt_no" 		=> $trans['receipt_no'],

								"remark" 			=> $trans['remarks'],

								"discountAmt"		=> $trans['discountAmt'],

								"payment_ref_number" => $trans['ref_no'],

								"date_upd" 			=> date('Y-m-d H:i:s'),

								"id_branch"         => $id_branch

							);

							$insPayment  = $this->$api_model->insertPayment($pay_array);
							//Mode details update :: Haritha 05-08-2025
            			    if($insPayment > 0)
            			    {
            			        //update mode detail table
            	               	$arrayPayMode = array('payment_amount'     => $trans['amount'],
                                                        'payment_date'		 => $trans['payment_date'],
                                                        'created_time'	     => date("Y-m-d H:i:s"),
                                                        "payment_mode"       => $trans['payment_mode'],
                                                        "remark"             => 'Offline CRON service',
                                                        "payment_ref_number" => $trans['ref_no'],
                                                        "payment_status"     => 1);
    
            	               	
    						    $update_pmd = array('payment_status'=> 9, // Cancelled
                                                    "updated_time"  => date('Y-m-d H:i:s'),
                                                    "remark"        =>  "Removed while direct success on cashfree ".date('Y-m-d H:i:s'),
                                                );
                                $update_existing_pmd = $this->payment_model->updData($update_pmd,'id_payment',$insPayment,'payment_mode_details');
                                $arrayPayMode['id_payment'] = $insPayment;
                                $payModeInsert = $this->payment_model->insertData($arrayPayMode,'payment_mode_details');
            	               	
            	               	//update due_date,installment
            	               	$dt_pay = date('Y-m-d H:i:s',strtotime(str_replace("/","-",$trans['payment_date'])));							
                            	$ins_cycle = $this->payment_model->get_due_date('service', $dt_pay,$isClientID['id_scheme_account']);  
                            					    
    
                                if(sizeof($ins_cycle[0]) > 0){
                                	$cycle_data = array('due_date' 		  =>  (isset($ins_cycle[0]['due_date_from'])?$ins_cycle[0]['due_date_from']: NULL),
                                	                    'due_date_to' 		  =>  (isset($ins_cycle[0]['due_date_to'])?$ins_cycle[0]['due_date_to']: NULL),
                                                    	'grace_date' 		  =>  (isset($ins_cycle[0]['grace_date'])?$ins_cycle[0]['grace_date']: NULL),
                                                    	'installment' 		  =>  (isset($ins_cycle[0]['installment'])?$ins_cycle[0]['installment']: NULL),
                                                    	'is_limit_exceed' 		  =>  (isset($ins_cycle[0]['is_limit_exceed'])?$ins_cycle[0]['is_limit_exceed']: 0), );
                                                    
                                	$this->payment_model->updData($cycle_data, 'id_payment', $insPayment, 'payment');
                                                                	
                                }	
                                //update total paid installment count
            	               	$paid = $this->payment_model->getPaidInsData($isClientID['id_scheme_account']);
                            	if(sizeof($paid) > 0){
                                	$paid_ins = array('total_paid_ins'  =>  $paid['paid_installments']);
                                	$this->payment_model->updData($paid_ins, 'id_scheme_account', $isClientID['id_scheme_account'], 'scheme_account');
                                                        	
                                }
            			    }
							
						} else {

							//update if offline record is with cancelled status

							$upd_array = array(
								"payment_status" 	=> 2,

								"receipt_no" 		=> $trans['receipt_no'],

								"remark" 			=> $trans['remarks'],

								"date_upd" 			=> date('Y-m-d H:i:s'),

								"payment_ref_number" => $trans['ref_no']

							);

							$updPayment  = $this->$api_model->updatePayment($upd_array, $trans['payment_type'], $isClientID['id_scheme_account'], $trans['payment_date']);
						}

						if ($this->db->trans_status() == TRUE) {

							$this->db->trans_commit();

							$trans_rec += 1;

							$pay_id .= $trans['ref_no'] . '|';
						} else {

							"Rollback!!";

							$this->db->trans_rollback();

							$this->db->_error_message();
							exit;

							$rejected_pay_id .= $trans['ref_no'] . '|';
						}
					}
				}
			}
		}

		if ($acc_id > 0 || $pay_id > 0 || $rejected_acc_id != "" ||  $rejected_pay_id != "") {

			$remark = array("acc" => $acc_id,	"pay" => $pay_id, "ac_error" => $rejected_acc_id, "pay_error" => $rejected_pay_id);

			$sync_data = array(

				"total_records"   => $records,

				"scheme_accounts" => $acc_rec,

				"payments"		  => $trans_rec,

				"sync_date"		  => date('Y-m-d H:i:s'),

				"remark"          => json_encode($remark)

			);

			$this->$acc_model->insert_sync($sync_data);

			$result =  array('message' => 'Total ' . $records . ' records affected ' . $acc_rec . ' scheme accounts and ' . $trans_rec . ' payments records. Error Records : Account = ' . $rejected_acc_id . ' Payment =' . $rejected_pay_id, 'class' => 'success', 'title' => 'Update Client Details');
		} else {

			$result = array('message' => 'No records to proceed ', 'class' => 'info', 'title' => 'Update Client Details');
		}

		echo "<pre>";
		print_r($result);
	}

	// to update scheme ac and payments

	function update_client()

	{

		$api_model = self::SYN_MODEL;

		$acc_model = self::ACC_MODEL;

		$record_to = 2; // 2 - Online 

		$branch_id = (isset($_POST['sync_branch_id']) ? $_POST['sync_branch_id'] : NULL);

		//	$trans_date = (isset($_POST['sync_trans_date'])?$_POST['sync_trans_date']:date('Y-m-d'));

		$trans_date = (isset($_GET['sync_trans_date']) ? $_GET['sync_trans_date'] : date('Y-m-d'));

		$acc_id = "";

		$acc_rec = 0;

		$trans_rec = 0;

		$records = 0;

		$pay_id = "";

		// echo $this->session->userdata('id_branch');exit; 

		$cus_reg_data = $this->$api_model->getcustomerByStatus('N', '-1', $record_to, $trans_date);

		if ($cus_reg_data) {

			$records += count($cus_reg_data);

			foreach ($cus_reg_data as $client) {

				// is_registered_online -> 0 - No, 1- Yes , 2 - online record

				if ($client['is_modified'] == 1 && $client['is_registered_online'] >= 1) {

					if ($client['clientid'] != null) {

						$isClientID =  $this->$api_model->checkClientID("", $client['clientid']);

						if ($isClientID['status']) {

							$acc_data = array(

								'closed_by'         => $client['closed_by'],

								'closing_date'      => $client['closing_date'],

								'closing_amount'    => $client['closing_amount'],

								'closing_weight'    => $client['closing_weight'],

								'closing_add_chgs'  => $client['closing_add_chgs'],

								'additional_benefits' => $client['additional_benefits'],

								'remark_close'      => $client['remark_close'],

								'is_closed'         => $client['is_closed'],

								'active'            => ($client['is_closed'] == 1 ? 0 : 1),

								'date_upd'	        => date("Y-m-d H:i:s")

							);

							$acc_status = $this->$api_model->update_closed_ac($acc_data, $client['clientid'], $client['id_customer_reg']);
						} else {

							$acc_data = array(

								'group_code'              => $client['group_code'],

								'scheme_acc_number'       => $client['scheme_ac_no'],

								'ref_no'                  => $client['clientid'],

								'date_upd'	              => date("Y-m-d H:i:s")

							);

							$acc_status = $this->$api_model->update_account($acc_data, $client['id_scheme_account'], $client['id_customer_reg']);
						}
					}

					if ($acc_status) {

						$acc_rec += 1;

						$acc_id .= $client['id_scheme_account'] . '|';

						$inter_data = array('is_transferred' => 'Y', 'is_modified' => 'N', 'transfer_date' => date('Y-m-d'), 'ref_no' => $client['ref_no']);

						$this->$api_model->updateData($inter_data, $branch, 'customer_reg');
					}
				}
			}
		}

		$trans_data = $this->$api_model->getRegisteredAccTransactions('N', '-1', $record_to, $trans_date);

		if ($trans_data) {

			$records += count($trans_data);

			foreach ($trans_data as $trans) {

				// payment_type -> 1- Online , 2 - Offine

				if ($trans['payment_type'] == 1) {

					// to update online record

					// check whether scheme a/c data updated

					$isClientID =  $this->$acc_model->checkClientID($trans['id_scheme_account'], "");

					if ($isClientID['status'] &&  $trans['is_modified'] == 1 && $trans['payment_status'] == 1) {

						$trans_data = array(
							'receipt_no' =>  $trans['receipt_no'],

							'payment_ref_number' =>  $trans['ref_no'],

							"payment_status" 	=> 1,

							'date_upd'	 => date("Y-m-d H:i:s")
						);

						$updPayment  = $this->$api_model->updatePayment($trans_data, $trans['payment_type'], $trans['id_scheme_account'], $trans['payment_date']);

						$trans_rec += 1;

						$pay_id .= $trans['ref_no'] . '|';
					}
				} else if ($trans['payment_type'] == 2 && ($trans['client_id'] != null || $trans['client_id'] != '')) {

					// to update offline record

					$isClientID =  $this->$api_model->checkClientID("", $trans['client_id']);

					if ($isClientID['status']) {

						if ($trans['payment_status'] == 1) {

							$pay_array = array(
								"id_scheme_account" => $isClientID['id_scheme_account'],

								"id_scheme" => $isClientID['id_scheme'],

								"id_branch" 		=> $trans['id_branch'],

								"date_payment" 		=> $trans['payment_date'],

								"date_add" 			=> $trans['payment_date'],

								//            			   						"id_metal" 			=> $trans['id_metal'],

								"metal_rate" 		=> $trans['rate'],

								"payment_amount"	=> $trans['amount'],

								"actual_trans_amt"	=> $trans['amount'],

								"metal_weight" 		=> $trans['weight'],

								"payment_mode" 		=> $trans['payment_mode'],

								"payment_status" 	=> 1,

								"payment_type" 		=> "Offline",

								"due_type"          => $trans['due_type'],

								"installment" 		=> $trans['installment_no'],

								"receipt_no" 		=> $trans['receipt_no'],

								"remark" 			=> $trans['remarks'] . " :: service",

								"discountAmt"		=> $trans['discountAmt'],

								"payment_ref_number" => $trans['ref_no'],

								"date_upd" 			=> date('Y-m-d H:i:s')

							);

							$insPayment  = $this->$api_model->insertPayment($pay_array);

							//Mode details update :: Haritha 05-08-2025
                            if($insPayment > 0)
                            {
                                //update mode detail table
                                   $arrayPayMode = array('payment_amount'     => $trans['amount'],
                                                        'payment_date'         => $trans['payment_date'],
                                                        'created_time'         => date("Y-m-d H:i:s"),
                                                        "payment_mode"       => $trans['payment_mode'],
                                                        "remark"             => 'Offline CRON service',
                                                        "payment_ref_number" => $trans['ref_no'],
                                                        "id_payment"         => $insPayment,
                                                        "payment_status"     => 1);
    
                                   
                                $update_pmd = array('payment_status'=> 9, // Cancelled
                                                    "updated_time"  => date('Y-m-d H:i:s'),
                                                    "remark"        =>  "Removed while direct success on cashfree ".date('Y-m-d H:i:s'),
                                                );
                                $update_existing_pmd = $this->payment_model->updData($update_pmd,'id_payment',$insPayment,'payment_mode_details');
                                $arrayPayMode['id_payment'] = $insPayment;
                                $payModeInsert = $this->payment_model->insertData($arrayPayMode,'payment_mode_details');
                                   //echo "<pre>"; print_r($this->db->last_query());exit;
                                   //update due_date,installment
                                   $dt_pay = date('Y-m-d H:i:s',strtotime(str_replace("/","-",$trans['payment_date'])));                            
                                $ins_cycle = $this->payment_model->get_due_date('service', $dt_pay,$isClientID['id_scheme_account']);  
                                                    
    
                                if(sizeof($ins_cycle[0]) > 0){
                                    $cycle_data = array('due_date'           =>  (isset($ins_cycle[0]['due_date_from'])?$ins_cycle[0]['due_date_from']: NULL),
                                                        'due_date_to'           =>  (isset($ins_cycle[0]['due_date_to'])?$ins_cycle[0]['due_date_to']: NULL),
                                                        'grace_date'           =>  (isset($ins_cycle[0]['grace_date'])?$ins_cycle[0]['grace_date']: NULL),
                                                        'installment'           =>  (isset($ins_cycle[0]['installment'])?$ins_cycle[0]['installment']: NULL),
                                                        'is_limit_exceed'           =>  (isset($ins_cycle[0]['is_limit_exceed'])?$ins_cycle[0]['is_limit_exceed']: 0), );
                                                    
                                    $this->payment_model->updData($cycle_data, 'id_payment', $insPayment, 'payment');
                                                                    
                                }    
                                //update total paid installment count
                                   $paid = $this->payment_model->getPaidInsData($isClientID['id_scheme_account']);
                                if(sizeof($paid) > 0){
                                    $paid_ins = array('total_paid_ins'  =>  $paid['paid_installments']);
                                    $this->payment_model->updData($paid_ins, 'id_scheme_account', $isClientID['id_scheme_account'], 'scheme_account');
                                                            
                                }
                            }
							if ($insPayment) {

								$trans_rec += 1;

								$pay_id .= $trans['ref_no'] . '|';
							}
						} else {

							//update if offline record is with cancelled status

							$upd_array = array(
								"payment_status" 	=> 2,

								"receipt_no" 		=> $trans['receipt_no'],

								"remark" 			=> $trans['remarks'],

								"date_upd" 			=> date('Y-m-d H:i:s'),

								"payment_ref_number" => $trans['ref_no']

							);

							$updPayment  = $this->$api_model->updatePayment($upd_array, $trans['payment_type'], $isClientID['id_scheme_account'], $trans['payment_date']);

							if ($updPayment) {

								$trans_rec += 1;

								$pay_id .= $trans['ref_no'] . '|';
							}
						}
					}
				}
			}
		}

		//$this->session->set_flashdata('chit_alert', array('message' => $records.' records updated','class' => 'success','title'=>'Update Client Details'));

		if ($acc_id != '' || $pay_id != '') {

			$remark = array("acc" => $acc_id,	"pay" => $pay_id);

			$sync_data = array(

				"total_records"   => $records,

				"scheme_accounts" => $acc_rec,

				"payments"		  => $trans_rec,

				"sync_date"		  => date('Y-m-d H:i:s'),

				"remark"          => json_encode($remark)

			);

			$this->$acc_model->insert_sync($sync_data);

			$result = array('message' => 'Total ' . $records . ' records .Updated ' . $acc_rec . ' scheme accounts and ' . $trans_rec . ' payments records. ', 'class' => 'success', 'title' => 'Update Client Details');
		} else {

			$result = array('message' => 'No updates to proceed', 'class' => 'danger', 'title' => 'Update Client Details');
		}

		$this->load->database('default', true);

		echo json_encode($result);
	}

	// offline data trans details

	// Delete scheme accounts if no payments from start date to $months [passed months].

	function deleteNoPayments_Acc($months)
	{

		$model = self::MODEL;

		if ($months > 0) {

			$delete  = $this->$model->deleteNoPayments_Acc($months);

			echo $delete;
		} else {

			echo "Invalid Month";
		}
	}

	function import_off_cusData()

	{

		$model = self::SET_MODEL;

		$pathToUpload = self::OFF_DATA_FILE_PATH;

		//	print_r($pathToUpload);exit;

		// get all file //

		//$filename = file_get_contents(self::OFF_DATA_FILE_PATH."customer_reg.csv");

		$filename = array_map('str_getcsv', file(self::OFF_DATA_FILE_PATH . "customer_reg.csv"));

		//	print_r($filename);

		$data = array(

			'xl_data'	 => $filename

		);

		print_r($data);
		exit;

		$this->parse_customer_reg($data);
	}

	function parse_customer_reg($data)

	{

		$records = array();

		//send the data in an array format

		//	print_r($data);exit;

		foreach ((array)$data  as $row) {

			print_r($row);
			exit;

			$records[] = array(

				'clientid'				=> $row['clientid'],

				'id_branch'					=> $row['id_branch'],

				'reg_date'					=> $row['reg_date'],

				'ac_name'					=> $row['ac_name'],

				'firstname'						=> $row['firstname'],

				'lastname'					=> $row['lastname'],

				'address1'				=> $row['address1'],

				'address2'				=> $row['address2'],

				'address3'				=> $row['address3'],

				'city'		=> $row['city'],

				'state'		    => $row['state'],

				'pincode'		    => $row['pincode'],

				'phone'			=> $row['phone'],

				'mobile'		=> $row['mobile'],

				'email'			=> $row['email'],

				'dt_of_birth'					=> $row['dt_of_birth'],

				'wed_date'					=> $row['wed_date'],

				'sync_scheme_code'					=> $row['sync_scheme_code'],

				'group_code'					=> $row['group_code'],

				'scheme_ac_no'					=> $row['scheme_ac_no'],

				'paid_installments'					=> $row['paid_installments']

			);

			//  print_r($records);exit;

		}
	}

	// Auto verify for actuall Pending & failed payment at last 2 days data's only - based on services  //HH

	function oldverify_cashfreepayment()

	{

		$pg_code = 4;

		$id_branch = '';

		$model =	self::PAY_MODEL;

		$previousDay = date('Y-m-d', strtotime("-3 days"));

		$currentDay = date('Y-m-d', strtotime(" 0 days"));

		$transData = array();

		//print_r($currentDay);exit;

		$gateway_info = $this->$model->getBranchGatewayData($id_branch, $pg_code);

		$secretKey      = $gateway_info['param_1'];

		$appId          = $gateway_info['param_3'];

		$api_url  = "https://api.cashfree.com/pg/orders";

		$payData = $this->$model->getPendpayment_Data($previousDay, $currentDay, $id_branch, $gateway_info['id_pg']);

		//echo $this->db->last_query();exit;

		$vCount = 0;

		if (sizeof($payData) > 0) {

			foreach ($payData as $tran) {
				if(!empty($tran['txn_ids'])){
				$postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&orderId=" . $tran['txn_ids'];

				$curl = curl_init();

				curl_setopt_array($curl, array(

					CURLOPT_URL => $api_url . "/" . $tran['txn_ids'] . "/payments",

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_ENCODING => '',

					CURLOPT_MAXREDIRS => 10,

					CURLOPT_TIMEOUT => 8,

					CURLOPT_FOLLOWLOCATION => true,

					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

					CURLOPT_CUSTOMREQUEST => 'GET',

					CURLOPT_HTTPHEADER => array(

						'x-client-id: ' . $appId,

						'x-client-secret: ' . $secretKey,

						'x-api-version: 2022-09-01'

					),

				));

				$pay_response = curl_exec($curl);

				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {

					echo "cURL Error #:" . $err; //exit;

				} else {

					$response = json_decode($pay_response);
					// echo "<pre>"; print_r($response);exit;
					// RAHUL 9/7
					$log_path = $this->log_dir . '/cashfree/mob_response_' . date("Y-m-d") . '.txt';
					$data = "\n ----- \n AUTO VERIFY CURL RESPONSE " . date("Y-m-d H:i:s") . ": " . json_encode($response);
					$data .= "\nPOST DATA: " . json_encode($postData);
					file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
					$txn_id      = $tran['txn_ids'];

					if (isset($response)) {

						$cf_response = $response[0]; // By default set first array as cf_response[which is last transaction data for the ref_trans_id]

						if (sizeof($response) > 0) { // Check size of response and update cf_response data if payment is success

							while (list(, $val) = each($response)) {

								if ($val->payment_status == 'SUCCESS') {

									$cf_response = $val;

									break;
								}
							}
						}



						$status_code = $cf_response->payment_status; // SUCCESS,

						/* if ($txn_id != ""  && $status_code == 'SUCCESS') { // Refund the payment while getting later success

							$refundData     = [

								"order_amount"      => $cf_response->order_amount,

								"payu_id"           => $cf_response->cf_payment_id, // referenceId

								"payment_ref_number" => $cf_response->cf_payment_id,
								
								//"payment_mode"       => (strtolower($cf_response->payment_group) == "credit_card" ? "CC" : (strtolower($cf_response->payment_group) == "debit_card" ? "DC" : (strtolower($cf_response->payment_group) == "net_banking" ? "NB" : (strtolower($cf_response->payment_group) == "na" ? "-" : "UPI")))),
								// "remark"            => $cf_response->payment_group." - verifyService(Auto)",
								"payment_mode"       => (strtolower($cf_response->payment_group) == "credit_card" ? "CC" : (strtolower($cf_response->payment_group) == "debit_card" ? "DC" : (strtolower($cf_response->payment_group) == "net_banking" ? "NB" :  (strtolower($cf_response->payment_group) == "na" ? "NA" : $this->update_paymentMode($cf_response->payment_group))))),

								"remark" => $cf_response->payment_group . " Auto verify(cashfree) - " . date('Y-m-d H:i:s'),
								"payment_status"    => 2, // Refund

								"ref_trans_id"      => $txn_id,

								"gateway_info"      => $gateway_info

							];

							$refundStatus   = $this->createRefund($refundData);
						} else */ if ($txn_id != "" && $status_code != 'PENDING' && $status_code != 'FLAGGED' && $status_code != '') {

							$updateData = array(

								"payu_id"           => $cf_response->cf_payment_id, // referenceId

								"payment_ref_number" => $cf_response->cf_payment_id,
								/* "payment_mode"      => ($cf_response->payment_group == "credit_card" ? "CC":($cf_response->payment_group == "debit_card" ? "DC":($cf_response->payment_group == "net_banking" ? "NB":(!empty($cf_response->payment_group) ? $cf_response->payment_group : 'UPI')))),  */
								//"payment_mode"       => (strtolower($cf_response->payment_group) == "credit_card" ? "CC" : (strtolower($cf_response->payment_group) == "debit_card" ? "DC" : (strtolower($cf_response->payment_group) == "net_banking" ? "NB" : "UPI"))),

								"payment_mode"       => (strtolower($cf_response->payment_group) == "credit_card" ? "CC" : (strtolower($cf_response->payment_group) == "debit_card" ? "DC" : (strtolower($cf_response->payment_group) == "net_banking" ? "NB" :  (strtolower($cf_response->payment_group) == "na" ? "NA" : $this->update_paymentMode($cf_response->payment_group))))),

								"remark"            => "Auto verify(Cash Free) on " . date('Y-m-d H:i:s'),

								//"payment_status"    => ($status_code == 'SUCCESS' ? 1 : ($status_code == 'CANCELLED' || $status_code == 'USER_DROPPED' ? 4 : ($status_code == 'FAILED' ? 3 : ($status_code == 'REFUND' ? 6 : 7))))
								"payment_status"    => (strtolower($status_code) == 'success' ? 1 : (strtolower($status_code) == 'cancelled' || strtolower($status_code) == 'user_dropped' || strtolower($status_code) == 'userCancelled' || strtolower($status_code) == 'dropped' ||strtolower($status_code) == 'bounced'  ? 4 : (strtolower($status_code) == 'failed' || strtolower($status_code) == 'failure' ? 3 : (strtolower($status_code) == 'refund' ? 6 : 7))))

							);

							//$this->db->trans_begin();

							$result =	$this->$model->updateGatewayResponse($updateData, $txn_id);

							if ($status_code == 'SUCCESS') {

								$payIds = $this->$model->getPayIds($txn_id);

								//print_r($payIds);exit;

								if (sizeof($payIds) > 0) {

									// Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]

									$actual_trans_amt   = $payIds[0]['actual_trans_amt'];

									$act_wal_redeemed 	= $payIds[0]['redeemed_amount'];

									$remaining_amt 		= 0;

									$remaining_wal_amt	= 0;

									if ($act_wal_redeemed > 0) {

										// Update payment table - payment mode as multi
										$this->payment_modal->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $txn_id);
									}

									foreach ($payIds as $py) {



										$tax 		= 0;

										$pay_amount = 0;

										if ($py['gst_type'] == 1) { // Exclusive

											$tax = $py['gst_amount'];
										}

										$pay_amount = ($py['payment_amount'] - $py['discountAmt'] + $tax);

										$mode_amount = $pay_amount;



										if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode

											if ($actual_trans_amt > $pay_amount) {

												$actual_trans_amt   -= $pay_amount;
											} else if ($actual_trans_amt == $pay_amount) {

												$actual_trans_amt   -= $pay_amount;
											} else if ($actual_trans_amt < $pay_amount) {

												$mode_amount			= $actual_trans_amt;

												$remaining_amt			= abs($actual_trans_amt - $pay_amount);

												$actual_trans_amt	= 0;

												$pay_amount 		  	= $remaining_amt;
											}

											/*echo "Having Gateway balance, add gateway mode.<br/>";

            									echo "actual_trans_amt : ".$actual_trans_amt."<br/>";

            									echo "remaining_amt : ".$remaining_amt."<br/>";*/

											// Insert payment Mode details   

											$arrayPayMode = array(

												'payment_amount'     => $mode_amount,

												// 'payment_date'		 => date("Y-m-d H:i:s"),

												'payment_date'		 => $pay['date_payment'],

												'created_time'	     => date("Y-m-d H:i:s"),

												"payment_mode"       => $updateData['payment_mode'],
												// "remark"             => $txMsg."[".$txTime."] auto verify",
												"remark" => "Auto verify(cashfree) - " . date('Y-m-d H:i:s'),
												"payment_ref_number" => $py['payment_ref_number'],

												"payment_status"     => $updateData['payment_status']

											);

											if ($py['due_type'] == 'GA') {

												$arrayPayMode['id_adv_payment'] = $py['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
											} else {

												$update_pmd = array(

													'payment_status' => 9, // Cancelled

													"updated_time"  => date('Y-m-d H:i:s'),

													"remark"        =>  "Removed while auto verify " . date('Y-m-d H:i:s'),
													'is_active'		=> 0
												);
												// print_r($py);exit;
												$update_existing_pmd = $this->payment_model->update_data($update_pmd, 'id_payment', $py['id_payment'], 'payment_mode_details');
												$arrayPayMode['id_payment'] = $py['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
											}
										}



										if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 

											if ($act_wal_redeemed > $pay_amount) {

												$act_wal_redeemed   -= $pay_amount;
											} else if ($act_wal_redeemed == $pay_amount) {

												$act_wal_redeemed   -= $pay_amount;
											}

											/*else if($act_wal_redeemed < $pay_amount){

            										$remaining_amt		   = abs($act_wal_redeemed-$pay_amount);

            										$act_wal_redeemed   = 0;

            									}*/

											/*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";

            									echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/

											// Insert payment Mode details               

											$arrayPayMode = array(

												'payment_amount'     => $pay_amount,

												// 'payment_date'		 => date("Y-m-d H:i:s"),
												'payment_date'		 => $pay['date_payment'],

												'created_time'	     => date("Y-m-d H:i:s"),

												"payment_mode"       => "REF_WALLET",

												"remark"             => "Wallet Utilized",

												"payment_ref_number" => $py['payment_ref_number'],

												"payment_status"     => 1

											);

											if ($py['due_type'] == 'GA') {

												$arrayPayMode['id_adv_payment'] = $py['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
											} else {

												$arrayPayMode['id_payment'] = $py['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
											}

											$WalletinsData = array(

												'id_wallet_account'	=> $py['id_wallet_account'],

												'transaction_type' 	=> 1, //0-Credit,1-Debit

												'type'             	=> 0, //CRM

												'id_sch_ac'        	=> $py['id_scheme_account'],

												'value'            	=> $pay_amount,

												'description'      	=> 'Chit Redeem',

												'date_transaction' 	=> date("Y-m-d H:i:s"),

												'id_employee'      	=> NULL,

												'date_add'	       	=> date("Y-m-d H:i:s"),

												'credit_for'      	=> 'Redeem',

												'id_payment'      	=> $py['id_payment']

											);

											$this->payment_model->insertData($WalletinsData, 'wallet_transaction');
										}

										// Referral Code :- allow_referral - 0 => No , 1 => Yes

										if ($py['allow_referral'] == 1) {

											$ref_data	=	$this->$model->get_refdata($py['id_scheme_account']);

											$ischkref	=	$this->$model->get_ischkrefamtadd($py['id_scheme_account']);

											if ($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE) {

												$this->insert_referral_data($ref_data['id_scheme_account']);
											} else if ($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE) {

												$this->insert_referral_data($ref_data['id_scheme_account']);
											}
										}

										$pay =  $this->$model->paymentDB("get", $py['id_payment']);

										/*	// Multi mode payment

                                        if($pay['payment_mode']!= NULL)

                                         {

                                             $arrayPayMode=array(

                                                            'id_payment'         => $pay['id_payment'],

                                                            'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),

                                                            'payment_date'         => date("Y-m-d H:i:s"),

                                                            'created_time'         => date("Y-m-d H:i:s"),

                                                            "payment_mode"       => $pay['payment_mode'],

                                                            "remark"             => $txMsg."[".$txTime."] manual verif",

                                                            "payment_ref_number" => $pay['payment_ref_number'],

                                                            "payment_status"     => 1

                                                            );

                                            if(!empty($arrayPayMode)){

                                                $cashPayInsert = $this->$model->insertData($arrayPayMode,'payment_mode_details'); 

                                            }

                                         }*/

										// Account number :- schemeacc_no_set - 0 => generate a/c no ,  0 => manual a/c no , 2 => integration , 3 => Integration Auto

										$this->load->model("account_model");

										if ($pay['schemeacc_no_set'] == 0  || $pay['schemeacc_no_set'] == 3) {

											// Generate a/c no
											if (empty($pay['acc_no'])) {
												$scheme_acc_number = $this->account_model->account_number_generator($pay['id_scheme'], $pay['branch']);

												if ($scheme_acc_number != NULL) {
													$updateAccData['scheme_acc_number'] = $scheme_acc_number;
												}

												if ($pay['gent_clientid'] == 1 && $scheme_acc_number != NULL) {        //upd client id & acc no cus reg table based on the settings//
													$updateAccData['ref_no'] = $this->config->item('cliIDcode') . "/" . $pay['group_code'] . '/' . $scheme_acc_number;
												}

												if ($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2) {
													$updateAccData['scheme_acc_number'] = $scheme_acc_number;
													$updateAccData['sync_scheme_code'] = $pay['sync_scheme_code'];
												}
												if ($pay['gent_clientid'] == 1 && $pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2) {
													$updateAccData['ref_no'] = $this->config->item('cliIDcode') . "/" . $pay['group_code'] . "/" . $scheme_acc_number;
												}
												$updSchAc = $this->account_model->update_account($updateAccData, $pay['id_scheme_account']);
											}
										}

										// Receipt Number :-  receipt_no_set - 0 => Donot generate , 1 => generate
										if (empty($pay['receipt_no']) && $pay['receipt_no_set'] == 1) {
											$receipt_no = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);

											$pay_array = array('receipt_no' => $receipt_no, 'approval_date' => date("Y-m-d H:i:s"));

											$result =  $this->$model->paymentDB("update", $pay['id_payment'], $pay_array);
										}

										if ($pay['edit_custom_entry_date'] == 1) {

											$pay_array = array('custom_entry_date' => $pay['custom_entry_date']);

											$result =  $this->$model->paymentDB("update", $pay['id_payment'], $pay_array);
										}

										if ($pay['firstPayamt_as_payamt'] == 1 || $pay['firstPayamt_maxpayable'] == 1) {
											$acc_array = array('firstPayment_amt' => $pay['payment_amount']);
											$result =  $this->account_model->update_account($acc_array, $pay['id_scheme_account']);
										}


										$dt_pay = date('Y-m-d H:i:s', strtotime($pay['date_payment']));
										$ins_cycle = $this->$model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);
										if (sizeof($ins_cycle[0]) > 0) {
											$cycle_data = array(
												'due_date' 		  => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
												'due_date_to' 		  => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
												'grace_date' 		  => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
												'installment' 		  => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
												'is_limit_exceed' 	=> (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
											);

											$this->$model->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
										}

										//update paid installments against account...
										$paid = $this->$model->getPaidInsData($pay['id_scheme_account']);
										if (sizeof($paid) > 0) {
											$paid_ins = array('total_paid_ins'  =>  $paid['paid_installments']);
											$this->$model->updData($paid_ins, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
										}
									}
								}
							}

							if ($this->db->trans_status() === TRUE) {

								$payres_array = array('dev_remark' => $status_code . "   " . $txn_id . " " . date('Y-m-d H:i:s'));

								$result =  $this->$model->paymentDB("updatestatus", $txn_id, $payres_array);

								echo $status_code . "   " . $txn_id . ", </br>";

								$vCount = $vCount + 1;

								$this->db->trans_commit();
							} else {

								echo $this->db->_error_message();

								$this->db->trans_rollback();
							}
						} else if ($txn_id != "") {

							$payres_array = array('dev_remark' => $status_code . "   " . $txn_id . " " . date('Y-m-d H:i:s'));

							$result =  $this->$model->paymentDB("updatestatus", $txn_id, $payres_array);

							echo $status_code . "   " . $txn_id . ", </br>";
						}
					} else {

						$payres_array = array('dev_remark' => "No respone from gateway " . $txn_id . " " . date('Y-m-d H:i:s'));

						$result =  $this->$model->paymentDB("updatestatus", $txn_id, $payres_array);

						echo "Not found";
					}
				}
				}	
			} //exit;

			if ($vCount > 0) {

				echo "Total : " . sizeof($payData) . " ." . $vCount . " payment records verified successfully.";
			} else {

				// echo " No records to verify. Message ". print_r($response); 

				echo "Total : " . sizeof($payData) . "<br/> No records updated.";
			}
		} else {

			echo "No payment records to verify.";
		}
	}

	// Auto verify for actuall Pending & failed payment at last 2 days data's only - based on services //

	function genInstallmentNo($id_sch_ac)

	{

		$model =	self::PAY_MODEL;

		$installmentNo = $this->$model->genInstallmentNo($id_sch_ac);

		return $installmentNo;
	}

	//To insert payment and registration details in intermediate table

	function insert_common_data_jil($id_payment)

	{

		$model = self::CHITAPI_MODEL;

		$this->load->model($model);

		//getting payment detail

		$pay_data = $this->$model->getPaymentByID($id_payment);

		//storing temp values

		$trans_date = $pay_data[0]['trans_date'];

		$approval_no = $pay_data[0]['approval_no'];

		$ref_no = $pay_data[0]['ref_no'];

		$id_scheme_account = $pay_data[0]['id_scheme_account'];

		//getting customer detail to post registration again

		$reg = $this->$model->getCustomerByID($id_scheme_account, $id_payment);

		$isExists = $this->$model->checkTransExists($trans_date, $approval_no, $ref_no);

		if (!$isExists) {

			//insert payment detail

			$status =	$this->$model->insert_transaction($pay_data[0]);

			if ($status) {

				//insert registration detail

				if ($reg) {

					$reg[0]['transfer_jil']	= 'N';

					$reg[0]['transfer_date'] = NULL;

					$reg[0]['ref_no']		= $ref_no;

					$status = $this->$model->insert_CustomerReg($reg[0]);
				}
			}
		}

		return true;
	}

	//To insert payment and registration details in intermediate table

	function insert_common_data($id_payment)

	{

		$model = self::SYN_MODEL;

		$this->load->model($model);

		//getting payment detail

		$pay_data = $this->$model->getPaymentByID($id_payment);

		//storing temp values

		$ref_no = $pay_data[0]['ref_no'];

		$id_scheme_account = $pay_data[0]['id_scheme_account'];

		// Validate gent_clientid setting — skip client_id if disabled
		$chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
		if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
			$pay_data[0]['client_id'] = '';
		}

		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account, $ref_no);

		if (!$isCusRegExists['status']) {

			$reg = $this->$model->getCustomerByID($id_scheme_account);

			//insert customer registration detail

			if ($reg) {

				$reg[0]['record_to'] = 1;

				$reg[0]['id_branch'] = ($reg[0]['id_branch'] == 0 ? NULL : $reg[0]['id_branch']);

				$reg[0]['is_registered_online'] = 2;  // 2 - online record

				$reg[0]['ref_no']		= $ref_no;

				// Skip clientid if gent_clientid is disabled
				if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
					$reg[0]['clientid'] = NULL;
				}

				$status = $this->$model->insert_CustomerReg($reg[0]);
			}
		}

		$isTranExists = $this->$model->checkTransExists($ref_no);

		if (!$isTranExists) {

			//insert payment detail

			$pay_data[0]['record_to'] = 1;

			$pay_data[0]['id_branch'] = ($pay_data[0]['id_branch'] == 0 ? NULL : $pay_data[0]['id_branch']);

			$pay_data[0]['payment_type'] = 1;	// 1 - online

			$status =	$this->$model->insert_transaction($pay_data[0]);
		}

		return true;
	}

	function insert_referral_data($id_scheme_account)

	{

		$log_model    = self::LOG_MODEL;

		$model        = self::PAY_MODEL;

		$cusmodel = $this->load->model("customer_model");

		$set_model    = self::SET_MODEL;

		$status = FALSE;

		$serviceID = 16;

		$chkreferral = $this->$model->get_referral_code($id_scheme_account);

		if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 1) {

			$data = $this->$model->get_empreferrals_datas($id_scheme_account);
		} else if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 0) {

			$data = $this->$model->get_cusreferrals_datas($id_scheme_account);
		}

		if (!empty($data)) {

			if ($data['referal_code'] != '' && $data['referal_value'] != ''  &&  $data['id_wallet_account'] != '') {

				// insert wallet transaction data //

				$wallet_data = array(

					'id_wallet_account' => $data['id_wallet_account'],

					'id_sch_ac'         => $id_scheme_account,

					'date_transaction' =>  date("Y-m-d H:i:s"),

					'id_employee'      =>  $this->session->userdata('uid'),

					'transaction_type' =>  0,

					'value'            => $data['referal_value'],

					'description'      => 'Referral Benefits - ' . $data['cusname'] . ''

				);

				//	echo"<pre>"; print_r($wallet_data);exit;

				$this->load->model("Wallet_model");

				$status = $this->Wallet_model->wallet_transactionDB('insert', '', $wallet_data);

				if ($status) {

					// Update credit flag in customer table

					/* is_refbenefit_crt = 0 -> already  benefit credited  & 1-> yet to credit benefits' */
					if ($chkreferral['is_refferal_by'] == 0 && $data['cusbenefitscrt_type'] == 0 && ($data['schrefbenifit_secadd'] == 0 || $data['schrefbenifit_secadd'] == 1)) {

						// customer referal - single  

						$this->$cusmodel->update_customer_only(array('is_refbenefit_crt_cus' => 0), $chkreferral['id_customer']);
					} else if ($chkreferral['is_refferal_by'] == 0 && $data['cusbenefitscrt_type'] == 1 && $data['schrefbenifit_secadd'] == 1) {

						// customer referal - multiple  

						$this->$cusmodel->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);
					} else if ($chkreferral['is_refferal_by'] == 1 && $data['empbenefitscrt_type'] == 0 && ($data['schrefbenifit_secadd'] == 0 || $data['schrefbenifit_secadd'] == 1)) {

						// emp referal - single  					

						$this->$cusmodel->update_customer_only(array('is_refbenefit_crt_emp' => 0), $chkreferral['id_customer']);
					} else if ($chkreferral['is_refferal_by'] == 1 && $data['empbenefitscrt_type'] == 1 && $data['schrefbenifit_secadd'] == 1) {

						// emp referal - single  			

						$this->$cusmodel->update_customer_only(array('is_refbenefit_crt_emp' => 1), $chkreferral['id_customer']);
					}

					$log_data = array(

						'id_log'     => $this->id_log,

						'event_date' => date("Y-m-d H:i:s"),

						'module'     => 'Wallet Transaction',

						'operation'  => 'Delete',

						'record'     => $status['insertID'],

						'remark'     => 'Wallet Transaction Insert successfully'

					);

					$this->$log_model->log_detail('insert', '', $log_data);
				}
			}
		}
	}

	function oldverifyEasebuzzPayments()

	{

		$pg_code = 8;

		$id_branch = 0;

		$model =	self::PAY_MODEL;

		$set_model =	self::SET_MODEL;

		$acc_model = self::ACC_MODEL;

		$previousDay = date('Y-m-d', strtotime("-3 days"));

		$currentDay = date('Y-m-d', strtotime(" 0 days"));

		$transData = array();

		$gateway_info = $this->$model->getBranchGatewayData($id_branch, $pg_code);

		$transData = $this->$model->getPendpayment_Data($previousDay, $currentDay, $id_branch, $gateway_info['id_pg']);

		// print_r($data);exit;

		$key      = $gateway_info['param_1'];

		$salt          = $gateway_info['param_3'];

		$api_url = $gateway_info['api_url'];

		$vCount = 0;

		if (sizeof($transData) > 0) {

			foreach ($transData as $tran) {

				$amount =  number_format((float) round($tran['payment_amount']), 1, '.', '');

				//hash format - 'key|txnid|amount|email|phone|salt'

				$hash_sequence = trim($key) . '|' . trim($tran['txn_ids']) . '|' . trim($amount) . '|' . trim($tran['email']) . '|' . trim($tran['mobile']) . '|' . trim($salt);

				//echo "<pre>"; print_r($hash_sequence);//exit;

				$hash_value =  strtolower(hash('sha512', $hash_sequence));

				//echo "<pre>"; print_r($hash_value);exit;

				$postData = "txnid=" . trim($tran['txn_ids']) . "&key=" . trim($key) . "&amount=" . trim($amount) . "&email=" . trim($tran['email']) . "&phone=" . trim($tran['mobile']) . "&hash=" . trim($hash_value);

				$curl = curl_init();

				curl_setopt_array($curl, array(

					CURLOPT_URL => $api_url,

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_ENCODING => "",

					CURLOPT_MAXREDIRS => 10,

					CURLOPT_TIMEOUT => 30,

					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

					CURLOPT_CUSTOMREQUEST => "POST",

					CURLOPT_POSTFIELDS => $postData,

					// Getting  server response parameters //

					CURLOPT_HTTPHEADER => array(

						"cache-control: no-cache",

						"content-type: application/x-www-form-urlencoded"

					),

				));

				$response = curl_exec($curl);

				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {

					echo "cURL Error #:" . $err;
				} else {

					$response = json_decode($response);

					//print_r($response);exit;

					if ($response->status == 1) {

						$trans_id = $response->msg->txnid;

						$paymentMode = $response->msg->mode;

						$referenceId = $response->msg->easepayid;

						$status_code = $response->msg->status;

						if (!empty($trans_id) && $trans_id != NULL) {

							$updateData = array(

								"payment_mode"       => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "netbanking" ? "NB" : $paymentMode))),

								"payu_id"            => $referenceId,

								"remark"             => "Auto verify-Easebuzz",

								"payment_ref_number" => $referenceId,

								"is_gateway_verified" => $status_code == 'success' ? 1 : 0,

								"payment_status"    => ($status_code == 'success' ? 1 : ($status_code == 'userCancelled' ? 4 : ($status_code == 'failure' ? 3 : ($status_code == 'refund' ? 6 : 7))))

							);

							//update gateway verified flag if one day reached

							$pay_date = strtotime($tran['date_payment']);

							$oldDate = $pay_date + 86400; // 86400 seconds in 24 hrs

							$cDate = strtotime(date('Y-m-d H:i:s'));

							if ($oldDate < $cDate && $status_code != 'success') {

								$updateData = array("is_gateway_verified" => 1);

								$this->$model->updateGatewayResponse($updateData, $trans_id);
							}

							$payment = $this->$model->updateGatewayResponse($updateData, $trans_id);

							if ($status_code == "success") {

								$payIds = $this->$model->getPayIds($trans_id);

								if (sizeof($payIds) > 0) {

									// Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]

									$actual_trans_amt   = $payIds[0]['actual_trans_amt'];

									$act_wal_redeemed 	= $payIds[0]['redeemed_amount'];

									$remaining_amt 		= 0;

									$remaining_wal_amt	= 0;

									if ($act_wal_redeemed > 0) {

										// Update payment table - payment mode as multi

										$this->payment_modal->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $trans_id);
									}

									foreach ($payIds as $pay) {



										$tax 		= 0;

										$pay_amount = 0;

										if ($pay['gst_type'] == 1) { // Exclusive

											$tax = $pay['gst_amount'];
										}

										$pay_amount = ($pay['payment_amount'] - $pay['discountAmt'] + $tax);

										$mode_amount = $pay_amount;



										if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode

											if ($actual_trans_amt > $pay_amount) {

												$actual_trans_amt   -= $pay_amount;
											} else if ($actual_trans_amt == $pay_amount) {

												$actual_trans_amt   -= $pay_amount;
											} else if ($actual_trans_amt < $pay_amount) {

												$mode_amount			= $actual_trans_amt;

												$remaining_amt			= abs($actual_trans_amt - $pay_amount);

												$actual_trans_amt	= 0;

												$pay_amount 		  	= $remaining_amt;
											}

											/*echo "Having Gateway balance, add gateway mode.<br/>";

                            									echo "actual_trans_amt : ".$actual_trans_amt."<br/>";

                            									echo "remaining_amt : ".$remaining_amt."<br/>";*/

											// Insert payment Mode details               

											$arrayPayMode = array(

												'payment_amount'     => $mode_amount,

												'payment_date'		 => date("Y-m-d H:i:s"),

												'created_time'	     => date("Y-m-d H:i:s"),

												"payment_mode"       => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : $paymentMode))),

												"remark"             => $txMsg . "[" . $txTime . "] Auto verify-Easebuzz",

												"payment_ref_number" => $pay['payment_ref_number'],

												"payment_status"     => $updateData['payment_status']

											);

											if ($pay['due_type'] == 'GA') {

												$arrayPayMode['id_adv_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
											} else {

												$arrayPayMode['id_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
											}
										}



										if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 

											if ($act_wal_redeemed > $pay_amount) {

												$act_wal_redeemed   -= $pay_amount;
											} else if ($act_wal_redeemed == $pay_amount) {

												$act_wal_redeemed   -= $pay_amount;
											}

											/*else if($act_wal_redeemed < $pay_amount){

                            										$remaining_amt		   = abs($act_wal_redeemed-$pay_amount);

                            										$act_wal_redeemed   = 0;

                            									}*/

											/*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";

                            									echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/

											// Insert payment Mode details               

											$arrayPayMode = array(

												'payment_amount'     => $pay_amount,

												'payment_date'		 => date("Y-m-d H:i:s"),

												'created_time'	     => date("Y-m-d H:i:s"),

												"payment_mode"       => "REF_WALLET",

												"remark"             => "Wallet Utilized",

												"payment_ref_number" => $pay['payment_ref_number'],

												"payment_status"     => 1

											);

											if ($py['due_type'] == 'GA') {

												$arrayPayMode['id_adv_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
											} else {

												$arrayPayMode['id_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
											}

											$WalletinsData = array(

												'id_wallet_account'	=> $pay['id_wallet_account'],

												'transaction_type' 	=> 1, //0-Credit,1-Debit

												'type'             	=> 0, //CRM

												'id_sch_ac'        	=> $pay['id_scheme_account'],

												'value'            	=> $pay_amount,

												'description'      	=> 'Chit Redeem',

												'date_transaction' 	=> date("Y-m-d H:i:s"),

												'id_employee'      	=> NULL,

												'date_add'	       	=> date("Y-m-d H:i:s"),

												'credit_for'      	=> 'Redeem',

												'id_payment'      	=> $pay['id_payment']

											);

											$this->payment_model->insertData($WalletinsData, 'wallet_transaction');
										}

										/*//Redeem amount in wallet credit for customer - by haritha 19-1-24

                                    						    if($pay['redeemed_amount'] > 0 && $pay['redeemed_amount'] != NULL && $pay['id_wallet_account'] != NULL)

                                    						    {

                                    						        $WalletinsData=array(

                                                    					                                    'id_wallet_account'=>$pay['id_wallet_account'],

                                                    					                                    'transaction_type' =>1, //0-Credit,1-Debit

                                                    					                                    'type'             =>0, //CRM

                                                    					                                    'id_sch_ac'        =>$pay['id_scheme_account'],

                                                    					                                    'value'            =>$pay['redeemed_amount'],

                                                    					                                    'description'      =>'Chit Redeem',

                                                    					                                    'date_transaction' => date("Y-m-d H:i:s"),

                                                    					                                    'id_employee'      =>NULL,

                                                    					                                    'date_add'	       => date("Y-m-d H:i:s"),

                                                    					                                    'credit_for'      =>'Redeem',

                                                    					                                    'id_payment'      => $pay['id_payment']

                                                    					                                    );

                                                    					                        $this->$model->insertData($WalletinsData,'wallet_transaction');

                                                    				 $arrayPayMode=array(

                                                    								'id_payment'         => $pay['id_payment'],

                                                							        'payment_amount'     => (isset($pay['redeemed_amount']) ? $pay['redeemed_amount'] : NULL),

                                                    								'payment_date'		 => date("Y-m-d H:i:s"),

                                                    								'created_time'	     => date("Y-m-d H:i:s"),

                                                    								"payment_mode"       => "REF_WALLET",

                                                            						"remark"             => $txMsg."[".$txTime."] mbl-status - Wallet Utilized",

                                                            						"payment_ref_number" => $referenceId,

                                                            						"payment_status"     => 1

                                                            					    );

                                                						    $cashPayInsert = $this->$model->insertData($arrayPayMode,'payment_mode_details'); 

                                    						    }

                                    						    //ends

                                						    // Multi mode payment

                                						    if($updateData['payment_mode']!= NULL)

                                             				{

                                             				    $payment_amt = $pay['redeemed_amount'] > 0 && $pay['redeemed_amount'] != NULL ? ($pay['payment_amount'] - $pay['redeemed_amount']) : $pay['payment_amount'];

                                             					$arrayPayMode=array(

                                                								'id_payment'         => $pay['id_payment'],

                                            							        'payment_amount'     => (isset($payment_amt) ? $payment_amt : NULL),

                                                								'payment_date'		 => date("Y-m-d H:i:s"),

                                                								'created_time'	     => date("Y-m-d H:i:s"),

                                                								"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "netbanking" ? "NB":$paymentMode))),

                                                        						"remark"             => "Auto verify-Easebuzz",

                                                        						"payment_ref_number" => $referenceId,

                                                        						"payment_status"    => 1

                                                        					    );

                                            					$cashPayInsert = $this->$model->insertData($arrayPayMode,'payment_mode_details'); 

                                             				}*/

										$schData = [];

										// Generate account  number  

										if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {

											if ($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {

												$ac_group_code = NULL;

												// Lucky draw

												if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 

													// Update Group code in scheme_account table 

													$updCode = $this->$model->updateGroupCode($pay);

													$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
												}

												$scheme_acc_number = $this->$acc_model->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);

												if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {

													$schData['scheme_acc_number'] = $scheme_acc_number;

													if ($pay['id_scheme_account'] > 0) {

														if (sizeof($schData) > 0) { // Update scheme account

															$this->$acc_model->update_account($schData, $pay['id_scheme_account']);
														}
													}
												}
											}
										}

										// Generate receipt number

										if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2)) {

											$receipt['receipt_no'] = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);

											$payment['status'] = $this->$model->update_receipt($pay['id_payment'], $receipt);
										}

										//Update First Payment Amount In Scheme Account

										$approval_type = $this->config->item('auto_pay_approval');

										if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {

											if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight

												$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight']);
											} else {

												$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount']);
											}

											$status = $this->$acc_model->update_account($fixPayable, $pay['id_scheme_account']);
										}
									}
								}
							}
						}

						echo $trans_id . "- Status : " . $status_code . "</br>";
					} else {

						echo $tr . "- Status : " . $response->msg . "</br>";
					}
				}
			}

			/*if($vCount > 0){

                echo $vCount." payment records verified successfully."; 	

            }

            else

            {

                echo " No records to verify."; 

            }*/
		} else {

			echo "No Payments to verify.";
		}
	}

	function verifyEasebuzzPayments()

	{

		$pg_code = 8;

		$id_branch = 0;

		$model =	self::PAY_MODEL;

		$set_model =	self::SET_MODEL;

		$acc_model = self::ACC_MODEL;

		$previousDay = date('Y-m-d', strtotime("-3 days"));

		$currentDay = date('Y-m-d', strtotime(" 0 days"));

		$transData = array();

		$gateway_info = $this->$model->getBranchGatewayData($id_branch, $pg_code);

		$transData = $this->$model->getPendpayment_Data($previousDay, $currentDay, $id_branch, $gateway_info['id_pg']);

		// print_r($data);exit;

		$key      = $gateway_info['param_1'];

		$salt          = $gateway_info['param_3'];

		$api_url = $gateway_info['api_url'];

		$vCount = 0;

		if (sizeof($transData) > 0) {

			foreach ($transData as $tran) {

				//Coded by haritha v1 api deprecated upgrade changes v2.1 :: 09/07/2025

				$amount =  number_format((float) round($tran['payment_amount']), 2, '.', '');
				//hash format - 'key|txnid|salt'
				$hash_sequence = trim($key) . '|' . trim($tran['txn_ids']) . '|' . trim($salt);
				//echo "<pre>"; print_r($hash_sequence);
				$hash_value =  strtolower(hash('sha512', $hash_sequence));
				//echo "<pre>"; print_r($hash_value);exit;
				//$postData = "txnid=".trim($tran['txn_ids'])."&key=".trim($key)."&amount=".trim($amount)."&email=".trim($tran['email'])."&phone=".trim($tran['mobile'])."&hash=".trim($hash_value);
				$postData = "key=" . trim($key) . "&txnid=" . trim($tran['txn_ids']) . "&hash=" . trim($hash_value);
				//echo "<pre>"; print_r($postData);exit;
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://dashboard.easebuzz.in/transaction/v2.1/retrieve',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $postData,
					// Getting  server response parameters //
					CURLOPT_HTTPHEADER => array(
						"cache-control: no-cache",
						"content-type: application/x-www-form-urlencoded"
					),
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if ($err) {
					echo "cURL Error #:" . $err;
				} else {
					$response = json_decode($response);
					//print_r($response);exit;

					// RAHUL 7/7
						$log_path = $this->log_dir.'/easebuzz/mob_response_'.date("Y-m-d").'.txt';
						$data = "\n ----- \n AUTO VERIFY CURL RESPONSE ".date("Y-m-d H:i:s").": ".json_encode($response);
						$data .= "\nPOST DATA: ".json_encode($postData);
						file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);

					if ($response->status == 1) {
						$response->msg = $response->msg[0];
						$trans_id = $response->msg->txnid;
						$paymentMode = $response->msg->mode;
						$referenceId = $response->msg->easepayid;
						$status_code = $response->msg->status;
						if (!empty($trans_id) && $trans_id != NULL) {

							$updateData = array(

								// "payment_mode"       => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "netbanking" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),

								"payment_mode"       => ((strtolower($paymentMode)== "credit_card") || (strtolower($paymentMode)== "cc") ? "CC":((strtolower($paymentMode) == "debit_card" )|| (strtolower($paymentMode)== "dc") ? "DC":((strtolower($paymentMode) == "netbanking") || (strtolower($paymentMode)== "nb")? "NB":(strtolower($paymentMode) == "na" ? "NA" : $this->update_paymentMode($paymentMode))))),

								"payu_id"            => $referenceId,

								"remark"             => "Auto verify-Easebuzz on " . date("Y-m-d H:i:s"),

								"payment_ref_number" => $referenceId,

								"is_gateway_verified" => $status_code == 'success' ? 1 : 0,

								//"payment_status"    => ($status_code == 'success' ? 1 : ($status_code == 'userCancelled' || $status_code == 'dropped' || $status_code == 'bounced' ? 4 : ($status_code == 'failure' ? 3 : ($status_code == 'refund' ? 6 : 7))))

								"payment_status"    => (strtolower($status_code) == 'success' ? 1 : (strtolower($status_code) == 'cancelled' || strtolower($status_code) == 'user_dropped' || strtolower($status_code) == 'userCancelled' || strtolower($status_code) == 'dropped' ||strtolower($status_code) == 'bounced'  ? 4 : (strtolower($status_code) == 'failed' || strtolower($status_code) == 'failure' ? 3 : (strtolower($status_code) == 'refund' ? 6 : 7))))

							);

							//update gateway verified flag if one day reached

							$pay_date = strtotime($tran['date_payment']);

							$oldDate = $pay_date + 86400; // 86400 seconds in 24 hrs

							$cDate = strtotime(date('Y-m-d H:i:s'));

							if ($oldDate < $cDate && $status_code != 'success') {

								$updateData = array("is_gateway_verified" => 1);

								$this->$model->updateGatewayResponse($updateData, $trans_id);
							}

							$payment = $this->$model->updateGatewayResponse($updateData, $trans_id);

							if ($status_code == "success") {

								$payIds = $this->$model->getPayIds($trans_id);

								//print_r($payIds);exit;

								if (sizeof($payIds) > 0) {

									// Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]

									$actual_trans_amt   = $payIds[0]['actual_trans_amt'];

									$act_wal_redeemed 	= $payIds[0]['redeemed_amount'];

									$remaining_amt 		= 0;

									$remaining_wal_amt	= 0;

									if ($act_wal_redeemed > 0) {

										// Update payment table - payment mode as multi

										$this->payment_modal->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $trans_id);
									}

									foreach ($payIds as $pay) {



										$tax 		= 0;

										$pay_amount = 0;

										if ($pay['gst_type'] == 1) { // Exclusive

											$tax = $pay['gst_amount'];
										}

										$pay_amount = ($pay['payment_amount'] - $pay['discountAmt'] + $tax);

										$mode_amount = $pay_amount;



										if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode

											if ($actual_trans_amt > $pay_amount) {

												$actual_trans_amt   -= $pay_amount;
											} else if ($actual_trans_amt == $pay_amount) {

												$actual_trans_amt   -= $pay_amount;
											} else if ($actual_trans_amt < $pay_amount) {

												$mode_amount			= $actual_trans_amt;

												$remaining_amt			= abs($actual_trans_amt - $pay_amount);

												$actual_trans_amt	= 0;

												$pay_amount 		  	= $remaining_amt;
											}

											/*echo "Having Gateway balance, add gateway mode.<br/>";

                            									echo "actual_trans_amt : ".$actual_trans_amt."<br/>";

                            									echo "remaining_amt : ".$remaining_amt."<br/>";*/

											// Insert payment Mode details               

											$arrayPayMode = array(

												'payment_amount'     => $mode_amount,

												// 'payment_date'		 => date("Y-m-d H:i:s"),
												'payment_date'		 => $pay['date_payment'],

												'created_time'	     => date("Y-m-d H:i:s"),

												//"payment_mode"       => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),

												"payment_mode"  	=> $updateData['payment_mode'],

												"remark"             => "Auto verify-Easebuzz on " . date("Y-m-d H:i:s"),

												"payment_ref_number" => $pay['payment_ref_number'],

												"payment_status"     => $updateData['payment_status']

											);

											if ($pay['due_type'] == 'GA') {

												$arrayPayMode['id_adv_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
											} else {

												$update_pmd = array(

													'payment_status' => 9, // Cancelled

													"updated_time"  => date('Y-m-d H:i:s'),

													"remark"        =>  "Removed while auto verify " . date('Y-m-d H:i:s'),

												);

												$update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');



												$arrayPayMode['id_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
											}
										}



										if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 

											if ($act_wal_redeemed > $pay_amount) {

												$act_wal_redeemed   -= $pay_amount;
											} else if ($act_wal_redeemed == $pay_amount) {

												$act_wal_redeemed   -= $pay_amount;
											}

											/*else if($act_wal_redeemed < $pay_amount){

                            										$remaining_amt		   = abs($act_wal_redeemed-$pay_amount);

                            										$act_wal_redeemed   = 0;

                            									}*/

											/*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";

                            									echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/

											// Insert payment Mode details               

											$arrayPayMode = array(

												'payment_amount'     => $pay_amount,

												// 'payment_date'		 => date("Y-m-d H:i:s"),
												'payment_date'		 => $pay['date_payment'],

												'created_time'	     => date("Y-m-d H:i:s"),

												"payment_mode"       => "REF_WALLET",

												"remark"             => "Wallet Utilized",

												"payment_ref_number" => $pay['payment_ref_number'],

												"payment_status"     => 1

											);

											if ($py['due_type'] == 'GA') {

												$arrayPayMode['id_adv_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
											} else {

												$update_pmd = array(

													'payment_status' => 9, // Cancelled

													"updated_time"  => date('Y-m-d H:i:s'),

													"remark"        =>  "Removed while auto verify " . date('Y-m-d H:i:s'),

												);

												$update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');



												$arrayPayMode['id_payment'] = $pay['id_payment'];

												$payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
											}

											$WalletinsData = array(

												'id_wallet_account'	=> $pay['id_wallet_account'],

												'transaction_type' 	=> 1, //0-Credit,1-Debit

												'type'             	=> 0, //CRM

												'id_sch_ac'        	=> $pay['id_scheme_account'],

												'value'            	=> $pay_amount,

												'description'      	=> 'Chit Redeem',

												'date_transaction' 	=> date("Y-m-d H:i:s"),

												'id_employee'      	=> NULL,

												'date_add'	       	=> date("Y-m-d H:i:s"),

												'credit_for'      	=> 'Redeem',

												'id_payment'      	=> $pay['id_payment']

											);

											$this->payment_model->insertData($WalletinsData, 'wallet_transaction');
										}

										/*// Multi mode payment

                                						    if($updateData['payment_mode']!= NULL)

                                             				{

                                             					$arrayPayMode=array(

                                                								'id_payment'         => $pay['id_payment'],

                                            							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),

                                                								'payment_date'		 => date("Y-m-d H:i:s"),

                                                								'created_time'	     => date("Y-m-d H:i:s"),

                                                								"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "netbanking" ? "NB":$paymentMode))),

                                                        						"remark"             => "Auto verify-Easebuzz",

                                                        						"payment_ref_number" => $referenceId,

                                                        						"payment_status"    => 1

                                                        					    );

                                            					$cashPayInsert = $this->$model->insertData($arrayPayMode,'payment_mode_details'); 

                                             				}*/

										$schData = [];

										// Generate account  number  

										if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {

											if ($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {

												$ac_group_code = NULL;

												// Lucky draw

												if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 

													// Update Group code in scheme_account table 

													$updCode = $this->$model->updateGroupCode($pay);

													$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
												}

												$scheme_acc_number = $this->$acc_model->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);

												if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {

													$schData['scheme_acc_number'] = $scheme_acc_number;

													if ($pay['id_scheme_account'] > 0) {

														if (sizeof($schData) > 0) { // Update scheme account

															$this->$acc_model->update_account($schData, $pay['id_scheme_account']);
														}
													}
												}
											}
										}

										// Generate receipt number

										if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2)) {

											$receipt['receipt_no'] = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);

											$payment['status'] = $this->$model->update_receipt($pay['id_payment'], $receipt);
										}

										//Update First Payment Amount In Scheme Account

										$approval_type = $this->config->item('auto_pay_approval');

										if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {

											if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight

												$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight']);
											} else {

												$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount']);
											}

											$status = $this->$acc_model->update_account($fixPayable, $pay['id_scheme_account']);
										}
									}
								}
							}

							$payres_array = array('dev_remark' => $status_code . "   " . $trans_id . " " . date('Y-m-d H:i:s'));

							$result =  $this->$model->paymentDB("updatestatus", $trans_id, $payres_array);

							echo $trans_id . "- Status : " . $status_code . "</br>";
						} else {

							$payres_array = array('dev_remark' => "No respone from gateway " . $tran['txn_ids'] . " " . date('Y-m-d H:i:s'));

							$result =  $this->$model->paymentDB("updatestatus", $tran['txn_ids'], $payres_array);

							echo $tran['txn_ids'] . "- Status : " . $response->msg . "</br>";

							// return true;

						}
					} else {

						$updateData = array("gateway_remark" => $response->msg);

						$this->$model->updateGatewayResponse($updateData, $tran['txn_ids']);

						echo $tran['txn_ids'] . "- Status : " . $response->msg . "</br>";
					}
				}
			}

			/*if($vCount > 0){

                echo $vCount." payment records verified successfully."; 	

            }

            else

            {

                echo " No records to verify."; 

            }*/
		} else {

			echo "No Payments to verify.";
		}
	}

	function cf_verify_pay_manual($secretKey, $appId, $ref_trans_id)

	{

		$model =	self::PAY_MODEL;

		$set_model =	self::SET_MODEL;

		$transData = array();

		$api_url = "https://api.cashfree.com/pg/orders";

		echo "<pre> secretKey : " . $secretKey . "</br>";

		echo "<pre> appId : " . $appId . "</br>";

		echo "<pre> Txn ID : " . $ref_trans_id . "</br>";

		$vCount = 0;

		$postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&orderId=" . $ref_trans_id;

		$curl = curl_init();

		curl_setopt_array($curl, array(

			CURLOPT_URL => $api_url . "/" . $ref_trans_id . "/payments",

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => '',

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 8,

			CURLOPT_FOLLOWLOCATION => true,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => 'GET',

			CURLOPT_HTTPHEADER => array(

				'x-client-id: ' . $appId,

				'x-client-secret: ' . $secretKey,

				'x-api-version: 2022-09-01'

			),

		));

		$pay_response = curl_exec($curl);

		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {

			echo "cURL Error #:" . $err;
			exit;
		} else {

			$response = json_decode($pay_response);

			echo print_r($response);

			$cf_response = $response[0]; // By default set first array as cf_response[which is last transaction data for the ref_trans_id]

			if (sizeof($response) > 0) { // Check size of response and update cf_response data if payment is success

				while (list(, $val) = each($response)) {

					if ($val->payment_status == 'SUCCESS') {

						$cf_response = $val;

						break;
					}
				}
			}

			echo "Processed CF Response : <pre>" . print_r($cf_response) . "</br>";

			$status_code = $cf_response->payment_status; // SUCCESS, 

			if ($ref_trans_id != "" && $status_code != 'PENDING' && $status_code != 'FLAGGED' && $status_code != '') {

				$updateData = array(

					"payu_id"           => $cf_response->cf_payment_id, // referenceId

					"payment_ref_number" => $cf_response->cf_payment_id,

					"payment_mode"      => ($cf_response->payment_group == "CREDIT_CARD" ? "CC" : ($cf_response->payment_group == "DEBIT_CARD" ? "DC" : ($cf_response->payment_group == "NET_BANKING" ? "NB" : $cf_response->payment_group))),

					"remark"            => $cf_response->payment_group . " - verifyService(Auto)",

					"is_gateway_verified" => 1,

					"payment_status"    => ($status_code == 'SUCCESS' ? 1 : ($status_code == 'CANCELLED' ? 4 : ($status_code == 'FAILED' ? 3 : ($status_code == 'REFUND' ? 6 : 7))))

				);

				echo "<pre> Update Pay Data : " . print_r($updateData) . "</br>";
				exit;
			}
		}
	}

	function razorPay_verify()

	{

		$pg_code = 7;

		$id_branch = 0;

		$model =	self::PAY_MODEL;

		$set_model =	self::SET_MODEL;

		$acc_model = self::ACC_MODEL;

		$previousDay = date('Y-m-d', strtotime("-3 days"));

		$currentDay = date('Y-m-d', strtotime(" 0 days"));

		$transData = array();

		$data['data'] = $this->$model->getPendpayment_Data($previousDay, $currentDay, $id_branch, 10);

		foreach ($data['data'] as $pay) {

			$transData[]     = $pay['order_id'];
		}

		$gateway_info = $this->$model->getBranchGatewayData($id_branch, $pg_code);

		$publicKey      = $gateway_info['param_3'];

		$secretKey          = $gateway_info['param_1'];

		//echo "<pre>"; print_r($transData);exit;

		$vCount = 0;

		if (sizeof($transData) > 0) {

			foreach ($transData as $tran) {

				$curl = curl_init();

				curl_setopt_array($curl, array(

					CURLOPT_URL => "https://api.razorpay.com/v1/orders/" . $tran . "/payments",

					CURLOPT_HTTPHEADER => array(

						"Content-Type: application/json"

					),

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_HTTPAUTH => CURLAUTH_BASIC,

					CURLOPT_USERPWD => "$publicKey:$secretKey",

					CURLOPT_FOLLOWLOCATION => true,

					CURLOPT_CUSTOMREQUEST => "GET",

				));

				$response = curl_exec($curl);

				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {

					echo "cURL Error #:" . $err;
				} else {

					$response = json_decode($response);

					//echo "<pre>"; print_r($response);exit;

					if (!empty($response->items) && $response->entity == 'collection' && $response->count > 0) {

						/* $rp_response = array();// By default set first array as cf_response[which is last transaction data for the ref_trans_id]
                        if(sizeof($response->items) > 0){ // Check size of response and update cf_response data if payment is success
                            while (list(, $val) = each($response->items)) {
                                // print_r($val);exit;
                                if ($val->status == 'captured' || $val->status == 'success') {
                                    $rp_response[] = $val;
                                    break;   
                                }
                            }
                        } */
                        $rp_response = []; // Default to empty
                        if (!empty($response->items)) {
                            // Filter for 'captured' or 'success' statuses
                            $captured_items = array_filter($response->items, function ($item) {
                                return $item->status === 'captured' || $item->status === 'success';
                            });
                            if (!empty($captured_items)) {
                                // Convert each captured stdClass object to array
                                foreach ($captured_items as $item) {
                                    $rp_response[] = $item;
                                }
                            } else {
                                // Default to the first item as array
                                $rp_response[] = $response->items[0];
                            }
                        }
                        foreach ($rp_response as $item) {

								$updateData = array(

									"payment_mode"       => ($item->method == "CREDIT_CARD" ? "CC" : ($item->method == "DEBIT_CARD" ? "DC" : ($item->method == "netbanking" ? "NB" : (!empty($item->method) ? $item->method : 'UPI')))),

									"payu_id"            => $item->id,

									"remark"             =>  "Auto - Verify by Razor pay on " . date("Y-m-d H:i:s"),

									"payment_ref_number" => $item->order_id,

									// "payment_status"     => 1
                                    "payment_status" => (($item->status == 'success' || $item->status == 'captured') ? 1 : ($item->status == 'cancelled' ? 4 : ($item->status == 'failed' ? 3 : ($item->status == 'refunded' ? 6 : 7))))

								);

								$payment = $this->$model->updData($updateData, 'payment_ref_number', $tran, 'payment');

								$payIds = $this->$model->getOrderIds($tran);

								if (sizeof($payIds) > 0) {

									foreach ($payIds as $pay) {

										// Multi mode payment

										if ($updateData['payment_mode'] != NULL) {

											$update_pmd = array(

												'payment_status' => 9, // Cancelled

												"updated_time"  => date('Y-m-d H:i:s'),

												"remark"        =>  "Removed while auto verify " . date('Y-m-d H:i:s'),

											);

											$update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');



											$arrayPayMode = array(

												'id_payment'         => $pay['id_payment'],

												'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),

												'payment_date'		 => date("Y-m-d H:i:s"),

												'created_time'	     => date("Y-m-d H:i:s"),

												"payment_mode"       => ($item->method == "CREDIT_CARD" ? "CC" : ($item->method == "DEBIT_CARD" ? "DC" : ($item->method == "netbanking" ? "NB" : (!empty($item->method) ? $item->method : 'UPI')))),

												"payment_ref_number" => $item->order_id,

												"payment_status"     => 1

											);

											$cashPayInsert = $this->$model->insertData($arrayPayMode, 'payment_mode_details');
										}

										$schData = [];

										$cusRegData = [];

										$transData = [];

										// Generate account  number  

										if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {

											if ($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {

												$ac_group_code = NULL;

												// Lucky draw

												if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 

													// Update Group code in scheme_account table 

													$updCode = $this->$model->updateGroupCode($pay['id_scheme_account']);

													$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
												}

												$scheme_acc_number = $this->$acc_model->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);

												if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {

													$schData['scheme_acc_number'] = $scheme_acc_number;

													$cusRegData['scheme_acc_number'] = $scheme_acc_number;
												}
											}
										}

										// Generate Client ID

										if ($pay['gent_clientid'] == 1 && $scheme_acc_number != NULL && empty($pay['ref_no'])) {

											$cliData = array(

												"cliID_short_code"	=> $this->config->item('cliIDcode'),

												"sync_scheme_code"	=> $pay['sync_scheme_code'],

												"code"	            => $pay['group_code'],

												"ac_no"			=> $scheme_acc_number

											);

											$schData['ref_no'] = $this->$model->generateClientID($cliData);

											$cusRegData['ref_no'] = $schData['ref_no'];

											$transData['ref_no'] = $schData['ref_no'];

											$cusRegData['group_code'] = $pay['group_code'];
										}

										// Generate receipt number

										if ($pay['receipt_no_set'] == 1) {

											$receipt['receipt_no'] = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);

											$payment['status'] = $this->$model->update_receipt($pay['id_payment'], $receipt);
										}

										if ($pay['id_scheme_account'] > 0) {

											if (sizeof($schData) > 0) { // Update scheme account

												$this->$model->update_account($schData, $pay['id_scheme_account']);
											}
										}

										//Update First Payment Amount In Scheme Account

										$approval_type = $this->config->item('auto_pay_approval');

										if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {

											if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight

												$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight']);
											} else {

												$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount']);
											}

											$status = $this->$model->update_account($fixPayable, $pay['id_scheme_account']);
										}
									}

								$payres_array = array('dev_remark' => $item->status . "   " . $item->notes->id_transaction . " " . date('Y-m-d H:i:s'));

								$result =  $this->$model->paymentDB("updatestatus", $item->notes->id_transaction, $payres_array);

								echo "<pre>" . $tran . " - " . $item->status . "\n";
							} else {

								$payres_array = array('dev_remark' => $item->status . " " . $item->notes->id_transaction . " " . date('Y-m-d H:i:s'));

								$result =  $this->$model->paymentDB("updatestatus", $item->notes->id_transaction, $payres_array);

								echo "<pre>" . $tran . " - " . $item->status . "\n";
							}
						}
					} else {

						$payres_array = array(

							'dev_remark' => "No response from razorpay " . $tran . " " . date('Y-m-d H:i:s'),

							"remark"    => "Auto - Verify by Razor pay - " . date('Y-m-d H:i:s'),

						);

						$result =  $this->$model->update_data($payres_array, 'payment_ref_number', $tran, 'payment');
					}
				}
			}

			if ($vCount >= 0) {

				echo " payment records verified successfully.";
			} else {

				// echo " No records to verify. Message ". print_r($response); 

				echo " No records to verify.";
			}
		} else {

			echo "Select Payments to verify.";
		}
	}

	/* Delete accounts without valid payments

	*  Shall change the date in controller. Accounts without  payment and accounts with failed, cancelled, pending payments will be deleted.

	*  Backup will be done in deletd_scheme_accounts and deleted_payment

	*  :: Pavithra

	**/

	function deleteNoPayAcc()
	{

		$from_date = date('Y-m-d', strtotime("-5 days"));

		$to_date = date('Y-m-d', strtotime("-1 days"));

		$result = $this->services_model->deleteNoPayAcc($from_date, $to_date);

		echo "<pre>";
		print_r($result);

		$ser_log_data = array(

			"service_name"	=> "Delete records without payments",

			"event_date"	=> date("Y-m-d H:i:s"),

			"module"		=> "Scheme Account",

			"operation"		=> "Delete",

			"record"		=> json_encode($result),

			"remark"		=> "Deleted No payment accounts. 1 day older"

		);

		$this->services_model->insertData($ser_log_data, 'service_log');
	}

	/* Delete 3months older invalid payments

	*  3 Month old failed, cancelled, pending payments will be deleted.

	*  Backup will be done in deleted_payment

	*  :: Pavithra

	**/

	function deleteInvalidPay()
	{

		$from_date = date('Y-m-d', strtotime("-120 days"));

		$to_date = date('Y-m-d', strtotime("-90 days"));

		$result = $this->services_model->deleteInvalidPay($from_date, $to_date);

		echo "<pre>";
		print_r($result);

		$ser_log_data = array(

			"service_name"	=> "Delete invalid payments",

			"event_date"	=> date("Y-m-d H:i:s"),

			"module"		=> "Scheme Account",

			"operation"		=> "Delete",

			"record"		=> json_encode($result),

			"remark"		=> "Deleted cancelled,pending,failed payments. 90 days older"

		);

		$this->services_model->insertData($ser_log_data, 'service_log');
	}

	public function update_dueMonthYear()
	{

		$accounts = $this->payment_model->get_scheme_acc_data();

		foreach ($accounts as $acc) {

			$status = $this->payment_model->update_dueMonYear($acc['id_scheme_account']);

			if ($status == 1) {

				echo 'Due month year updated for id_scheme_acc :  ' . $acc['id_scheme_account'];
			} else {

				echo 'Failed for id_scheme_acc : ' . $acc['id_scheme_account'];
			}
		}
	}

	/*  Send birthday and wedding day wishes sms everyday cron service function

    Note : Only SMS worked , Have to work for email and whatsapp.... Dt Ad : 26-01-2024 , By:AB

*/

	public function trigger_wishes()
	{

		$model = self::MODEL;

		$customer_data = $this->$model->get_customer_wishes_data();

		/*print_r($this->db->last_query());exit;

	    echo '<pre>';print_r($customer_data);exit;*/

		foreach ($customer_data as $cus) {

			if ($cus['send_bday'] == 1) {

				//send birthday wish

				$birthday[] = $cus;
			}

			if ($cus['send_wedday'] == 1) {

				//send wedding day wish

				$weddingday[] = $cus;
			}
		}

		if (sizeof($birthday) > 0 || sizeof($weddingday) > 0) {

			if (sizeof($birthday) > 0) {

				$this->send_wishesToCus($birthday, 'BDAY_WISH');
			}

			if (sizeof($weddingday) > 0) {

				$this->send_wishesToCus($weddingday, 'WED_WISH');
			}
		} else {

			$response =  'No customers...</br>';

			echo $response;

			// Log

			$log_path = $this->log_dir . 'trigger_wishes/' . date("Y-m-d") . '.txt';

			$data = "\n ----- \n Trigger Services " . date("Y-m-d H:i:s") . ": " . json_encode($response);

			file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
		}
	}

	public function send_wishesToCus($customer, $code)
	{

		$model = self::MODEL;

		$checkService = $this->services_model->checkServiceCode($code);

		if ($checkService['sms'] == 1) {

			//get sms content...

			foreach ($customer as $cus) {

				$wishes =  $this->$model->Get_service_code_sms($code, $cus['id_customer'], '');

				if ($this->config->item('sms_gateway') == '1') {

					$sendsms = $this->sms_model->sendSMS_MSG91($wishes['mobile'], $wishes['message'], '', $wishes['dlt_te_id'], '');
				} elseif ($this->config->item('sms_gateway') == '2') {

					$sendsms = $this->sms_model->sendSMS_Nettyfish($wishes['mobile'], $wishes['message'], 'trans');
				} elseif ($this->config->item('sms_gateway') == '3') {

					$sendsms = $this->sms_model->sendSMS_SpearUC($wishes['mobile'], $wishes['message'], '', $wishes['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '4') {

					$sendsms = $this->sms_model->sendSMS_Asterixt($wishes['mobile'], $wishes['message'], '', $wishes['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '5') {

					$sendsms = $this->sms_model->sendSMS_Qikberry($wishes['mobile'], $wishes['message'], '', $wishes['dlt_te_id']);
				}

				//$sendsms = 1;

				if ($sendsms) {

					$response = '<font color="green">' . $code . ' Wishes sent in sms successfully for ' . $wishes['mobile'] . '</font><br/>';

					echo $response;

					// Log

					$sub_log_path = $this->log_dir . 'trigger_wishes/';

					$log_path = $sub_log_path . date("Y-m-d") . '.txt';

					if (!is_dir($sub_log_path)) {

						mkdir($sub_log_path, 0777, true);
					}

					$data = "\n ----- \n Trigger Services " . date("Y-m-d H:i:s") . ": " . json_encode($response);

					file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
				} else {

					$response = '<font color="red">Unable to send ' . $code . '  wishes for ' . $wishes['mobile'] . '</font><br/>';

					echo $response;

					// Log

					$sub_log_path = $this->log_dir . 'trigger_wishes/';

					$log_path = $sub_log_path . date("Y-m-d") . '.txt';

					if (!is_dir($sub_log_path)) {

						mkdir($sub_log_path, 0777, true);
					}

					$data = "\n ----- \n Trigger Services " . date("Y-m-d H:i:s") . ": " . json_encode($response);

					// echo '<pre>'; print_r($log_path); exit; 

					file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
				}
			}
		} else {

			$response = 'sms not enabled in services for ' . $code . '</br>';

			echo $response;

			// Log

			$log_path = $this->log_dir . 'trigger_wishes/' . date("Y-m-d") . '.txt';

			$data = "\n ----- \n Trigger Services " . date("Y-m-d H:i:s") . ": " . json_encode($response);

			file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
		}
	}

	/* Sending wishes sms ends*/

	// wallet reminder //sms to customr 

	public function send_wallet_reminder()
	{

		$model = self::MODEL;

		$customer_data = $this->$model->get_cus_wallet_data();

		$this->sendwalletreminder($customer_data, 'WALLET_REMIN');
	}

	public function sendwalletreminder($customer, $code)
	{

		$model = self::MODEL;

		$checkService = $this->services_model->checkServiceCode($code);

		if ($checkService['sms'] == 1) {

			//get sms content...

			foreach ($customer as $cus) {

				$cus_reminfer =  $this->$model->Get_service_code_sms($code, $cus['id_customer'], '');

				if ($this->config->item('sms_gateway') == '1') {

					$sendsms = $this->sms_model->sendSMS_MSG91($cus_reminfer['mobile'], $cus_reminfer['message'], '', $cus_reminfer['dlt_te_id'], '');
				} elseif ($this->config->item('sms_gateway') == '2') {

					$sendsms = $this->sms_model->sendSMS_Nettyfish($cus_reminfer['mobile'], $cus_reminfer['message'], 'trans');
				} elseif ($this->config->item('sms_gateway') == '3') {

					$sendsms = $this->sms_model->sendSMS_SpearUC($cus_reminfer['mobile'], $cus_reminfer['message'], '', $cus_reminfer['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '4') {

					$sendsms = $this->sms_model->sendSMS_Asterixt($cus_reminfer['mobile'], $cus_reminfer['message'], '', $cus_reminfer['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '5') {

					$sendsms = $this->sms_model->sendSMS_Qikberry($cus_reminfer['mobile'], $cus_reminfer['message'], '', $cus_reminfer['dlt_te_id']);
				}

				//$sendsms = 1;

				if ($sendsms) {

					$response = '<font color="green">' . $code . ' Wallet reminder sent in sms successfully for ' . $cus_reminfer['mobile'] . '</font><br/>';

					echo $response;

					// Log

					$sub_log_path = $this->log_dir . 'wallet_reminder/';

					$log_path = $sub_log_path . date("Y-m-d") . '.txt';

					if (!is_dir($sub_log_path)) {

						mkdir($sub_log_path, 0777, true);
					}

					$data = "\n ----- \n wallet_reminder Services " . date("Y-m-d H:i:s") . ": " . json_encode($response);

					//  print_r($data);exit;

					file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
				} else {

					$response = '<font color="red">Unable to send ' . $code . '  wishes for ' . $cus_reminfer['mobile'] . '</font><br/>';

					echo $response;

					// Log

					$sub_log_path = $this->log_dir . 'wallet_reminder/';

					$log_path = $sub_log_path . date("Y-m-d") . '.txt';

					if (!is_dir($sub_log_path)) {

						mkdir($sub_log_path, 0777, true);
					}

					$data = "\n ----- \n wallet reminder Services " . date("Y-m-d H:i:s") . ": " . json_encode($response);

					//  print_r($data);exit;

					file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
				}
			}
		} else {

			$response = 'sms not enabled in services for ' . $code . '</br>';

			echo $response;

			// Log

			$sub_log_path = $this->log_dir . 'wallet_reminder/';

			$log_path = $sub_log_path . date("Y-m-d") . '.txt';

			if (!is_dir($sub_log_path)) {

				mkdir($sub_log_path, 0777, true);
			}

			$data = "\n ----- \n wallet reminder Services " . date("Y-m-d H:i:s") . ": " . json_encode($response);

			//  print_r($data);exit;

			file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
		}
	}



	/**     CREATE REFUND 

	 * 

	 * {"cf_payment_id":3048670870,"cf_refund_id":"refund_57497690","created_at":"2024-09-24T13:59:35+05:30","entity":"refund","metadata":null,"order_id":"172614941166e2f323df79b","processed_at":null,"refund_amount":1.00,"refund_arn":null,"refund_charge":0.00,"refund_currency":"INR","refund_id":"rf_66f2786d41aed83fee39","refund_mode":null,"refund_note":"Refund requested for later success payment","refund_speed":{"requested":"STANDARD","accepted":"STANDARD","processed":null,"message":null},"refund_splits":[],"refund_status":"PENDING","refund_type":"MERCHANT_INITIATED","status_description":"In Progress"}

	 * ["SUCCESS", "PENDING", "CANCELLED", "ONHOLD", "FAILED"]

	 * 

	 */



	function createRefund($refundData)
	{

		$refund_id = 'rf_' . $this->generateRandomUniqueString(20);

		$secretKey = $refundData['gateway_info']['param_1'];

		$appId     = $refundData['gateway_info']['param_3'];

		$api_url   = "https://api.cashfree.com/pg/orders";



		$curl = curl_init();



		curl_setopt_array($curl, [

			CURLOPT_URL => $api_url . "/" . $refundData['ref_trans_id'] . "/refunds",

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => "",

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 30,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => "POST",

			CURLOPT_POSTFIELDS => json_encode([

				'refund_amount' => $refundData['order_amount'],

				'refund_id'     => $refund_id, // string | required | length between 3 and 40

				'refund_note'   => 'Refund requested for later success payment',

				'refund_speed'  => 'STANDARD'

			]),

			CURLOPT_HTTPHEADER => [

				"accept: application/json",

				"content-type: application/json",

				"x-api-version: 2022-09-01",

				'x-client-id: ' . $appId,

				'x-client-secret: ' . $secretKey,

			],

		]);



		$pay_response = curl_exec($curl);

		$err = curl_error($curl);



		curl_close($curl);

		$log_folder     = $this->log_dir . "" . date("Y-m-d") . "/cf_refund";

		if (!is_dir($log_folder)) {

			mkdir($log_folder, 0777, TRUE);
		}

		$log_path       = $log_folder . '/create.txt';

		if ($err) {

			//echo "cURL Error #:" . $err;

			$data           = "\n " . date("Y-m-d H:i:s") . " \n " . "cURL Error #:" . $err;

			file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);

			return ["status" => FALSE, "msg" => "curl error", "error" => $err];
		} else {

			$response = json_decode($pay_response);

			echo "<pre>";
			print_r($response);

			$data           = "\n " . date("Y-m-d H:i:s") . " \n " . "API Response #:" . $pay_response;

			file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);

			if (isset($response->code)) { // Error

				$msg            = $response->message;

				$remark         = "Create Refund Failed" . $response->type . " " . $response->code;

				//$payment_status = 2; // Update status as awaiting

				$refund_status  = FALSE;
			} else if (isset($response->refund_status)) { // Refund created

				$msg            = $response->refund_status;

				$remark         = "Refund Initiated";

				$payment_status = ($response->refund_status == "SUCCESS" ? 6 : ($response->refund_status == "CANCELLED" ? 11 : ($response->refund_status == "ONHOLD" ? 12 : ($response->refund_status == "FAILED" ? 13 : ($response->refund_status == "PENDING" ? 10 : 2)))));

				$refund_status  = FALSE;





				$updateData = array(

					"payment_mode"       => $refundData['payment_mode'],

					"payu_id"            => $refundData['payu_id'],

					"remark"             => $refundData['remark'],

					"payment_ref_number" => $refundData['payment_ref_number'],

					"payment_status"     => $payment_status,

					"refund_id"          => $refund_id,

					"is_gateway_verified" => 1

				);



				$updPayStatus = $this->payment_model->updateGatewayResponse($updateData, $refundData['ref_trans_id']);



				// ALTER TABLE `payment` ADD `refund_id` VARCHAR(50) NULL DEFAULT NULL AFTER `due_date_to`;



				if ($updPayStatus['status']) {

					print_r($updPayStatus);

					$payIds  = $this->payment_model->getRefundPayData($refundData['ref_trans_id']);

					foreach ($payIds as $payId) {

						$pay_status_array = array(

							'id_payment'		=>  $payId['id_payment'],

							'id_status_msg' 	=>  $updateData['payment_status'],

							'date_upd'			=>  date('Y-m-d H:i:s')

						);

						$ppsm    = $this->payment_model->insertData($pay_status_array, "payment_status");

						$linsData = array(

							"date"          => date("Y-m-d H:i:s"),

							"operation"     => "Refund-INI",

							"record"        => $payId['id_payment'],

							"request_from"  => "Service",

							"remark"        => "Refund Initiated. Refund ID : " . $refund_id

						);

						$this->payment_model->insertData($linsData, "log_payment");

						$insData = array(

							"created_on"    => date("Y-m-d H:i:s"),

							"ref_trans_id"  => $refundData['ref_trans_id'],

							"refund_id"     => $refund_id,

							"refund_status" => $updateData['payment_status'],

							"message"       => $msg

						);

						$this->payment_model->insertData($insData, "payment_refund_log");
					}
				}
			}

			return ["status" => $refund_status, "msg" => $msg];
		}
	}



	function generateRandomUniqueString($length)
	{

		$randomString = uniqid() . bin2hex(random_bytes(5));

		return substr($randomString, 0, $length);  // Ensure the desired length

	}



	/* Refund Webhooks Data

    * 

        {

            "data": {

                "refund": {

                    "cf_refund_id": 57497690,

                    "cf_payment_id": 789727431,

                    "refund_id": "rf_66f2786d41aed83fee39",

                    "order_id": "172614941166e2f323df79b",

                    "refund_amount": 1.00,

                    "refund_currency": "INR",

                    "entity": "Refund",

                    "refund_type": "MERCHANT_INITIATED",

                    "refund_arn": "3048670870",

                    "refund_status": "FAILED",

                    "status_description": "Refund processed successfully",

                    "created_at": "2022-02-28T12:54:25+05:30",

                    "processed_at": "2022-02-28T13:04:27+05:30",

                    "refund_charge": 0,

                    "refund_note": "Test",

                    "refund_splits": [

                        {

                            "merchantVendorId": "sampleID12345",

                            "amount": 1,

                            "percentage": null

                        },

                        {

                            "merchantVendorId": "otherVendor",

                            "amount": 1,

                            "percentage": null

                        }

                    ],

                    "metadata": null,

                    "refund_mode": "STANDARD"

                }

            },

            "event_time": "2022-02-28T13:04:28+05:30",

            "type": "REFUND_STATUS_WEBHOOK"

        }

    **/



	function getRefundStatusHook($id_pg)
	{



		$response       = json_decode(file_get_contents('php://input'));

		$log_folder     = $this->log_dir . "" . date("Y-m-d") . "/cf_refund";

		if (!is_dir($log_folder)) {

			mkdir($log_folder, 0777, TRUE);
		}

		$log_path       = $log_folder . '/hook.txt';

		$data           = "\n " . date("Y-m-d H:i:s") . " \n Response : " . json_encode($response);

		file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);



		if (sizeof($response) == 0) {

			exit;
		}



		/* // Signature check

    	    $gateway_info   = $this->payment_model->getGatewayData($id_pg);

            $secretKey      = $gateway_info['param_1'];   

            $appId          = $gateway_info['param_3'];  

            $computeSig     = base64_encode(hash_hmac('sha256', $signStr, $secretKey, true));

        */

		$actual_trans_amt = 0;



		if (isset($response->data) && $response->type == "REFUND_STATUS_WEBHOOK") {

			if (isset($response->data->refund)) {

				$refundRespData = $response->data->refund;

				//echo "<pre>";print_r($refundRespData);exit;

				$lastStatus = $this->payment_model->getLastRefundStatus($refundRespData->refund_id);

				if (($lastStatus == 6 && $refundRespData->refund_status == "SUCCESS") || ($lastStatus == 11 && $refundRespData->refund_status == "CANCELLED") || ($lastStatus == 12 && $refundRespData->refund_status == "ONHOLD") || ($lastStatus == 13 && $refundRespData->refund_status == "FAILED")) {

					echo $lastStatus . ". Status already updated.";
					exit;
				}



				if ($refundRespData->refund_status != "PENDING") {

					$updateData["last_update"]      = date("Y-m-d H:i:s");

					$updateData["date_upd"]         = date("Y-m-d H:i:s");

					$updateData["payment_status"]   = ($refundRespData->refund_status == "SUCCESS" ? 6 : ($refundRespData->refund_status == "CANCELLED" ? 11 : ($refundRespData->refund_status == "ONHOLD" ? 12 : ($refundRespData->refund_status == "FAILED" ? 13 : ($refundRespData->refund_status == "PENDING" ? 10 : 2)))));

					$updID = $this->payment_model->update_data($updateData, 'refund_id', $refundRespData->refund_id, "payment");

					if (!empty($updID)) {

						$payIds  = $this->payment_model->getRefundPayData($refundRespData->order_id);

						foreach ($payIds as $payId) {

							$actual_trans_amt = $payId['actual_trans_amt'];

							$pay_status_array = array(

								'id_payment'		=>  $payId['id_payment'],

								'id_status_msg' 	=>  $updateData['payment_status'],

								'date_upd'			=>  date('Y-m-d H:i:s')

							);

							$psm     = $this->payment_model->insertData($pay_status_array, "payment_status");

							$linsData = array(

								"date"          => date("Y-m-d H:i:s"),

								"operation"     => "Refund-INI",

								"record"        => $payId['id_payment'],

								"request_from"  => "Hook",

								"remark"        => $refundRespData->status_description . " Status : " . $refundRespData->refund_status

							);

							$this->payment_model->insertData($linsData, "log_payment");

							$insData = array(

								"created_on"    => date("Y-m-d H:i:s"),

								"ref_trans_id"  => $payId['ref_trans_id'],

								"refund_id"     => $refundRespData->refund_id,

								"refund_status" => $updateData['payment_status'],

								"message"       => $refundRespData->status_description . " Status : " . $refundRespData->refund_status

							);

							$this->payment_model->insertData($insData, "payment_refund_log");
						}

						$dataRes   = "\n " . date("Y-m-d H:i:s") . " Refund ID : " . $refundRespData->refund_id . " updated.";

						file_put_contents($log_path, $dataRes, FILE_APPEND | LOCK_EX);

						echo "Refund status updated successfully.";
					} else {

						echo $this->db->last_query();
					}

					if ($refundRespData->refund_status == "FAILED") {

						$gateway_info   = $this->payment_model->getGatewayData($id_pg);

						$refundData     = [

							"order_amount"      => $actual_trans_amt,

							"payment_status"    => 6, // Refund

							"ref_trans_id"      => $refundRespData->order_id,

							"gateway_info"      => $gateway_info

						];

						$refundStatus   = $this->createRefund($refundData); // Retry refund

					}
				} else {

					echo $refundRespData->refund_id . " refund still pending <br/>";
				}
			}
		}
	}



	function getRefundStatus()
	{

		$pg_code        = $_GET("pg_code");

		$id_branch      = $_GET("id_branch");

		$pendingRefunds = $this->payment_model->getPendingRefunds($pg_code, $id_branch);

		$secretKey      = $pendingRefunds['gateway_info']['param_1'];

		$appId          = $pendingRefunds['gateway_info']['param_3'];

		$api_url        = "https://api.cashfree.com/pg/orders";

		if (sizeof($pendingRefunds['data'] > 0)) {

			foreach ($pendingRefunds['data'] as $data) {

				$curl = curl_init();



				curl_setopt_array($curl, [

					CURLOPT_URL => $api_url . "/" . $data['ref_trans_id'] . "/refunds/" . $data['refund_id'],

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_ENCODING => "",

					CURLOPT_MAXREDIRS => 10,

					CURLOPT_TIMEOUT => 30,

					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

					CURLOPT_CUSTOMREQUEST => "GET",

					CURLOPT_HTTPHEADER => [

						"accept: application/json",

						"x-api-version: 2022-09-01",

						'x-client-id: ' . $appId,

						'x-client-secret: ' . $secretKey,

					],

				]);



				$pay_response = curl_exec($curl);

				$err = curl_error($curl);



				curl_close($curl);



				if ($err) {

					echo "cURL Error #:" . $err;
				} else {

					$response = json_decode($pay_response);

					echo "<pre>";
					print_r($response);

					if (isset($response->code)) {

						$insData = array(

							"created_on"    => date("Y-m-d H:i:s"),

							"ref_trans_id"  => $data['ref_trans_id'],

							"refund_id"     => $data['refund_id'],

							"message"       => $response->message,

							//	"remark"        => "Get refund details failed.".$response->type." ".$response->code

						);

						$this->payment_model->insertData($insData, "payment_refund_log");
					} else if (isset($response->refund_status)) {

						if ($response->refund_status != "PENDING") {

							//refund_status - string - This can be one of ["SUCCESS", "PENDING", "CANCELLED", "ONHOLD", "FAILED"]

							$updateData = array(

								"payment_mode"       => $refundData['payment_mode'],

								"payu_id"            => $refundData['payu_id'],

								"remark"             => $refundData['remark'],

								"payment_ref_number" => $refundData['payment_ref_number'],

								"payment_status"     => ($response->refund_status == "SUCCESS" ? 6 : ($response->refund_status == "CANCELLED" ? 11 : ($response->refund_status == "ONHOLD" ? 12 : ($response->refund_status == "FAILED" ? 13 : 2)))),

								"refund_id"          => $data['refund_id']

							);



							$result = $this->payment_model->updateGatewayResponse($updateData, $refundData['ref_trans_id']);

							var_dump($result);

							// ALTER TABLE `payment` ADD `refund_id` VARCHAR(50) NULL DEFAULT NULL AFTER `due_date_to`;

							if ($result['status']) {

								$payIds  = $this->payment_model->getRefundPayData($refundData['ref_trans_id']);

								foreach ($payIds as $payId) {

									$pay_status_array = array(

										'id_payment'		=>  $payId['id_payment'],

										'id_status_msg' 	=>  $updateData['payment_status'],

										'date_upd'			=>  date('Y-m-d H:i:s')

									);

									$ppsm    = $this->payment_model->insertData($pay_status_array, "payment_status");

									$insData = array(

										"date"          => date("Y-m-d H:i:s"),

										"operation"     => "Refund-INI",

										"record"        => $payId['id_payment'],

										"request_from"  => "Service",

										"remark"        => "Get Refund Details. Refund ID : " . $data['refund_id']

									);

									$this->payment_model->insertData($insData, "log_payment");

									$insData = array(

										"created_on"    => date("Y-m-d H:i:s"),

										"ref_trans_id"  => $payId['ref_trans_id'],

										"refund_id"     => $data['refund_id'],

										"refund_status" => $updateData['payment_status']

									);

									$this->payment_model->insertData($insData, "payment_refund_log");
								}
							}
						} else {

							echo $data['refund_id'] . " refund still pending <br/>";
						}
					}
				}
			}
		} else {

			echo "No pending refunds";
		}
	}



	function test()
	{

		$refundData     = [

			"order_amount"      => 1,

			"payu_id"           => 1, // referenceId

			"payment_ref_number" => 1,

			"payment_mode"      => 10,

			"remark"            =>  " - verifyService(Auto)",

			"ref_trans_id"      => "172614941166e2f323df79b"

		];

		$response = json_decode(file_get_contents('php://input'));

		echo "<pre>";
		print_r($response);

		$data           = "\n " . date("Y-m-d H:i:s") . " \n " . "API Response #:" . $pay_response;

		file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);

		if (isset($response->code)) { // Error

			$msg            = $response->message;

			$remark         = "Create Refund Failed" . $response->type . " " . $response->code;

			//$payment_status = 2; // Update status as awaiting

			$refund_status  = FALSE;
		} else if (isset($response->refund_status)) { // Refund created

			$msg            = $response->refund_status;

			$remark         = "Refund Initiated";

			$payment_status = ($response->refund_status == "SUCCESS" ? 6 : ($response->refund_status == "CANCELLED" ? 11 : ($response->refund_status == "ONHOLD" ? 12 : ($response->refund_status == "FAILED" ? 13 : ($response->refund_status == "PENDING" ? 10 : 2)))));

			$refund_status  = FALSE;

			$refund_id      = "rf_66f2786d41aed83fee39";



			$updateData = array(

				"payment_mode"       => $refundData['payment_mode'],

				"payu_id"            => $refundData['payu_id'],

				"remark"             => $refundData['remark'],

				"payment_ref_number" => $refundData['payment_ref_number'],

				"payment_status"     => $payment_status,

				"refund_id"          => $refund_id,

				"is_gateway_verified" => 1

			);

			$this->db->trans_begin();



			$updPayStatus = $this->payment_model->updateGatewayResponse($updateData, $refundData['ref_trans_id']);

			// ALTER TABLE `payment` ADD `refund_id` VARCHAR(50) NULL DEFAULT NULL AFTER `due_date_to`;



			if ($updPayStatus['status']) {

				print_r($updPayStatus);

				$payIds  = $this->payment_model->getRefundPayData($refundData['ref_trans_id']);

				foreach ($payIds as $payId) {

					$pay_status_array = array(

						'id_payment'		=>  $payId['id_payment'],

						'id_status_msg' 	=>  $updateData['payment_status'],

						'date_upd'			=>  date('Y-m-d H:i:s')

					);

					$ppsm    = $this->payment_model->insertData($pay_status_array, "payment_status");

					$linsData = array(

						"date"          => date("Y-m-d H:i:s"),

						"operation"     => "Refund-INI",

						"record"        => $payId['id_payment'],

						"request_from"  => "Service",

						"remark"        => "Refund Initiated. Refund ID : " . $refund_id

					);

					$this->payment_model->insertData($linsData, "log_payment");

					$insData = array(

						"created_on"    => date("Y-m-d H:i:s"),

						"ref_trans_id"  => $refundData['ref_trans_id'],

						"refund_id"     => $refund_id,

						"refund_status" => $updateData['payment_status'],

						"message"       => $msg

					);

					$this->payment_model->insertData($insData, "payment_refund_log");
				}
			}

			if ($this->db->trans_status() === TRUE) {

				$this->db->trans_commit();
			} else {

				$this->db->trans_rollback();

				echo $this->db->last_query();
			}
		}



		print_r(["status" => $refund_status, "msg" => $msg]);
	}


	function update_paymentMode($mode){

		$mode = strtolower(trim($mode));

		// Check if mode_name or short_code already matches (case-insensitive)
		$this->db->from('payment_mode');
		 $this->db->where('LOWER(short_code)', $mode);
		$query = $this->db->get();

		$short_code = strtoupper($mode); // e.g., upi_payment -> UPI_PAYMENT

		if ($query->num_rows() == 0 && !empty($mode)) {
			// Convert mode to proper format
			$formatted_mode_name = ucwords(str_replace('_', ' ', $mode)); // e.g., upi_payment -> Upi Payment
			

			// Insert new payment mode
			$data = array(
				'mode_name'   => $formatted_mode_name,
				'short_code'  => $short_code,
				'status'      => 1,
				'sort_order'  => 0,
				'show_in_pay' => 0
			);
			$this->db->insert('payment_mode', $data);
		}
		return $short_code;
	}

	//AFTER IMPORT UPDATES START>>>

	/* ORDER TO RUN...
	1. upd_firstPayAs_startDate   --> to update first payment date as start date
	2. dayDurationSchemeService--> to update installment no, due date
	3. upd_total_paid_ins  --> to update total installment */


	
	public function passwdord_hash() {
		$sql = "SELECT id_employee, FROM_BASE64(passwd) as passwd FROM employee";
		$result = $this->db->query($sql)->result_array();

		foreach ($result as $res) {
			$id_employee = $res['id_employee'];
			$plain_pass  = trim($res['passwd']);
			$pwd_hash    = password_hash($plain_pass, PASSWORD_DEFAULT);
			$this->db->query(
				"UPDATE employee SET pwd_hash = ? WHERE id_employee = ?",
				[$pwd_hash, $id_employee]
			);
		}
	}

	public function old_dayDurationSchemeService($id_scheme = "", $id_scheme_account = "")
    {



        //get active scheme account and its payment from this scheme id....



        $payments = $this->db->query("SELECT p.id_payment,p.id_scheme_account,date(p.date_payment) as date_payment,sa.start_date,p.due_type,p.due_date,p.grace_date,p.is_limit_exceed 

                                FROM payment p 

                                LEFT JOIN scheme_account sa on (sa.id_scheme_account = p.id_scheme_account) 

                                WHERE   p.payment_status = 1 and p.installment is null
                                " . ($id_scheme > 0 ? "and sa.id_scheme = " . $id_scheme : '') . "

                                " . ($id_scheme_account > 0 ? "and sa.id_scheme_account = " . $id_scheme_account : '') . "

                                 order by p.id_scheme_account ASC ,date(p.date_payment) asc

                               ")->result_array();

//         print_r($this->db->last_query());exit;

        //pass id scheme account and date payment to the query...

        // echo '<pre>';print_r($payments);exit;

        foreach ($payments as $pay) {



            $dueData = $this->payment_model->get_due_date('service', $pay['date_payment'], $pay['id_scheme_account']);

            // print_r($dueData);exit;

            if (sizeof($dueData[0]) == 0) {



                $dueData = $this->payment_model->get_due_date('service', $pay['date_payment'], $pay['id_scheme_account']);
            }







            //update the respective from the response...

            if (sizeof($dueData[0]) > 0) {

                $cycle_data = array(

                    'due_date'           => (isset($dueData[0]['due_date_from']) ? $dueData[0]['due_date_from'] : NULL),

                    'due_date_to'           => (isset($dueData[0]['due_date_to']) ? $dueData[0]['due_date_to'] : NULL),

                    'grace_date'           => (isset($dueData[0]['grace_date']) ? $dueData[0]['grace_date'] : NULL),

                    'installment'           => (isset($dueData[0]['installment']) ? $dueData[0]['installment'] : NULL),

                    'is_limit_exceed'           => (isset($dueData[0]['is_limit_exceed']) ? $dueData[0]['is_limit_exceed'] : 0),

                    'due_type'           => (isset($dueData[0]['due_type']) ? $dueData[0]['due_type'] : 0),

                );



                $this->payment_model->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');

                // print_r($this->db->last_query());exit;

                $dueData[0]['id_scheme_account'] = $pay['id_scheme_account'];

                $dueData[0]['id_payment'] = $pay['id_payment'];

                $dueData[0]['date_payment'] = $pay['date_payment'];

                echo "</br>";

                echo "DAYS DURATION UPDATED FOR >>> " . json_encode($dueData[0]);
            }
        }
    }



    public function dayDurationSchemeService($id_scheme = "", $id_scheme_account = "")
    {



        //get active scheme account and its payment from this scheme id....



        $payments = $this->db->query("SELECT p.id_payment,p.installment,p.id_scheme_account,date(p.date_payment) as date_payment,sa.start_date,p.due_type,p.due_date,p.grace_date,p.is_limit_exceed ,IFNULL(s.max_chance,0) as max_chance,s.allow_advance_in,s.allow_unpaid_in,s.allow_advance,s.allow_unpaid,if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_months,if(s.allow_advance=1,s.advance_months,0) as advance_months,
		s.installment_cycle,s.pay_duration,s.payment_chances,s.scheme_type,s.total_installments
                                FROM payment p 

                                LEFT JOIN scheme_account sa on (sa.id_scheme_account = p.id_scheme_account) 
								LEFT JOIN scheme s on (s.id_scheme = p.id_scheme) 


                                WHERE   p.payment_status = 1 and p.installment is null and sa.total_paid_ins < s.total_installments
                                " . ($id_scheme != '' && $id_scheme > 0 ? "and sa.id_scheme = " . $id_scheme : '') . "

                                " . ($id_scheme_account != '' && $id_scheme_account > 0 ? "and sa.id_scheme_account = " . $id_scheme_account : '') . "

                                 order by p.id_scheme_account,p.date_payment asc limit 500

                               ")->result_array();

        // print_r($this->db->last_query());exit;

        //pass id scheme account and date payment to the query...

        //  echo '<pre>';print_r($payments);exit;

        foreach ($payments as $pay) {
			


			$allowed_due = 0;
                // $date = date('Y-m-d');
				$date = $pay['date_payment'];
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

				$id_scheme_account =  $pay['id_scheme_account'];
				$allow_advance_in =  $pay['allow_advance_in'];
				$allow_unpaid_in =  $pay['allow_unpaid_in'];

				if ($pay['installment_cycle'] == 0 && $pay['pay_duration'] == 0 && $pay['scheme_type'] == 3) {
					// Monthly scheme, daily payments (single or multiple)
					$daysInMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
					$baseChance  = ($pay['max_chance'] > 0 ? $pay['max_chance'] : 1);
					$max_chance  = $baseChance * $daysInMonth;
				} /* elseif ($pay['payment_chance'] == 1) {
					// Other cases where multiple chances are allowed
					$max_chance = ($pay['max_chance'] > 0 ? $pay['max_chance'] : 1);
				} */ else {
					// Single chance only
					$max_chance = ($pay['max_chance'] == 0 ? 1 : $pay['max_chance']);
					// $max_chance = 1;
				}

				// $max_chance = ($pay['max_chance'] == 0 ? 1 : $pay['max_chance']);
				$allow_advance =  $pay['allow_advance'];
				$allow_unpaid =  $pay['allow_unpaid'];
				$allow_unpaid_months =  $pay['allow_unpaid_months'];
				$advance_months =  $pay['advance_months'];
                               
                //get range for current date....
                $range = $this->payment_model->get_due_date('current_range', $date,  $id_scheme_account);
				/* if( $pay['id_payment'] == 71223){
					print_r($this->db->last_query());exit;
				} */
                if (!empty($range)) {
                    //take the no of paid dues with due_type customer paid already...
                    $paid_dueData = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count 
												FROM payment where payment_status = 1 and id_scheme_account = " . $id_scheme_account . " 
												and date(date_payment) BETWEEN '" . $range[0]['due_date_from'] . "' AND '" . $range[0]['due_date_to'] . "'
												group by due_type")->result_array();
                    // print_r($paid_dueData);exit;
					/* if( $pay['id_payment'] == 4904){
					print_r($paid_dueData);exit;
				} */
				
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
                //take the no of remaining dues with due_type customer want to pay..
                $remaining_dueData = $this->payment_model->get_due_date('allow_pay', $date, $id_scheme_account);
				/* if( $pay['id_payment'] == 71223){
					// print_r($remaining_dueData);exit;
					print_r($this->db->last_query());exit;
				} */
                // print_r($this->db->last_query());exit;
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
				
                //calculate can pay advance due and pending dues...
                $chances_allowed_due = $max_chance - ($paid_multiple_chance + $paid_normal_due);
                $allow_advance_in = explode(',', $allow_advance_in);
                $allow_unpaid_in = explode(',', $allow_unpaid_in);

				/* if( $pay['id_payment'] == 4904){
					print_r( sizeof($chances_allowed_due));exit;
				} */
				
            //   if ($chances_allowed_due <= 1 && (($allow_advance == 1 && $advance_months > 0 && (in_array('1', $allow_advance_in) || in_array('4', $allow_advance_in)) && $remaining_advance_due > 0) || ($allow_unpaid == 1 && $allow_unpaid_months > 0 && (in_array('1', $allow_unpaid_in) || in_array('4', $allow_unpaid_in)) && $remaining_pending_due > 0))) {
				
			/* if( $pay['id_payment'] == 4796){
					print_r( ($chances_allowed_due));exit;
				} */
				 $sch_advance = $advance_months;  //5 
                    $cur_advance = ($remaining_advance_due > 0 ? ($sch_advance < $remaining_advance_due && $remaining_advance_due > 0 && $paid_advance_due < $sch_advance ? $sch_advance : abs($sch_advance - $paid_advance_due)) : 0);   //5 - 0 = 5
                    // $canPay_advance = ($remaining_advance_due < $cur_advance ? $remaining_advance_due : $cur_advance); //14 < 0 ? 14 :
					$canPay_advance = $remaining_advance_due ; 
                    //pending
                    $sch_unpaid = $allow_unpaid_months;  //2
                    $cur_unpaid = ($remaining_pending_due > 0 ? ($sch_unpaid < $remaining_pending_due && $remaining_pending_due > 0 && $paid_pending_due < $sch_unpaid ? $sch_unpaid : abs($sch_unpaid - $paid_pending_due)) : 0);  //2 < 0 ? 2 : (2-0)
                    //$canPay_pending = ($remaining_pending_due  > $cur_unpaid ? $remaining_pending_due : $cur_unpaid );   //2 > 5 ? 5 : 2
                    // $canPay_pending = ($remaining_pending_due > $cur_unpaid ? $cur_unpaid : $remaining_pending_due);
					$canPay_pending = $remaining_pending_due;
					/* if( $pay['id_payment'] == 71223){
						print_r( ($cur_unpaid));exit;
					} */
              if ($chances_allowed_due <= 1 && ($remaining_advance_due > 0 || $remaining_pending_due > 0)) {
                    //advance..
                   
                    //print_r($remaining_normal_due);exit;
					/* if( $pay['id_payment'] == 71223){
					print_r( ($remaining_normal_due));exit;
				} */

					if($remaining_normal_due > 0){
						 $due_type = 'ND';
                        $allowed_due = $remaining_normal_due;
					}
					/* else if ($chances_allowed_due != 0 && $paid_normal_due != 0 && $max_chance > 1 && sizeof($range) > 0 && $max_chance > $chances_allowed_due) {
							$due_type = 'MND';
							$allowed_due = $chances_allowed_due;
						} */
                    else if ($remaining_normal_due == 0 && $canPay_pending > 0) {            //only pending
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
                    }/* else {
                        $due_type = 'ND';
                        $allowed_due = $remaining_normal_due;
                    } */
                }
				/* if( $pay['id_payment'] == 71223){
					print_r( ($remaining_pending_due));exit;
				} */
				
				else {

					if($remaining_normal_due > 0){
						 $due_type = 'ND';
                        $allowed_due = $remaining_normal_due;
					}
                    else if ($chances_allowed_due != 0 && $paid_normal_due != 0 && $max_chance > 1 && sizeof($range) > 0 && $max_chance > $chances_allowed_due) {
                        $due_type = 'MND';
                        $allowed_due = $chances_allowed_due;
                    } else if ($remaining_normal_due == 0 && $canPay_pending > 0) {            //only pending
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
                }
				/* if( $pay['id_payment'] == 71223){
					print_r( ($due_type));exit;
				} */
				/* if( $pay['id_payment'] == 71223){
					print_r( $due_type);exit;
				} */
                //allow pay , discuss for APN and multiple payment chances
                // print_r($allowed_due);exit;
               /*  if ($allowed_due > 0 && $paid_installments < $total_installments) {
                    $allow_pay = 'Y';
                } else {
                    $allow_pay = 'N';
                } */



				/* if( $pay['id_payment'] == 4904){
					print_r( $pay['id_scheme_account']);exit;
				} */

            $dueData = $this->payment_model->get_due_date($due_type, $pay['date_payment'], $pay['id_scheme_account']);
			/*  if( $pay['id_payment'] == 4904){
					print_r( $due_type);exit;
				} */

            // print_r($dueData);exit;

            /* if (sizeof($dueData[0]) == 0) {



                $dueData = $this->payment_model->get_due_date($due_type, $pay['date_payment'], $pay['id_scheme_account']);
            } */







            //update the respective from the response...

			/* if( $pay['id_payment'] == 71223){
					print_r( $dueData);exit;
				} */

			/* if( $pay['id_payment'] == 4904){
					print_r( ($dueData));exit;
				} */
            if (sizeof($dueData[0]) > 0) {

                $cycle_data = array(

                    'due_date'           => (isset($dueData[0]['due_date_from']) ? $dueData[0]['due_date_from'] : NULL),

                    'due_date_to'           => (isset($dueData[0]['due_date_to']) ? $dueData[0]['due_date_to'] : NULL),

                    'grace_date'           => (isset($dueData[0]['grace_date']) ? $dueData[0]['grace_date'] : NULL),

                    'installment'           => (isset($dueData[0]['installment']) ? $dueData[0]['installment'] : NULL),

                    'is_limit_exceed'           => (isset($dueData[0]['is_limit_exceed']) ? $dueData[0]['is_limit_exceed'] : 0),

                    // 'due_type'           => (isset($dueData[0]['due_type']) ? $dueData[0]['due_type'] : 0),
					 'due_type'           => (isset($due_type) ? $due_type : NULL),

                );



                $this->payment_model->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');

                // print_r($this->db->last_query());exit;

                $dueData[0]['id_scheme_account'] = $pay['id_scheme_account'];

                $dueData[0]['id_payment'] = $pay['id_payment'];

                $dueData[0]['date_payment'] = $pay['date_payment'];

                echo "</br>";

                // echo "DAYS DURATION UPDATED FOR >>> " . json_encode($dueData[0]);
				echo "DAYS DURATION UPDATED FOR >>> id_payment " . $pay['id_payment'] . " " . json_encode($cycle_data);
            }
        }
    }



    public function upd_total_paid_ins($id_scheme = "", $id_scheme_account = "")
    {

        $accounts =  $this->db->query("SELECT sa.id_scheme,sa.id_scheme_account,sa.total_paid_ins

                                FROM scheme_account sa 

                                WHERE  1 " . ($id_scheme > 0 ? " and sa.id_scheme = " . $id_scheme : '') . "

                                " . ($id_scheme_account > 0 ? " and sa.id_scheme_account = " . $id_scheme_account : '') . "

                                 

                               ")->result_array();



        foreach ($accounts as $acc) {

            $paid = $this->payment_model->getPaidInsData($acc['id_scheme_account']);

            $paid_ins = array('total_paid_ins'  =>  $paid['paid_installments']);

            $this->payment_model->updData($paid_ins, 'id_scheme_account', $acc['id_scheme_account'], 'scheme_account');
			 echo "</br>";

        echo "TOTAL PAID INS UPDATED FOR >>> " . json_encode($paid);
        }



        echo "</br>";

        echo "TOTAL PAID INS UPDATED FOR >>> " . json_encode($paid);
    }



    public function upd_firstPayAs_startDate($id_scheme = "", $id_scheme_account = "")
    {

        $accounts = $this->db->query("select p.id_scheme_account, MIN(date(p.date_payment)) as first_pay_date

                                                from payment p 

                                                where p.payment_status = 1 

                                                 " . ($id_scheme > 0 ? " and p.id_scheme = " . $id_scheme : '') . "

                                                " . ($id_scheme_account > 0 ? " and p.id_scheme_account = " . $id_scheme_account : '') . "
                                                and p.date_payment != '0000-00-00'

                                                group by p.id_scheme_account

                                                order by p.date_payment

                               ")->result_array();



        foreach ($accounts as $acc) {

            $start_date = array('start_date'  =>  $acc['first_pay_date']);

            $this->payment_model->updData($start_date, 'id_scheme_account', $acc['id_scheme_account'], 'scheme_account');



            echo "</br>";

            echo "FIRST PAYMENT DATE UPDATED FOR >>> " . json_encode($acc);
        }



        echo "</br>oveerrrr";
    }

	public function upd_firstPayAs_firstPayAmt()
    {

        $accounts = $this->db->query("select sa.id_scheme_account,sa.id_branch,sa.start_date,sa.firstPayment_amt,pay.payamt
										FROM scheme_account sa
										LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
										LEFT JOIN (WITH RankedPayments AS (
											SELECT 
												SUM(p.payment_amount) AS payamt,
												p.id_scheme_account,
												DATE_FORMAT(p.date_payment, '%Y%m') AS payment_month,
												ROW_NUMBER() OVER (PARTITION BY p.id_scheme_account ORDER BY p.date_payment ASC) AS rn
											FROM payment p
											WHERE p.payment_status = 1 
											AND p.due_type IN ('', 'ND') 
											GROUP BY p.id_scheme_account, DATE_FORMAT(p.date_payment, '%Y%m')
										)
										SELECT payamt, id_scheme_account, payment_month
										FROM RankedPayments
										WHERE rn = 1
										)  as pay on pay.id_scheme_account = sa.id_scheme_account
										where s.active = 1  and s.get_amt_in_schjoin = 1 and (s.firstPayamt_as_payamt = 1 or s.firstPayamt_maxpayable = 1 ) and sa.active = 1 and sa.is_closed = 0 and sa.firstPayment_amt != pay.payamt

                               ")->result_array();




        foreach ($accounts as $acc) {

            $firstPayment_amt = array('firstPayment_amt'  =>  $acc['payamt']);

            $this->payment_model->updData($firstPayment_amt, 'id_scheme_account', $acc['id_scheme_account'], 'scheme_account');



            echo "</br>";

            echo "FIRST PAYMENT AMT UPDATED FOR >>> " . json_encode($acc);
        }



        echo "</br>oveerrrr";
    }


    //AFTER IMPORT UPDATES ENDS<<<<
	//NEW FLOW STARTS/////
	function verify_payment()
	{
		$txns = $_POST;

		$pg_map = [
			1 => 'verify_PayUpayments',
			2 => 'verify_hdfcpayment',
			3 => 'verifyWithTechProcess',
			4 => 'payment_verify',
			7 => 'verifyRazorPayments',
			8 => 'verify_easebuzzpayment'
		];

		$pg_code = $txns['pg_code'] ?? null;

		if (isset($pg_map[$pg_code])) {
			$method = $pg_map[$pg_code];
			if ($pg_code == 4) {
				$this->$method(3, $txns); // special case for Cashfree manual verification
			} else {
				$this->$method($txns);
			}
		} else {
			log_message('error', "Invalid PG code: {$pg_code}");
		}
	}
	function payment_verify($type = '', $data = '')
	{
		switch ($type) {
			case 1: // Webhook
				$data = json_decode(file_get_contents('php://input'));
				$this->logWebhook($data);
				$this->new_webhook($data);
				break;

			case 2: // Auto verification
			case 3: // Manual verification
				$this->new_verification($type, $data);
				break;

			default:
				log_message('error', "Invalid payment verification type: {$type}");
		}
	}
	function oldcheckTransExist($transactionId, $transStatus, $id_payments = []) 
	{
		if (empty($id_payments)) {
			return ['status' => false, 'rows' => []];
		}

		$placeholders = implode(',', array_fill(0, count($id_payments), '?'));
		$sql = "SELECT id_payment, payu_id, ref_trans_id 
				FROM payment 
				WHERE ref_trans_id = ? 
				AND payment_status != 1 
				AND id_payment IN ($placeholders)";
		$bindings = array_merge([$transactionId], $id_payments);

		$query = $this->db->query($sql, $bindings);

		return [
			'status' => $query->num_rows() > 0,
			'rows'   => $query->result_array()
		];
	}	
	function checkTransExist($transactionId, $transStatus, $id_payments = []) 
	{
		// Base SQL
		$sql = "SELECT id_payment, payu_id, ref_trans_id 
				FROM payment 
				WHERE ref_trans_id = ? 
				AND payment_status != 1";

		$bindings = [$transactionId];

		// If id_payments is not empty, add IN condition
		if (!empty($id_payments)) {
			$placeholders = implode(',', array_fill(0, count($id_payments), '?'));
			$sql .= " AND id_payment IN ($placeholders)";
			$bindings = array_merge($bindings, $id_payments);
		}

		$query = $this->db->query($sql, $bindings);

		return [
			'status' => $query->num_rows() > 0,
			'rows'   => $query->result_array()
		];
	}
	
	function new_webhook($data){

		/* {"data":{"order":{"order_id":"176077114968f33c4d60933","order_amount":2500,"order_currency":"INR","order_tags":{"payment_1":"10057","payment_2":"10058"}},"payment":{"cf_payment_id":"5114921577349","payment_status":"USER_DROPPED","payment_amount":2500,"payment_currency":"INR","payment_message":"Simulated response message","payment_time":"2025-10-18T12:35:53+05:30","bank_reference":"5114921577349","auth_id":null,"payment_method":{"app":{"channel":null,"upi_id":null,"provider":null}},"payment_group":"wallet","international_payment":null,"payment_surcharge":{"payment_surcharge_service_charge":0,"payment_surcharge_service_tax":0}},"customer_details":{"customer_name":"RAHUL J","customer_id":"55821","customer_email":"08VOL9XT@gmail.com","customer_phone":"9789498476"},"payment_gateway_details":{"gateway_name":"CASHFREE","gateway_order_id":null,"gateway_payment_id":null,"gateway_status_code":null,"gateway_order_reference_id":null,"gateway_settlement":null,"gateway_reference_name":null},"payment_offers":null,"terminal_details":null},"event_time":"2025-10-18T12:36:06+05:30","type":"PAYMENT_USER_DROPPED_WEBHOOK"} */

		$this->load->model("chit_transaction_model");
		// $data = json_decode(file_get_contents('php://input'));

		// print_r($data);exit;
		$order_res = $data->data->order;
		$payment_res = $data->data->payment;
		$order_tags = (array)$order_res->order_tags;
		$id_payment = array_values($order_tags);
		$transactionId = $order_res->order_id;
		$transStatus = strtolower($payment_res->payment_status);
		$paymentMode   = strtolower($payment_res->payment_group);
		$cfPayId = $payment_res->cf_payment_id;
		$txTime        = $data->event_time;
		$orderId = $payment_res->cf_payment_id;

		$transactionDetail = $this->checkTransExist($transactionId,$transStatus, $id_payment);

		$transactionValid = $transactionDetail['status'];
        if(!empty($transactionId) && $transactionValid){
    		$gatewayResponse = $this->prepareGatewayResponse($paymentMode, $cfPayId, $transStatus, "WebHook update");
			$payment = $this->chit_transaction_model->updateGatewayResponse($gatewayResponse, $transactionDetail);
			$this->payment_update($payment);
        }
	}
	function new_verification($type,$data = ''){
		
		$model =	self::PAY_MODEL;		
		$transData = array();
		if($type == 2){
			$remark = "Auto Verification";
			$pg_code = 4;
			$id_branch = '';
			$previousDay = date('Y-m-d', strtotime("-3 days"));
			$currentDay = date('Y-m-d', strtotime(" 0 days"));
			$gateway_info = $this->chit_transaction_model->getBranchGatewayData($id_branch, $pg_code);
			$payData = $this->chit_transaction_model->getPendpayment_Data($previousDay, $currentDay, $id_branch, $gateway_info['id_pg']);
		}
		else if ($type == 3) {
        $gateway_info = $this->chit_transaction_model->getBranchGatewayData($data['id_branch'], $data['pg_code']);
		$remark = "Manual Verification";
    
            // Normalize data: wrap it in an array if not already a list
            if (isset($data['txn_ids']) && is_array($data['txn_ids'])) {
                $payData = [];
                foreach ($data['txn_ids'] as $txn) {
                    $payData[] = [
                        'txn_ids' => $txn,
                        'pg_code' => $data['pg_code'],
                        'id_branch' => $data['id_branch']
                    ];
                }
            } else {
                $payData = [$data]; // fallback if not array
            }
        }
		//print_r($currentDay);exit;
		$secretKey      = $gateway_info['param_1'];
		$appId          = $gateway_info['param_3'];
		$api_url  = $gateway_info['api_url'];	
		
		//echo $this->db->last_query();exit;
		$headers = [
			'x-client-id: ' . $gateway_info['param_3'],
			'x-client-secret: ' . $gateway_info['param_1'],
			'x-api-version: 2022-09-01'
		];

		if (empty($payData)) {
			echo "No payment records to verify.";
			return;
		}
		$vCount = 0;
		$result= [];
		foreach ($payData as $tran) {

			$txn_id = $tran['txn_ids'] ?? '';
        	if (empty($txn_id)) continue;

					//POSTDATA
					$postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&orderId=" . $tran['txn_ids'];
					//PAYMENT RESPONSE WITHOUT ORDER_TAGS
					$response = $this->curlRequest("$api_url/{$tran['txn_ids']}/payments", $headers);
					if($type ==2){
					echo "<pre> PAYMENT RESPONSE -";print_r($txn_id); echo  "<br>";print_r($response);echo "<pre>"; echo "<br>"; 
					}
					if (is_array($response) && isset($response['error'])) {
						echo "cURL Error: " . $response['error'];
						continue;
					}
					//ORDER RESPONSE WITH ORDER TAGS
					$ord_response = $this->curlRequest("$api_url/{$tran['txn_ids']}", $headers);
					if($type ==2){
					echo "<pre> ORDER RESPONSE -";print_r($txn_id); echo  "<br>";print_r($ord_response);echo "<pre>"; echo "<br>"; 
					}
					$order_tags = isset($ord_response->order_tags) ? (array)$ord_response->order_tags : [];
					$id_payment = array_values($order_tags);
					//LOG
					$this->logGatewayResponse($remark, $txn_id, $response, $ord_response, $postData);

					if ((!empty($response->code) && $response->code == 'order_not_found') || empty($response) ) {
						 $gwData = [
							"remark" => ($type == 2 ? "AUTO" : "MANUAL") . " VERIFIED - " . date('Y-m-d_H-i-s'),
							"is_gateway_verified" => 1,
							"payment_status"      => 4,
							"dev_remark"          => $response->code ?? "No response"
						];

						$transactionDetail = $this->checkTransExist($txn_id, 'cancelled', $id_payment);
						if (!empty($transactionDetail['status'])) {
							$this->chit_transaction_model->updateGatewayResponse($gwData, $transactionDetail);
							/* if($type ==3){
								echo "<pre> Order Not Found -";print_r($txn_id); echo  "(cancelled)<pre>"; echo "<br>"; 
							} */
							$result[] = $txn_id . " - cancelled";
							$vCount++;
						}
						continue;
					}

					if (empty($response) || !is_array($response)) {
						$this->$model->paymentDB("updatestatus", $txn_id, ['dev_remark' => "No response from gateway {$txn_id}"]);
						continue;
					}

						/* if (isset($response[0])) {
							$cf_response = $response[0];
						} else {
							$cf_response = null;
						} */
						$cf_response = $response[0] ?? null; 
						
						foreach ($response as $r) {
							if (($r->payment_status ?? '') === 'SUCCESS') {
								$cf_response = $r;
								break;
							}
						}

						if (!$cf_response) continue;

						$status_code = strtolower($cf_response->payment_status); // SUCCESS,
						$payment_mode = strtolower($cf_response->payment_group);
						$transactionDetail = $this->checkTransExist($txn_id,$status_code, $id_payment);
						$transactionValid = $transactionDetail['status'];

						if (!empty($txn_id)  && $transactionValid && $status_code != 'PENDING' && $status_code != 'FLAGGED' && $status_code != '') {
							$updateData = $this->prepareGatewayResponse($cf_response->payment_group, $cf_response->cf_payment_id, $status_code, $remark);
							$payment = $this->chit_transaction_model->updateGatewayResponse($updateData, $transactionDetail);
							/* if($type ==3){
								echo "<pre> GATEWAY RESPONSE -";print_r($txn_id); echo  "(";print_r($status_code);echo ")<pre>"; echo "<br>"; 
							} */
							$result[] = $txn_id . " - ". $status_code;
							$this->payment_update($payment);
						}
                        $vCount++;
			} //exit;
			echo implode("<br>", $result) . "<br><br>";
			echo "Total Records: " . count($payData) . "<br>";	
			echo "Verified: $vCount";
	}
	function payment_update($payment){
		if ($payment['status'] == true && sizeof($payment['payids']) > 0) 
		{
			foreach ($payment['payids'] as $payid) {
				//perform on pay success functionalities
				if ($payid['payment_status'] == 1) {
					$this->chit_transaction_model->onPayTranStream($payid['id_payment']);
					$serviceID = 3;
				} else {
					$serviceID = 7;
				}

				//send notifications
				$service = $this->services_model->checkService($serviceID);
				if ($service['sms'] == 1) {
					$id = $payid['id_payment'];
					$data = $this->services_model->get_SMS_data($serviceID, $id);
					$mobile = $data['mobile'];
					$message = $data['message'];
					//$this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
					if ($this->config->item('sms_gateway') == '1') {
						$this->sms_model->sendSMS_MSG91($mobile, $message, '', $service['dlt_te_id']);
					} elseif ($this->config->item('sms_gateway') == '2') {
						$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
					}
				}
			}
		}
	}
	/** ✅ Reusable Helpers **/
	private function getGatewayHeaders($gateway_info)
	{
		return [
			'x-client-id: ' . $gateway_info['param_3'],
			'x-client-secret: ' . $gateway_info['param_1'],
			'x-api-version: 2022-09-01'
		];
	}
	private function logWebhook($data)
	{
		$dir = $this->log_dir . '/cashfree';
		if (!is_dir($dir)) mkdir($dir, 0777, true);

		$log_path = "$dir/web_hook" . date("Y-m-d") . ".txt";
		$lg_data = "\n WebHook --" . date('Y-m-d H:i:s') . " -- : " . json_encode($data, true);
		file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);
	}
	private function ensureLogDirectory($folder)
	{
		$path = $this->log_dir . date("Y-m-d") . '/' . $folder;
		if (!is_dir($path)) mkdir($path, 0777, true);
	}
	// Helper function
	private function prepareGatewayResponse($paymentMode, $cfPayId, $transStatus, $remarkPrefix = "Update"){
		return [
			"payment_mode"       => $this->mapPaymentMode($paymentMode),
			"payu_id"            => $cfPayId,
			"remark"             => "$remarkPrefix on " . date('Y-m-d H:i:s'),
			"payment_ref_number" => $cfPayId,
			"payment_status"     => $this->mapPaymentStatus($transStatus)
		];
	}
	private function logGatewayResponse($remark, $txn_id, $response, $ord_response, $postData) {
		$log_path = $this->log_dir . '/cashfree/mob_response_' . date("Y-m-d") . '.txt';
		$data = "\n-----\n$remark CURL RESPONSE " . date("Y-m-d H:i:s") . ": " . json_encode($response) . json_encode($ord_response);
		$data .= "\nPOST DATA: " . json_encode($postData);
		file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
	}
	private function mapPaymentMode($mode){
		$mode = strtolower(trim($mode));
		switch($mode){
			case 'credit_card': return 'CC';
			case 'debit_card':  return 'DC';
			case 'net_banking': return 'NB';
			case 'na':          return 'NA';
			default:            return $this->update_paymentMode($mode);
		}
	}
	private function mapPaymentStatus($status){
		$status = strtolower($status);
		switch($status){
			case 'success':
			case 'captured':
				return 1;
			case 'usercancelled':
			case 'user_dropped':
			case 'dropped':
			case 'bounced':
				return 4;
			case 'failed':
				return 3;
			case 'refund':
				return 6;
			default:
				return 7;
		}
	}
	private function curlRequest($url, $headers = []) {
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 8,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => $headers,
		]);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) return ['error' => $err];
		return json_decode($response);
	}

}
?>