<?php 
ob_start();
error_reporting(0);
session_start();
define('mySERVER', 'mysql.cms.gre.ac.uk');
define('myUSERNAME', 'sk9699a');
define('myPASSWORD', 'Pasha1996');
define('myDATABASE', 'mdb_sk9699a');


$db= mysqli_connect(mySERVER,myUSERNAME,myPASSWORD,myDATABASE);

$site_url =  "http://".$_SERVER['HTTP_HOST'];
$site_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
define('SITE_URL', $site_url);

$base_folder_name = str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
define('FOLDER_PATH', $_SERVER['DOCUMENT_ROOT'].$base_folder_name);

define('LIST_COUNT', 10);

if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

?>   



