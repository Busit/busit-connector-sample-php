<?php

if( !defined('PROPER_START') )
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

if( !isset($_POST[$_SESSION['__ANTISPAM__'][0]]) || $_POST[$_SESSION['__ANTISPAM__'][0]] != $_SESSION['__ANTISPAM__'][1] || !isset($_SESSION['BUSIT_TOKEN']) || !isset($_SESSION['BUSIT_INSTANCE']) )
	template::redirect('/timefilter/1_params?e=2');

try
{
	$_POST['name'] = trim($_POST['name']);
	if( strlen($_POST['name']) <= 2 || strlen($_POST['name']) > 150 )
		template::redirect('/timefilter/1_params?e=3');
	$_SESSION['FORM']['name'] = $_POST['name'];
	
	if( !is_array($_POST['time']) )
		template::redirect('/timefilter/1_params?e=1');
	
	$_SESSION['FORM']['time'] = $_POST['time'];
	
	$time = array();
	for( $i = 0; $i < count($_POST['time']); $i++ )
	{
		$t = $_POST['time'][$i];

		if( !isset($t['day']) || !is_array($t['day']) || count($t['day']) == 0 || strlen(trim($t['from'])) <= 0 || strlen(trim($t['to'])) <= 0 )
			template::redirect('/timefilter/1_params?e=1');
		
		$t['from'] = trim($t['from']);
		$t['to'] = trim($t['to']);
		
		if( !preg_match("/^([0-9]{1,2})\\s*(?::|h)\\s*([0-9]{1,2})$/i", $t['from']) || 
			!preg_match("/^([0-9]{1,2})\\s*(?::|h)\\s*([0-9]{1,2})$/i", $t['to']) )
			template::redirect('/timefilter/1_params?e=1');
		
		foreach( $t['day'] as $d )
			if( !preg_match("/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday)$/", $d) )
				template::redirect('/timefilter/1_params?e=1');
			
		$time[] = $t;
	}
	$_SESSION['FORM']['time'] = $time;
	
	api::send('self/vanilla/instance/config/update', array('id'=>$_SESSION['BUSIT_INSTANCE'], 'data'=>json_encode($time)), "i:{$_SESSION['BUSIT_INSTANCE']}:{$_SESSION['BUSIT_TOKEN']}");

	unset($_SESSION['BUSIT_TOKEN']);
	unset($_SESSION['BUSIT_INSTANCE']);
	unset($_SESSION['__ANTISPAM__']);
	unset($_SESSION['FORM']);

	template::redirect("https://apps.busit.com/done");
}
catch(Exception $e)
{
	template::redirect('/timefilter/1_params?e=0');
}

?>
