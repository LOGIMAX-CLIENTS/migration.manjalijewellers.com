<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*

| -------------------------------------------------------------------------

| URI ROUTING 

| -------------------------------------------------------------------------

| This file lets you re-map URI requests to specific controller functions.

|

a

| Typically there is a one-to-one relationship between a URL string

| and its corresponding controller class/method. The segments in a

| URL normally follow this pattern:

|

|	example.com/class/method/id/

|

| In some instances, however, you may want to remap this relationship

| so that a different class/function is called than the one

| corresponding to the URL.

|

| Please see the user guide for complete details:

|

|	http://codeigniter.com/user_guide/general/routing.html

|

| -------------------------------------------------------------------------

| RESERVED ROUTES

| -------------------------------------------------------------------------

|

| There area two reserved routes:

|

|	$route['default_controller'] = 'welcome';

|

| This route indicates which controller class should be loaded if the

| URI contains no data. In the above example, the "welcome" class

| would be loaded.

|

|	$route['404_override'] = 'errors/page_missing';

|

| This route will tell the Router what URI segments to use if those provided

| in the URL cannot be matched to a valid route.

|

*/

$route['default_controller'] = "chit_admin/index";

$route['404_override'] = '';

$route['admin']='chit_admin/index';

$route['admin/login'] = 'chit_admin/login';

$route['admin/dashboard/Upload_apk'] = 'admin_dashboard/Upload_apk';

$route['admin/login/authenticate'] = 'chit_admin/authenticate';

$route['admin/logout'] = 'chit_admin/logout';

$route['login/forget'] = 'chit_admin/forget_password';

$route['login/send_password'] = 'chit_admin/send_password';

/** Masters routing **/

//scheme

$route['scheme/ajax_scheme_list']= 'admin_scheme/ajax_get_schemes_list';

$route['scheme']= 'admin_scheme/index';

$route['scheme/add']= 'admin_scheme/sch_form/Add';

$route['scheme/save']= 'admin_scheme/sch_post/Add';

$route['scheme/edit/(:any)']= 'admin_scheme/sch_form/Edit/$1';

$route['scheme/update/(:any)']= 'admin_scheme/sch_post/Edit/$1';

$route['scheme/delete/(:any)']= 'admin_scheme/sch_post/Delete/$1';

$route['scheme/get_metals']='admin_scheme/get_metals';

$route['scheme/get_classifications']='admin_scheme/get_classifications';

$route['scheme/get_branches']='admin_scheme/get_branches';

$route['scheme/get_units']='admin_scheme/get_units';

$route['scheme/get_schemes']='admin_scheme/ajax_get_schemes';

$route['scheme/get_schemes/(:any)']='admin_scheme/ajax_get_schemes/$1';

$route['scheme/get_scheme/(:any)']='admin_scheme/ajax_get_scheme/$1';

$route['scheme/get/fix_schemes']='admin_scheme/ajax_fixweight_schemes';

//payment charges

$route['charges']= 'admin_settings/payment_charges';

//customer

$route['customer']								= 'admin_customer/index';

$route['customer/ajax_list']					= 'admin_customer/ajax_customers';

$route['customer/add']							= 'admin_customer/cus_form/Add';

$route['customer/save']							= 'admin_customer/cus_post/Add';

$route['customer/edit/(:any)']					= 'admin_customer/cus_form/Edit/$1';

$route['customer/customer_edit/(:any)']					= 'admin_dashboard/customer_edit/$1';

$route['customer/update/(:any)']				= 'admin_customer/cus_post/Edit/$1';

$route['customer/delete/(:any)']				= 'admin_customer/cus_post/Delete/$1';

$route['customer/login/(:any)']					='admin_customer/login/$1';

$route['customer/get_customers'] 				='admin_customer/ajax_get_customers';

$route['customer/get_customer/(:any)']			='admin_customer/ajax_get_customer/$1';

$route['customer/check_username/(:any)']		= 'admin_customer/check_username/$1';

$route['customer/check_mobile']					= 'admin_customer/check_mobile';

$route['customer/check_email']					= 'admin_customer/check_email';
$route['customer/ajax_check_delete/(:any)']		= 'admin_customer/ajax_check_delete/$1';

$route['customer/profile/status/(:any)/(:any)']	='admin_customer/profile_status/$1/$2';

$route['customer/status/(:any)/(:any)']			='admin_customer/customer_status/$1/$2';

$route['customer/dload/(:any)/(:any)']			='admin_customer/download/$1/$2';  

//Customer Profile 

$route['customer/cus_profile']					= 'admin_customer/cus_profile/list';

$route['customer/cus_profile/update/(:any)']	= 'admin_customer/cus_profile/update';

//Customer Profile 

// employee 

$route['employee/ajax_emp_list']				= 'admin_employee/ajax_get_emp_list';

$route['employee']								= 'admin_employee/emp_list';

$route['employee/add']							= 'admin_employee/emp_form/Add';

$route['employee/save']							= 'admin_employee/emp_post/Add';

$route['employee/update/(:any)']				= 'admin_employee/emp_post/Edit/$1';

$route['employee/delete/(:any)']				= 'admin_employee/emp_post/Delete/$1';

$route['employee/edit/(:any)']					= 'admin_employee/emp_form/Edit/$1';

$route['employee/getselected']					= 'admin_employee/get_emp_selected';

$route['employee/dept']							= 'admin_employee/get_dept';

$route['employee/designation']					= 'admin_employee/get_designation';

$route['employee/checkuser']					= 'admin_employee/isUserAvailable';

$route['employee/check_mobile']					= 'admin_employee/check_mobile';

$route['employee/status/(:any)/(:any)']			='admin_employee/employee_status/$1/$2'; //employee Active/Inactive Options HH//

//scheme account

$route['account/close/invoice_his_custom/(:any)']	= 'admin_manage/invoice_his_custom/$1';

$route['account/customer_enquiry/list']	='admin_manage/customer_enquiry/list';

$route['account/get/ajax_account_list']		= 'admin_manage/ajax_get_account_list';

$route['account/get/ajax_list']			= 'admin_manage/ajax_get_scheme_account';

$route['account/get/ajax_list/(:any)']	= 'admin_manage/ajax_get_scheme_account/$1';

$route['account/scheme_group/delete/(:any)']	='admin_manage/schemegroup_form/Delete/$1';

$route['account/new']='admin_manage/index';

$route['account/reg/(:any)/(:any)/(:any)']='admin_manage/account_registration/$1/$2/$3';

$route['account/registration/save/(:any)']='admin_manage/registration_form_post/$1';

$route['account/add']='admin_manage/account_form/Add';

$route['account/save']='admin_manage/account_post/Add';

$route['account/edit/(:any)']='admin_manage/account_form/Edit/$1';

$route['account/update/(:any)']='admin_manage/account_post/Edit/$1';

$route['account/delete/(:any)']='admin_manage/account_post/Delete/$1';

$route['account/payment_detail/(:any)']='admin_manage/ajax_get_pay_detail/$1';

$route['account/registration']='admin_manage/registration_list';

$route['account/update/client']='admin_manage/update_client';

$route['account/status/(:any)/(:any)']='admin_manage/account_status/$1/$2';

$route['account/customer/(:any)']='admin_manage/get_customer_accounts/$1';

// manual_schemeaccount number update

$route['schemeaccount/update'] = 'admin_manage/manual_schemeaccount';

//settlement

$route['settlement/weight/add'] 				= 'admin_payment/weight_settlement/View';

$route['settlement/weight/list'] 				= 'admin_payment/weight_settlement/List';

$route['settlement/weight/ajax_list'] 			= 'admin_payment/weight_settlement/';

$route['ajax/monthly_rate/(:any)']   			= 'admin_payment/monthly_rate/$1';

$route['settlement/update/account']   			= 'admin_payment/update_settlement';

//settlement detail

$route['settlement/weight/detail/list/(:any)']		= 'admin_payment/weight_settlement_detail/List/$1';

$route['settlement/weight/detail/ajax_list/(:any)'] 	= 'admin_payment/weight_settlement_detail/ajax/$1';

$route['settlement/get_scheme']					= 'admin_payment/ajax_get_scheme';

//Customer Account Details

$route['reports/customer_account_details/(:any)'] 	   = 'admin_reports/customer_account_details/$1';

//scheme account closing

$route['account/get/ajax_closed_acc_list']	= 'admin_manage/ajax_get_closed_account_list';

$route['account/close']						= 'admin_manage/close_account_list';

$route['account/closed/view/(:any)']		= 'admin_manage/closed_acc_detail/$1';

$route['account/close/scheme/(:any)']		= 'admin_manage/close_account_form/Close/$1';

$route['account/close/update/(:any)']		= 'admin_manage/close_account_form/Save/$1';

$route['account/revert/(:any)']				= 'admin_manage/close_account_form/Revert/$1';

$route['account/close/reject/(:any)']		= 'admin_manage/close_account_form/Reject/$1';

$route['account/close/otp/(:any)/(:any)/(:any)']	= 'admin_manage/acc_close_otp/$1/$2/$3';

$route['account/fetch/otp/(:any)/(:any)']			= 'admin_manage/acc_fetch_otp/$1/$2';

$route['account/scheme_reg/list']				    ='admin_manage/schemereg_list'; 

$route['account/scheme_reg/list/(:any)']		    ='admin_manage/schemereg_list/$1';  // scheme reg

$route['account/get/ajax_account_list']		= 'admin_manage/ajax_get_account_list';

$route['account/list']='admin_manage/open_account_list';

$route['account/get/ajax_account']		= 'admin_manage/get_all_scheme_account';

//Branch wise emp name in Scheme Join Page admin //HH

$route['reports/employee_list_brancwise']  = 'admin_reports/branchwise_employee';

//Kyc Approval Data//HH

$route['reports/kyc_data/list/(:any)']				    ='admin_reports/kycdata_list/$1';

$route['reports/kycapproval_data']              = 'admin_reports/kycapproval_data';

//Kyc Approval Data//

$route['account/get_dynamic_kyc/(:any)/(:any)/(:any)/(:any)'] = 'admin_manage/ajax_get_dynamic_kyc/$1/$2/$3/$4';
$route['account/get_dynamic_kyc/(:any)/(:any)/(:any)'] = 'admin_manage/ajax_get_dynamic_kyc/$1/$2/$3';
$route['account/get_dynamic_kyc_for_customer/(:any)'] = 'admin_manage/ajax_get_dynamic_kyc_for_customer/$1';
$route['account/save_kyc_dynamic/(:any)/(:any)/(:any)/(:any)'] = 'admin_manage/ajax_save_kyc_dynamic/$1/$2/$3/$4';

//Cus Scheme Enquiry Data//HH

$route['reports/sch_enquiry/list']				    ='admin_reports/sch_enquirt_list'; 

$route['reports/schenquiry_list']              = 'admin_reports/schenquiry_list';

//Cus Scheme Enquiry Data//

//Online Payment Report

$route['reports/online_payment_report']  = 'admin_reports/online_payment_report';

$route['reports/old_metal_report']           = 'admin_reports/old_metal_report/list';

$route['reports/accountRemarks']           = 'admin_manage/accountRemarks';

//scheme_group//

$route['account/scheme_group/list']				='admin_manage/scheme_group/list'; 

$route['account/ajaxscheme_group/list']		    ='admin_manage/ajax_scheme_group_list'; 

$route['account/scheme_group/add'] 		          = 'admin_manage/schemegroup_form/View';

$route['account/scheme_group/edit/(:any)']      ='admin_manage/schemegroup_form/Edit/$1';

$route['account/scheme_group/update/(:any)']      ='admin_manage/schemegroup_form/Update/$1';

$route['account/scheme_group/save'] 			= 'admin_manage/schemegroup_form/Save';

//scheme_group

//mails

$route['mail/closing']    					='admin_manage/closing_request/';

$route['mail/joining']    					='admin_mails/joining_request/';

$route['mail/joining/update/(:any)/(:any)'] ='admin_mails/update_joining_status/$1/$2';

//payment

$route['payment/add']    				  = 'admin_payment/payment/View/';

$route['payment/edit/(:any)']    		  = 'admin_payment/payment/View/$1';

$route['payment/save']    				  = 'admin_payment/payment/Save/';

$route['payment/save_all']    			  = 'admin_payment/payment/SaveAll/';

$route['payment/update/(:any)'] 		  = 'admin_payment/payment/Update/$1';

$route['payment/delete/(:any)'] 		  = 'admin_payment/payment/Delete/$1';

$route['payment/get/ajax_data']    		  = 'admin_payment/ajax_form_data';

$route['payment/get/ajax_data/(:any)']    = 'admin_payment/ajax_form_data/$1';

$route['payment/get/ajax/account/(:any)'] = 'admin_payment/ajax_account_detail/$1';

$route['payment/get/ajax/customer/account/(:any)'] = 'admin_payment/ajax_customer_schemes/$1';

$route['payment/get/ajax/customer/account_amount/(:any)'] = 'admin_payment/ajax_customer_schemes_amount/$1';

$route['payment/list']                    = 'admin_payment/payment/List';

$route['payment/ajax_list/range'] 		  = 'admin_payment/ajax_payment_range';

$route['payment/ajax_list'] 			  = 'admin_payment/payment/Ajax';

$route['payment/ajax_list/(:any)']  = 'admin_payment/payment/Ajax/$1';

$route['payment/invoice/(:any)'] 	      = 'admin_payment/generateInvoice/$1';

$route['payment/get/total'] 	          = 'admin_payment/ajax_payment_stat';

$route['payment/status/(:any)']    		  = 'admin_payment/payment/Status/$1';

// $route['payment/verify/transaction']      = 'admin_payment/verify_payment';
$route['payment/verify/transaction']      = 'chit_transaction/manualVerify';

$route['verify/online/payment']           = 'admin_payment/verify_payment_view';

$route['get/online/payment']              = 'admin_payment/ajax_online_payment';

$route['ajax/online/payment']             = 'admin_payment/ajax_onlinePayments';

$route['online/payment'] 		          = 'admin_payment/online_payment_list';

$route['online/payment/update_status']    = 'admin_payment/update_pay_status';

$route['online/get/ajax_payment/(:any)']  ='admin_payment/ajax_get_payment/$1';

$route['payment/edit_payment/(:any)']                    = 'admin_payment/payment/Edit_payment/$1';

$route['payment/update_payment/(:any)']                    = 'admin_payment/payment/Update_payment/$1';

//post dated Payment

$route['postdated/payment/add']    			    	= 'admin_payment/postdate_payment/View/';

$route['postdated/payment/edit/(:any)']    		    = 'admin_payment/postdate_payment/View/$1';

$route['postdated/payment/save']    			    = 'admin_payment/postdate_payment/Save/';

$route['postdated/payment/update'] 		            = 'admin_payment/postdate_payment/Update';

$route['postdated/payment/delete/(:any)'] 			= 'admin_payment/postdate_payment/Delete/$1';

$route['postdated/payment/get/ajax_data']    		= 'admin_payment/ajax_form_data';

$route['postdated/payment/get/ajax_data/(:any)']	= 'admin_payment/ajax_form_data/$1';

$route['postdated/payment/get/ajax/account/(:any)'] = 'admin_payment/ajax_account_detail/$1';

$route['postdated/payment/list']                    = 'admin_payment/postdate_payment/List';

$route['postdated/payment/ajax_list'] 			  	= 'admin_payment/postdate_payment/Ajax';

$route['postdated/payment/ajax_list/(:any)'] 	    = 'admin_payment/postdate_payment/Ajax/$1';

$route['postdated/payment/ajax_payment_status'] 	= 'admin_payment/ajax_payment_status';

$route['postdated/status/list']                     = 'admin_payment/ajax_postpayment_data';

$route['postdated/payment_entry/edit/(:any)']    	= 'admin_payment/postdate_payment_form/Edit/$1';

$route['postdated/payment_entry/save/(:any)']    	= 'admin_payment/postdate_payment_form/Update/$1';

$route['postdated/payment_entry/status/(:any)']    	= 'admin_payment/postdate_payment_form/Status/$1';

//Payment Data //HH

$route['payment/pay_list']                    = 'admin_payment/payments_data';

$route['payment/payments_data_list']             = 'admin_payment/payments_data_list';

/*

$route['payment']    ='admin_payment/payment_list/';

$route['payment/new']='admin_payment/payment_form/';

$route['payment/scheme_account']='admin_payment/pay_scheme_account/';

$route['payment/update_status']='admin_payment/update_pay_status';

$route['payment/save']='admin_payment/insert_payment';

$route['payment/verify']='admin_payment/verify_payment';

$route['payment/approve/(:any)']='admin_payment/payment_approve/$1'; */

//otp_settings

$route['otp_settings/settings/edit/(:any)']	= 'admin_settings/otp_settings/$1';

//$route['gst_setting/settings/edit/(:any)']	= 'admin_settings/gst_settings/$1';

$route['otp_settings/settings']	           = 'admin_settings/get_otpsettings';

//free_paid installments 

$route['scheme/freepay/installments'] = 'admin_services/scheme_freepayment';

//scheme payment history

$route['account/close/scheme_history/(:any)']	= 'admin_manage/close_account_history_form/$1';

$route['account/close/invoice_history/(:any)']	= 'admin_manage/invoice_history_form/$1';

$route['account/check_group']	= 'admin_manage/check_group';

$route['reports/msg91_translog']     = "admin_reports/msg91_log";

$route['reports/msg91_delivery/(:any)']     = "admin_reports/msg91_delivReport/$1";

//new reports

$route['reports/employee_list']  = 'admin_reports/payment_employee';

$route['reports/interwalTrans_list'] 		 = 'admin_reports/interWalletTrans_list';

$route['reports/payment_employee_collection']  = 'admin_reports/ajax_payment_list';

$route['reports/payment_employee_wise']  = 'admin_reports/payment_employee_wise';

//payment_by_daterange

$route['reports/payment_daterange']           = 'admin_reports/payment_by_daterange';

$route['payment/ajax_list/range_list'] 		  = 'admin_reports/payment_list_daterange';

$route['get/schemename_list'] 		          = 'admin_reports/getscheme_name';

$route['reports/collection_report']           = 'admin_reports/collection_report';

// paymode_wise_list

$route['reports/payment_modewise_data'] 	  = 'admin_reports/payment_modewise_data';

$route['reports/paymentmodewise_datalist'] 	   = 'admin_reports/payment_modewise_list';

// payment_datewise_schemedata

$route['reports/payment_datewise_schemedata'] = 'admin_reports/payment_datewise_data';

$route['reports/payment_datewise_schemelist'] = 'admin_reports/payment_datewise_list';

// online / offline payment collection //

$route['reports/payment_online_offline_collec_data'] = 'admin_reports/payments_on_off_collection_data';

$route['reports/payment_online_offline_collec_list'] = 'admin_reports/payments_on_off_collection_list';

//payment cancel report //HH

$route['reports/payment_cancel_report']	            ='admin_reports/payment_cancel_list'; 

// payment_datewise_schemecollection

 $route['reports/paydatewise_schcoll_data']		 = 'admin_reports/paydatewise_schemecoll_data';

$route['reports/paydatewise_schcoll_list']	 = 'admin_reports/paydatewise_schemecoll_list';

// payment_outstanding

$route['reports/payment_outstanding']		 = 'admin_reports/payment_outstanding';

$route['reports/payment_outstanding_list']	 = 'admin_reports/payment_outstanding_list';

//end of new reports	

//reports

$route['reports/edit_acc_pay'] = 'admin_reports/edit_acc_pay';

$route['reports/customer_enquiry']	 ='admin_reports/customer_enquiry';

$route['reports/payment_pending']        = 'admin_reports/payment_due_list/';

$route['reports/payment_list/(:any)']    = 'admin_reports/payment_list/$1';

$route['reports/payment_details']        = 'admin_reports/payment_details';

$route['reports/payment_schemewise']     = 'admin_reports/payment_schemewise';

$route['reports/payment_datewise']		 = 'admin_reports/payment_datewise';

$route['reports/payment_datewise_ajax']	 = 'admin_reports/payment_datewise_ajax';

$route['reports/payment_modewise'] 		 = 'admin_reports/payment_modewise';

$route['reports/accounts_schemewise']    = 'admin_reports/accounts_schemewise';

$route['reports/payment/account/(:any)'] = 'admin_reports/scheme_account_report/$1';

$route['reports/payment/range']          = 'admin_reports/payment_by_range';

$route['reports/payment/range/date']     = 'admin_reports/payment_date_range';

$route['reports/payment/failed']         = 'admin_reports/failed_payments';

$route['reports/get/payment/failed']     = 'admin_reports/failed_data';

$route['reports/update/daily_collection']= 'admin_rateapi/update_daily_collection';

$route['reports/employee_ref_success_list']  = 'admin_reports/employee_ref_success_list';

$route['reports/payment_cus_ref_success']  = 'admin_reports/cus_ref_success_list';

$route['reports/payment/refferl_account/(:any)'] = 'admin_reports/emp_referral_account/$1';

$route['reports/payment/cus_refferl_account/(:any)'] = 'admin_reports/cus_refferl_account/$1';

$route['reports/employee_ref_success']        = 'admin_reports/employee_ref_success';

$route['reports/cus_ref_success']  = 'admin_reports/cus_ref_success';

//member report

$route['reports/member_report']  = 'admin_reports/member_report';

//Account employee wise reports

$route['reports/Employee_account'] = 'admin_reports/employee_account';

$route['reports/ajax_emp_account_list']	=	'admin_reports/ajax_get_emp_account_list';

$route['log/index']                     = 'Log/index';
$route['log/ajax_get_logs_by_date']    = 'Log/ajax_get_logs_by_date';
$route['log/ajax_get_file_content']    = 'Log/ajax_get_file_content';
$route['log/download_file/(:any)']     = 'Log/download_file/$1';
$route['Log/index']                     = 'Log/index';
$route['Log/ajax_get_logs_by_date']    = 'Log/ajax_get_logs_by_date';
$route['Log/ajax_get_file_content']    = 'Log/ajax_get_file_content';
$route['Log/download_file/(:any)']     = 'Log/download_file/$1';

$route['log/ajax_list']        			 = 'admin_reports/log/Ajax';

$route['log/ajax_list_detail']       	 = 'admin_reports/log/Detail';

$route['log/list']          			 = 'admin_reports/log/List';

$route['log/detail/(:any)']    			 = 'admin_reports/log/View/$1';

$route['form_logger/(:any)']               = 'admin_reports/form_logger/$1';

$route['form_logger/(:any)/(:any)']        = 'admin_reports/form_logger/$1/$2';

$route['form_logger/(:any)/(:any)/(:any)'] = 'admin_reports/form_logger/$1/$2/$3';

$route['form_logger/(:any)/(:any)/(:any)/(:any)'] = 'admin_reports/form_logger/$1/$2/$3/$4';

$route['customer/withoutAccount'] 				= 'admin_dashboard/cust_wo_acc_details';

$route['customer/without_acc_details'] 	      = 'admin_dashboard/cust_wo_accounts_details';

$route['account/withoutPayment'] 				= 'admin_dashboard/acc_wo_pay_details';

$route['reports/detail/registration/(:any)']     = 'admin_dashboard/reg_detail/$1';

$route['reports/detail/account/(:any)']          = 'admin_dashboard/acc_detail/$1';

$route['reports/detail/renewals/(:any)']          = 'admin_dashboard/get_renewals_list/$1';

$route['reports/detail/closed_acc/(:any)']       = 'admin_dashboard/closed_acc_detail/$1';

$route['reports/detail/close_due/(:any)']       = 'admin_dashboard/about_to_close/$1';

$route['reports/detail/payment/(:any)']          = 'admin_dashboard/pay_detail/$1';

$route['reports/detail/awaiting']          		= 'admin_dashboard/awaiting_detail';

$route['reports/detail/enquiry/(:any)']          = 'admin_dashboard/enquiry_detail/$1';

$route['reports/detail/pay_status/(:any)/(:any)']= 'admin_dashboard/paid_unpaid_status/$1/$2';

$route['reports/detail/all_pay_status']			 = 'admin_dashboard/total_payment_details';

$route['reports/detail/due/(:any)']				= 'admin_dashboard/due_list/$1';

$route['reports/detail/postdated/pay_status/(:any)/(:any)/(:any)']= 'admin_dashboard/postdated_pay_detail/$1/$2/$3';

$route['admin/dashboard'] = 'admin_dashboard/index';

$route['rate/ajax/weekstat'] = 'admin_dashboard/ajax_get_ratestat';

//BRANCH

$route['settings/branch']      					= 'admin_settings/branch_form';

$route['settings/branches']      				= 'admin_settings/branch_form/get_branches';

$route['settings/branch_list'] 					= 'admin_settings/ajax_get_branches';

$route['settings/branch/add']  					= 'admin_settings/branch_form/Add';

$route['settings/branch/edit/(:any)']			='admin_settings/branch_form/Edit/$1';

$route['settings/branch/update/(:any)']			='admin_settings/branch_form/Update/$1';

$route['settings/branch_stat/(:any)/(:any)']	='admin_settings/branch_form/Update_status/$1/$2';

//SETTINGS	

$route['settings/general/list']				    = 'admin_settings/general_settings/List';

$route['settings/general/add']				    = 'admin_settings/general_settings/View';

$route['settings/general/save']				    = 'admin_settings/general_settings/Save';

$route['settings/general/edit/(:any)']			= 'admin_settings/general_settings/View/$1';

$route['settings/general/update/(:any)']		= 'admin_settings/general_settings/Update/$1';

// Feature Flags AJAX
$route['settings/get_feature_flags']			= 'admin_settings/get_feature_flags';
$route['settings/save_feature_flags']			= 'admin_settings/save_feature_flags';

$route['settings/country/getcurrency/(:any)']	= 'admin_settings/get_countryCurrency/$1';

$route['settings/country/update']		= 'admin_settings/update_country';

//payment mode master

$route['settings/paymode/ajax'] 				= 'admin_settings/ajax_get_paymentMode';

$route['settings/paymode/list'] 				= 'admin_settings/payment_mode/List';

$route['settings/paymode/ajax_list'] 			= 'admin_settings/payment_mode/Ajax';

$route['settings/paymode/ajax_list/(:any)'] 	= 'admin_settings/payment_mode/Ajax/$1';

$route['settings/paymode/add']					= 'admin_settings/payment_mode/View';

$route['settings/paymode/edit/(:any)'] 			= 'admin_settings/payment_mode/View/$1';

$route['settings/paymode/save'] 				= 'admin_settings/payment_mode/Save';

$route['settings/paymode/update/(:any)'] 		= 'admin_settings/payment_mode/Update/$1';

$route['settings/paymode/delete/(:any)'] 		= 'admin_settings/payment_mode/Delete/$1';

//bank master

$route['settings/bank/ajax'] 				= 'admin_settings/ajax_get_bank';

$route['settings/bank/list'] 				= 'admin_settings/bank/List';

$route['settings/bank/ajax_list'] 			= 'admin_settings/bank/Ajax';

$route['settings/bank/ajax_list/(:any)'] 	= 'admin_settings/bank/Ajax/$1';

$route['settings/bank/add']					= 'admin_settings/bank/View';

$route['settings/bank/edit/(:any)'] 		= 'admin_settings/bank/View/$1';

$route['settings/bank/save'] 				= 'admin_settings/bank/Save';

$route['settings/bank/update/(:any)'] 		= 'admin_settings/bank/Update/$1';

$route['settings/bank/delete/(:any)'] 		= 'admin_settings/bank/Delete/$1';

//gift master

$route['settings/gift/ajax'] 				= 'admin_settings/ajax_get_gift';

$route['settings/gift/list'] 				= 'admin_settings/gift/List';

$route['settings/gift/ajax_list'] 			= 'admin_settings/gift/Ajax';

$route['settings/gift/ajax_list/(:any)'] 	= 'admin_settings/gift/Ajax/$1';

$route['settings/gift/add']					= 'admin_settings/gift/View';

$route['settings/gift/edit/(:any)'] 		= 'admin_settings/gift/View/$1';

$route['settings/gift/save'] 				= 'admin_settings/gift/Save';

$route['settings/gift/update/(:any)'] 		= 'admin_settings/gift/Update/$1';

$route['settings/gift/delete/(:any)'] 		= 'admin_settings/gift/Delete/$1';

//drawee master

$route['settings/drawee/ajax']              = 'admin_settings/ajax_get_drawee';

$route['settings/drawee/list'] 				= 'admin_settings/drawee/List';

$route['settings/drawee/ajax_list'] 		= 'admin_settings/drawee/Ajax';

$route['settings/drawee/ajax_list/(:any)'] 	= 'admin_settings/drawee/Ajax/$1';

$route['settings/drawee/add']				= 'admin_settings/drawee/View';

$route['settings/drawee/edit/(:any)'] 		= 'admin_settings/drawee/View/$1';

$route['settings/drawee/save'] 				= 'admin_settings/drawee/Save';

$route['settings/drawee/update/(:any)'] 	= 'admin_settings/drawee/Update/$1';

$route['settings/drawee/delete/(:any)'] 	= 'admin_settings/drawee/Delete/$1';



// notification master 

$route['settings/notification/ajaxlist'] 		= 'admin_settings/notification/ajaxList';

// $route['settings/notification/list'] 		= 'admin_settings/notification/List';



$route['settings/notification/list'] 		= 'admin_settings/notification/List';



//Notificstion master 

//rate master

$route['settings/rate/list'] 				= 'admin_settings/metal_rates/List';

$route['settings/rate/discount'] 		= 'admin_settings/metal_rates_discount';

$route['settings/rate/ajax_list'] 		    = 'admin_settings/metal_rates/Ajax';

$route['settings/rate/ajax_list/(:any)'] 	= 'admin_settings/metal_rates/Ajax/$1';

$route['settings/rate/add']				    = 'admin_settings/metal_rates/View';

$route['settings/rate/edit/(:any)'] 		= 'admin_settings/metal_rates/View/$1';

$route['settings/rate/save'] 				= 'admin_settings/metal_rates/Save';

//$route['settings/rate/mjdma_update'] 		= 'admin_settings/metal_rates/mjdma_update';

$route['settings/rate/update/(:any)'] 	    = 'admin_settings/metal_rates/Update/$1';

$route['settings/rate/delete/(:any)'] 	    = 'admin_settings/metal_rates/Delete/$1';

$route['settings/mjdma_update'] 	    	= 'admin_rateapi/update_rateapi';

//Offline Rate list//HH

$route['settings/offline_rate_history/list']	='admin_settings/offrate_list'; 

$route['settings/offratelist_data']              = 'admin_settings/offratelist_data';

//Offline Rate list//

//menu

$route['settings/menu/list'] 				= 'admin_settings/menu/List';

$route['settings/menu/list/(:any)']			= 'admin_settings/menu/List/$1';

$route['settings/menu/ajax_list'] 			= 'admin_settings/menu/Ajax';

$route['settings/menu/ajax_list/(:any)']	= 'admin_settings/menu/Ajax/$1';

$route['settings/menu/add']					= 'admin_settings/menu/View';

$route['settings/menu/edit/(:any)'] 		= 'admin_settings/menu/View/$1';

$route['settings/menu/save'] 				= 'admin_settings/menu/Save';

$route['settings/menu/update/(:any)'] 		= 'admin_settings/menu/Update/$1';

$route['settings/menu/delete/(:any)'] 		= 'admin_settings/menu/Delete/$1';

//permission

$route['settings/access/view'] 				= 'admin_settings/permission/View';

$route['settings/access/get'] 				= 'admin_settings/permission/Get';

$route['settings/access/add'] 				= 'admin_settings/permission/Save';

$route['settings/access/list'] 				= 'admin_settings/permission/List';

$route['settings/access/ajax_list'] 		= 'admin_settings/permission/List';

$route['settings/access/ajax_list/(:any)'] 	= 'admin_settings/permission/List/$1';

$route['settings/get/user_rights'] 	       	= 'admin_settings/get_access_rights';

$route['settings/access/dashboard_add'] 	= 'admin_settings/permission/Dashboardsave';




//profile

$route['settings/profile/list'] 				= 'admin_settings/profile/List';

$route['settings/profile/ajax_list'] 			= 'admin_settings/profile/Ajax';

$route['settings/profile/ajax_list/(:any)'] 	= 'admin_settings/profile/Ajax/$1';

$route['settings/profile/add']					= 'admin_settings/profile/View';

$route['settings/profile/edit/(:any)'] 			= 'admin_settings/profile/View/$1';

$route['settings/profile/save'] 				= 'admin_settings/profile/Save';

$route['settings/profile/update/(:any)'] 		= 'admin_settings/profile/Update/$1';

$route['settings/profile/delete/(:any)'] 		= 'admin_settings/profile/Delete/$1';

$route['settings/payment_charges']				= 'admin_settings/payment_charges';

$route['settings/payment_charges/add']			= 'admin_settings/payment_charges/Add';

$route['settings/payment_charges/save']			= 'admin_settings/payment_charges/Save';

$route['settings/payment_charges/edit/(:any)']	= 'admin_settings/payment_charges/Edit/$1';

$route['settings/payment_charges/update/(:any)']= 'admin_settings/payment_charges/Update/$1';

$route['settings/payment_charges/delete/(:any)']= 'admin_settings/payment_charges/Delete/$1';

$route['settings/payment_charges/ajax_list']	= 'admin_settings/payment_charges/Ajax_charges';

$route['settings/company']						= 'admin_settings/index';

$route['settings/company/list']					= 'admin_settings/comp_list';

$route['settings/company/create']				= 'admin_settings/company_form/Add';

$route['settings/company/save']					= 'admin_settings/company_post/Save';

$route['settings/company/update/(:any)']		= 'admin_settings/company_post/Update/$1';

$route['settings/company/edit/(:any)']			= 'admin_settings/company_form/Edit/$1';

$route['settings/company/getcountry']			= 'admin_settings/get_country';

$route['settings/company/getstate']				= 'admin_settings/get_state';

$route['settings/company/getcity']				= 'admin_settings/get_city';

//import customer

$route['settings/import/customer']				= 'admin_import_export/import_customer_form';

$route['settings/import/customer_data']			= 'admin_import_export/import_customer';

$route['settings/import/customer_list/(:any)/(:any)']	= 'admin_import_export/import_customer_list/$1/$2';

$route['settings/import/ajax_customer_list/(:any)/(:any)']	= 'admin_import_export/ajax_import_customer_list/$1/$2';

$route['settings/import/account']				= 'admin_import_export/import_acc_form';

$route['settings/import/account_data']			= 'admin_import_export/import_account';

$route['settings/import/list/(:any)/(:any)']	= 'admin_import_export/import_list/$1/$2';

$route['settings/import/send_login']			= 'admin_import_export/send_login';

$route['settings/import/send_login_email']		= 'admin_import_export/send_login_email';

$route['settings/import/download']				= 'admin_import_export/upload';

$route['settings/import/ajax_list/(:any)/(:any)']	= 'admin_import_export/ajax_import_list/$1/$2';

$route['settings/import/payment']='admin_settings/import_payment_form';

$route['settings/import/payment_data']='admin_settings/import_payment';

$route['settings/import/unique_code']='admin_settings/import_unique_code';

$route['settings/import/unique_data']='admin_settings/import_unique_data';

$route['settings/export']				= 'admin_settings/export_form';

$route['settings/export_list']			= 'admin_settings/ajax_get_exportlist';

$route['settings/export/get_account']	= 'admin_settings/ajax_get_account';

$route['settings/export_to_excel']		= 'admin_settings/export_to_excel';

$route['settings/export/account/list']	= 'admin_settings/account_list';

$route['settings/export/account']		= 'admin_settings/export_account';

$route['settings/weight']				= 'admin_settings/weight_form';

$route['settings/weight_list']			= 'admin_settings/ajax_get_weights';

$route['settings/weight/add']			= 'admin_settings/weight_form/Add';

$route['settings/weight/edit/(:any)']	= 'admin_settings/weight_form/Edit/$1';

$route['settings/weight/delete/(:any)']	= 'admin_settings/weight_form/Delete/$1';

$route['settings/weight/update/(:any)']	= 'admin_settings/weight_form/Update/$1';

$route['settings/classification']				= 'admin_settings/classification_form';

$route['settings/classification_list']			= 'admin_settings/ajax_get_classifications';

$route['settings/classification/add']			= 'admin_settings/classification_form/Add';

$route['settings/classification/edit/(:any)']	= 'admin_settings/classification_form/Edit/$1';

$route['settings/classification/delete/(:any)']	= 'admin_settings/classification_form/Delete/$1';

$route['settings/classification/update/(:any)']	= 'admin_settings/classification_form/Update/$1';

$route['settings/dept']					= 'admin_settings/dept_form';

$route['settings/dept_list']			= 'admin_settings/ajax_get_depts';

$route['settings/dept/add']             = 'admin_settings/dept_form/Add';

$route['settings/dept/edit/(:any)']     = 'admin_settings/dept_form/Edit/$1';

$route['settings/dept/delete/(:any)']	= 'admin_settings/dept_form/Delete/$1';

$route['settings/dept/update/(:any)']	= 'admin_settings/dept_form/Update/$1';

$route['settings/design']				= 'admin_settings/design_form';

$route['settings/design_list']			= 'admin_settings/ajax_get_designs';

$route['settings/design/add']			= 'admin_settings/design_form/Add';

$route['settings/design/edit/(:any)']   = 'admin_settings/design_form/Edit/$1';

$route['settings/design/delete/(:any)'] = 'admin_settings/design_form/Delete/$1';

$route['settings/design/update/(:any)'] = 'admin_settings/design_form/Update/$1';

//database

$route['settings/clear/database']       = 'admin_settings/clear_database';

$route['settings/backup/database']      = 'admin_settings/db_backup';

$route['settings/backup/database/list'] = 'admin_settings/ajax_backup_list';

//gateway

$route['gateway/settings/edit_demo/(:any)']  = 'admin_settings/gateway_settings/Update_demo/$1';

$route['gateway/settings/edit_pro/(:any)']  = 'admin_settings/gateway_settings/Update_pro/$1';

//sms gateway

$route['sms/settings/edit/(:any)']      = 'admin_settings/sms_api_settings/Update/$1';

//mail settings

$route['mail/settings/edit/(:any)']      = 'admin_settings/mail_settings/Update/$1';

//Limit settings

$route['limit/settings/edit/(:any)']      = 'admin_settings/limit_settings/Update/$1';

//Discount settings

$route['discount/settings/edit/(:any)']      = 'admin_settings/discount_settings/Update/$1';

//rateapi

$route['api/update/rate/(:any)']        = 'admin_rateapi/update_rateapi/$1';

//wallet master

$route['wallet/master/list'] 			= 'admin_wallet/wallet_setting/List';

$route['wallet/master/ajax_list'] 		= 'admin_wallet/wallet_setting';

$route['wallet/master/ajax_list/(:any)'] 		= 'admin_wallet/wallet_setting/$1';

$route['wallet/master/add'] 			= 'admin_wallet/wallet_setting/View';

$route['wallet/master/save'] 			= 'admin_wallet/wallet_setting/Save';

$route['wallet/master/edit/(:any)'] 	= 'admin_wallet/wallet_setting/View/$1';

$route['wallet/master/update/(:any)'] 	= 'admin_wallet/wallet_setting/Update/$1';

$route['wallet/master/delete/(:any)'] 	= 'admin_wallet/wallet_setting/Delete/$1';

//Manage Wallet

$route['wallet/account/list'] 			= 'admin_wallet/wallet_account/List';

$route['wallet/account/ajax_list'] 		= 'admin_wallet/wallet_account';

$route['wallet/account/ajax_list/(:any)']= 'admin_wallet/wallet_account/Ajax/$1';

$route['wallet/account/add'] 			= 'admin_wallet/wallet_account/View';

$route['wallet/account/save'] 			= 'admin_wallet/wallet_account/Save';

$route['wallet/account/edit/(:any)'] 	= 'admin_wallet/wallet_account/View/$1';

$route['wallet/account/update/(:any)'] 	= 'admin_wallet/wallet_account/Update/$1';

$route['wallet/account/delete/(:any)'] 	= 'admin_wallet/wallet_account/Delete/$1';

$route['wallet/get/customers/(:any)'] 	= 'admin_wallet/ajax_get_customers/$1';

$route['wallet/get/setting'] 	        = 'admin_wallet/ajax_get_setting';

 // employee 

$route['wallet/get/employee/(:any)'] 	= 'admin_wallet/ajax_get_employee/$1';

//Manage Wallet

$route['wallet/transaction/list'] 			= 'admin_wallet/wallet_transaction/List';

$route['wallet/transaction/ajax_list'] 		= 'admin_wallet/wallet_transaction';

$route['wallet/transaction/ajax_list/(:any)']= 'admin_wallet/wallet_transaction/$1';

$route['wallet/transaction/add'] 			= 'admin_wallet/wallet_transaction/View';

$route['wallet/transaction/save'] 			= 'admin_wallet/wallet_transaction/Save';

$route['wallet/transaction/edit/(:any)'] 	= 'admin_wallet/wallet_transaction/View/$1';

$route['wallet/transaction/update/(:any)'] 	= 'admin_wallet/wallet_transaction/Update/$1';

$route['wallet/transaction/delete/(:any)'] 	= 'admin_wallet/wallet_transaction/Delete/$1';

//Jilaba Api

$route['japi/transactions'] 			= 'chitapi/transactions';

$route['japi/transactions/status'] 		= 'chitapi/transactionsByStatus';

$route['japi/transactions_ob/status'] 	= 'chitapi/transactionsByStatus_obj';

$route['japi/transaction/update'] 		= 'chitapi/updateTransaction';

$route['japi/transaction/updatedata'] 	= 'chitapi/updateTransactionData';

$route['japi/transactions/update'] 		= 'chitapi/updateTransactions';

$route['japi/transactions/updatedata'] 	= 'chitapi/updateTransactionsData';

//$route['japi/transactions/update/range'] = 'chitapi/updateTransactionsByRange';

$route['japi/registrations'] 				= 'chitapi/registrations';

$route['japi/registrations/status']			= 'chitapi/registrationsByStatus';

$route['japi/registration/update'] 			= 'chitapi/updateRegistration';

$route['japi/registration/updatedata'] 		= 'chitapi/updateRegistrationData';

$route['japi/registrations/update'] 		= 'chitapi/updateRegistrations';

$route['japi/registrations/updatedata'] 	= 'chitapi/updateRegistrationsData';

//$route['japi/registrations/update/range']	= 'chitapi/updateRegistrationsByRange';

$route['japi/newcustomers']            = 'chitapi/newCustomers';

$route['japi/newcustomer/add']         = 'chitapi/insertNewCustomer';

$route['japi/newcustomers/add']        = 'chitapi/insertNewCustomers';

//SKTM Api (SCM,TKTM)

$route['api/transactions'] 				= 'sktm_syncapi/transactions';

$route['api/transactions/status'] 		= 'sktm_syncapi/transactionsByStatus';

$route['api/transactions_ob/status'] 	= 'sktm_syncapi/transactionsByStatus_obj';

$route['api/transaction/update'] 		= 'sktm_syncapi/updateTransaction';

$route['api/transaction/updatedata'] 	= 'sktm_syncapi/updateTransactionData';

$route['api/transactions/update'] 		= 'sktm_syncapi/updateTransactions';

$route['api/transactions/updatedata'] 	= 'sktm_syncapi/updateTransactionsData';

//$route['api/transactions/update/range'] = 'sktm_syncapi/updateTransactionsByRange';

$route['api/registrations'] 				= 'sktm_syncapi/registrations';

$route['api/registrations/status']			= 'sktm_syncapi/registrationsByStatus';

$route['api/registration/update'] 			= 'sktm_syncapi/updateRegistration';

$route['api/registration/updatedata'] 		= 'sktm_syncapi/updateRegistrationData';

$route['api/registrations/update'] 			= 'sktm_syncapi/updateRegistrations';

$route['api/registrations/updatedata'] 		= 'sktm_syncapi/updateRegistrationsData';

//$route['api/registrations/update/range']	= 'sktm_syncapi/updateRegistrationsByRange';

$route['api/newcustomers']            = 'sktm_syncapi/newCustomers';

$route['api/newcustomer/add']         = 'sktm_syncapi/insertNewCustomer';

$route['api/newcustomers/add']        = 'sktm_syncapi/insertNewCustomers';

// group_message 

//all customer

$route['sms/send/group_message_allcus']            = 'admin_usersms/sendsms_allcustomer';

//selected customer

$route['sms/send/get_selectcustomer']            = 'admin_usersms/get_selectcustomer_list';

$route['sms/send/group_message_selectcus']            = 'admin_usersms/sendsms_selectcustomer';

// group_message

//group_email

//all customer

$route['sms/send/group_email_allcus']            = 'admin_usersms/sendemail_allcustomer';

$route['sms/send/group_email_cus']            = 'admin_usersms/sendemail_selectedcustomer';

$route['email/send/group_msg']          = 'admin_usersms/send_allcus_email';

// group_email

//Catlog Modules settings//HH

$route['settings/module/list']              = 'admin_usersms/catlog_module_form/list';

$route['settings/module/ajax']              = 'admin_usersms/catlog_module_form/Ajax';

$route['settings/module/add']               = 'admin_usersms/catlog_module_form/Add';

$route['settings/module/save']               = 'admin_usersms/catlog_module_post/Save';

$route['settings/module/edit/(:any)']       = 'admin_usersms/catlog_module_form/Edit/$1';

$route['settings/module/update/(:any)']       = 'admin_usersms/catlog_module_post/Update/$1';

$route['settings/module/delete/(:any)']     = 'admin_usersms/catlog_module_form/Delete/$1';

//sms

$route['sms/send']          			= 'admin_usersms/index';

$route['sms/send/group_message']        = 'admin_usersms/send_group_sms';

$route['sms/send/group']                = 'admin_usersms/open_group_form/Add';

$route['sms/get_schemes']				= 'admin_usersms/ajax_get_schemes';

$route['sms/get_scheme/(:any)']			= 'admin_usersms/ajax_get_scheme/$1';

$route['sms/login']                     = 'admin_usersms/send_login';

$route['sms/login_email']               = 'admin_usersms/send_login_email';

$route['sms/send/sms']                  = 'admin_usersms/send_login_detail';

$route['sms/service/list']              = 'admin_usersms/sms_service_form/List';

$route['sms/service/ajax']              = 'admin_usersms/sms_service_form/Ajax';

$route['sms/service/add']               = 'admin_usersms/sms_service_form/Add';

$route['sms/service/save']               = 'admin_usersms/sms_service_post/Save';

$route['sms/service/edit/(:any)']       = 'admin_usersms/sms_service_form/Edit/$1';

$route['sms/service/update/(:any)']       = 'admin_usersms/sms_service_post/Update/$1';

$route['sms/service/delete/(:any)']     = 'admin_usersms/sms_service_form/Delete/$1';

$route['email/compose']                 = 'admin_usersms/compose_view';

$route['email/send']                    = 'admin_usersms/send_email';

$route['email/group']                   = 'admin_usersms/compose_group_view';

$route['email/send/group_message']      = 'admin_usersms/send_group_email';

//Notification

$route['notification/list']              	= 'admin_usersms/notification_form/List';

$route['notification/add']               	= 'admin_usersms/notification_form/Add';

$route['notification/ajax']             	= 'admin_usersms/notification_form/Ajax';

$route['notification/save']               	= 'admin_usersms/notification_service_post/Save';

$route['notification/edit/(:any)']       	= 'admin_usersms/notification_form/Edit/$1';

$route['notification/update/(:any)']       	= 'admin_usersms/notification_service_post/Update/$1';

$route['notification/delete/(:any)']     	= 'admin_usersms/notification_form/Delete/$1';

$route['notification/on_off/(:any)']     	= 'admin_usersms/notification_on_off/$1';

$route['admin_usersms/notification_status/(:any)/(:any)'] = 'admin_usersms/notification_status/$1/$2';

//send due alert for customer 

$route['send/sendnotification'] 			= 'admin_usersms/send_notificationform';

$route['send/notification'] 				= 'admin_usersms/notidata_gen';

$route['send/duecusnotification']			='admin_usersms/due_notification'; 

//offers

$route['settings/offers']					= 'admin_settings/offers_form';

$route['settings/offers_list']				= 'admin_settings/ajax_get_offers';

$route['settings/offers/add']             	= 'admin_settings/offers_form/Add';

$route['settings/offers/save']             	= 'admin_settings/offers_form/Save';

$route['settings/offers/edit/(:any)']     	= 'admin_settings/offers_form/Edit/$1';

$route['settings/offers/delete/(:any)']		= 'admin_settings/offers_form/Delete/$1';

$route['settings/offers/update/(:any)']		= 'admin_settings/offers_form/Update/$1';

//new arrivals

$route['settings/new_arrivals']					= 'admin_settings/new_arrivals_form';

$route['settings/new_arrivals_list']			= 'admin_settings/ajax_get_new_arrivals';

$route['settings/new_arrivals/add']             = 'admin_settings/new_arrivals_form/Add';

$route['settings/new_arrivals/save']            = 'admin_settings/new_arrivals_form/Save';

$route['settings/new_arrivals/edit/(:any)']     = 'admin_settings/new_arrivals_form/Edit/$1';

$route['settings/new_arrivals/delete/(:any)']	= 'admin_settings/new_arrivals_form/Delete/$1';

$route['settings/new_arrivals/update/(:any)']	= 'admin_settings/new_arrivals_form/Update/$1';

// payu active cardbrand

$route['settings/cardbrand']='admin_settings/cardbrand_form';

$route['settings/cardbrand_list']='admin_settings/ajax_get_cardbrand';

$route['settings/cardbrand/add']='admin_settings/cardbrand_form/Add';

$route['settings/cardbrand/edit/(:any)']='admin_settings/cardbrand_form/Edit/$1';

$route['settings/cardbrand/delete/(:any)']='admin_settings/cardbrand_form/Delete/$1';

$route['settings/cardbrand/update/(:any)']='admin_settings/cardbrand_form/Update/$1';

// branch_settings

$route['branch/settings/edit/(:any)'] =  'admin_settings/branch_settings/$1';

$route['branch/branchname_list']	          = 'admin_manage/get_branch_name';

// branch_settings

// branch_name

$route['branch/list']	                       = 'admin_settings/branch_form';

$route['branch/branches']      				   = 'admin_settings/branch_form/get_branches';

$route['branch/branch_list'] 					= 'admin_settings/ajax_get_branches';

$route['branch/branch_name/add']  				= 'admin_settings/branch_form/Add';

$route['branch/branch_name/edit/(:any)']		='admin_settings/branch_form/Edit/$1';

$route['branch/branch_name/update/(:any)']			='admin_settings/branch_form/Update/$1';

$route['branch/branch_stat/(:any)/(:any)']	    ='admin_settings/branch_form/Update_status/$1/$2';

$route['branch/branches/check/(:any)']   		= 'admin_settings/branch_form/branch_check/$1';

// branch_name

// branch wise metal_rates

$route['branch/metal_rate/ajax_list/(:any)'] 	  = 'admin_settings/matal_ratelist/$1';

// branch wise metal_rates

//receipt_no settings

//receipt

//$route['receipt/settings/edit/(:any)']	= 'admin_settings/receipt_settings/$1';

$route['receipt_number/update']	        = 'admin_payment/manual_receiptnumber';

//schemeacount no  settings

//$route['schemeacc_no/settings/edit/(:any)']	= 'admin_settings/schemeacc_no_settings/$1';

//schemegroup  settings

//$route['scheme_group/settings/edit/(:any)']	= 'admin_settings/scheme_group_settings/$1';

// offline payment

 $route['api/insertOfflinePayment']        = 'chitapi/insertOfflinePayment';

// offline payment

//Promotion sms and otp setting

$route['promotion_credit/settings/edit/(:any)']      = 'admin_settings/promotioncredit__settings/Update/$1';

$route['otp_credit/settings/edit/(:any)']             = 'admin_settings/otpcredit_settings/Update/$1';

$route['promotion/settings/edit/(:any)']              = 'admin_settings/promotion_api_settings/Update/$1';

//Promotion sms and otp setting

// wallet settings

$route['wallet/settings/edit/(:any)']    = 'admin_settings/wallettype_account/$1';

// Referral  settings

$route['refferbenifits/settings/edit/(:any)']    = 'admin_settings/ref_benefits_setting/$1';

// Employee referral Report

$route['reports/employee/referral']  = 'admin_reports/get_employee_details';

/* End of file routes.php */

//wallet master

$route['wallet/category/list'] 			     = 'admin_wallet/wallet_category/List';

$route['wallet/category/ajax_list'] 		    = 'admin_wallet/wallet_category';

$route['wallet/category/ajax_list/(:any)'] 		= 'admin_wallet/wallet_category/$1';

$route['wallet/category/add'] 			        = 'admin_wallet/wallet_category/View';

$route['wallet/category/save'] 			= 'admin_wallet/wallet_category/Save';

$route['wallet/category/edit/(:any)'] 	= 'admin_wallet/wallet_category/View/$1';

$route['wallet/category/update/(:any)'] 	= 'admin_wallet/wallet_category/Update/$1';

$route['wallet/category/delete/(:any)'] 	= 'admin_wallet/wallet_category/Delete/$1';

// wallet category settingsDB //

$route['wallet/category/setting/list'] 			     = 'admin_wallet/wallet_category_settings/List';

$route['wallet/category/setting/ajax_list'] 		    = 'admin_wallet/wallet_category_settings';

$route['wallet/category/setting/update'] 	           = 'admin_wallet/wallet_category_settings/Update';

// Catalog Module

//purity

$route['purity/list']           			= 'admin_ret_catalog/purity/List';

$route['purity/status/(:any)/(:any)']       = 'admin_ret_catalog/purity_status/$1/$2';

$route['purity/ajax']         				= 'admin_ret_catalog/purity/ajax';

$route['purity/add']          				= 'admin_ret_catalog/purity/Add';

$route['purity/save']        				= 'admin_ret_catalog/purity/Save';

$route['purity/edit/(:any)']  			    = 'admin_ret_catalog/purity/Edit/$1';

$route['purity/delete/(:any)']				= 'admin_ret_catalog/purity/Delete/$1';

$route['purity/update/(:any)']				= 'admin_ret_catalog/purity/Update/$1';

//color

$route['color/list']           			   	= 'admin_ret_catalog/color/List';

$route['color/status/(:any)/(:any)']        = 'admin_ret_catalog/color_status/$1/$2';

$route['color/ajax']         				= 'admin_ret_catalog/color/ajax';

$route['color/add']          				= 'admin_ret_catalog/color/Add';

$route['color/save']        				= 'admin_ret_catalog/color/Save';

$route['color/edit/(:any)']  			    = 'admin_ret_catalog/color/Edit/$1';

$route['color/delete/(:any)']				= 'admin_ret_catalog/color/Delete/$1';

$route['color/update/(:any)']				= 'admin_ret_catalog/color/Update/$1';

//cut

$route['cut/list']           		   	= 'admin_ret_catalog/cut/List';

$route['cut/status/(:any)/(:any)']      = 'admin_ret_catalog/cut_status/$1/$2';

$route['cut/ajax']         				= 'admin_ret_catalog/cut/ajax';

$route['cut/add']          				= 'admin_ret_catalog/cut/Add';

$route['cut/save']        				= 'admin_ret_catalog/cut/Save';

$route['cut/edit/(:any)']  			    = 'admin_ret_catalog/cut/Edit/$1';

$route['cut/delete/(:any)']				= 'admin_ret_catalog/cut/Delete/$1';

$route['cut/update/(:any)']				= 'admin_ret_catalog/cut/Update/$1';

//clarity

$route['clarity/list']           		   	= 'admin_ret_catalog/clarity/List';

$route['clarity/status/(:any)/(:any)']      = 'admin_ret_catalog/clarity_status/$1/$2';

$route['clarity/ajax']         				= 'admin_ret_catalog/clarity/ajax';

$route['clarity/add']          				= 'admin_ret_catalog/clarity/Add';

$route['clarity/save']        				= 'admin_ret_catalog/clarity/Save';

$route['clarity/edit/(:any)']  			    = 'admin_ret_catalog/clarity/Edit/$1';

$route['clarity/delete/(:any)']				= 'admin_ret_catalog/clarity/Delete/$1';

$route['clarity/update/(:any)']				= 'admin_ret_catalog/clarity/Update/$1';

// Category List

$route['catalog/category/list']					  = 'admin_catalog/catagory_detalis/List';

$route['catalog/category/ajax_list'] 		      = 'admin_catalog/catagory_detalis';

$route['catalog/category/add'] 			          = 'admin_catalog/catagory_detalis/View';

$route['catalog/category/save'] 			      = 'admin_catalog/catagory_detalis/Save';

$route['catalog/category/edit/(:any)'] 	          = 'admin_catalog/catagory_detalis/View/$1';

$route['catalog/category/update/(:any)'] 	      = 'admin_catalog/catagory_detalis/Update/$1';

$route['catalog/category/delete/(:any)'] 	      = 'admin_catalog/catagory_detalis/Delete/$1';

// Product List

$route['catalog/product/list']					  = 'admin_catalog/product_detalis/List';

$route['catalog/product/ajax_list'] 		      = 'admin_catalog/product_detalis';

$route['catalog/product/add'] 			          = 'admin_catalog/product_detalis/View';

$route['catalog/product/save'] 			      = 'admin_catalog/product_detalis/Save';

$route['catalog/product/edit/(:any)'] 	          = 'admin_catalog/product_detalis/View/$1';

$route['catalog/product/update/(:any)'] 	      = 'admin_catalog/product_detalis/Update/$1';

$route['catalog/product/delete/(:any)'] 	      = 'admin_catalog/product_detalis/Delete/$1';

//payment gateway

$route['settings/payment']='admin_settings/gateway_form';

$route['settings/payment_gateway_list']='admin_settings/ajax_get_paymentgateway';

$route['settings/payment_gateway/add']='admin_settings/gateway_form/Add';

$route['settings/payment_gateway/edit']='admin_settings/gateway_form/Edit';

$route['settings/paymentgateway/delete/(:any)']='admin_settings/gateway_form/Delete/$1';

$route['settings/paymentgateway/update/(:any)']='admin_settings/gateway_form/Update/$1';

//payment gateway

$route['reports/inter_wallet_detail/(:any)']          = 'admin_dashboard/inter_wallet_details/$1';

$route['reports/inter_wallet_wc']          = 'admin_dashboard/inter_wallet_accounts_detail';

$route['reports/inter_wallet_woc']          = 'admin_dashboard/inter_wallet_accounts__woc';

//thermal printer

$route['payment/thermal_invoice/(:any)/(:any)'] 	= 'admin_payment/thermal_invoice/$1/$2';

//master village

$route['settings/village']								= 'admin_settings/village_list';

$route['settings/village_form/ajax_list'] 		      = 'admin_settings/village_form';

$route['settings/village_form/add'] 			          = 'admin_settings/village_form/View';

$route['settings/village_form/save'] 			      = 'admin_settings/village_form/Save';

$route['settings/village_form/edit/(:any)'] 	          = 'admin_settings/village_form/View/$1';

$route['settings/village_form/update/(:any)'] 	      = 'admin_settings/village_form/Update/$1';

$route['settings/village_form/delete/(:any)'] 	      = 'admin_settings/village_form/Delete/$0';

//master village

//Employee collection summary

$route['reports/employee_wise_collection']  = 'admin_reports/employee_wise_summary';

$route['reports/employee_wise_summary']  = 'admin_reports/employee_collection';

//Employee collection summary

//Integration Tool reports // HH

//customer reg &Transactions//

$route['reports/inter_table/list']          = 'admin_reports/inter_table';

$route['reports/intertable_list']          = 'admin_reports/intertable_list';

$route['reports/inter_table/list']          = 'admin_reports/inter_table';

$route['reports/intertable_translist']          = 'admin_reports/intertable_translist';

//Terms and conditions

$route['settings/terms_and_conditions/list'] 				= 'admin_settings/terms_conditions/List';

$route['settings/terms_and_conditions/ajax_list'] 			= 'admin_settings/terms_conditions/Ajax';

$route['settings/terms_and_conditions/ajax_list/(:any)'] 	= 'admin_settings/terms_conditions/Ajax/$1';

$route['settings/terms_and_conditions/add']					= 'admin_settings/terms_conditions/View';

$route['settings/terms_and_conditions/edit/(:any)'] 			= 'admin_settings/terms_conditions/View/$1';

$route['settings/terms_and_conditions/save'] 				= 'admin_settings/terms_conditions/Save';

$route['settings/terms_and_conditions/update/(:any)'] 		= 'admin_settings/terms_conditions/Update/$1';

$route['settings/terms_and_conditions/delete/(:any)'] 		= 'admin_settings/terms_conditions/Delete/$1';

//Purchase Payment - Akshaya Thiruthiyai Spl updt//

$route['reports/get_purchase_payment']       = 'admin_reports/get_purchase_payment';

// metal filter //HH

$route['metal/metalname_list']	          = 'admin_manage/get_metal_name';

//metal filter//

//Autodebit subscription Status Report//HH

$route['reports/get_autodebit_subscription']       = 'admin_reports/get_autodebit_subscription';

$route['reports/ajax_autodebit_subscription']      = 'admin_reports/ajax_get_autodebit_subscription';

// Auto Debit Subscription Routes
$route['subscription/create/(:num)']               = 'Autodebit/create/$1';
$route['autodebit/callback/(:num)']                = 'Autodebit/callback/$1';
$route['autodebit/webhook']                        = 'Autodebit/webhook';
$route['subscription/cancel/(:num)']               = 'Autodebit/cancel/$1';
$route['subscription/retry/(:num)']                = 'Autodebit/retry/$1';
$route['autodebit/redirect/(:any)']                = 'Autodebit/redirect/$1';
$route['autodebit/redirect/(:any)/(:any)']         = 'Autodebit/redirect/$1/$2';


$route['settings/retail_setting/list']              = 'admin_usersms/ret_settings_form/list';

$route['settings/retail_setting/ajax']              = 'admin_usersms/ret_settings_form/Ajax';

$route['settings/retail_setting/add']               = 'admin_usersms/ret_settings_form/Add';

$route['settings/retail_setting/save']               = 'admin_usersms/ret_settings_post/Save';

$route['settings/retail_setting/edit/(:any)']       = 'admin_usersms/ret_settings_form/Edit/$1';

$route['settings/retail_setting/update/(:any)']       = 'admin_usersms/ret_settings_post/Update/$1';

$route['settings/retail_setting/delete/(:any)']     = 'admin_usersms/ret_settings_form/Delete/$1';

$route['reports/scheme_payment_daterange']           = 'admin_reports/scheme_payment_daterange';

$route['reports/gift_report']  = 'admin_reports/get_gift_report';

//closed A/C report//HH

$route['reports/closed_acc_report']	            ='admin_reports/closed_account_list'; 

$route['reports/closedaccount_list']              = 'admin_reports/closedaccount_list';

//Agent

$route['agent/ajax_getAgents']					= 'admin_agent/ajax_getAgents';

$route['agent/agent_report/list']        = 'admin_agent/agent_report/list';

$route['agent/agent_report/summary'] = 'admin_agent/agent_report/summary';

$route['agent/agent_settlement/list']					= 'admin_agent/agent_settlement/list';

$route['agent']								= 'admin_agent/index';

$route['agent/ajax_list']					= 'admin_agent/ajax_agents';

$route['agent/add']							= 'admin_agent/agent_form/Add';

$route['agent/save']							= 'admin_agent/agent_post/Add';

$route['agent/edit/(:any)']					= 'admin_agent/agent_form/Edit/$1';

$route['agent/agent_edit/(:any)']					= 'admin_dashboard/agent_edit/$1';

$route['agent/update/(:any)']				= 'admin_agent/agent_post/Edit/$1';

$route['agent/delete/(:any)']				= 'admin_agent/agent_post/Delete/$1';

$route['agent/login/(:any)']					='admin_agent/login/$1';

$route['agent/get_agents'] 				='admin_agent/ajax_get_agents';

$route['agent/get_agent/(:any)']			='admin_agent/ajax_get_agent/$1';

$route['agent/check_username/(:any)']		= 'admin_agent/check_username/$1';

$route['agent/check_mobile']					= 'admin_agent/check_mobile';

$route['agent/check_email']					= 'admin_agent/check_email';

$route['agent/profile/status/(:any)/(:any)']	='admin_agent/profile_status/$1/$2';

$route['agent/status/(:any)/(:any)']			='admin_agent/agent_status/$1/$2';

$route['agent/dload/(:any)/(:any)']			='admin_agent/download/$1/$2';

$route['agent/approval']  = 'admin_agent/agent_settlement_list/';

//product division

$route['product_division/list']           		   	= 'admin_ret_catalog/product_division/List';

$route['product_division/status/(:any)/(:any)']     = 'admin_ret_catalog/product_division_status/$1/$2';

$route['product_division/ajax']         			= 'admin_ret_catalog/product_division/ajax';

$route['product_division/add']          			= 'admin_ret_catalog/product_division/Add';

$route['product_division/save']        				= 'admin_ret_catalog/product_division/Save';

$route['product_division/edit/(:any)']  			= 'admin_ret_catalog/product_division/Edit/$1';

$route['product_division/delete/(:any)']		    = 'admin_ret_catalog/product_division/Delete/$1';

$route['product_division/update/(:any)']			= 'admin_ret_catalog/product_division/Update/$1';

// Bank Deposit

$route['deposit/list']					    = 'admin_ret_catalog/bank_deposit/List';

$route['deposit/ajax_list'] 		        = 'admin_ret_catalog/bank_deposit/ajax_list';

$route['deposit/add'] 			            = 'admin_ret_catalog/bank_deposit/View';

$route['deposit/save'] 			            = 'admin_ret_catalog/bank_deposit/Save';

$route['deposit/edit/(:any)'] 	            = 'admin_ret_catalog/bank_deposit/View/$1';

$route['deposit/update/(:any)'] 	        = 'admin_ret_catalog/bank_deposit/Update/$1';

$route['deposit/delete/(:any)'] 	        = 'admin_ret_catalog/bank_deposit/Delete/$1';

$route['deposit/check_ref_id'] 	            = 'admin_ret_catalog/bank_deposit/check_ref_id';

/* Location: ./application/config/routes.php */

//profession

$route['settings/profession']				= 'admin_settings/profession_form';

$route['settings/profession_list']			= 'admin_settings/ajax_get_profession';

$route['settings/profession/add']			= 'admin_settings/profession_form/Add';

$route['settings/profession/edit/(:any)']   = 'admin_settings/profession_form/Edit/$1';

$route['settings/profession/delete/(:any)'] = 'admin_settings/profession_form/Delete/$1';

$route['settings/profession/update/(:any)'] = 'admin_settings/profession_form/Update/$1';

$route['settings/company/getprofession']	= 'admin_settings/get_profession';

//Created by Durga 13.02.2023

$route['reports/detail/registration_bydate/(:any)/(:any)']     = 'admin_dashboard/reg_detail_bydate/$1/$2';

$route['account/withoutPayment_bydate/(:any)/(:any)']     = 'admin_dashboard/acc_wo_pay_details_bydate/$1/$2';

$route['customer/withoutAccount_bydate/(:any)/(:any)']     = 'admin_dashboard/cust_wo_acc_details_bydate/$1/$2';

//account wise summary reports

$route['reports/scheme_customer_daterange']           = 'admin_reports/scheme_customer_daterange';

//WEDDING-BIRTHDAY REPORTS

$route['reports/customer_wishes/(:any)']= 'admin_reports/customer_wishes/$1';

$route['reports/edit_acc_pay']          = 'admin_reports/edit_acc_pay';

// added by Durga 12/05/2023

// version 

$route['settings/version/list']='admin_settings/version_details/List';

$route['settings/version/add']='admin_settings/version_details/View';

$route['settings/version/edit/(:any)'] 			= 'admin_settings/version_details/View/$1';

$route['settings/version/save'] 				= 'admin_settings/version_details/Save';

$route['settings/version/update/(:any)'] 		= 'admin_settings/version_details/Update/$1';

$route['settings/version/delete/(:any)'] 		= 'admin_settings/version_details/Delete/$1';

$route['settings/version/ajax'] 				= 'admin_settings/ajax_get_version';

$route['kyc/settings/update'] 				= 'admin_settings/kyc_settings/update';

//Added by Durga 29-06-2023

//scheme wise mode wise

$route['reports/payment_modeandgroupwise_data']  = 'admin_reports/payment_modeandgroupwise_data';

$route['reports/payment_modeandgroupwise_datalist']  = 'admin_reports/payment_modeandgroupwise_list';

//Added by Durga 19-07-2023

$route['employee/enable_device'] 				= 'admin_employee/enable_device';



//collection App - Dashboard

$route['admin/dashboard/collection_App/(:any)/(:any)/(:any)/(:any)']     = 'admin_dashboard/get_collection_list';



//general advance report

$route['reports/general_advance']           = 'admin_reports/general_advance_view';

$route['reports/general_advance_list']           = 'admin_reports/general_advance_list';



//monthly chit report

$route['reports/monthly_chit_report'] 				= 'admin_reports/monthly_report_view';





//Zone Maaster



$route['customer/zone/list']							= 'admin_customer/zone/list';



//Zone Maaster


//matiruty _report--start

$route['reports/maturity_report']  = 'admin_reports/maturity_report_view';
//matiruty report --end 

$route['get/giftname_list']   = 'admin_reports/giftname_list';
$route['reports/get_yet_to_issue']  = 'admin_reports/get_yet_to_issue';


// renewal live report

$route['reports/renewal_live_report']  = 'admin_reports/renewal_live_report';

//customer - languages known
$route['settings/company/getlanguages']			= 'admin_settings/get_languages_known';
// Print Template Designer
$route['print-templates'] = 'admin_print_template/index';
$route['print-templates/ajax-list'] = 'admin_print_template/ajax_list';
$route['print-templates/add'] = 'admin_print_template/add';
$route['print-templates/save'] = 'admin_print_template/save';
$route['print-templates/designer/(:num)'] = 'admin_print_template/designer/$1';
$route['print-templates/save-design/(:num)'] = 'admin_print_template/save_design/$1';
$route['print-templates/load-design/(:num)'] = 'admin_print_template/load_design/$1';
$route['print-templates/preview/(:num)'] = 'admin_print_template/preview/$1';
$route['print-templates/preview-live/(:num)'] = 'admin_print_template/preview_live/$1';
$route['print-templates/delete/(:num)'] = 'admin_print_template/delete/$1';
$route['print-templates/set-default/(:num)'] = 'admin_print_template/set_default/$1';
$route['print-templates/fields/(:any)'] = 'admin_print_template/get_fields/$1';
$route['print-templates/custom-blocks'] = 'admin_print_template/get_custom_blocks';
$route['print-templates/toggle-active/(:num)'] = 'admin_print_template/toggle_active/$1';
$route['admin_print_template/get_custom_blocks'] = 'admin_print_template/get_custom_blocks';
$route['admin_print_template/save_custom_block'] = 'admin_print_template/save_custom_block';
$route['admin_print_template/get_block_templates'] = 'admin_print_template/get_block_templates';

// Print Template Designer V2
$route['print-templates/designer-v2/(:num)'] = 'admin_print_template/designer_v2/$1';
$route['print-templates/mc-va-config/(:num)'] = 'admin_print_template/get_mc_va_config/$1';
$route['print-templates/save-mc-va-config'] = 'admin_print_template/save_mc_va_config';
$route['print-templates/default-template/(:num)'] = 'admin_print_template/load_default_template/$1';
$route['print-templates/fields-v2/(:any)'] = 'admin_print_template/get_fields_v2/$1';
$route['print-templates/save-design-v2/(:num)'] = 'admin_print_template/save_design_v2/$1';
$route['print-templates/preview-live-v2/(:num)'] = 'admin_print_template/preview_live_v2/$1';

// Computed Variables (V2 Designer)
$route['print-templates/computed-vars/(:num)'] = 'admin_print_template/get_computed_vars/$1';
$route['print-templates/save-computed-var/(:num)'] = 'admin_print_template/save_computed_var/$1';
$route['print-templates/delete-computed-var/(:num)/(:num)'] = 'admin_print_template/delete_computed_var/$1/$2';

// Invoice Builder V2
$route['invoice-builder'] = 'admin_invoice_builder/index';
$route['invoice-builder/ajax-list'] = 'admin_invoice_builder/ajax_list';
$route['invoice-builder/add'] = 'admin_invoice_builder/add';
$route['invoice-builder/save'] = 'admin_invoice_builder/save';
$route['invoice-builder/designer/(:num)'] = 'admin_invoice_builder/designer/$1';
$route['invoice-builder/save-design/(:num)'] = 'admin_invoice_builder/save_design/$1';
$route['invoice-builder/load-design/(:num)'] = 'admin_invoice_builder/load_design/$1';
$route['invoice-builder/preview/(:num)'] = 'admin_invoice_builder/preview/$1';
$route['invoice-builder/delete/(:num)'] = 'admin_invoice_builder/delete/$1';
$route['invoice-builder/set-default/(:num)'] = 'admin_invoice_builder/set_default/$1';
$route['invoice-builder/fields/(:any)'] = 'admin_invoice_builder/get_fields/$1';
$route['settings/company/getlanguages']            = 'admin_settings/get_languages_known';

// Bank Ledger 
$route['settings/ledger/list']                = 'admin_settings/ledger/List';
$route['settings/ledger/add']                 = 'admin_settings/ledger/View';
$route['settings/ledger/list/(:any)']         = 'admin_settings/ledger/List/$1';
$route['settings/ledger/edit/(:any)']         = 'admin_settings/ledger/View/$1';
$route['settings/ledger/Save']                = 'admin_settings/ledger/Save';
$route['settings/ledger/Update/(:any)']       = 'admin_settings/ledger/Update/$1';
$route['settings/ledger/Delete/(:any)']       = 'admin_settings/ledger/Delete/$1';
$route['reports/ledger'] = 'admin_ret_reports/ledger_report/list';
$route['reports/ledger/ajax'] = 'admin_ret_reports/ledger_report/ajax';

// CR/DR Ledger
$route['crdr_ledger/list'] 				= 'admin_ret_catalog/ret_crdr_ledger/list';
$route['crdr_ledger/ajax'] 				= 'admin_ret_catalog/ret_crdr_ledger/ajax';
$route['crdr_ledger/add'] 				= 'admin_ret_catalog/ret_crdr_ledger/Add';
$route['crdr_ledger/save'] 				= 'admin_ret_catalog/ret_crdr_ledger/Save';
$route['crdr_ledger/edit/(:any)'] 		= 'admin_ret_catalog/ret_crdr_ledger/Edit/$1';
$route['crdr_ledger/update/(:any)'] 	= 'admin_ret_catalog/ret_crdr_ledger/Update/$1';
$route['crdr_ledger/delete/(:any)'] 	= 'admin_ret_catalog/ret_crdr_ledger/delete/$1';
$route['crdr_ledger/status/(:any)/(:any)'] = 'admin_ret_catalog/ret_crdr_ledger/update_status/$1/$2';

// ═══════════════════════════════════════════════════════════
// Dynamic Print Designer (Fabric.js Canvas)
// ═══════════════════════════════════════════════════════════
$route['print-designer']                          = 'print_designer/index';
$route['print-designer/get-tables']               = 'print_designer/get_tables';
$route['print-designer/get-variables/(:any)']     = 'print_designer/get_variables/$1';
$route['print-designer/get-row-data']             = 'print_designer/get_row_data';
$route['print-designer/save']                     = 'print_designer/save';
$route['print-designer/load/(:num)']              = 'print_designer/load/$1';
$route['print-designer/list']                     = 'print_designer/template_list';
$route['print-designer/delete/(:num)']            = 'print_designer/delete/$1';
$route['print-designer/duplicate/(:num)']         = 'print_designer/duplicate/$1';
$route['print-designer/set-default/(:num)']       = 'print_designer/set_default/$1';
$route['print-designer/export-pdf']               = 'print_designer/export_pdf';
$route['print-designer/preview/(:num)/(:num)']    = 'print_designer/preview/$1/$2';

// ═══════════════════════════════════════════════════════════
// Employee Sales Incentive Configuration
// ═══════════════════════════════════════════════════════════
$route['emp_incentive']                        = 'admin_emp_incentive/config_list';
$route['emp_incentive/ajax_list']              = 'admin_emp_incentive/ajax_get_configs';
$route['emp_incentive/add']                    = 'admin_emp_incentive/config_form/Add';
$route['emp_incentive/save']                   = 'admin_emp_incentive/config_post/Add';
$route['emp_incentive/edit/(:num)']            = 'admin_emp_incentive/config_form/Edit/$1';
$route['emp_incentive/update/(:num)']          = 'admin_emp_incentive/config_post/Edit/$1';
$route['emp_incentive/delete']                 = 'admin_emp_incentive/ajax_delete_config';
$route['emp_incentive/get_products']           = 'admin_emp_incentive/get_products_by_category';
$route['emp_incentive/get_designs']            = 'admin_emp_incentive/get_designs_by_product';
$route['emp_incentive/get_sub_designs']        = 'admin_emp_incentive/get_sub_designs_by_design';
$route['reports/emp_sales_incentive']          = 'admin_emp_incentive/incentive_report/list';

// Stock Age Master
// ═══════════════════════════════════════════════════════════
$route['stock_age_master']                     = 'admin_stock_age_master/index';
$route['stock_age_master/ajax_list']           = 'admin_stock_age_master/ajax_get_list';
$route['stock_age_master/form/(:num)']         = 'admin_stock_age_master/form/$1';
$route['stock_age_master/form']                = 'admin_stock_age_master/form';
$route['stock_age_master/save']                = 'admin_stock_age_master/save';
$route['stock_age_master/delete']              = 'admin_stock_age_master/delete';
$route['stock_age_master/toggle_status']       = 'admin_stock_age_master/toggle_status';
$route['stock_age_master/dropdown']            = 'admin_stock_age_master/get_dropdown_data';
$route['reports/emp_sales_incentive/ajax']     = 'admin_emp_incentive/incentive_report/ajax';
$route['reports/emp_sales_incentive/summary']  = 'admin_emp_incentive/incentive_report/summary';
$route['reports/emp_sales_incentive/mark_paid'] = 'admin_emp_incentive/ajax_mark_paid';
$route['emp_incentive/toggle_status']          = 'admin_emp_incentive/ajax_toggle_status';
