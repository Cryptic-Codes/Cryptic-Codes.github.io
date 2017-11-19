<?php
ob_start();

$startRequest = microtime(true)*1000;

function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

$dev = true;
$quick_disable_obfu = false;
require('sys/config.php');

$db = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['database']};port={$config['db']['port']};charset={$config['db']['charset']}", $config['db']['username'], $config['db']['password']);

if(isset($_POST['uid']) || isset($_COOKIE['uid']) || isset(getallheaders()['Authorization'])) {
	if(isset(getallheaders()['Authorization'])) {
		$_POST['uid'] = explode(' ', getallheaders()['Authorization'])[1];
	}
    $apiKey = $_POST['apiKey'];
    
    $user = array();
    
    if(isset($_POST['uid'])) {
        $user['uid'] = $_POST['uid'];
    } else {
        $user['uid'] = $_COOKIE['uid'];
    }
    
    if(trim($user['uid']) == "") {
    	unset($user);
    } else {
	    $user['token'] = $_POST['token'];
	    $user['email'] = $_POST['email'];
	    $user['emailVerified'] = ($_POST['emailVerified'] === 'true' ? true : false);
	
	    $q = "SELECT * FROM b_users WHERE `uid` ='{$user['uid']}'";
	    $r = $db->query($q);
	    if($r->rowCount() != 0) {
	        $userRow = $r->fetch();
	        $user['roblox'] = intval($userRow['roblox']);
	        $user['role'] = $userRow['role'];
	        $user['email'] = $userRow['email'];
	    } else {
	    	if(isset($user['uid']) && isset($user['email'])) {
		        $p = $db->prepare("INSERT INTO `b_users` (`uid`, `email`, `role`, `roblox`) VALUES (:uid, :email, DEFAULT, NULL)");
		        $p->bindParam(':uid', $user['uid'], PDO::PARAM_STR);
		        $p->bindParam(':email', $user['email'], PDO::PARAM_STR);
		        $p->execute();
		        $out['sqlError'] = $p->errorInfo();
		        $user['role'] = 'User';
		        $user['roblox'] = null;
	    	}
	    }
	    
	    
		$roles = json_decode(file_get_contents('.assets/json/roles.json'), true);
		function get_role_perms($role) {
			global $roles;
			global $out;
			$perms = array();
			foreach($roles[$role]['permissions'] as $perm) {
				if(substr($perm, 0, 4) == 'role') {
					$perms = array_merge($perms, get_role_perms(substr($perm, 5)));
				} else {
					array_push($perms, $perm);
				}
			}
			return $perms;
		}
		$user['permissions'] = get_role_perms(strtolower($user['role']));
		
	    
	    $out['user'] = array('uid'=>$user['uid'], 'roblox'=>$user['roblox'], 'role'=>$user['role']);
    }
}

function has_permission($perm) {
	global $user;
	if(isset($user)) {
		if(isset($user['permissions'])) {
			return in_array(strtolower($perm), $user['permissions']);
		}
		return false;
	}
	return false;
}

chdir("/home/allml1/BloxAnalytics/");

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
$remoteIP = getRealIpAddr();
header("x-page-path: " . $page_path);
if($remoteIP !== "162.211.80.219" && substr($page_path, 0, 10) !== "/__/roblox" && substr($page_path, 0, 7) !== "/assets" && substr($page_path, 0, 5) !== "/chat") {
    $requestQuery = "INSERT INTO `b_requests` (`id`, `ip`, `page`, `time`, `cfduid`, `user`) VALUES (NULL, '{$remoteIP}', '{$page_path}', CURRENT_TIMESTAMP, '{$_COOKIE['__cfduid']}', '{$_REQUEST['uid']}')";
    $request = $db->query($requestQuery);
}

chdir("/home/allml1/BloxAnalytics/");
if(isset($user) && $user['role'] == "Admin") {
    if($page_path == "/errors" || $page_path == "/error_log" || $page_path == "/errors.txt" || $page_path == "/error_log.txt"){
        // echo "<pre>";
        // require("error_log");
        // echo "</pre>";
        // die();
        require('sys/errors.php');
        die();
    }
}

$page_path_parts = pathinfo($page_path);
if(substr($page_path, 0, 3) == "/__") {
    require("part.php");
    die();
} elseif(substr($page_path, 0, 5) == "/chat") {
    require('sys/chat.php');
    die();
}  elseif(substr($page_path, 0, 14) == "/manifest.json") {
    require('manifest.json');
    die();
}  elseif(substr($page_path, 0, 2) == "/_" || substr($page_path, 0, 7) == "/static") {
	if(substr($page_path, 0, 7) == "/static") {
		header("Location: https://static.al1l.com" . substr($page_path, 7));
	} elseif(substr($page_path, 0, 2) == "/_") {
		header("Location: https://static.al1l.com" . substr($page_path, 2));
	} else {
		echo 'Error!';
	}
	die();
    function FileSizeConvert($bytes) {
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2))).$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
    $new_path = '/home/allml1/Static/' . substr($page_path, 3);
    if(substr($page_path, 0, 7) == "/static")
        $new_path = '/home/allml1/Static/' . substr($page_path, 8);
    if(is_file($new_path)) {
        $path_parts = pathinfo($new_path);
        
        require("sys/mime.php");
        header("Content-type: text/plain");
        
        echo file_get_contents($new_path);        
    } elseif(is_dir($new_path)) {
        echo '<html><head><title>Index of '.$page_path.'</title></head><body><h1>Index of '.$page_path.'</h1><table><tbody><tr><th valign="top">&nbsp;</th><th><a href="?C=N;O=D">Name</a></th><th><a href="?C=M;O=A">Last modified</a></th><th><a href="?C=S;O=A">Size</a></th><th><a href="?C=D;O=A">Description</a></th></tr><tr><th colspan="5"><hr></th></tr>';
        echo '<tr><td valign="top">&nbsp;</td><td><a href="'.dirname($page_path, 1).'">Parent Directory</a></td><td>&nbsp;</td><td align="right">  - </td><td>&nbsp;</td></tr>';
        $file_and_dirs = array_diff(scandir($new_path), array('..', '.'));
        foreach($file_and_dirs as $file ) {
            $file_path = $new_path . $file;
            if(is_dir($file_path)) $file = $file . '/';
            $size = FileSizeConvert(filesize($file_path));
            $time = date ("Y-m-d h:i:s", filemtime($file_path));
            $link = $page_path.'/'.$file;
            echo '<tr><td valign="top">&nbsp;</td><td><a href="'.$link.'">'.$file.'</a></td><td align="right">'.$time.'</td><td align="right">'.$size.'</td><td>&nbsp;</td></tr>';
        }
        echo '<tr><th colspan="5"><hr></th></tr></tbody></table></body></html>';
    } else {
        echo '404: Not found.';
    }
    die();
} elseif(substr($page_path, 0, 7) == "/assets") {
    $new_path = __DIR__ . substr($page_path, 0, 1) . '.' . substr($page_path, 1);
    if(is_file($new_path)) {
        ob_start();
        $path_parts = pathinfo($new_path);
        $contents = file_get_contents($new_path);
        
        require("sys/mime.php");
        header("Content-type: " . $mime_types[$path_parts['extension']]);
        $my_js_files = array(
            '/assets/js/main.min.js', 
            
            '/assets/js/app.min.js', 
            
            '/assets/js/chat.min.js'
        );
        if(($path_parts['extension'] == 'js' || $path_parts['extension'] == 'css') && !in_array($page_path, $my_js_files)) {
            echo '/*! '.$page_path.' */'.PHP_EOL;
        }
        if(in_array($page_path, $my_js_files) && !$quick_disable_obfu) {
            echo '/*! @license BloxAdmin v1.0
'.$page_path.'

---

Copyright (c) 2017, Allen Lantz,

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE. */'.PHP_EOL;
            require("sys/Packer.php");
            $packer = new Packer($contents, 'Normal', true, true, true);
            $contents = $packer->pack();
        }
        
        echo $contents;
        ob_flush();
    } elseif(is_dir($new_path)) {
        $root = $new_path;

        function is_in_dir($file, $directory, $recursive = true, $limit = 1000) {
            $directory = realpath($directory);
            $parent = realpath($file);
            $i = 0;
            while ($parent) {
                if ($directory == $parent) return true;
                if ($parent == dirname($parent) || !$recursive) break;
                $parent = dirname($parent);
            }
            return false;
        }
        
        $path = null;
        if (isset($_GET['file'])) {
            $path = $_GET['file'];
            if (!is_in_dir($_GET['file'], $root)) {
                $path = null;
            } else {
                $path = '/'.$path;
            }
        }
        
        if (is_file($root.$path)) {
            readfile($root.$path);
            return;
        }
        
        if ($path) echo '<a href="' . $page_path . '/' .urlencode(substr(dirname($root.$path), strlen($root) + 1)).'">..</a><br />';
        foreach (glob($root.$path.'/*') as $file) {
            $file = realpath($file);
            $link = substr($file, strlen($root) + 1);
            echo '<a href="' . $page_path . '/' .urlencode($link).'">'.basename($file).'</a><br />';
        }
    } else {
        echo '404: Not Found!';
    }
    die();
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"/>

	<title>BloxAdmin | Loading</title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="application-name" content="BloxAdmin"/>
    <meta name="apple-mobile-web-app-title" content="BloxAdmin"/>
    <meta name="theme-color" content="#e53935"/>
    <meta name="msapplication-navbutton-color" content="#e53935"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="msapplication-starturl" content="/dashboard"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=5" />
    
    <meta property="og:site_name" content="BloxAdmin" />
    <meta property="og:title" content="BloxAdmin" />
    <meta property="og:description" content="BloxAdmin was created to create a better way to manage your ROBLOX places in a way that is safe and easy. With BloxAdmin you have full control on how your place is managed and best of all it's free" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://blox.al1l.com/" />
    <meta property="og:image" content="https://t0.rbxcdn.com/68af1749afc5555759604356b3d6d6bc" />
    <meta property="og:image:alt" content="Roblox Admin" />

    <link rel="manifest" href="/manifest.json">
	<link rel="apple-touch-icon" sizes="76x76" href="/assets/img/apple-icon.png" />
	<link rel="icon" type="image/png" href="/assets/img/favicon.png" />
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/css/material-dashboard.css" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300|Material+Icons' rel='stylesheet' type='text/css' />
    
    <style>
    #loader {
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        background: #ececec;
        z-index: 10000;
    }

    #loaderContent {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: fadein 2s;
        -webkit-animation: fadein 2s;
        -moz-animation: fadein 2s;
        -ms-animation: fadein 2s;
        -o-animation: fadein 2s;
    }

    #loaderImage {
        height: 160px;
        width: 160px;
    }

    @keyframes fadein {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @-moz-keyframes fadein {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @-webkit-keyframes fadein {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @-ms-keyframes fadein {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @-o-keyframes fadein {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    
    .alpha {
        position: fixed;
        top: 0;
        z-index: 20000;
        width: 100%;
        pointer-events: none
    }
    
    .alpha-content {
        text-align: center;
        padding: 5px 21px;
        margin: 0 auto;
    }
    
    .alpha-text {
        color: black;
        padding: 5px 21px;
        font-weight: bold;
        animation: fadein 2s;
        -webkit-animation: fadein 2s;
        -moz-animation: fadein 2s;
        -ms-animation: fadein 2s;
        -o-animation: fadein 2s;
        /* text-shadow: -1px 0 white, 0 1px white, 1px 0 white, 0 -1px white; */
    }
    
    body {
        overflow: hidden !important;
    }
	
	.loader {
		position: fixed;
		top: 0;
		z-index: 20000;
		pointer-events: none;
		height: 3px;
		background: red;
	}
	
	.loader.hide {
		opacity: 0;
		transition: opacity 1.3s;
	}
    </style>
</head>

<body>
    <div class="loader" style="width: 0%"></div>
    <div class="alpha">
        <div class="alpha-content">
            <p class="alpha-text"><?php echo ucwords($config['app']['stage']); ?> version <?php echo $config['app']['version']; ?> </p>
        </div>
    </div>
    <div id="loader">
        <div id="loaderContent">
            <svg id="loaderImage" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-dual-ring" style="background: none;">
                <circle cx="50" cy="50" ng-attr-r="{{config.radius}}" ng-attr-stroke-width="{{config.width}}" ng-attr-stroke="{{config.c1}}" ng-attr-stroke-dasharray="{{config.dasharray}}" fill="none" stroke-linecap="round" r="30" stroke-width="3" stroke="#f44336" stroke-dasharray="47.12388980384689 47.12388980384689"
                transform="rotate(24 50 50)">
                    <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
                </circle>
                <circle cx="50" cy="50" ng-attr-r="{{config.radius2}}" ng-attr-stroke-width="{{config.width}}" ng-attr-stroke="{{config.c2}}" ng-attr-stroke-dasharray="{{config.dasharray2}}" ng-attr-stroke-dashoffset="{{config.dashoffset2}}" fill="none" stroke-linecap="round"
                r="26" stroke-width="3" stroke="#ff9800" stroke-dasharray="40.840704496667314 40.840704496667314" stroke-dashoffset="40.840704496667314" transform="rotate(-24 50 50)">
                    <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;-360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
                </circle>
            </svg>
        </div>
    </div>
    <script src="https://www.gstatic.com/firebasejs/4.3.0/firebase.js"></script>
	<div class="wrapper">

	    <div class="sidebar" data-color="red" data-image="../assets/img/bg.webp">
			<div class="logo">
				<a href="/" class="simple-text" id="navTitle">
					<i class="fa fa-refresh fa-spin"></i>&nbsp; Loading
				</a>
			</div>

	    	<div class="sidebar-wrapper">
	            <ul class="nav" id="navList">
	                <li id="nav_dashboard">
	                    <a href="/dashboard">
	                        <i class="material-icons">dashboard</i>
	                        <p>Dashboard</p>
	                    </a>
	                </li>
	                <li id="nav_account">
	                    <a href="/account">
	                        <i class="material-icons">account_circle</i>
	                        <p>Account</p>
	                    </a>
	                </li>
	                <li id="nav_admin" class="hide">
	                    <a href="/admin">
	                        <i class="material-icons">security</i>
	                        <p>Admin</p>
	                    </a>
	                </li>
	                <li id="nav_wiki">
	                    <a href="/wiki/">
	                        <i class="material-icons">info_outline</i>
	                        <p>Wiki</p>
	                    </a>
	                </li>
	            </ul>
	    	</div>
	    </div>

	    <div class="main-panel">
			<nav class="navbar navbar-transparent navbar-absolute">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="#" id="cardsTitle"><i class="fa fa-refresh fa-spin"></i>&nbsp; Loading</a>
					</div>
					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav navbar-right">
							<li class="hide" id="cardNav_dashboard">
								<a href="/dashboard" class="dropdown-toggle" data-toggle="dropdown">
									<i class="material-icons">dashboard</i>
									<p class="hidden-lg hidden-md">Dashboard</p>
								</a>
							</li>
							<li class="dropdown hide" id="cardNav_notifications">
								<a href="#" class="dropdown-toggle ignore-link" data-toggle="dropdown">
									<i class="material-icons">notifications</i>
									<span class="notification">5</span>
									<p class="hidden-lg hidden-md">Notifications</p>
								</a>
								<ul class="dropdown-menu">
									<li><iframe src="https://blox.al1l.com/__/notifications" id="notification_frame" style="width:100%; height:100%; border: 0;"></iframe></li>
									<!--<li><a href="#" class="ignore-link">Mike John responded to your email</a></li>
									<li><a href="#" class="ignore-link">You have 5 new tasks</a></li>
									<li><a href="#" class="ignore-link">You're now friend with Andrew</a></li>
									<li><a href="#" class="ignore-link">Another Notification</a></li>
									<li><a href="#" class="ignore-link">Another One</a></li>-->
								</ul>
							</li>
							<li class="hide" id="cardNav_signout">
								<a href="#" class="dropdown-toggle ignore-link" data-toggle="dropdown">
	 							   <i class="material-icons">lock</i>
	 							   <p class="hidden-lg hidden-md">SIGN OUT</p>
	 							   <span class="hidden-sm hidden-xs">SIGN OUT</span>
		 						</a>
							</li>
							<li class="hide" id="cardNav_signin">
								<a href="/account/signin" class="dropdown-toggle" data-toggle="dropdown">
	 							   <i class="material-icons">lock_open</i>
	 							   <p class="hidden-lg hidden-md">SIGN IN</p>
	 							   <span class="hidden-sm hidden-xs">SIGN IN</span>
		 						</a>
							</li>
							<li class="hide" id="cardNav_search">
							</li>
						</ul>

						<form class="navbar-form navbar-right hide" role="search" id="cardNav_search">
							<div class="form-group  is-empty">
								<input type="text" class="form-control" placeholder="Search" id="search_input">
								<span class="material-input"></span>
							</div>
							<button type="submit" class="btn btn-white btn-round btn-just-icon" id="search_go">
								<i class="material-icons">search</i><div class="ripple-container"></div>
							</button>
						</form>
					</div>
				</div>
			</nav>

			<div class="content">
				<div class="container-fluid">
                    <div class="row" id="cards">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="card">
                                <div class="card-content">
                                    <div class="text-center">
                                        <h3><i class="fa fa-refresh fa-spin"></i>&nbsp; Loading</h3>
                                        <p>Please wait while the page loads.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>

			<footer class="footer">
				<div class="container-fluid">
					<nav class="pull-left">
						<ul>
							<li>
								<a href="#">
									Home
								</a>
							</li>
							<li>
								<a href="#">
									Company
								</a>
							</li>
							<li>
								<a href="#">
									Portfolio
								</a>
							</li>
							<li>
								<a href="#">
								   Blog
								</a>
							</li>
						</ul>
					</nav>
					<p class="copyright pull-right">
						&copy; 2017 <a href="http://www.creative-tim.com">Creative Tim</a>, made with love for a better web
					</p>
				</div>
			</footer>
		</div>
	</div>
    <div id="modals"></div>
    <iframe id='gamelaunch' class='hidden'></iframe>
    
</body>

	<!--   Core JS Files   -->
	<script src="/assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="/assets/js/jquery.cookie-1.4.1.min.js" type="text/javascript"></script>
	<script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="/assets/js/material.min.js" type="text/javascript"></script>
    <script src="//cdn.ckeditor.com/4.7.3/full/ckeditor.js"></script>

	<!--  Charts Plugin
	<script src="/assets/js/chartist.min.js"></script> -->

	<!--  Notifications Plugin    -->
	<script src="/assets/js/bootstrap-notify.js"></script>

	<!-- Material Dashboard javascript methods -->
	<script src="/assets/js/material-dashboard.js"></script>
    
	<!-- Main -->
<? if($dev || boolval($_REQUEST['dev'])) { ?>
	<script src="/assets/js/main.js"></script>
	<script src="/assets/js/chat.js"></script>
<? } else { ?>
	<!-- Main -->
	<script src="/assets/js/main.min.js"></script>
	<script src="/assets/js/chat.min.js"></script>
<? } ?>

</html>
<?php
$len = ob_get_length();
echo '<!-- Content-Length: ' . $len . ' -->';
global $startRequest;
header('x-time-took: ' . microtime(true)*1000 - $startRequest);
header('Content-Length: ' . $len);
header('x-Content-Length: ' . $len);
ob_end_flush();
?>