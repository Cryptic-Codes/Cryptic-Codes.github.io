<?php
ob_start();

chdir("/home/allml1/public_html/");

$start = microtime(true); 
 
define('PATH', '/'); 
define('ROOT_PATH', dirname(__FILE__));
$page = 'Home';

if(!ini_get('upload_tmp_dir')){
	$tmp_dir = sys_get_temp_dir();
} else {
	$tmp_dir = ini_get('upload_tmp_dir');
}

ini_set('open_basedir', ROOT_PATH . PATH_SEPARATOR  . $tmp_dir);

$directory = $_SERVER['REQUEST_URI'];
$page_path = $_SERVER['REQUEST_URI'];
if (preg_match("/\?/", $page_path)) {
	$page_path = substr($page_path, 0, strpos($page_path, "?"));
	$page_query = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "?") + 1);
}

$directories = explode("/", $directory);
$lim = count($directories);

$page_path = rtrim($page_path, '/');

chdir("/home/allml1/BloxAnalytics/");

if($page_path == "/errors" || $page_path == "/error_log" || $page_path == "/errors.txt" || $page_path == "/error_log.txt"){
    echo "<pre>";
    require("error_log");
    echo "</pre>";
    ob_end_flush();
    die();
}

chdir("/home/allml1/BloxAnalytics/");

require("sys/loader.php");

ob_end_flush();
?>