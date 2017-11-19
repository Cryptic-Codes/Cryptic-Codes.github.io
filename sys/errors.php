<?
if(!isset($user) && $user['role'] == "Admin") {
	die("Sorry! Only administrators are allowed to view this page.");
} else {
	echo '
	<html>
		<head>
			<title>ServerConfig - Error Log</title>
			<style type="text/css" media="screen">
			#editor { 
				position: absolute;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
			}
			</style>
		</head>
		<body>
			<div id="editor">'.file_get_contents('error_log').'
			</div>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.7/ace.js" type="text/javascript" charset="utf-8"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.7/mode-json.js" type="text/javascript" charset="utf-8"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.7/theme-monokai.js" type="text/javascript" charset="utf-8"></script>
			<script type="text/javascript">
				var editor = ace.edit("editor");
				editor.setReadOnly(true);
				editor.setTheme("ace/theme/monokai");
			</script>
			
		</body>
	</html>
	';
}
?>