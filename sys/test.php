<?
$_REQUEST['min'] = true;

$out['in'] = 'edit_wiki';
$out['out'] = has_permission($out['in']);

$out['perms'] = $user['permissions'];