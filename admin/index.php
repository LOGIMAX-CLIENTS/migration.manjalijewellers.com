<?php
// ── Maintenance Mode Gate ──────────────────────────────────────────
// During deploys, deploy-mono.sh touches .maintenance_active flag.
// Serve static maintenance page (no CI/DB needed) and exit early.
// Must run before global_configs include — config may be mid-swap.
$__maint_base = dirname(dirname(__DIR__));  // admin/ → repo root → prod/ (or staging/)
$__maint_flag = $__maint_base . '/.maintenance_active';
if (!file_exists($__maint_flag)) {
    // Staging layout: /var/www/{client}/staging/admin → base would be /var/www/{client}
    $__maint_flag = dirname($__maint_base) . '/.maintenance_active';
}
if (file_exists($__maint_flag)) {
    http_response_code(503);
    header('Retry-After: 120');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    // Prefer shared/ copy (always present), fall back to repo copy
    $__maint_page = dirname($__maint_base) . '/shared/maintenance.html';
    if (!file_exists($__maint_page)) {
        $__maint_page = $__maint_base . '/shared/maintenance.html';
    }
    if (!file_exists($__maint_page)) {
        $__maint_page = dirname(__DIR__) . '/maintenance.html';
    }
    if (file_exists($__maint_page)) {
        readfile($__maint_page);
    } else {
        echo '<h1>We\'ll be right back</h1><p>Scheduled maintenance in progress. Please try again in a few minutes.</p>';
    }
    exit;
}
// ── End Maintenance Gate ───────────────────────────────────────────

// Auto-load global_configs.php if present (server deployments symlink this from shared/)
// On servers: CLIENT_ID is set via Apache SetEnv → missing file is a fatal error
// Locally:   CLIENT_ID is not set → file won't exist, silently skip (database.php is configured directly)
if (file_exists(__DIR__ . '/../global_configs.php')) {
    require_once __DIR__ . '/../global_configs.php';
} elseif (getenv('CLIENT_ID')) {
    die('An error occurred. Please contact the administrator. <!-- cfg:' . getenv('CLIENT_ID') . ' -->');
}
/*
|--------------------------------------------------------------------------
| CORS HEADERS
|--------------------------------------------------------------------------
*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description, X-API-KEY');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}


/*

 *---------------------------------------------------------------

 * APPLICATION ENVIRONMENT

 *---------------------------------------------------------------

 *

 * You can load different configurations depending on your

 * current environment. Setting the environment also influences

 * things like logging and error reporting.

 *

 * This can be set to anything, but default usage is:

 *

 *     development

 *     testing

 *     production

 *

 * NOTE: If you change these, also change the error_reporting() code below

 *

 */
	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');

// ==========================================================================
// CLIENT_ID Resolution (Three-Layer Override System)
// Priority: 1. Apache/Nginx SetEnv  2. ?client= param (dev only)  3. 'default'
// Production: SetEnv CLIENT_ID ashoka (in Apache vhost)
// Testing:    ?client=ashoka (query parameter)
// ==========================================================================
$env_client = getenv('CLIENT_ID');
$query_client = isset($_GET['client']) ? $_GET['client'] : null;
$client_id = $env_client ?: $query_client ?: 'default';
define('CLIENT_ID', $client_id);
define('CLIENT_ID_FROM_QUERY', !$env_client && $query_client ? true : false);  // true only when resolved via ?client= param
define('ENABLE_OVERRIDES', true);  // Kill switch: set false to disable all overrides

/*

 *---------------------------------------------------------------

 * ERROR REPORTING

 *---------------------------------------------------------------

 *

 * Different environments will require different levels of error reporting.

 * By default development will show errors but testing and live will hide them.

 */



if (defined('ENVIRONMENT'))

{

	switch (ENVIRONMENT)

	{

		case 'development':
			ini_set("track_errors", 1);
			ini_set('display_errors', 1);
			ini_set("html_errors", 1);
			error_reporting(E_ALL);

		break;



		case 'testing':

		case 'production':

			error_reporting(0);

		break;



		default:

			exit('The application environment is not set correctly.');

	}

}



/*

 *---------------------------------------------------------------

 * SYSTEM FOLDER NAME

 *---------------------------------------------------------------

 *

 * This variable must contain the name of your "system" folder.

 * Include the path if the folder is not in the same  directory

 * as this file.

 *

 */

	$system_path = 'system';



/*

 *---------------------------------------------------------------

 * APPLICATION FOLDER NAME

 *---------------------------------------------------------------

 *

 * If you want this front controller to use a different "application"

 * folder then the default one you can set its name here. The folder

 * can also be renamed or relocated anywhere on your server.  If

 * you do, use a full server path. For more info please see the user guide:

 * http://codeigniter.com/user_guide/general/managing_apps.html

 *

 * NO TRAILING SLASH!

 *

 */

	$application_folder = 'application';



/*

 * --------------------------------------------------------------------

 * DEFAULT CONTROLLER

 * --------------------------------------------------------------------

 *

 * Normally you will set your default controller in the routes.php file.

 * You can, however, force a custom routing by hard-coding a

 * specific controller class/function here.  For most applications, you

 * WILL NOT set your routing here, but it's an option for those

 * special instances where you might want to override the standard

 * routing in a specific front controller that shares a common CI installation.

 *

 * IMPORTANT:  If you set the routing here, NO OTHER controller will be

 * callable. In essence, this preference limits your application to ONE

 * specific controller.  Leave the function name blank if you need

 * to call functions dynamically via the URI.

 *

 * Un-comment the $routing array below to use this feature

 *

 */

	// The directory name, relative to the "controllers" folder.  Leave blank

	// if your controller is not in a sub-folder within the "controllers" folder

	// $routing['directory'] = '';



	// The controller class file name.  Example:  Mycontroller

	// $routing['controller'] = '';



	// The controller function you wish to be called.

	// $routing['function']	= '';





/*

 * -------------------------------------------------------------------

 *  CUSTOM CONFIG VALUES

 * -------------------------------------------------------------------

 *

 * The $assign_to_config array below will be passed dynamically to the

 * config class when initialized. This allows you to set custom config

 * items or override any default config values found in the config.php file.

 * This can be handy as it permits you to share one application between

 * multiple front controller files, with each file containing different

 * config values.

 *

 * Un-comment the $assign_to_config array below to use this feature

 *

 */

	// $assign_to_config['name_of_config_item'] = 'value of config item';







// --------------------------------------------------------------------

// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE

// --------------------------------------------------------------------



/*

 * ---------------------------------------------------------------

 *  Resolve the system path for increased reliability

 * ---------------------------------------------------------------

 */



	// Set the current directory correctly for CLI requests

	if (defined('STDIN'))

	{

		chdir(dirname(__FILE__));

	}



	if (realpath($system_path) !== FALSE)

	{

		$system_path = realpath($system_path).'/';

	}



	// ensure there's a trailing slash

	$system_path = rtrim($system_path, '/').'/';



	// Is the system path correct?

	if ( ! is_dir($system_path))

	{

		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));

	}



/*

 * -------------------------------------------------------------------

 *  Now that we know the path, set the main path constants

 * -------------------------------------------------------------------

 */

	// The name of THIS file

	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));



	// The PHP file extension

	// this global constant is deprecated.

	define('EXT', '.php');



	// Path to the system folder

	define('BASEPATH', str_replace("\\", "/", $system_path));



	// Path to the front controller (this file)

	define('FCPATH', str_replace(SELF, '', __FILE__));



	// Name of the "system folder"

	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));





	// The path to the "application" folder

	if (is_dir($application_folder))

	{

		define('APPPATH', $application_folder.'/');

	}

	else

	{

		if ( ! is_dir(BASEPATH.$application_folder.'/'))

		{

			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);

		}



		define('APPPATH', BASEPATH.$application_folder.'/');

	}

	if( ! ini_get('date.timezone') )
	{
	   date_default_timezone_set("Asia/Calcutta");
	}

/*

 * --------------------------------------------------------------------

 * LOAD THE BOOTSTRAP FILE

 * --------------------------------------------------------------------

 *

 * And away we go...

 *

 */

require_once BASEPATH.'core/CodeIgniter.php';



/* End of file index.php */

/* Location: ./index.php */
