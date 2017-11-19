<?php

require('sys/part_util.php');

$out         = array();
$out['took'] = 0;
// $out['request'] = $request;
// $out['requestQ'] = $requestQuery;
header("Content-Type: application/json");

$args = explode('/', $page_path);
$args = array_slice($args, 2);

$out['app'] = $config['app'];

// $db = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['database']};port={$config['db']['port']};charset={$config['db']['charset']}", $config['db']['username'], $config['db']['password']);

function get_user_places() {
	
    global $user;
    global $db;
    $out    = array();
    $roblox = $user['roblox'];
    if ($roblox != null) {
        $q    = "SELECT placeId FROM b_places WHERE `creator` ='{$roblox}'";
        $r    = $db->query($q);
        $rows = $r->fetchAll(PDO::FETCH_ASSOC);
        
        $arr = array();
        
        if ($r->rowCount() != 0) {
            foreach ($rows as $place) {
                array_push($arr, $place['placeId']);
            }
        }
        
        $out['places']      = $arr;
        $out['totalPlaces'] = $r->rowCount();
    } else {
        $out['places']      = array();
        $out['totalPlaces'] = 0;
    }
    return $out;
}

switch (strtolower($args[0])) {
    case 'data':
        require("sys/data.php");
        break;
    case 'roblox':
        require("sys/roblox.php");
        break;
    case 'page':
        require("sys/page.php");
        break;
    case 'test':
        require("sys/test.php");
        break;
    case 'notifications':
		header("Content-Type: text/html");
        require("sys/notifications.php");
        die();
        break;
    default:
        $out = get_error(404);
        break;
}

if (empty($out)) {
    $out = get_404_Page();
}

if ($user) {
    $out['user'] = array(
        'uid' => $user['uid'],
        'roblox' => $user['roblox'],
        'role' => $user['role']
    );
}

global $startRequest;
$out['took'] = microtime(true) * 1000 - $startRequest;
header('x-time-took: ' . $out['took']);

if (boolval($_REQUEST['min'])) {
    unset($out['user']);
    unset($out['app']);
    unset($out['message']);
    unset($out['took']);
}

echo json_encode($out);
$len = ob_get_length();
header('Content-Length: ' . $len);
ob_end_flush();