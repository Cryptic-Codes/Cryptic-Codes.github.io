<?php

if(isset($user)) {
    switch(strtolower($args[1])) {
        case 'bundle':
        case 'user-bundle':
        case 'place-bundle':
        case 'server-bundle':
            if(!$_REQUEST['select'])
                $_REQUEST['select'] = "*";
            $select = array();
            foreach(explode(',', $_REQUEST['select']) as $i=>$a) {
                $split = explode('#', $a);
                if($split[1] === null) {
                    $select[strtolower(trim($split[0]))] = array();
                } else {
                    $select[strtolower(trim($split[0]))] = explode(':', $split[1]);
                }
            }
            // $out[sel] = $select;
            function sel($key, $val = null) {
                if($_REQUEST['select'] == "*")
                    return true;
                
                global $select;
                $key = strtolower($key);
                if($val === null) {
                    return array_key_exists($key, $select);
                } else {
                    return in_array($val, $select[$key]);
                }
                return false;
            }
            $_POST['serverId'] = $_REQUEST['subject'];
            $out['id'] = $_POST['serverId'];
            $out['subject'] = $_REQUEST['subject'];
            switch(strtolower($args[1])) {
		        case 'user-bundle':
		        	break;
		        case 'place-bundle':
		        	break;
            	case 'server-bundle':
            	case 'bundle':
		            $q = "SELECT * FROM b_bundles WHERE `server` ='{$_POST['serverId']}'"; // AND `isOpen`='1'
		            $r = $db->query($q);
		            if($r->rowCount() != 0) {
		                $bundleRow = $r->fetch(PDO::FETCH_ASSOC);
		                $fullBundle = json_decode($bundleRow['data'], true);
		                $bundle = array();
		                
		                $out['code'] = 200;
		                $out['message'] = "Bundle Built";
		                
		                if(sel('connections'))
		                    $bundle['connections'] = $fullBundle['connections'];
		                if(sel('chat'))
		                    $bundle['chat'] = $fullBundle['chat'];
		                if(sel('users'))
		                    $bundle['users'] = $fullBundle['users'];
		                if(sel('queue'))
		                    $bundle['queue'] = $fullBundle['queue'];
		                if(sel('averageConnectionTime'))
		                    $bundle['averageConnectionTime'] = $fullBundle['averageConnectionTime'];
		                if(sel('totalUsers'))
		                    $bundle['totalUsers'] = $fullBundle['totalUsers'];
		                
		                $out['bundle'] = $bundle;
		            } else {
		                $q = "SELECT * FROM b_servers WHERE `id` ='{$_POST['serverId']}'"; // AND `isOpen`='1'
		                $r = $db->query($q);
		                if($r->rowCount() != 0) {
		                    $serverRow = $r->fetch(PDO::FETCH_ASSOC);
		                    
		                    $out['code'] = 200;
		                    $out['message'] = "Bundle Built";
		                    
		                    $bundle = array();
		                    // $out['err'] = $p->errorInfo();
		                    
		                    if(sel('chat')) {
		                        $condition = "`server`= :serverId";
		                        $setId = false;
		                        if(isset($select['chat'][0])) {
		                            if($select['chat'][0] == "after" && isset($select['chat'][1])) {
		                                $condition .= " AND `id`> :rowId";
		                            } elseif($select['chat'][0] == "before" && isset($select['chat'][1])) {
		                                $condition .= " AND `id`< :rowId";
		                            } elseif($select['chat'][0] == "id" && isset($select['chat'][1])) {
		                                $condition .= " AND `id`= :rowId";
		                            }
		                        }
		                        $p = $db->prepare("SELECT id, time, sender, username, recipient, content FROM `b_chatMessages` WHERE {$condition}");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        $bundle['chat'] = $p->fetchAll(PDO::FETCH_ASSOC);
		                    }
		                    
		                    if(sel('totalMessages')) {
		                        $p = $db->prepare("SELECT id, time, sender, username, recipient, content FROM `b_chatMessages` WHERE `server`=:serverId");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        // $out['q'] = $p;
		                        // $out['err'] = $p->errorInfo();
		                        $bundle['totalMessages'] = $p->rowCount();
		                    }
		                    
		                    if(sel('connections')) {
		                        $condition = "`server`= :serverId";
		                        $setId = false;
		                        if(isset($select['connections'][0])) {
		                            if($select['connections'][0] == "after" && isset($select['connections'][1])) {
		                                $condition .= " AND `id`> :rowId";
		                            } elseif($select['connections'][0] == "before" && isset($select['connections'][1])) {
		                                $condition .= " AND `id`< :rowId";
		                            } elseif($select['connections'][0] == "id" && isset($select['connections'][1])) {
		                                $condition .= " AND `id`= :rowId";
		                            }
		                        }
		                        if($setId)
		                            $p->bindParam(':rowId', $select['connections'][1], PDO::PARAM_INT);
		                        $p = $db->prepare("SELECT id, userId, joinTime, leaveTime FROM `b_joinHistory` WHERE {$condition}");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        $bundle['connections'] = $p->fetchAll(PDO::FETCH_ASSOC);
		                    }
		                    
		                    if(sel('averageConnectionTime')) {
		                        $condition = "`server`= :serverId";
		                        $p = $db->prepare("SELECT joinTime, leaveTime FROM `b_joinHistory` WHERE {$condition}");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        $rows = $p->fetchAll(PDO::FETCH_ASSOC);
		                        $times = array();
		                        foreach($rows as $row) {
		                            $join = strtotime($row['joinTime']);
		                            if($row['leaveTime'] == null) {
		                                $leave = time();
		                            } else {
		                                $leave = strtotime($row['leaveTime']);
		                            }
		                            $time = $leave - $join;
		                            array_push($times, $time);
		                        }
								if(count($times) == 0) {
									$bundle['averageConnectionTime'] = 0;
								} else {
									$bundle['averageConnectionTime'] = array_sum($times) / count($times);
								}
		                    }
		                    
		                    if(sel('colectiveTime')) {
		                        $condition = "`server`= :serverId";
		                        $p = $db->prepare("SELECT joinTime, leaveTime FROM `b_joinHistory` WHERE {$condition}");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        $rows = $p->fetchAll(PDO::FETCH_ASSOC);
		                        $times = array();
		                        foreach($rows as $row) {
		                            $join = strtotime($row['joinTime']);
		                            if($row['leaveTime'] == null) {
		                                $leave = time();
		                            } else {
		                                $leave = strtotime($row['leaveTime']);
		                            }
		                            $time = $leave - $join;
		                            array_push($times, $time);
		                        }
		                        $bundle['colectiveTime'] = array_sum($times);
		                    }
		                    
		                    if(sel('users')) {
		                        $p = $db->prepare("SELECT userId FROM b_joinHistory WHERE id IN (SELECT MIN(id) FROM b_joinHistory WHERE `server`=:serverId GROUP BY userId) AND `server`=:serverId");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        // $out['q'] = $p;
		                        // $out['err'] = $p->errorInfo();
		                        $bundle['users'] = $p->fetchAll(PDO::FETCH_COLUMN, 0);
		                    }
		                    
		                    if(sel('totalUsers')) {
		                        $p = $db->prepare("SELECT userId FROM b_joinHistory WHERE id IN (SELECT MIN(id) FROM b_joinHistory WHERE `server`=:serverId GROUP BY userId) AND `server`=:serverId");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        // $out['q'] = $p;
		                        // $out['err'] = $p->errorInfo();
		                        $bundle['totalUsers'] = $p->rowCount();
		                    }
		                    
		                    if(sel('onlineUsers')) {
		                        $p = $db->prepare("SELECT userId FROM b_joinHistory WHERE id IN (SELECT MIN(id) FROM b_joinHistory WHERE `server`=:serverId AND `leaveTime` is NULL GROUP BY userId) AND `server`=:serverId");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        // $out['q'] = $p;
		                        // $out['err'] = $p->errorInfo();
		                        $bundle['onlineUsers'] = $p->rowCount();
		                    }
		                    
		                    if(sel('queue')) {
		                        $p = $db->prepare("SELECT id, task, data FROM b_queue WHERE `server`=:serverId");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        // $out['q'] = $p;
		                        // $out['err'] = $p->errorInfo();
		                        $bundle['queue'] = $p->fetchAll(PDO::FETCH_ASSOC);
		                        foreach($bundle['queue'] as $i=>$q) {
		                            $j = json_decode($q['data'], true);
		                            if($j)
		                                $bundle['queue'][$i]['data'] = $j;
		                        }
		                    }
		                    
		                    if(sel('openTime')) {
		                        $p = $db->prepare("SELECT startTime, heartbeat FROM b_servers WHERE `id`=:serverId");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        // $out['q'] = $p;
		                        // $out['err'] = $p->errorInfo();
		                        $row = $p->fetch(PDO::FETCH_ASSOC);
		                        $start = strtotime($row['startTime']);
		                        $end = strtotime($row['heartbeat']);
		                        $bundle['openTime'] = $end - $start;
		                    }
		                    
		                    if(sel('lastHeatbeat')) {
		                        $p = $db->prepare("SELECT heartbeat FROM b_servers WHERE `id`=:serverId");
		                        $p->bindParam(':serverId', $_POST['serverId'], PDO::PARAM_STR);
		                        $p->execute();
		                        // $out['q'] = $p;
		                        // $out['err'] = $p->errorInfo();
		                        $bundle['lastHeatbeat'] = $p->fetch(PDO::FETCH_ASSOC)['heartbeat'];
		                    }
		                    
		                    
		                    // $out['task']['id'] = intval($db->lastInsertId());
		                    // $out['task']['serverId'] = $_POST['serverId'];
		                    // $out['task']['task'] = $_POST['task'];
		                    // $out['task']['data'] = json_decode($_POST['data']);
		                    
		                    $out['bundle'] = $bundle;
		                    
		                } else {
		                    $out = get_error(404);
		                    break;
		                }
		            } 
		            break;
            }
        case 'updatewiki':
            if($user['role'] !== "Admin") {
                $out = get_error(401);
                break;
            } else if(!isset($_POST['content']) && !isset($_POST['title']) && !isset($_POST['subtitle']) && !isset($_POST['short'])) {
                $out = get_error(400);
                break;
            }
            $q = "SELECT * FROM b_wiki WHERE `short` ='{$_POST['short']}'";
            $r = $db->query($q);
            if($r->rowCount() != 0) {
                $wikiRow = $r->fetch(PDO::FETCH_ASSOC);
                
                $p = $db->prepare("UPDATE `b_wiki` SET `title`=:title,`subtitle`=:subtitle,`content`=:content WHERE `short`=:short");
                $p->bindParam(':title', $_POST['title'], PDO::PARAM_STR);
                $p->bindParam(':subtitle', $_POST['subtitle'], PDO::PARAM_STR);
                $p->bindParam(':content', $_POST['content'], PDO::PARAM_STR);
                $p->bindParam(':short', strtolower($_POST['short']), PDO::PARAM_STR);
                $p->execute();
                if($p->errorInfo()[0] === "00000") {
                    $out['code'] = 200;
                    $out['message'] = "Page updated";
                    $q = "SELECT * FROM b_wiki WHERE `short`='{$_POST['short']}'";
                    $r = $db->query($q);
                    $wikiRow = $r->fetch(PDO::FETCH_ASSOC);
                    $out['in'] = $_POST;
                    $out['wiki'] = $wikiRow;
                    $r = $db->query($q);
                    break;
                } else {
                    $out = get_error(500);
                    $out['sql'] = $p->errorInfo();
                    break;
                }
            } else {
                $out = get_error(404);
                break;
            }
            break;
        case 'newtask':
            $q = "SELECT * FROM b_servers WHERE `id` ='{$_POST['serverId']}' AND `isOpen`='1'";
            $r = $db->query($q);
            if($r->rowCount() != 0) {
                $out['code'] = 200;
                $out['message'] = "Task sent";
                $serverRow = $r->fetch(PDO::FETCH_ASSOC);
                
                $p = $db->prepare("INSERT INTO `b_queue` (`server`, `task`, `data`) VALUES (?, ?, ?);");
                $p->execute(array($_POST['serverId'], $_POST['task'], $_POST['data']));
                
                $out['task']['id'] = intval($db->lastInsertId());
                $out['task']['serverId'] = $_POST['serverId'];
                $out['task']['task'] = $_POST['task'];
                $out['task']['data'] = json_decode($_POST['data']);
            } else {
                $out = get_error(404);
                break;
            }
            break;
        case 'authticket':
            $out['code'] = 200;
            $out['message'] = "Got game info";
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, 'https://www.roblox.com/game-auth/getauthticket');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Cookie: GuestData=UserID=0; .ROBLOSECURITY=' . $_COOKIE['robloxToken'],
                'Host: www.roblox.com',
                'Referer: https://www.roblox.com/'
            )); 
            
            $ticket = curl_exec($ch);
            curl_close($ch);
            // die();
            $out['gameInfo'] = $ticket;
            break;
        case 'place':
            if(!isset($user)) {
                $out = get_error(401);
                break;
            }
            $placeId = $_GET['place'];

            $q = "SELECT * FROM b_places WHERE `placeId` ='{$placeId}'";
            $r = $db->query($q);
            $row = $r->fetch(PDO::FETCH_ASSOC);
            if($user['role'] !== "Admin")
                if($user['roblox'] != $row['creator']) {
                    $out = get_error(401);
                    break;
                }
            $out = array_merge($out, $row);
            $out['code'] = 200;
            $out['message'] = "Place found";
            
            $qSS = "SELECT id FROM b_servers WHERE `placeId` ='{$placeId}'";
            $rSS = $db->query($qSS);
            $out['totalServers'] = $rSS->rowCount();
            
            $qSO = "SELECT id FROM b_servers WHERE `placeId`='{$placeId}' AND `isOpen`='1'";
            $rSO = $db->query($qSO);
            $out['openServers'] = $rSO->rowCount();
            $serverRows = $rSO->fetchAll(PDO::FETCH_ASSOC);
            
            $qM = "SELECT id FROM b_chatMessages WHERE `placeId`='{$placeId}'";
            $rM = $db->query($qM);
            $out['totalMessages'] = $rM->rowCount();
            
            $qPS = "SELECT id FROM b_joinHistory WHERE `placeId`='{$placeId}'";
            $rPS = $db->query($qPS);
            $out['totalPlayers'] = $rPS->rowCount();
            
            $qPO = "SELECT id FROM b_joinHistory WHERE `placeId`='{$placeId}' AND `leaveTime` is NULL";
            $rPO = $db->query($qPO);
            $out['onlinePlayers'] = $rPO->rowCount();
            
            if($rSO->rowCount() != 0) {
                foreach($serverRows as $svr) {
                    $out['servers'][$svr['id']] = true;
                }
            }
            break;
        case 'places':
            if(!isset($user)) {
                $out = get_error(401);
                break;
            }
            $out['code'] = 200;
            $out['message'] = "Places found";
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
            }
            break;
        case 'server':
            if(!isset($user)) {
                $out = get_error(401);
                break;
            }
            $uuid = $_GET['server'];
            $q = "SELECT * FROM b_servers WHERE `id` ='{$uuid}'";
            $r = $db->query($q);
            if($r->rowCount() != 0) {
                $serverRow = $r->fetch(PDO::FETCH_ASSOC);
                $players = array();
                $messages = array();
                
                $out['code'] = 200;
                $out['message'] = "Server found";
                
                $out['server']['uuid'] = $serverRow['id'];
                $out['server']['palceId'] = intval($serverRow['placeId']);
                $out['server']['isOpen'] = boolval($serverRow['isOpen']);
                $out['server']['serverInstance']  = null;
                
                $qM = "SELECT id, time, sender, recipient, content FROM b_chatMessages WHERE `server` ='{$uuid}'";
                $rM = $db->query($qM);
                $out['server']['totalMessages'] = $rM->rowCount();
                $out['server']['messages'] = array();
                if($rM->rowCount() != 0) {
                    $messagesRow = $rM->fetchAll(PDO::FETCH_ASSOC);
                    $out['server']['messages'] = $messagesRow;
                }
                
                $qPC = "SELECT id FROM b_joinHistory WHERE `server` ='{$uuid}' AND `leaveTime` is NULL";
                $rPC = $db->query($qPC);
                $out['server']['onlinePlayers'] = $rPC->rowCount();
                
                $qP = "SELECT id, userId, joinTime, leaveTime, serverInstance FROM b_joinHistory WHERE `server` ='{$uuid}'";
                $rP = $db->query($qP);
                $out['server']['totalPlayers'] = $rP->rowCount();
                $out['server']['players'] = array();
                if($rP->rowCount() != 0) {
                    $playersRow = $rP->fetchAll(PDO::FETCH_ASSOC);
                    $out['server']['players'] = $playersRow;
                    $out['server']['serverInstance'] = $playersRow[0]['serverInstance'];
                }
                
            } else {
                $out = get_error(404);
                break;
            }
            break;
        case 'roblox':
            $out['isLinked'] = ($user['roblox'] != null);
            if($user['roblox'] != null) {
                $out['code'] = 200;
                $out['message'] = "User found";
                $out['id'] = intval($user['roblox']);
                $roblox_user = json_decode(file_get_contents("https://api.roblox.com/Users/" . $user['roblox']), true);
                $out['username'] = $roblox_user['Username'];
            } else {
                $out['code'] = 404;
                $out['message'] = "User not linked";
                $out['id'] = null;
                $out['username'] = null;
            }
            break;
        case 'roblox-user':
            $out['isLinked'] = ($user['roblox'] != null);
            if(isset($args[2])) {
				$dom = new DOMDocument('1.0');
				$out = array_merge($out, json_decode(file_get_contents("https://api.roblox.com/Users/".$args[2]), true));
				$out['thumb'] = json_decode(file_get_contents("https://www.roblox.com/headshot-thumbnail/json?userId=".$args[2]."&height=150&width=150"), true)['Url'];
				@$dom->loadHTMLFile('https://www.roblox.com/users/'.$args[2].'/profile/');
				$spans = $dom->getElementsByTagName('span');
				foreach($spans as $span) {
					if($span->getAttribute('class') === "profile-about-content-text linkify")
						$out['description'] = $span->textContent;
				}
				$out['role'] = 'user';
				$tagElm = $dom->getElementById('userStatusText');
				$out['tag'] = $tagElm->textContent;
				$out['role'] = 'user';
                $p = $db->prepare("SELECT role FROM b_users WHERE `roblox`=?");
                $p->execute(array($args[2]));
				if($p->rowCount() != 0) {
					$row = $p->fetch();
					$out['role'] = $row['role'];
				}
			} else {
                $out = get_error(400);
				break;
            }
            break;
        default:
            $out = get_error(404);
            break;
    }
} else {
    $out = get_error(401);
}