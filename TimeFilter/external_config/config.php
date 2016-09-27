<?php

if( !defined('PROPER_START') )
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

if( !isset($_SESSION['BUSIT_INSTANCE']) && !isset($_GET['instance']) )
	$template->output("<p>{$lang['error']}</p>");
else
{
	session_unset();

	$_SESSION['BUSIT_INSTANCE'] = $_GET['instance'];
	if( $_GET['token'] )
	{
		$_SESSION['BUSIT_TOKEN'] = $_GET['token'];
		template::redirect("/timefilter/1_params");
	}
	

	$data = array(
		'instance' => security::encode($_GET['instance']),
		'requestor' => 'Busit Application Facebook',
		'grants' => 'ACCESS,SELF_ACCESS,SELF_INSTANCE_SELECT,SELF_INSTANCE_UPDATE,SELF_LINK_INSERT,SELF_LINK_DELETE',
		'referer' => "https://apps.busit.com{$_SERVER['REQUEST_URI']}"
	);
	
	template::redirect("https://apps.busit.com/auth?data=" . urlencode(json_encode($data)));
}

?>
