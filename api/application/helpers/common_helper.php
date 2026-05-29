<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Common functions used over the project
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Logimax Team
 */
// ------------------------------------------------------------------------
if ( ! function_exists('isValidVal')) {
	/**
	 * Check's whether given value is set & not empty. Returns TRUE if it does not contains null or empty value.
	 *
	 * @param any
	 * @return	bool
	 */
	function isValidVal($val)
	{
	    return (!isset($data) || trim($data)==='');
	}
}
if ( ! function_exists('get_values')) {
    /**
	 * Function to get post values
	 *
	 * @param any
	 * @return	any
	 */
    function get_values()
    {
		return (array)json_decode(file_get_contents('php://input'));
	}
}
if ( ! function_exists('filterArrByKeyVal')) {
    /**
	 * Function to filter multi dimensional array
	 *
	 * @param $arr - array to be filtered, $key - filter array based on key, $keyValue - filter array based on value
	 * @return	array
	 */
    function filterArrByKeyVal($arr, $key, $keyValue)
	{
	    return array_filter($arr, function($value) use ($key, $keyValue) {
	       return $value[$key] == $keyValue; 
	    });
	}
}
if ( ! function_exists('mail_htmlContent_helper')) {
	/**
	 * Return general HTML content for mail by passing email content message
	 *
	 * @param string
	 * @return	string
	 */
	function mail_htmlContent_helper($email_content)
	{
		$data = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title></title>
		</head>
		<body>
		<p>
		'.$email_content.'
		</p>
		</body>
		</html>';
		return $data;
	}
}
if ( ! function_exists('send_email_helper')) {
	/**
	 * Send email to given mail id
	 *
	 * @param string $email_to
	 * @param string $email_subject
	 * @param string $email_message
	 * @return bool
	 */
	function send_email_helper($email_to,$email_subject,$email_message,$email_cc = "") {
		$CI = get_instance();
		$company_name = "";
		$mail_server = "";
		$mail_password = "";
		/*$records = generalentry_helper();
		$company_name	= $records['admin_company_name'];
		$mail_server	= $records['admin_mail_server'];
		$mail_password	= $records['admin_mail_password'];*/
		$config = Array(
			'protocol'  => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => $mail_server,
			'smtp_pass' => $mail_password,
			'mailtype'  => 'html', 
			'charset'   => 'iso-8859-1'
			);
		$CI->load->library('email', $config);
		$CI->email->set_newline("\r\n");
		// Set to, from, message, etc.				
		$CI->email->from($mail_server, $company_name);
		$CI->email->to($email_to);
		if($email_cc != "") {
			$CI->email->cc($email_cc);
		}
		$CI->email->subject($email_subject);
		$CI->email->message($email_message);
		return $CI->email->send();
	}
}
if ( ! function_exists('db_errormessage')) {
	/**
	 * Return db error message
	 *
	 * @return	string or 0
	 */
	function db_errormessage($error_code)
	{
		$db_error_message = array (							
							"1000" => "hashchk",
							"1001" => "isamchk",
							"1002" => "NO",
							"1003" => "YES",
							"1004" => "Can't create file",
							"1005" => "Can't create table",
							"1006" => "Can't create database",
							"1007" => "Can't create database; database exists",
							"1008" => "Can't create database; database doesn't exists",
							"1009" => "Error dropping database",
							"1010" => "Error dropping database",
							"1011" => "Error on delete",
							"1012" => "Can't read record in system table",
							"1013" => "Can't get status",
							"1014" => "Can't get working directory",
							"1015" => "Can't lock file",
							"1016" => "Can't open file",
							"1017" => "Can't find file",
							"1018" => "Can't read dir",
							"1019" => "Can't change dir",
							"1020" => "Record has changed since last read in table",
							"1021" => "Disk full; waiting for someone to free some space",
							"1022" => "Can't write; duplicate key in table",
							"1023" => "Error on close",
							"1024" => "Error reading file",
							"1025" => "Error on rename",
							"1026" => "Error writing file",
							"1027" => "locked against change",
							"1028" => "Sort aborted",
							"1029" => "View doesn't exist",
							"1030" => "Got error storage engine",
							"1031" => "Table storage engine doesn't have this option",
							"1032" => "Can't find record",
							"1033" => "Incorrect information in file ",
							"1034" => "Incorrect key file for table; try to repair it",
							"1035" => "Old key file for table; repair it!",
							"1036" => "Table is read only",
							"1037" => "Out of memory; restart server and try again (needed  bytes)",
							"1038" => "Out of sort memory; increase server sort buffer size",
							"1039" => "Unexpected EOF found when reading file  ",
							"1040" => "Too many connections",
							"1041" => "Out of memory; check if mysql or some other process uses all available memory; if not, you may have to use 'ulimit' to allow mysql to use more memory or you can add more swap space",
							"1042" => "Can't get hostname for your address",
							"1043" => "Bad handshake",
							"1044" => "Access denied for user to database",
							"1045" => "Access denied for user",
							"1046" => "No database selected",
							"1047" => "Unknown command",
							"1048" => "Column cannot be null",
							"1049" => "Unknown database",
							"1050" => "Table already exists",
							"1051" => "Unknown table ",
							"1052" => "Column is ambiguous",
							"1053" => "Server shutdown in progress",
							"1054" => "Unknown column",
							"1055" => "GROUP BY not allowed",
							"1056" => "Can't group",
							"1057" => "Statement has sum functions and columns in same statement",
							"1058" => "Column count doesn't match value count",
							"1059" => "Identifier name is too long",
							"1060" => "Duplicate column name ",
							"1061" => "Duplicate key name ",
							"1062" => "Duplicate entry",
							"1063" => "Incorrect column specifier",
							"1064" => "Parse Error",
							"1065" => "Query was empty",
							"1066" => "Not unique table/alias",
							"1067" => "Invalid default value",
							"1068" => "Multiple primary key defined",
							"1069" => "Too many keys specified",
							"1070" => "Too many key parts specified",
							"1071" => "Specified key was too long",
							"1072" => "column doesn't exist in table",
							"1073" => "BLOB column",
							"1074" => "Column length too big; use BLOB or TEXT instead",
							"1075" => "Incorrect table definition; there can be only one auto column and it must be defined as a key",
							"1076" => "ready for connections.",
							"1077" => " Normal shutdown",
							"1078" => " Got signal",
							"1079" => " Shutdown complete",
							"1080" => " Forcing close",
							"1081" => "Can't create IP socket",
							"1082" => "Table has no index like the one used in CREATE INDEX; recreate the table",
							"1083" => "Field separator argument is not what is expected; check the manual",
							"1084" => "You can't use fixed rowlength with BLOBs; please use 'fields terminated by'",
							"1085" => "The file must be in the database directory or be readable by all",
							"1086" => "File already exists",
							"1087" => "Some Records have been Skipped",
							"1088" => "Duplicate Record",
							"1089" => "Incorrect sub part key; the used key part isn't a string, the used length is longer than the key part, or the storage engine doesn't support unique sub keys",
							"1090" => "You can't delete all columns with ALTER TABLE; use DROP TABLE instead",
							"1091" => "Can't DROP the Table",
							"1092" => "Duplicate Record",
							"1093" => "You can't specify target table for update in FROM clause",
							"1094" => "Unknown thread",
							"1095" => "You are not owner of thread",
							"1096" => "No tables used",
							"1097" => "Too many strings",
							"1098" => "Can't generate a unique log-filename",
							"1099" => "Table was locked with a READ lock and can't be updated",
							"1100" => "Table was not locked with LOCK TABLES",
							"1101" => "BLOB/TEXT column can't have a default value",
							"1102" => "Incorrect database name ",
							"1103" => "Incorrect table name ",
							"1104" => "The SELECT would examine more than MAX_JOIN_SIZE rows; check your WHERE and use SET SQL_BIG_SELECTS=1 or SET SQL_MAX_JOIN_SIZE=# if the SELECT is okay",
							"1105" => "Unknown error",
							"1106" => "Unknown procedure ",
							"1107" => "Incorrect parameter count to procedure ",
							"1108" => "Incorrect parameters to procedure ",
							"1109" => "Unknown table",
							"1110" => "Column specified twice",
							"1111" => "Invalid use of group function",
							"1112" => "Table uses an extension that doesn't exist in this MySQL version",
							"1113" => "A table must have at least 1 column",
							"1114" => "The table is full",
							"1115" => "Unknown character set",
							"1116" => "Too many tables can't join",
							"1117" => "Too many columns",
							"1118" => "Row size too large. You have to change some columns to TEXT or BLOBs",
							"1119" => "Thread stack overrun",
							"1120" => "Cross dependency found in OUTER JOIN; examine your ON conditions",
							"1121" => "Some Column is used with UNIQUE or INDEX but is not defined as NOT NULL",
							"1122" => "Can't load function ",
							"1123" => "Can't initialize function",
							"1124" => "No paths allowed for shared library",
							"1125" => "Function already exists",
							"1126" => "Can't open shared library",
							"1127" => "Can't find function from library",
							"1128" => "Function is not defined",
							"1129" => "Host is blocked because of many connection errors",
							"1130" => "Host is not allowed to connect to this MySQL server",
							"1131" => "You are using MySQL as an anonymous user and anonymous users are not allowed to change passwords",
							"1132" => "You must have privileges to update tables in the mysql database to be able to change passwords for others",
							"1133" => "Can't find any matching row in the user table",
							"1134" => "Some Row values are matched/Changed",
							"1135" => "Can't create a new thread",
							"1136" => "Column count doesn't match",
							"1137" => "Can't reopen table",
							"1138" => "Invalid use of NULL value",
							"1139" => "Got error from regexp",
							"1140" => "Mixing of GROUP columns (MIN(),MAX(),COUNT(),...) with no GROUP columns is illegal if there is no GROUP BY clause",
							"1141" => "There is no such grant defined for user",
							"1142" => "Table access denied to user",
							"1143" => "Column access denied to user",
							"1144" => "Illegal GRANT/REVOKE command; please consult the manual to see which privileges can be used",
							"1145" => "The host or user argument to GRANT is too long",
							"1146" => "Table doesn't exist",
							"1147" => "There is no such grant defined for user",
							"1148" => "The used command is not allowed with this MySQL version",
							"1149" => "You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use",
							"1150" => "Delayed insert thread couldn't get requested lock for table",
							"1151" => "Too many delayed threads in use",
							"1152" => "Aborted connection",
							"1153" => "Got a packet bigger than 'max_allowed_packet' bytes",
							"1154" => "Got a read error from the connection pipe",
							"1155" => "Got an error from fcntl()",
							"1156" => "Got packets out of order",
							"1157" => "Couldn't uncompress communication packet",
							"1158" => "Got an error reading communication packets",
							"1159" => "Got timeout reading communication packets",
							"1160" => "Got an error writing communication packets",
							"1161" => "Got timeout writing communication packets",
							"1162" => "Result string is longer than 'max_allowed_packet' bytes",
							"1163" => "The used table type doesn't support BLOB/TEXT columns",
							"1164" => "The used table type doesn't support AUTO_INCREMENT columns",
							"1165" => "INSERT DELAYED can't be used with table. because it is locked with LOCK TABLES",
							"1166" => "Incorrect column name",
							"1167" => "The used storage engine can't index column",
							"1168" => "Unable to open underlying table which is differently defined or of non-MyISAM type or doesn't exist",
							"1169" => "Can't write, because of unique constraint, to table ",
							"1170" => "BLOB/TEXT column used in key specification without a key length",
							"1171" => "All parts of a PRIMARY KEY must be NOT NULL; if you need NULL in a key, use UNIQUE instead",
							"1172" => "Result consisted of more than one row",
							"1173" => "This table type requires a primary key",
							"1174" => "This version of MySQL is not compiled with RAID support",
							"1175" => "You are using safe update mode and you tried to update a table without a WHERE that uses a KEY column",
							"1176" => "Key doesn't exist in table",
							"1177" => "Can't open table",
							"1178" => "The storage engine for the table doesn't support",
							"1179" => "You are not allowed to execute this command in a transaction",
							"1180" => "Got error during COMMIT",
							"1181" => "Got error during ROLLBACK",
							"1182" => "Got error during FLUSH_LOGS",
							"1183" => "Got error during CHECKPOINT",
							"1184" => "Aborted connection",
							"1185" => "The storage engine for the table does not support binary table",
							"1186" => "Binlog closed, cannot RESET MASTER",
							"1187" => "Failed rebuilding the index of dumped table",
							"1188" => "Error from master",
							"1189" => "Net error reading from master",
							"1190" => "Net error writing to master",
							"1191" => "Can't find FULLTEXT index matching the column list",
							"1192" => "Can't execute the given command because you have active locked tables or an active transaction",
							"1193" => "Unknown system variable ",
							"1194" => "Table is marked as crashed and should be repaired",
							"1195" => "Table is marked as crashed and last repair failed",
							"1196" => "Some non-transactional changed tables couldn't be rolled back",
							"1197" => "Multi-statement transaction required more than 'max_binlog_cache_size' bytes of storage; increase this mysqld variable and try again",
							"1198" => "This operation cannot be performed with a running slave; run STOP SLAVE first",
							"1199" => "This operation requires a running slave; configure slave and do START SLAVE",
							"1200" => "The server is not configured as slave; fix in config file or with CHANGE MASTER TO",
							"1201" => "Could not initialize master info structure; more error messages can be found in the MySQL error log",
							"1202" => "Could not create slave thread; check system resources",
							"1203" => "User already has active connection",
							"1204" => "You may only use constant expressions with SET",
							"1205" => "Lock wait timeout exceeded; try restarting transaction",
							"1206" => "The total number of locks exceeds the lock table size",
							"1207" => "Update locks cannot be acquired during a READ UNCOMMITTED transaction",
							"1208" => "DROP DATABASE not allowed while thread is holding global read lock",
							"1209" => "CREATE DATABASE not allowed while thread is holding global read lock",
							"1210" => "Incorrect arguments",
							"1211" => "no Permission for create new users",
							"1212" => "Incorrect table definition; all MERGE tables must be in the same database",
							"1213" => "Deadlock found when trying to get lock; try restarting transaction",
							"1214" => "The used table type doesn't support FULLTEXT indexes",
							"1215" => "Cannot add foreign key constraint",
							"1216" => "Cannot add or update a child row: a foreign key constraint fails",
							"1217" => "Cannot delete or update a parent row: a foreign key constraint fails",
							"1218" => "Error connecting to master",
							"1219" => "Error running query on master",
							"1220" => "Error when executing command",
							"1221" => "Incorrect usage",
							"1222" => "The used SELECT statements have a different number of columns",
							"1223" => "Can't execute the query because you have a conflicting read lock",
							"1224" => "Mixing of transactional and non-transactional tables is disabled",
							"1225" => "Option used twice in statement",
							"1226" => "User has exceeded the resource",
							"1227" => "Access denied; you need the privilege for this operation",
							"1228" => "Variable is a SESSION variable and can't be used with SET GLOBAL",
							"1229" => "Variable is a GLOBAL variable and should be set with SET GLOBAL",
							"1230" => "Variable doesn't have a default value",
							"1231" => "Variable can't be set to the value",
							"1232" => "Incorrect argument type to variable",
							"1233" => "Variable can only be set, not read",
							"1234" => "Incorrect usage/placement",
							"1235" => "This version of MySQL doesn't yet support",
							"1236" => "Fatal error",
							"1237" => "Slave SQL thread ignored the query because of replicate table rules",
							"1238" => "Incorrect variable",
							"1239" => "Incorrect foreign key definition",
							"1240" => "Key reference and table reference don't match",
							"1241" => "Operand should contain column(s)",
							"1242" => "Subquery returns more than 1 row",
							"1243" => "Unknown prepared statement handler",
							"1244" => "Help database is corrupt or does not exist",
							"1245" => "Cyclic reference on subqueries",
							"1246" => "Converting column Error",
							"1247" => "Reference not supported",
							"1248" => "Every derived table must have its own alias",
							"1249" => "Select statement was reduced during optimization",
							"1250" => "Table name not allowed here",
							"1251" => "Client does not support authentication protocol requested by server; consider upgrading MySQL client",
							"1252" => "All parts of a SPATIAL index must be NOT NULL",
							"1253" => "COLLATION is not valid for CHARACTER SET",
							"1254" => "Slave is already running",
							"1255" => "Slave already has been stopped",
							"1256" => "Uncompressed data size too large",
							"1257" => "ZLIB: Not enough memory",
							"1258" => "ZLIB: Not enough room in the output buffer",
							"1259" => "ZLIB: Input data corrupted",
							"1260" => "line(s) were cut by GROUP_CONCAT()",
							"1261" => "Record doesn't contain data for all columns",
							"1262" => "Record was truncated; it contained more data than there were input columns",
							"1263" => "Column was set to data type implicit default",
							"1264" => "Value was Out of range",
							"1265" => "Data truncated",
							"1266" => "Using storage engine for table",
							"1267" => "Illegal mix of collations",
							"1268" => "Cannot drop one or more of the requested users",
							"1269" => "Can't revoke all privileges for one or more of the requested users",
							"1270" => "Illegal mix of collations for operation",
							"1271" => "Illegal mix of collations for operation",
							"1272" => "Variable is not a struct variable",
							"1273" => "Unknown collation",
							"1274" => "SSL parameters in CHANGE MASTER are ignored because this MySQL slave was compiled without SSL support; they can be used later if MySQL slave with SSL is started",
							"1275" => "Server is running in secure mode, but password in the old format; please change the password to the new format",
							"1276" => "Field or reference was resolved",
							"1277" => "Incorrect parameter or combination of parameters for START SLAVE UNTIL",
							"1278" => "It is recommended to use -skip-slave-start when doing step-by-step replication with START SLAVE UNTIL; otherwise, you will get problems if you get an unexpected slave's mysqld restart",
							"1279" => "SQL thread is not to be started so UNTIL options are ignored",
							"1280" => "Incorrect index name",
							"1281" => "Incorrect catalog name",
							"1282" => "Query cache failed to set size",
							"1283" => "Column cannot be part of FULLTEXT index",
							"1284" => "Unknown key cache",
							"1285" => "MySQL is started in -skip-name-resolve mode; you must restart it without this switch for this grant to work",
							"1286" => "Unknown table engine",
							"1287" => "deprecated Syntax Error",
							"1288" => "The target table is not updatable",
							"1289" => "The feature is disabled",
							"1290" => "The MySQL server is running with Prevent statement. so it cannot execute this statement",
							"1291" => "Column has duplicated value",
							"1292" => "Truncated incorrect value",
							"1293" => "Incorrect table definition; there can be only one TIMESTAMP column with CURRENT_TIMESTAMP in DEFAULT or ON UPDATE clause",
							"1294" => "Invalid ON UPDATE column",
							"1295" => "This command is not supported in the prepared statement protocol yet",
							"1296" => "Got error",
							"1297" => "Got temporary error",
							"1298" => "Unknown or incorrect time zone",
							"1299" => "Invalid TIMESTAMP value",
							"1300" => "Invalid character string",
							"1301" => "Result was larger than max_allowed_packet",
							"1302" => "Conflicting declarations",
							"1303" => "Can't create another stored routine",
							"1304" => "Record already exists",
							"1305" => "Record does not exist",
							"1306" => "Failed to DROP",
							"1307" => "Failed to CREATE",
							"1308" => "no matching label",
							"1309" => "Redefining label",
							"1310" => "End-label without match",
							"1311" => "Referring to uninitialized variable",
							"1312" => "PROCEDURE can't return a result set in the given context",
							"1313" => "RETURN is only allowed in a FUNCTION",
							"1314" => "Statement is not allowed in stored procedures",
							"1315" => "The update log is deprecated and replaced by the binary log; SET SQL_LOG_UPDATE has been ignored",
							"1316" => "The update log is deprecated and replaced by the binary log; SET SQL_LOG_UPDATE has been translated to SET SQL_LOG_BIN",
							"1317" => "Query execution was interrupted",
							"1318" => "Incorrect number of arguments",
							"1319" => "Undefined CONDITION",
							"1320" => "No RETURN found in FUNCTION",
							"1321" => "FUNCTION ended without RETURN",
							"1322" => "Cursor statement must be a SELECT",
							"1323" => "Cursor SELECT must not have INTO",
							"1324" => "Undefined CURSOR",
							"1325" => "Cursor is already open",
							"1326" => "Cursor is not open",
							"1327" => "Undeclared variable",
							"1328" => "Incorrect number of FETCH variables",
							"1329" => "No data - zero rows fetched, selected, or processed",
							"1330" => "Duplicate parameter",
							"1331" => "Duplicate variable",
							"1332" => "Duplicate condition",
							"1333" => "Duplicate cursor",
							"1334" => "Failed to ALTER",
							"1335" => "Subselect value not supported",
							"1336" => "Statement is not allowed in stored function or trigger",
							"1337" => "Variable or condition declaration after cursor or handler declaration",
							"1338" => "Cursor declaration after handler declaration",
							"1339" => "Case not found for CASE statement",
							"1340" => "Configuration file is too big",
							"1341" => "Malformed file type header in file",
							"1342" => "Unexpected end of file while parsing comment",
							"1343" => "Error while parsing parameter",
							"1344" => "Unexpected end of file while skipping unknown parameter",
							"1345" => "EXPLAIN/SHOW can not be issued; lacking privileges for underlying table",
							"1346" => "File has unknown type in its header",
							"1347" => "Wrong Object",
							"1348" => "Column is not updatable",
							"1349" => "View's SELECT contains a subquery in the FROM clause",
							"1350" => "View's SELECT contains a clause",
							"1351" => "View's SELECT contains a variable or parameter",
							"1352" => "View's SELECT refers to a temporary table",
							"1353" => "View's SELECT and view's field list have different column counts",
							"1354" => "View merge algorithm can't be used here for now",
							"1355" => "View being updated does not have complete key of underlying table in it",
							"1356" => "View references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them",
							"1357" => "Can't drop or alter within another stored routine",
							"1358" => "GOTO is not allowed in a stored procedure handler",
							"1359" => "Trigger already exists",
							"1360" => "Trigger does not exist",
							"1361" => "Trigger's is view or temporary table",
							"1362" => "Updating of row is not allowed in trigger",
							"1363" => "There is no row in trigger",
							"1364" => "Field doesn't have a default value",
							"1365" => "Division by 0",
							"1366" => "Incorrect value",
							"1367" => "Illegal value found during parsing",
							"1368" => "CHECK OPTION on non-updatable view",
							"1369" => "CHECK OPTION failed",
							"1370" => "command denied to user",
							"1371" => "Failed purging old relay logs",
							"1372" => "Password hash should be a digit hexadecimal number",
							"1373" => "Target log not found in binlog index",
							"1374" => "I/O error reading log index file",
							"1375" => "Server configuration does not permit binlog purge",
							"1376" => "Failed on fseek()",
							"1377" => "Fatal error during log purge",
							"1378" => "A purgeable log is in use, will not purge",
							"1379" => "Unknown error during log purge",
							"1380" => "Failed initializing relay log position",
							"1381" => "You are not using binary logging",
							"1382" => "The syntax is reserved for purposes internal to the MySQL server",
							"1383" => "WSAStartup Failed",
							"1384" => "Can't handle procedures with different groups yet",
							"1385" => "Select must have a group with this procedure",
							"1386" => "Can't use ORDER clause with this procedure",
							"1387" => "Binary logging and replication forbid changing the global server",
							"1388" => "Can't map file",
							"1389" => "Wrong magic",
							"1390" => "Prepared statement contains too many placeholders",
							"1391" => "Key part length cannot be 0",
							"1392" => "View text checksum failed",
							"1393" => "Can not modify more than one base table through a join view",
							"1394" => "Can not insert into join view without fields list",
							"1395" => "Can not delete from join view",
							"1396" => "Operation failed",
							"1397" => "XAER_NOTA: Unknown XID",
							"1398" => "XAER_INVAL: Invalid arguments (or unsupported command)",
							"1399" => "XAER_RMFAIL: The command cannot be executed when global transaction is in the state",
							"1400" => "XAER_OUTSIDE: Some work is done outside global transaction",
							"1401" => "XAER_RMERR: Fatal error occurred in the transaction branch - check your data for consistency",
							"1402" => "XA_RBROLLBACK: Transaction branch was rolled back",
							"1403" => "There is no such grant defined for user on host on routine ",
							"1404" => "Failed to grant EXECUTE and ALTER ROUTINE privileges",
							"1405" => "Failed to revoke all privileges to dropped routine",
							"1406" => "Data too long",
							"1407" => "Bad SQLSTATE",
							"1408" => "Ready for connections",
							"1409" => "Can't load value from file with fixed size rows to variable",
							"1410" => "You are not allowed to create a user with GRANT",
							"1411" => "Incorrect value",
							"1412" => "Table definition has changed, please retry transaction",
							"1413" => "Duplicate handler declared in the same block",
							"1414" => "OUT or INOUT argument for routine is not a variable or NEW pseudo-variable in BEFORE trigger",
							"1415" => "Not allowed to return a result set",
							"1416" => "Cannot get geometry object from data you send to the GEOMETRY field",
							"1417" => "A routine failed and has neither NO SQL nor READS SQL DATA in its declaration and binary logging is enabled; if non-transactional tables were updated, the binary log will miss their changes",
							"1418" => "This function has none of DETERMINISTIC, NO SQL, or READS SQL DATA in its declaration and binary logging is enabled (you *might* want to use the less safe log_bin_trust_function_creators variable)",
							"1419" => "You do not have the SUPER privilege and binary logging is enabled (you *might* want to use the less safe log_bin_trust_function_creators variable)",
							"1420" => "You can't execute a prepared statement which has an open cursor associated with it. Reset the statement to re-execute it.",
							"1421" => "The statement has no open cursor",
							"1422" => "Explicit or implicit commit is not allowed in stored function or trigger",
							"1423" => "Field of view underlying table doesn't have a default value",
							"1424" => "Recursive stored functions and triggers are not allowed",
							"1425" => "Too big scale specified for column",
							"1426" => "Too big precision specified for column",
							"1427" => "For float(M,D), double(M,D) or decimal(M,D), M must be >= D (column )",
							"1428" => "You can't combine write-locking of system table with other tables",
							"1429" => "Unable to connect to foreign data source",
							"1430" => "There was a problem processing the query on the foreign data source. Data source error",
							"1431" => "The foreign data source you are trying to reference does not exist. Data source error",
							"1432" => "Can't create federated table. The data source connection string is not in the correct format",
							"1433" => "The data source connection string is not in the correct format",
							"1434" => "Can't create federated table. Foreign data src error",
							"1435" => "Trigger in wrong schema",
							"1436" => "Thread stack overrun",
							"1437" => "Routine body is too long",
							"1438" => "Cannot drop default keycache",
							"1439" => "Display width out of range for column",
							"1440" => "XAER_DUPID: The XID already exists",
							"1441" => "Datetime function: field overflow",
							"1442" => "Can't update table in stored function/trigger because it is already used by statement which invoked this stored function/trigger.",
							"1443" => "The definition of table prevents operation on table",
							"1444" => "The prepared statement contains a stored routine call that refers to that same statement. It's not allowed to execute a prepared statement in such a recursive manner",
							"1445" => "Not allowed to set autocommit from a stored function or trigger",
							"1446" => "Definer is not fully qualified",
							"1447" => "View has no definer information (old table format). Current user is used as definer. Please recreate the view!",
							"1448" => "You need the SUPER privilege for creation view with definer",
							"1449" => "There is no registered",
							"1450" => "Changing schema is not allowed",
							"1451" => "Cannot delete or update a parent row",
							"1452" => "Cannot add or update a child row",
							"1453" => "Variable  must be quoted with `...`, or renamed",
							"1454" => "No definer attribute for trigger .. The trigger will be activated under the authorization of the invoker, which may have insufficient privileges. Please recreate the trigger.",
							"1455" => " has an old format, you should re-create the object(s)",
							"1456" => "Recursive limit  (as set by the max_sp_recursion_depth variable) was exceeded for routine",
							"1457" => "Failed to load routine. The table mysql.proc is missing, corrupt, or contains bad data (internal code )",
							"1458" => "Incorrect routine name ",
							"1459" => "Table upgrade required.",
							"1460" => "AGGREGATE is not supported for stored functions",
							"1461" => "Can't create more than max_prepared_stmt_count statements",
							"1462" => "Record contains view recursion",
							"1463" => "non-grouping field is used",
							"1464" => "The used table type doesn't support SPATIAL indexes",
							"1465" => "Triggers can not be created on system tables",
							"1466" => "Leading spaces are removed from name",
							"1467" => "Failed to read auto-increment value from storage engine",
							"1468" => "user name",
							"1469" => "host name",
							"1470" => "String is too long",
							"1471" => "The target table is not insertable",
							"1472" => "Table is differently defined or of non-MyISAM type or doesn't exist",
							"1473" => "Too high level of nesting for select",
							"1474" => "Name has become ''(Empty)",
							"1475" => "First character of the FIELDS TERMINATED string is ambiguous; please use non-optional and non-empty FIELDS ENCLOSED BY",
							"1476" => "Invalid column reference in LOAD DATA",
							"1477" => "Being purged log was not found",
							"1478" => "XA_RBTIMEOUT: Transaction branch was rolled back: took too long",
							"1479" => "XA_RBDEADLOCK: Transaction branch was rolled back: deadlock was detected",
							"1480" => "Too many active concurrent transactions"
							);
		if(array_key_exists($error_code, $db_error_message)) {
			return $db_error_message[$error_code];
		}
		return "0";
	}
}