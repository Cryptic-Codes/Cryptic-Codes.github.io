<?php
include_once 'part_util.php';

$out = array();
header("Content-Type: application/json");

$args = explode('/', $page_path);
$args = array_slice($args, 2);


$config = json_decode('{
    "db": {
        "type": "mysql",
        "username": "allml1_blox",
        "password": "AcyJzMwpguLta80TFS",
        "database": "allml1_blox",
        "host": "localhost",
        "port": "3306",
        "charset": "utf8",
        "prefix": "b_"
    }
}', true);
$db = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['database']};port={$config['db']['port']};charset={$config['db']['charset']}", $config['db']['username'], $config['db']['password']);
if(isset($_POST['uid'])) {
    $apiKey = $_POST['apiKey'];
    
    $user = array();
    
    $user['uid'] = $_POST['uid'];
    $user['token'] = $_POST['token'];
    $user['email'] = $_POST['email'];
    $user['emailVerified'] = ($_POST['emailVerified'] === 'true' ? true : false);

    $q = "SELECT * FROM {$config['db']['prefix']}users WHERE `uid` ='{$user['uid']}'";
    $r = $db->query($q);
    if($r->rowCount() != 0) {
        $userRow = $r->fetch();
        $user['roblox'] = intval($userRow['roblox']);
        $user['isLinked'] = ($user['roblox'] != null);
        $user['role'] = $userRow['role'];
        $user['email'] = $userRow['email'];
    } else if(isset($_POST['token']) && isset($_POST['email']) && isset($_POST['emailVerified'])) {
        $p = $db->prepare("INSERT INTO `b_users` (`uid`, `email`, `role`, `roblox`) VALUES (:uid, :email, DEFAULT, NULL)");
        $p->bindParam(':uid', $user['uid'], PDO::PARAM_STR);
        $p->bindParam(':email', $user['email'], PDO::PARAM_STR);
        $p->execute();
        $out['sqlError'] = $p->errorInfo();
        $user['role'] = 'User';
        $user['roblox'] = null;
    }
    $out['user'] = $user;
}

function get_user_places() {
    global $user;
    global $db;
    $out = array();
    $roblox = $user['roblox'];
    if($roblox != null) {
        $q = "SELECT placeId FROM b_places WHERE `creator` ='{$roblox}'";
        $r = $db->query($q);
        $rows = $r->fetchAll(PDO::FETCH_ASSOC);
        
        $arr = array();
        
        if($r->rowCount() != 0) {
            foreach($rows as $place) {
                array_push($arr, $place['placeId']);
            }
        }
        
        $out['places'] = $arr;
        $out['totalPlaces'] = $r->rowCount();
    } else {
        $out['places'] = array();
        $out['totalPlaces'] = 0;
    }
    return $out;
}

$out['serverOpen'] = false;

if($user && $user['isLinked']){
    if(count($args) >= 3 || (count($args) >= 2 && strtolower($args[1]) === "all")) {
        if(strpos($args[0], '-') !== false) {
            $q = "SELECT * FROM b_servers WHERE `id`='{$args[0]}'";
            $r = $db->query($q);
            $server = $r->fetch(PDO::FETCH_ASSOC);
            if($r->rowCount() != 0) {
                $q = "SELECT * FROM b_places WHERE `creator`='{$user['roblox']}' AND `placeId`='{$server['placeId']}'";
                $r = $db->query($q);
                $place = $r->fetch(PDO::FETCH_ASSOC);
                if($r->rowCount() != 0) {
                    $out['server'] = $server;
                    $out['server']['placeId'] = intval($server['placeId']);
                    $out['server']['isOpen'] = boolval($server['isOpen']);
                    $out['serverOpen'] = boolval($server['isOpen']);
                    switch(strtolower($args[1])) {
                        case "all":
                            $qm = "SELECT id, time, sender, recipient, content, server, username FROM b_chatMessages WHERE `server`='{$args[0]}'";
                            $rm = $db->query($qm);
                            if($rm) {
                                $out['code'] = 200;
                                $out['message'] = "Messages Found";
                                $out['messages'] = $rm->fetchAll(PDO::FETCH_ASSOC);
                            } else {
                                $out = get_error(500);
                            }
                            break;
                        case "before":
                            $qm = "SELECT id, time, sender, recipient, content, username FROM b_chatMessages WHERE `server`='{$args[0]}' AND id < {$args[2]};";
                            $rm = $db->query($qm);
                            if($rm) {
                                $out['code'] = 200;
                                $out['message'] = "Messages Found";
                                $out['messages'] = $rm->fetchAll(PDO::FETCH_ASSOC);
                            } else {
                                $out = get_error(500);
                            }
                            break;
                        case "after":
                            $qm = "SELECT id, time, sender, recipient, content, username FROM b_chatMessages WHERE `server`='{$args[0]}' AND id > {$args[2]};";
                            $rm = $db->query($qm);
                            if($rm) {
                                $out['code'] = 200;
                                $out['message'] = "Messages Found";
                                $out['messages'] = $rm->fetchAll(PDO::FETCH_ASSOC);
                            } else {
                                $out = get_error(500);
                            }
                            break;
                        default:
                            $out = get_error(401);
                            break;
                    }
                } else {
                    $out = get_error(401);
                }
            } else {
                $out = get_error(404);
            }
        } else {
            $q = "SELECT * FROM b_places WHERE `creator`='{$user['roblox']}' AND `placeId`='{$args[0]}'";
            $r = $db->query($q);
            $place = $r->fetch(PDO::FETCH_ASSOC);
            if($r->rowCount() != 0) {
                $out['place']['placeId'] = intval($place['placeId']);
                $out['place']['creator'] = intval($place['creator']);
                $out['place']['addTime'] = $place['addTime'];
                switch(strtolower($args[1])) {
                    case "all":
                        $qm = "SELECT id, time, sender, recipient, content, server, username FROM b_chatMessages WHERE `placeId`='{$args[0]}'";
                        $rm = $db->query($qm);
                        if($rm) {
                            $out['code'] = 200;
                            $out['message'] = "Messages Found";
                            $out['messages'] = $rm->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            $out = get_error(500);
                        }
                        break;
                    case "before":
                        $qm = "SELECT id, time, sender, recipient, content, username FROM b_chatMessages WHERE `placeId`='{$args[0]}' AND id < {$args[2]};";
                        $rm = $db->query($qm);
                        if($rm) {
                            $out['code'] = 200;
                            $out['message'] = "Messages Found";
                            $out['messages'] = $rm->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            $out = get_error(500);
                        }
                        break;
                    case "after":
                        $qm = "SELECT id, time, sender, recipient, content, username FROM b_chatMessages WHERE `placeId`='{$args[0]}' AND id > {$args[2]};";
                        $rm = $db->query($qm);
                        if($rm) {
                            $out['code'] = 200;
                            $out['message'] = "Messages Found";
                            $out['messages'] = $rm->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            $out = get_error(500);
                        }
                        break;
                    default:
                        $out = get_error(401);
                        break;
                }
            } else {
                $out = get_error(401);
            }
        }
    } else {
        $out = get_error(400);
    }
} else {
    $out = get_error(401);
}

if((strtolower(end($args)) === "min" || boolval($_REQUEST['min'])) && strtolower($_REQUEST['min']) !== "false") {
    unset($out['user']);
    unset($out['place']);
    unset($out['server']);
}

echo json_encode($out);
