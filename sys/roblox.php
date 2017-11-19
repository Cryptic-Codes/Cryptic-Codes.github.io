<?php

$in = json_decode(file_get_contents("php://input"), true);
unset($out['app']);
switch(strtolower($args[1])) {
    case 'dump':
        $pl = $in['payload'];
        $_REQUEST = array_merge($_REQUEST, $in);
    
        $q = "UPDATE `b_servers` SET `heartbeat`=CURRENT_TIMESTAMP WHERE `token` ='{$in['server']['token']}'";
        $r = $db->query($q);
        $q = "SELECT * FROM b_servers WHERE `token` ='{$in['server']['token']}';";
        $r = $db->query($q);
        if($r->rowCount() != 0) {
            $serverRow = $r->fetch();
            
            $out['code'] = 200;
            $out['message'] = "Dump recived";
            
            if(isset($pl['sentChatMessage'])) {
                foreach($pl['sentChatMessage'] as $msg) {
                    $time = date("Y-m-d H:i:s", intval($msg['time']));
                    $q = "INSERT INTO `b_chatMessages` (`id`, `sender`, `username`, `recipient`, `content`, `server`, `placeId`, `time`) VALUES (DEFAULT, '{$msg['from']}', '{$msg['username']}', '{$msg['to']}', :content, '{$in['server']['uuid']}', '{$in['server']['placeId']}', '{$time}')";
                    $p = $db->prepare($q);
                    $p->bindParam(':content', $msg['text'], PDO::PARAM_STR);
                    $p->execute();
                }
            }
            
            if(isset($pl['error'])) {
                foreach($pl['error'] as $msg) {
                    $time = date("Y-m-d H:i:s", intval($msg['time']));
                    $q = "INSERT INTO `b_chatMessages` (`id`, `sender`, `username`, `recipient`, `content`, `server`, `placeId`, `time`) VALUES (DEFAULT, '0', '{$msg['script']}', '{$msg['trace']}', :content, '{$in['server']['uuid']}', '{$in['server']['placeId']}', '{$time}')";
                    $p = $db->prepare($q);
                    $p->bindParam(':content', $msg['message'], PDO::PARAM_STR);
                    $p->execute();
                }
            }
            
            if(isset($pl['playerJoin'])) {
                foreach($pl['playerJoin'] as $event) {
                    $time = date("Y-m-d H:i:s", intval($event['time']));
                    $q = "INSERT INTO `b_joinHistory` (`id`, `userId`, `placeId`, `serverInstance`, `server`, `joinTime`, `leaveTime`) VALUES (DEFAULT, '{$event['userId']}', '{$in['server']['placeId']}', '{$event['instance']}', '{$in['server']['uuid']}', '{$time}', NULL)";
                    $p = $db->prepare($q);
                    $p->execute();
                    $out['sql'] = $p->errorInfo();
                }
            }
            
            if(isset($pl['playerLeave'])) {
                foreach($pl['playerLeave'] as $event) {
                    $time = date("Y-m-d H:i:s", intval($event['time']));
                    $q = "UPDATE `b_joinHistory` SET `leaveTime`='{$time}' WHERE `server`='{$in['server']['uuid']}' AND `userId`='{$event['userId']}' AND `leaveTime` is NULL";
                    $p = $db->prepare($q);
                    $p->execute();
                }
            }
            
            // Build return payload
            
            $out['payload'] = array();

            // TODO: Instead of deleteing, update a column named `executed` to '1'
            $q = "SELECT * FROM `b_queue` WHERE `server`='{$in['server']['uuid']}' AND `executed`=0; UPDATE `b_queue` SET `executed`=1 WHERE `server`='{$in['server']['uuid']}';";
            $r = $db->query($q);
            $rows = $r->fetchAll(PDO::FETCH_ASSOC);
            if($r->rowCount() != 0) {
                foreach($rows as $row) {
                    if(!isset($out['payload'][$row['task']])) {
                        $out['payload'][$row['task']] = array();
                    }
                    array_push($out['payload'][$row['task']], json_decode($row['data']));
                }
            }
        } else {
            $out = get_error(404);
            break;
        }
        break;
    case 'newserver':
        if(isset($in['placeId']) && isset($in['apiKey'])) {
            $q = "SELECT * FROM b_places WHERE `placeId`='{$in['placeId']}'";
            $p = $db->prepare($q);
            $p->execute();
            if($p->rowCount() == 0) {
                $asset = json_decode(file_get_contents("http://api.roblox.com/marketplace/productinfo?assetId=" . $in['placeId']), true);
                $creator = $asset['Creator']['Id'];
                $q = "INSERT INTO `b_places` (`placeId`, `creator`, `addTime`, `settings`) VALUES ('{$in['placeId']}', '{$creator}', CURRENT_TIMESTAMP, '{}')";
                $r = $db->query($q);
                $out['creator'] = $creator;
            }
            $placeRow = $p->fetch(PDO::FETCH_ASSOC);
            
            
            $uuid = UUID::v4();
            $lenSeed = 156;
            $place = $in['placeId'];
            $token = random_str($lenSeed) . '.' . random_str($lenSeed*2) . '.' . random_str(($lenSeed/2)+1);
            $api = $in['apiKey'];
            
            $q = "INSERT INTO `{$config['db']['prefix']}servers` (`id`, `placeId`, `token`, `startTime`, `isOpen`) VALUES ('{$uuid}', '{$place}', '{$token}', CURRENT_TIMESTAMP, '1')";
            $r = $db->query($q);
            if($r->errorInfo()[0] === "00000") {
                $out['code'] = 200;
                $out['message'] = 'Server created';
                $out['server']['uuid'] = $uuid;
                $out['server']['placeId'] = $in['placeId'];
                $out['server']['token'] = $token;
                $out['server']['apiKey'] = $api;
                $out['server']['settings'] = json_decode($placeRow['settings']);
            } else {
                $out['code'] = 500;
                $out['message'] = 'Server database error!';
                $out['server']['uuid'] = 'ERROR!!';
            }
        } else {
            $out = get_error(400);
            break;
        }
        break;
    case 'closeserver':
        $new_uuid = UUID::v4();
        $lenSeed = 156;
        if(isset($in['server']) && isset($in['server']['apiKey']) && isset($in['server']['token']) && isset($in['server']['uuid'])) {
            $uuid = $in['server']['uuid'];
            $place = $in['server']['placeId'];
            $token = $in['server']['token'];
            $q = "UPDATE `b_servers` SET `token`='server closed',`isOpen`='0' WHERE `id`='{$uuid}'";
            $r = $db->query($q);
            if($r->errorInfo()[0] === "00000") {
                $bundle = json_decode(httpPost("https://blox.al1l.com/__/data/bundle", array(
                    'uid' => '6GG52hOD0nY57bsoneRjnIooFeD3',
                    'serverId' => $uuid,
                    'min' => true,
                    'select' => '*'
                )), true);
                
                // Save bundle
                $p = $db->prepare("INSERT INTO `b_bundles`(`id`, `placeId`, `server`, `data`) VALUES (NULL, '{$place}', '{$uuid}', :data)");
                $p->bindParam(":data", json_encode($bundle['bundle']), PDO::PARAM_STR);
                $p->execute();
                
                $q  = "DELETE FROM `b_chatMessages` WHERE `server`='{$uuid}'; ";
                $q .= "DELETE FROM `b_joinHistory` WHERE `server`='{$uuid}'; ";
                $q .= "DELETE FROM `b_queue` WHERE `server`='{$uuid}'; ";
                $q .= "DELETE FROM `b_servers` WHERE `id`='{$uuid}'; ";
                $r = $db->query($q);
                
                $out['bundle'] = $bundle['bundle'];
                if($bundle['code'] = 200) {
                    $out['code'] = 200;
                    $out['message'] = 'Server closed';
                    $out['server'] = $in['server'];
                } else {
                    $out['code'] = 200500;
                    $out['message'] = 'Server closed, but could not save bundle!';
                    $out['server'] = $in['server'];
                    error_log("Could not save bundle for server {$uuid}!");
                }
            } else {
                $out = get_error(500);
                break;
            }
        } else {
            $out = get_error(400);
            break;
        }
        break;
    case 'verifyaccount':
        if($in['token'] === "MQvSB93I8PfklARqCS0yuB73omilLGksRjUfCEM7Sitlu5nObR6z7C5BmyzCNSP4") {
            $q = "SELECT * FROM {$config['db']['prefix']}users WHERE `email` ='{$in['email']}'";
            $r = $db->query($q);
            if($r->rowCount() != 0) {
                $userRow = $r->fetch();
                if(isset($in['userId']) && isset($in['placeId']) && isset($in['gameId']) && isset($in['email'])) {
                    $roblox_user = json_decode(file_get_contents("https://api.roblox.com/Users/" . $in['userId']), true);
                    $q ="UPDATE `b_users` SET `roblox`='{$in['userId']}' WHERE `email`='{$in['email']}'";
                    $r = $db->query($q);
                    
                    if($r->errorInfo()[0] === "00000") {
                        $out['code'] = 200;
                        $out['message'] = 'Account '.$roblox_user['Username'].' verified!';
                        $out['color'] = 'green';
                    } else {
                        $out['code'] = 500;
                        $out['message'] = 'Server database error!';
                        $out['color'] = 'red';
                    }
                } else {
                    $out['code'] = 400;
                    $out['message'] = 'Missing input data';
                    $out['color'] = 'red';
                }
            } else {
                $out['code'] = 404;
                $out['message'] = 'Account not found!';
                $out['color'] = 'red';
            }
        } else {
            $out['code'] = 401;
            $out['message'] = 'Invalid token! Restart your game.';
            $out['color'] = 'red';
        }
        break;
}