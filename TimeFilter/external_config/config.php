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
	$_SESSION['BUSIT_INSTANCE'] = $_GET['instance'];
	if( $_GET['token'] )
	{
		$_SESSION['BUSIT_TOKEN'] = $_GET['token'];
		template::redirect("/timefilter/1_params");
	}
	
	$data = array(
		'instance' => security::encode($_GET['instance']),
		'requestor' => 'Busit Time Filter',
		'grants' => 'ACCESS,SELF_ACCESS,SELF_INSTANCE_SELECT,SELF_INSTANCE_UPDATE',
		'referer' => "https://{$_SERVER['HTTP_HOST']}/timefilter/1_params"
	);
	
	template::redirect("https://{$GLOBALS['CONFIG']['HOSTNAME']}/panel/instance/grant?data=" . urlencode(json_encode($data)));
}

?>