<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will try to guess the protocol, domain
| and path to your installation. However, you should always configure this
| explicitly and never rely on auto-guessing, especially in production
| environments.
|
*/
//$config['base_url'] = 'https://coimbatorejewellery.in/wcrm/v5/';
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $root = ($https ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
    $root.= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    $config['base_url'] = $root;
// Coin Enquiry settings Fr user sdie //
//$config['enable_coin_enq'] = 1;
$config['ecom_url'] = '';
$config['sbi_virtual_pay_url'] = '';
$config['auto_pay_approval'] = 2; // 0 => No , 1 => Yes (Update status as success) , 2 => Yes (Update status as success and insert data in intermediatetables), 
$config['paystore_url'] = 'https://play.google.com/store/apps/details'; // paystore_url 
$config['wallet_cat_id'] = 1; // Id of wallet category (SSS)
$config['pay_branchId'] = NULL; // Id of online branch (SSS)
$config['defBranchRate'] = 2; // NULL - No Branch, 1 -> Rate of branch 1 (To display user),
$config['isCusEmailReq'] = 0; // 1 - Mandatory, 0 - Non Mandatory
$config['DTHshow'] = 0;  // display restrictions for users 
$config['sms_gateway'] = 1; // 1 - Msg91 , 2 - Netty Fish , 3 -SpearUC
// Account
$config['showGCodeInAcNo'] = 1; // 0 => No (1234) , 1 => Yes (SSA 1234)
$config['cliIDcode'] = "LMX";       // Client ID generation Short code
// App Store settings    
$config['paystore_url'] = 'https://play.google.com/store/apps/details'; // paystore_url 
$config['aPackage'] = ''; // play store package
$config['iPackage'] = ''; // app store package
// Scheme Settings
$config['default_acno_label'] = 'Transaction Pending';
// Integration 
$config['integrationType'] = 0;  // 1 - jilaba , 2 - sync tool, 3 - EJ ERP Integration, 4 -  SKTM (SCM,TKTM only - Tool for offline, API for online), 5 - Khimji Integration (Directly integration with ACME without storing data in intermediate tables)
$config['autoSyncExisting'] = 1; // 1 - Yes, 0 - No
$config['directAPI'] = 0;// 1 - Yes, 0 - No (only for SKTM space)
$config['directAPIurl'] = 'https://chitappback.spacetextiles.net/v1/';

// EJ ERP Integration Settings
$config['erp_baseURL'] = ''; // TEST
$config['ejUserName'] = '';
$config['ejPassword'] = '';
// Khimji Integration Settings
$config['khimji-baseURL'] = 'https://nac.acmepadm.com:8443/acme-customer-app-web/';
$config['khimji-X-Key'] = ''; // TEST
$config['khimji-Authorization'] = ''; // TEST
// ZOOP API
$config['zoop_enabled'] = 1; // 0 => No , 1 => Yes
$config['zoop_url'] = 'https://preprod.aadhaarapi.com/'; // PREPROD URL : https://preprod.aadhaarapi.com , PROD URL : https://prod.aadhaarapi.com
$config['agency_id'] = '';
$config['api_key'] = '';
// Whats App API
$config['wa_gateway'] = 2; // 1- Creative point, 2 - Qikchat
$config['whatsappurl'] = "http://whatsappsms.creativepoint.in/api/"; // Creative point
$config['whats-instanceid'] = ""; // Creative point
$config['qikchat-api-key'] = "LKDs-jhBt-twz7"; // Qikchat
// User Registration fields : 0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory
$config['custom_fields'] = array(
    //default fields......
    "firstname" => 2,
    "mobile" => 2,
    "country" => 2,
    "state" => 2,
    "city" => 2,
    //can be changed as per client wise	
    "lastname" => 1,
    "email" => 1,
    "gender" => 2,
    "custype" => 0,
    "title" => 2,
    "date_of_birth" => 1,
    "maritalstatus" => 0,
    "village" => 2,
    "address1" => 2,
    "address2" => 0,
    "pincode" => 2,
    "nominee_name" => 0,
    "nominee_mobile" => 0,
    "nominee_relationship" => 0,
    "nominee_address1" => 0,
    "nominee_address2" => 0,
    "adharno" => 0,
    "pannumber" => 0,
    "dlcno" => 0,
    //previous extra field	
    "pan" => 0,
);
// User Registration fields : 0 -> No need to show, 1 -> Show field, 2 -> Show field and is mandatory
/* $config['app_custom_fields'] = array(
                                 "email" 	=> 1,
                                 "address1" 	=> 2, // Street
                                 "address2" 	=> 2, // area
                                 "country" 	=> 2,
                                 "state" 	=> 2,
                                 "city" 		=> 2,
                                 "lastname"  => 1,
                                 "custype" => 2,
                                 "village" =>  0
                                 );  */
$config['app_custom_fields'] = array(
    //default fields......
    "firstname" => 2,
    "mobile" => 2,
    "country" => 2,
    "state" => 2,
    "city" => 2,
    //can be changed as per client wise	
    "lastname" => 1,
    "email" => 1,
    "gender" => 2,
    "custype" => 0,
    "title" => 2,
    "date_of_birth" => 1,
    "maritalstatus" => 0,
    "village" => 0,
    "address1" => 2,
    "address2" => 0,
    "pincode" => 1,
    "nominee_name" => 0,
    "nominee_mobile" => 0,
    "nominee_relationship" => 0,
    "nominee_address1" => 0,
    "nominee_address2" => 0,
    "adharno" => 0,
    "pannumber" => 0,
    "dlcno" => 0,
    //previous extra field	
    "pan" => 0,
);
$config['searchbyaccno'] = "3"; // 1- search by account only, 2 - search by mobile, 3 - both
$config['otherprofile_req'] = "1";
$config['allow_any_cors'] = true;
$config['digi_custom_fields'] = array(
	//default fields......
	"account_name" => 1,
	"address1" => 1,
	"nominee_name" => 1,
	"nominee_mobile" => 1,
);


/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = 'index.php';
/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of 'AUTO' works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'AUTO'			Default - auto detects
| 'PATH_INFO'		Uses the PATH_INFO
| 'QUERY_STRING'	Uses the QUERY_STRING
| 'REQUEST_URI'		Uses the REQUEST_URI
| 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
|
*/
$config['uri_protocol'] = 'AUTO';
/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/urls.html
*/
$config['url_suffix'] = '';
/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language'] = 'english';
/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
$config['charset'] = 'UTF-8';
/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the 'hooks' feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = TRUE;
/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/core_classes.html
| http://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'MY_';
/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify with a regular expression which characters are permitted
| within your URLs.  When someone tries to submit a URL with disallowed
| characters they will get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-@=';
/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| By default CodeIgniter enables access to the $_GET array.  If for some
| reason you would like to disable it, set 'allow_get_array' to FALSE.
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The other items let you set the query string 'words' that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd'; // experimental not currently in use
/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to
| determine what gets logged. Threshold options are:
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 1;
/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/logs/ folder. Use a full server path with trailing slash.
|
*/
$config['log_path'] = '';
/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';
/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/cache/ folder.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';
/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class or the Session class you
| MUST set an encryption key.  See the user guide for info.
|
*/
$config['encryption_key'] = 'ChitScheme-SaRaVaNa';
/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'sess_cookie_name'		= the name you want for the cookie
| 'sess_expiration'			= the number of SECONDS you want the session to last.
|   by default sessions last 7200 seconds (two hours).  Set to zero for no expiration.
| 'sess_expire_on_close'	= Whether to cause the session to expire automatically
|   when the browser window is closed
| 'sess_encrypt_cookie'		= Whether to encrypt the cookie
| 'sess_use_database'		= Whether to save the session data to a database
| 'sess_table_name'			= The name of the session database table
| 'sess_match_ip'			= Whether to match the user's IP address when reading the session data
| 'sess_match_useragent'	= Whether to match the User Agent when reading the session data
| 'sess_time_to_update'		= how many seconds between CI refreshing Session Information
|
*/
$config['sess_cookie_name'] = 'jwlone_wc_user';
$config['sess_expiration'] = 7200;
$config['sess_expire_on_close'] = FALSE;
$config['sess_encrypt_cookie'] = FALSE;
$config['sess_use_database'] = FALSE;
$config['sess_table_name'] = 'ci_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_match_useragent'] = TRUE;
$config['sess_time_to_update'] = 300;
/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix' = Set a prefix if you need to avoid collisions
| 'cookie_domain' = Set to .your-domain.com for site-wide cookies
| 'cookie_path'   =  Typically will be a forward slash
| 'cookie_secure' =  Cookies will only be set if a secure HTTPS connection exists.
|
*/
$config['cookie_prefix'] = "";
$config['cookie_domain'] = "";
$config['cookie_path'] = "/";
$config['cookie_secure'] = FALSE;
/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
*/
$config['global_xss_filtering'] = TRUE;
/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Enables a CSRF cookie token to be set. When set to TRUE, token will be
| checked on a submitted form. If you are accepting user data, it is strongly
| recommended CSRF protection be enabled.
|
| 'csrf_token_name' = The token name
| 'csrf_cookie_name' = The cookie name
| 'csrf_expire' = The number in seconds the token should expire.
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not 'echo' any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;
/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are 'local' or 'gmt'.  This pref tells the system whether to use
| your server's local time as the master 'now' reference, or convert it to
| GMT.  See the 'date helper' page of the user guide for information
| regarding date handling.
|
*/
$config['time_reference'] = 'local';
/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
*/
$config['rewrite_short_tags'] = FALSE;
/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which CodeIgniter should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = '';
/* End of file config.php */
/* Location: ./application/config/config.php */

$config["sch_AccNo_padding"] = "6";
$config["receipt_no_padding"] = "6";